<?php
include 'config.php';

function registerUser($username, $password, $first_name, $last_name) {
    global $conn;
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT INTO users (username, password, first_name, last_name) VALUES (:username, :password, :first_name, :last_name)");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->execute();
}

function loginUser($username, $password) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['timezone'] = $user['timezone']; // Add timezone to session
        return true;
    } else {
        return false;
    }
}

function userExists($email) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
}

function saveResetToken($email, $token) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO password_resets (email, token) VALUES (:email, :token)");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':token', $token);
    $stmt->execute();
}

function getEmailByToken($token) {
    global $conn;
    $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = :token");
    $stmt->bindParam(':token', $token);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['email'] : false;
}

function updatePassword($email, $password) {
    global $conn;
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("UPDATE users SET password = :password WHERE username = :email");
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
}

function deleteResetToken($token) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = :token");
    $stmt->bindParam(':token', $token);
    $stmt->execute();
}

function getUserSettings($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateUserSettings($user_id, $settings) {
    global $conn;
    $stmt = $conn->prepare("UPDATE users SET trello_api_key = :trello_api_key, trello_token = :trello_token, trello_board_id = :trello_board_id, openai_api_key = :openai_api_key, first_name = :first_name, last_name = :last_name, username = :username, email = :email, timezone = :timezone, db_host = :db_host, db_user = :db_user, db_pass = :db_pass, db_name = :db_name WHERE id = :user_id");
    $stmt->bindParam(':trello_api_key', $settings['trello_api_key']);
    $stmt->bindParam(':trello_token', $settings['trello_token']);
    $stmt->bindParam(':trello_board_id', $settings['trello_board_id']);
    $stmt->bindParam(':openai_api_key', $settings['openai_api_key']);
    $stmt->bindParam(':first_name', $settings['first_name']);
    $stmt->bindParam(':last_name', $settings['last_name']);
    $stmt->bindParam(':username', $settings['username']);
    $stmt->bindParam(':email', $settings['email']);
    $stmt->bindParam(':timezone', $settings['timezone']);
    $stmt->bindParam(':db_host', $settings['db_host']);
    $stmt->bindParam(':db_user', $settings['db_user']);
    $stmt->bindParam(':db_pass', $settings['db_pass']);
    $stmt->bindParam(':db_name', $settings['db_name']);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
}

function updateDatabaseConfig($settings) {
    $config_file = 'config.php';
    $config_data = "<?php\n";
    $config_data .= "\$servername = '{$settings['db_host']}';\n";
    $config_data .= "\$username = '{$settings['db_user']}';\n";
    $config_data .= "\$password = '{$settings['db_pass']}';\n";
    $config_data .= "\$dbname = '{$settings['db_name']}';\n";
    $config_data .= "try {\n";
    $config_data .= "\t\$conn = new PDO(\"mysql:host=\$servername;dbname=\$dbname\", \$username, \$password);\n";
    $config_data .= "\t\$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);\n";
    $config_data .= "} catch (PDOException \$e) {\n";
    $config_data .= "\techo \"Connection failed: \" . \$e->getMessage();\n";
    $config_data .= "}\n";
    $config_data .= "?>";

    file_put_contents($config_file, $config_data);
}

function getLastTrelloCheckTime($user_id, $timezone) {
    $cache_file = 'schedule_cache_' . $user_id . '.json';
    if (file_exists($cache_file)) {
        $cache_data = json_decode(file_get_contents($cache_file), true);
        $date = new DateTime($cache_data['date'], new DateTimeZone('UTC'));
        $date->setTimezone(new DateTimeZone($timezone));
        return $date->format('F d, Y \a\t h:i A');
    }
    return null;
}
?>
