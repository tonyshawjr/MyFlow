<?php
include 'functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if ($password === $password_confirm) {
        $email = getEmailByToken($token);

        if ($email) {
            updatePassword($email, $password);
            deleteResetToken($token);
            $msg = "Password reset successfully.";
        } else {
            $msg = "Invalid token.";
        }
    } else {
        $msg = "Passwords do not match.";
    }
}

$title = "Reset Password";
include 'templates/header.php';
?>

<div class="min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full card">
        <h1 class="text-3xl font-bold mb-2">Reset Password</h1>
        <form method="post" action="reset_password.php">
            <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">
            <div class="mb-4">
                <label for="password" class="block text-gray-700">New Password</label>
                <input type="password" name="password" class="mt-1 p-2 w-full border rounded">
            </div>
            <div class="mb-4">
                <label for="password_confirm" class="block text-gray-700">Confirm Password</label>
                <input type="password" name="password_confirm" class="mt-1 p-2 w-full border rounded">
            </div>
            <button type="submit" class="button">Reset Password</button>
        </form>
        <?php if (isset($msg)) { echo "<p class='mt-4'>$msg</p>"; } ?>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
