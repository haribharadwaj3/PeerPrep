<?php

// In-memory meetings data (can be replaced with a database like MySQL or SQLite)
$meetings = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data and decode it as JSON
    $rawData = file_get_contents('php://input');
    $meetingDetails = json_decode($rawData, true);

    if (!$meetingDetails) {
        echo json_encode(['error' => 'Invalid JSON data']);
        exit;
    }

    // Retrieve data from the decoded JSON
    $topic = $meetingDetails['topic'];
    $start_time = $meetingDetails['start_time'];
    $duration = $meetingDetails['duration'];
    $agenda = $meetingDetails['agenda'];

    // Generate a unique meeting ID (You can use UUID or a random string)
    $meetingId = uniqid('meeting_');
    $join_url = "http://localhost:5000/meeting.php?id=" . $meetingId;

    // Store the meeting data (for demonstration purposes, we'll store it in memory)
    $meetings[$meetingId] = [
        'meetingId' => $meetingId,
        'topic' => $topic,
        'start_time' => $start_time,
        'duration' => $duration,
        'agenda' => $agenda,
        'join_url' => $join_url
    ];

    // Send back the response
    echo json_encode([
        'meeting' => $meetings[$meetingId]
    ]);
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
