<?php
// Database connection settings
$host = 'localhost';  // Database host
$dbname = 'peerprep'; // Database name
$username = 'root';   // Default MySQL username
$password = '';       // Default MySQL password (empty in XAMPP)


error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Create a new PDO instance to interact with the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Display error message if connection fails
    die("Database connection failed: " . $e->getMessage());
}
?>
