<?php


// In-memory meetings data (can be replaced with a database like MySQL or SQLite)
$meetings = [];

if (isset($_GET['id'])) {
    $meetingId = $_GET['id'];

    // Check if the meeting exists
    if (isset($meetings[$meetingId])) {
        $meeting = $meetings[$meetingId];
        echo json_encode(['meeting' => $meeting]);
    } else {
        echo json_encode(['error' => 'Meeting not found']);
    }
} else {
    echo json_encode(['error' => 'No meeting ID provided']);
}
?>
