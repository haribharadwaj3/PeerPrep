<?php

// In-memory meetings data (can be replaced with a database like MySQL or SQLite)
$meetings = [];

if (isset($_GET['id'])) {
    $meetingId = $_GET['id'];

    // Check if the meeting exists
    if (isset($meetings[$meetingId])) {
        $meeting = $meetings[$meetingId];
        echo "You have joined the meeting: " . $meeting['topic'] . " | Join URL: " . $meeting['join_url'];
    } else {
        echo "Meeting not found";
    }
} else {
    echo "No meeting ID provided";
}
?>
