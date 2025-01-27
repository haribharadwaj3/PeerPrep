<?php

session_start();
$dsn = 'mysql:host=localhost;dbname=peerprep';
$username = 'root';
$password = '';
try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the sign-up details
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $mobilenumber = $_POST['mobilenumber'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hashing the password

    // Insert data into the users table
    $query = $pdo->prepare("INSERT INTO users (fullname, username, mobilenumber, email, password) VALUES (?, ?, ?, ?, ?)");
    $success = $query->execute([$fullname, $username, $mobilenumber, $email, $password]);

    if ($success) {
        echo "<script>
                alert('Sign-up successful!');
                window.location.href = '1page.html'; // Redirect to the next page
              </script>";
        exit();
    } else {
        echo "<script>
                alert('Invalid email or password!');
                window.location.href='home.html'; // Redirect back to the login page
              </script>";
    }
}
?>
