<footer class="p-4 mt-8">
    <div class="container mx-auto text-center">
        &copy; <?php echo date('Y'); ?> MyFlow. All rights reserved.
        <?php if (isset($_SESSION['user_id'])): ?>
            <p class="text-gray-500 text-sm">Last Trello check: <?php echo $last_check_time; ?></p>
            <a href="schedule.php?refresh=1" class="text-blue-500 text-sm">Manual Refresh</a>
        <?php endif; ?>
    </div>
</footer>
</body>
</html>
