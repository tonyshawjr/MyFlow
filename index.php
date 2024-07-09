<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$title = "Welcome";
include 'templates/header.php';
?>

<div class="min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full card">
        <h1 class="text-3xl font-bold mb-2">Welcome to MyFlow</h1>
        <a href="schedule.php" class="text-blue-500">Go to Schedule</a> | 
        <a href="settings.php" class="text-blue-500">Settings</a> | 
        <a href="logout.php" class="text-blue-500">Logout</a>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
