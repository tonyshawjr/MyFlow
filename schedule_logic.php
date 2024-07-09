<?php
function getTrelloData($settings) {
    $lists_url = "https://api.trello.com/1/boards/{$settings['trello_board_id']}/lists?key={$settings['trello_api_key']}&token={$settings['trello_token']}&fields=name,id";
    $lists_response = file_get_contents($lists_url);
    $lists = json_decode($lists_response, true);

    $cards_url = "https://api.trello.com/1/boards/{$settings['trello_board_id']}/cards?key={$settings['trello_api_key']}&token={$settings['trello_token']}&fields=name,idList,start,due,labels";
    $cards_response = file_get_contents($cards_url);
    $cards = json_decode($cards_response, true);

    return [$lists, $cards];
}

function analyzeTasks($lists, $cards) {
    $today = new DateTime();
    $today = $today->format('Y-m-d');
    $dayOfWeek = date('l');
    
    $ignoreListId = "5cfe9f491bedfa5b376a5372";
    
    $relevantCards = array_filter($cards, function($card) use ($today, $ignoreListId) {
        $start = isset($card['start']) ? substr($card['start'], 0, 10) : null;
        $due = isset($card['due']) ? substr($card['due'], 0, 10) : null;
        return ($start && $start <= $today) && ($due && $due >= $today) && $card['idList'] != $ignoreListId;
    });

    usort($relevantCards, function($a, $b) {
        return strcmp($a['due'], $b['due']);
    });
    
    $events = [];

    // Morning email check
    $events[] = [
        "title" => "Check Emails",
        "duration" => "15m",
        "time" => "08:00 AM - 08:15 AM",
        "location" => "Office"
    ];

    // Define fixed meetings
    $fixedMeetings = [];
    if ($dayOfWeek == "Monday") {
        $fixedMeetings[] = [
            "title" => "CoC Website Weekly",
            "duration" => "45m",
            "time" => "11:00 AM - 11:45 AM",
            "location" => "Google Meet"
        ];
    } elseif ($dayOfWeek == "Tuesday") {
        $fixedMeetings[] = [
            "title" => "COC Tasks Review",
            "duration" => "1h",
            "time" => "10:00 AM - 11:00 AM",
            "location" => "Google Meet"
        ];
    } elseif ($dayOfWeek == "Thursday") {
        $fixedMeetings[] = [
            "title" => "IT Task Priority",
            "duration" => "30m",
            "time" => "10:00 AM - 10:30 AM",
            "location" => "Google Meet"
        ];
    }

    // Add fixed meetings to events
    $events = array_merge($events, $fixedMeetings);
    
    // Time blocks for tasks excluding fixed meeting times
    $timeBlocks = [
        ["start" => "08:15 AM", "end" => "09:45 AM"],
        ["start" => "10:00 AM", "end" => "11:30 AM"],
        ["start" => "11:30 AM", "end" => "12:00 PM"],
        ["start" => "12:45 PM", "end" => "02:15 PM"],
        ["start" => "02:30 PM", "end" => "03:45 PM"]
    ];

    // Adjust time blocks to account for fixed meetings
    foreach ($fixedMeetings as $meeting) {
        $meetingStart = DateTime::createFromFormat('h:i A', explode(' - ', $meeting['time'])[0]);
        $meetingEnd = DateTime::createFromFormat('h:i A', explode(' - ', $meeting['time'])[1]);
        $timeBlocks = array_filter($timeBlocks, function($block) use ($meetingStart, $meetingEnd) {
            $blockStart = DateTime::createFromFormat('h:i A', $block['start']);
            return $blockStart < $meetingStart || $blockStart >= $meetingEnd;
        });
    }

    $taskIndex = 0;
    foreach ($timeBlocks as $i => $block) {
        if ($taskIndex < count($relevantCards)) {
            $taskName = $relevantCards[$taskIndex]['name'];
            $taskIndex++;
        } elseif ($i >= count($timeBlocks) / 2) {  // Only add TC Workdays in the second half of the day
            $taskName = "Tidemark Creative Workday";
        } else {
            $taskName = $relevantCards[$taskIndex % count($relevantCards)]['name'];
        }

        $blockStart = DateTime::createFromFormat('h:i A', $block['start']);
        $blockEnd = DateTime::createFromFormat('h:i A', $block['end']);
        $duration = $blockEnd->diff($blockStart)->format('%h:%i');

        $events[] = [
            "title" => $taskName,
            "duration" => $duration,
            "time" => "{$block['start']} - {$block['end']}",
            "location" => "Office"
        ];

        // Add a break after each task block, except before lunch or email checks
        if ($i < count($timeBlocks) - 1 && !in_array($block['end'], ["12:00 PM", "12:45 PM", "03:45 PM"])) {
            $breakStart = $blockEnd;
            $breakEnd = clone $breakStart;
            $breakEnd->modify('+15 minutes');
            $events[] = [
                "title" => "Break",
                "duration" => "15m",
                "time" => "{$breakStart->format('h:i A')} - {$breakEnd->format('h:i A')}",
                "location" => "Office"
            ];
        }
    }

    // Lunch break
    $events[] = [
        "title" => "Lunch Break",
        "duration" => "30m",
        "time" => "12:00 PM - 12:30 PM",
        "location" => "Office"
    ];

    // After-lunch email check
    $events[] = [
        "title" => "Check Emails",
        "duration" => "15m",
        "time" => "12:30 PM - 12:45 PM",
        "location" => "Office"
    ];

    // End-of-day email check
    $events[] = [
        "title" => "Check Emails",
        "duration" => "15m",
        "time" => "03:45 PM - 04:00 PM",
        "location" => "Office"
    ];

    // Sort events by time
    usort($events, function($a, $b) {
        return DateTime::createFromFormat('h:i A', explode(' - ', $a['time'])[0]) <=> DateTime::createFromFormat('h:i A', explode(' - ', $b['time'])[0]);
    });

    return $events;
}

function getRandomQuote() {
    $quotes = [
        "The best way to get started is to quit talking and begin doing. - Walt Disney",
        "The pessimist sees difficulty in every opportunity. The optimist sees opportunity in every difficulty. - Winston Churchill",
        "Don’t let yesterday take up too much of today. - Will Rogers",
        "You learn more from failure than from success. Don’t let it stop you. Failure builds character. - Unknown",
        "It’s not whether you get knocked down, it’s whether you get up. - Vince Lombardi",
        "If you are working on something that you really care about, you don’t have to be pushed. The vision pulls you. - Steve Jobs",
        "People who are crazy enough to think they can change the world, are the ones who do. - Rob Siltanen",
        "Failure will never overtake me if my determination to succeed is strong enough. - Og Mandino",
        "Entrepreneurs are great at dealing with uncertainty and also very good at minimizing risk. That’s the classic entrepreneur. - Mohnish Pabrai",
        "We may encounter many defeats but we must not be defeated. - Maya Angelou"
    ];
    return $quotes[array_rand($quotes)];
}
?>
