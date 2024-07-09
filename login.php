<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: schedule.php');
    exit;
}

include 'functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (loginUser($username, $password)) {
        header('Location: schedule.php');
        exit;
    } else {
        $error = "Invalid username or password";
    }
}

$title = "Login";
include 'templates/header.php';
?>

<div class="min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full card">
        <h1 class="text-3xl font-bold mb-2">Login</h1>
        <form method="post" action="login.php">
            <div class="mb-4">
                <label for="username" class="block text-gray-700">Username</label>
                <input type="text" name="username" class="mt-1 p-2 w-full rounded">
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700">Password</label>
                <input type="password" name="password" class="mt-1 p-2 w-full rounded">
            </div>
            <button type="submit" class="button">Login</button>
        </form>
        <?php if (isset($error)) { echo "<p class='text-red-500 mt-4'>$error</p>"; } ?>
        <div class="mt-4">
            <a href="register.php" class="text-blue-500">Register</a> |
            <a href="forgot_password.php" class="text-blue-500">Forgot Password?</a>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
