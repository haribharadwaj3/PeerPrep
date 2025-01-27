<?php
require 'config.php';
session_start(); // Start the session to track user login

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve email and password from the login form
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Retrieve user data from the database
    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if user exists and password matches
    if ($user && password_verify($password, $user['password'])) {
        // Set session variables after successful login
        $_SESSION['user_id'] = $user['id']; // Store user ID in session
        $_SESSION['username'] = $user['username']; // Store username in session (optional, you can use this later)
        $_SESSION['email'] = $user['email']; // Store email in session

        // Redirect to a page after successful login
        echo "<script>
                alert('Log-in successful!');
                window.location.href = '1page.html'; // Redirect to the profile page
              </script>";
        exit();
    } else {
        // Show an error message if the login credentials are invalid
        echo "<script>
                alert('Invalid email or password!');
                window.location.href='login.html'; // Redirect back to the login page
              </script>";
    }
}
?>
