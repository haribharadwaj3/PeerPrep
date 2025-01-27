<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if (isset($_GET['id'])) {
    $doubt_id = (int) $_GET['id'];

    // Ensure the logged-in user can only delete their own doubts
    $sql = "DELETE FROM doubts WHERE id = :id AND user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $doubt_id, 'user_id' => $_SESSION['user_id']]);

    // Redirect back to the doubts page after deletion
    header("Location: doubts.php");
    exit();
} else {
    echo "Invalid request.";
}
?>
