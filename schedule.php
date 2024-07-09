<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include 'functions.php';
include 'schedule_logic.php';

$settings = getUserSettings($_SESSION['user_id']);
$first_name = $settings['first_name'];
$title = "Today's Schedule";

include 'templates/header.php';

// Cache logic
$cache_file = 'schedule_cache_' . $_SESSION['user_id'] . '.json';
$refresh = isset($_GET['refresh']) ? true : false;
if (file_exists($cache_file) && !$refresh) {
    $cache_data = json_decode(file_get_contents($cache_file), true);
    $cache_date = new DateTime($cache_data['date']);
    $current_date = new DateTime();

    if ($cache_date->format('Y-m-d') == $current_date->format('Y-m-d')) {
        $events = $cache_data['events'];
    } else {
        list($lists, $cards) = getTrelloData($settings);
        $events = analyzeTasks($lists, $cards);
        file_put_contents($cache_file, json_encode(['date' => $current_date->format('Y-m-d'), 'events' => $events]));
    }
} else {
    list($lists, $cards) = getTrelloData($settings);
    $events = analyzeTasks($lists, $cards);
    file_put_contents($cache_file, json_encode(['date' => (new DateTime())->format('Y-m-d'), 'events' => $events]));
}

$today_date = (new DateTime())->format('F d, Y');
$current_time = (new DateTime())->format('H:i');
$greeting = ($current_time < '12:00') ? 'Good morning' : 'Good afternoon';
$quote = getRandomQuote();

$last_check_time = getLastTrelloCheckTime($_SESSION['user_id'], $settings['timezone']);
?>

<div class="min-h-screen flex items-center justify-center">
    <div class="card p-8 rounded-lg shadow-lg max-w-md w-full text-center">
        <h1 class="text-3xl font-bold mb-2"><?php echo $greeting; ?>, <?php echo $first_name; ?></h1>
        <p class="text-gray-500 mb-6"><?php echo $today_date; ?></p>
        <p class="text-xl italic text-center mb-4">"<?php echo $quote; ?>"</p>
        <div class="space-y-4">
            <?php foreach ($events as $event): ?>
            <div class="task p-4 rounded-lg">
                <h2 class="text-xl font-semibold"><?php echo $event['title']; ?> <span class="text-gray-500 font-normal">(<?php echo $event['duration']; ?>)</span></h2>
                <p class="text-gray-500"><?php echo $event['time']; ?> • <?php echo $event['location']; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
