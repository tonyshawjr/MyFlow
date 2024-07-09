<?php
include 'functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $token = bin2hex(random_bytes(50));

    if (userExists($email)) {
        saveResetToken($email, $token);
        $reset_link = "http://yourdomain.com/reset_password.php?token=$token";
        $subject = "Password Reset Request";
        $message = "Click the link to reset your password: $reset_link";
        $headers = "From: no-reply@yourdomain.com";

        if (mail($email, $subject, $message, $headers)) {
            $msg = "Check your email for the password reset link.";
        } else {
            $msg = "Failed to send email.";
        }
    } else {
        $msg = "Email does not exist.";
    }
}

$title = "Forgot Password";
include 'templates/header.php';
?>

<div class="min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full card">
        <h1 class="text-3xl font-bold mb-2">Forgot Password</h1>
        <form method="post" action="forgot_password.php">
            <div class="mb-4">
                <label for="email" class="text-gray-700">Email</label>
                <input type="email" name="email" class="mt-1 p-2 w-full rounded">
            </div>
            <button type="submit" class="button">Submit</button>
        </form>
        <?php if (isset($msg)) { echo "<p class='mt-4'>$msg</p>"; } ?>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
