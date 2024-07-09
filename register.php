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
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];

    registerUser($username, $password, $first_name, $last_name);
    header('Location: login.php');
    exit;
}

$title = "Register";
include 'templates/header.php';
?>

<div class="min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full card">
        <h1 class="text-3xl font-bold mb-2">Register</h1>
        <form method="post" action="register.php">
            <div class="mb-4">
                <label for="username" class="block text-gray-700">Username</label>
                <input type="text" name="username" class="mt-1 p-2 w-full rounded">
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700">Password</label>
                <input type="password" name="password" class="mt-1 p-2 w-full rounded">
            </div>
            <div class="mb-4">
                <label for="first_name" class="block text-gray-700">First Name</label>
                <input type="text" name="first_name" class="mt-1 p-2 w-full rounded">
            </div>
            <div class="mb-4">
                <label for="last_name" class="block text-gray-700">Last Name</label>
                <input type="text" name="last_name" class="mt-1 p-2 w-full rounded">
            </div>
            <button type="submit" class="button">Register</button>
        </form>
        <div class="mt-4">
            <a href="login.php" class="text-blue-500">Already have an account? Login</a>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
