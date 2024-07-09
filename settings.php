<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include 'functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate database settings
    if (empty($_POST['db_host']) || empty($_POST['db_user']) || empty($_POST['db_name'])) {
        $error = "Database settings cannot be empty.";
    } else {
        $settings = [
            'trello_api_key' => $_POST['trello_api_key'],
            'trello_token' => $_POST['trello_token'],
            'trello_board_id' => $_POST['trello_board_id'],
            'openai_api_key' => $_POST['openai_api_key'],
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'username' => $_POST['username'],
            'email' => $_POST['email'],
            'timezone' => $_POST['timezone'],
            'db_host' => $_POST['db_host'],
            'db_user' => $_POST['db_user'],
            'db_pass' => $_POST['db_pass'],
            'db_name' => $_POST['db_name']
        ];
        updateUserSettings($_SESSION['user_id'], $settings);
        updateDatabaseConfig($settings);
        header('Location: schedule.php');
        exit;
    }
}

$settings = getUserSettings($_SESSION['user_id']);
$title = "Settings";
include 'templates/header.php';

// List of timezones
$timezones = DateTimeZone::listIdentifiers();
?>

<div class="min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full card">
        <h1 class="text-3xl font-bold mb-2">Settings</h1>
        <form method="post" action="settings.php">
            <div class="mb-4">
                <label for="first_name" class="block text-gray-700">First Name</label>
                <input type="text" name="first_name" class="mt-1 p-2 w-full rounded" value="<?php echo $settings['first_name']; ?>">
            </div>
            <div class="mb-4">
                <label for="last_name" class="block text-gray-700">Last Name</label>
                <input type="text" name="last_name" class="mt-1 p-2 w-full rounded" value="<?php echo $settings['last_name']; ?>">
            </div>
            <div class="mb-4">
                <label for="username" class="block text-gray-700">Username</label>
                <input type="text" name="username" class="mt-1 p-2 w-full rounded" value="<?php echo $settings['username']; ?>">
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700">Email</label>
                <input type="text" name="email" class="mt-1 p-2 w-full rounded" value="<?php echo $settings['email']; ?>">
            </div>
            <div class="mb-4">
                <label for="trello_api_key" class="block text-gray-700">Trello API Key</label>
                <input type="text" name="trello_api_key" class="mt-1 p-2 w-full rounded" value="<?php echo $settings['trello_api_key']; ?>">
            </div>
            <div class="mb-4">
                <label for="trello_token" class="block text-gray-700">Trello Token</label>
                <input type="text" name="trello_token" class="mt-1 p-2 w-full rounded" value="<?php echo $settings['trello_token']; ?>">
            </div>
            <div class="mb-4">
                <label for="trello_board_id" class="block text-gray-700">Trello Board ID</label>
                <input type="text" name="trello_board_id" class="mt-1 p-2 w-full rounded" value="<?php echo $settings['trello_board_id']; ?>">
            </div>
            <div class="mb-4">
                <label for="openai_api_key" class="block text-gray-700">OpenAI API Key</label>
                <input type="text" name="openai_api_key" class="mt-1 p-2 w-full rounded" value="<?php echo $settings['openai_api_key']; ?>">
            </div>
            <div class="mb-4">
                <label for="timezone" class="block text-gray-700">Timezone</label>
                <select name="timezone" class="mt-1 p-2 w-full rounded">
                    <?php foreach ($timezones as $timezone): ?>
                        <option value="<?php echo $timezone; ?>" <?php if ($timezone == $settings['timezone']) echo 'selected'; ?>><?php echo $timezone; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label for="db_host" class="block text-gray-700">Database Host</label>
                <input type="text" name="db_host" class="mt-1 p-2 w-full rounded" value="<?php echo $settings['db_host']; ?>">
            </div>
            <div class="mb-4">
                <label for="db_user" class="block text-gray-700">Database User</label>
                <input type="text" name="db_user" class="mt-1 p-2 w-full rounded" value="<?php echo $settings['db_user']; ?>">
            </div>
            <div class="mb-4">
                <label for="db_pass" class="block text-gray-700">Database Password</label>
                <input type="text" name="db_pass" class="mt-1 p-2 w-full rounded" value="<?php echo $settings['db_pass']; ?>">
            </div>
            <div class="mb-4">
                <label for="db_name" class="block text-gray-700">Database Name</label>
                <input type="text" name="db_name" class="mt-1 p-2 w-full rounded" value="<?php echo $settings['db_name']; ?>">
            </div>
            <button type="submit" class="button">Update</button>
        </form>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
