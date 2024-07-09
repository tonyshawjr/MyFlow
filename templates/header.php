<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body class="<?php echo isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light-mode'; ?>">
<nav class="header p-4">
    <div class="container mx-auto flex justify-between items-center">
        <a href="index.php" class="site-name font-bold text-xl">MyFlow</a>
        <div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="settings.php" class="mr-4">Settings</a>
                <a href="logout.php" class="mr-4">Logout</a>
            <?php endif; ?>
            <button onclick="toggleTheme()" class="focus:outline-none">
                <i id="theme-icon" class="fas <?php echo isset($_COOKIE['theme']) && $_COOKIE['theme'] == 'dark-mode' ? 'fa-sun' : 'fa-moon'; ?>"></i>
            </button>
        </div>
    </div>
</nav>
<script>
function toggleTheme() {
    const body = document.body;
    const icon = document.getElementById('theme-icon');
    body.classList.toggle('dark-mode');
    body.classList.toggle('light-mode');
    const newTheme = body.classList.contains('dark-mode') ? 'dark-mode' : 'light-mode';
    document.cookie = `theme=${newTheme}; path=/;`;
    icon.classList.toggle('fa-sun');
    icon.classList.toggle('fa-moon');
}

document.addEventListener('DOMContentLoaded', () => {
    const themeCookie = document.cookie.split('; ').find(row => row.startsWith('theme='));
    const theme = themeCookie ? themeCookie.split('=')[1] : 'light-mode';
    const body = document.body;
    const icon = document.getElementById('theme-icon');
    body.classList.add(theme);
    if (theme === 'dark-mode') {
        icon.classList.remove('fa-moon');
        icon.classList.add('fa-sun');
    } else {
        icon.classList.remove('fa-sun');
        icon.classList.add('fa-moon');
    }
});
</script>
<div class="container mx-auto mt-8">
