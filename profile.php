<?php
// Start the session to track user login
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to view your profile.";
    exit();
}

require 'config.php'; // Include the database connection

// Get the logged-in user's ID
$user_id = $_SESSION['user_id']; 

// Query to fetch user details
$sql_user = "SELECT * FROM users WHERE id = :user_id";
$stmt_user = $pdo->prepare($sql_user);
$stmt_user->execute(['user_id' => $user_id]);

// Fetch user details
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);

// Query to fetch doubts related to the logged-in user
$sql_doubts = "SELECT * FROM doubts WHERE user_id = :user_id";
$stmt_doubts = $pdo->prepare($sql_doubts);
$stmt_doubts->execute(['user_id' => $user_id]);

// Fetch the results for doubts
$doubts = $stmt_doubts->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            padding: 10px 20px;
            background-color: #483A23;
            box-shadow: 0 2px 4px rgba(0,0, 0, 0.2);
        }
        .bip{
            color: white;
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 24px;
            color: white;
        }
    
        .dropdown-menu-end {
            right: 0;
            left: auto;
        }
    
        #navbarDropdown {
            font-size: 23px; 
            color: red; 
        }
    
        .profile-header {
            text-align: center;
            margin-top: 20px;
        }
        .profile-info, .doubts, .resources {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .profile-info h3, .doubts h3, .resources h3 {
            color: #007bff;
        }
        .resource-item, .doubt-item {
            border-bottom: 1px solid #ddd;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .resource-item a {
            color: #007bff;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            
            <a class="navbar-brand" href="#">PeerPrep</a>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link  d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        
                        <i class="bi bi-person-circle bip"></i> 
                    </a>
                    
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="1page.html">Home Page</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container">
        <div class="profile-header">
            <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>
        </div>

        <div class="profile-info">
            <h3>Your Details</h3>
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['fullname'] ?? 'Not Provided'); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($user['mobilenumber'] ?? 'Not Provided'); ?></p>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
        </div>

        
        <div class="doubts">
            <h3>Your Doubts</h3>
            <?php
            if (empty($doubts)) {
                echo "<p>Your Doubts: No doubts submitted yet.</p>";
            } else {
                foreach ($doubts as $doubt) {
                    echo "<div class='doubt-item'>
                            <span><strong>Subject:</strong> </span> " . htmlspecialchars($doubt['subject']) . "<br>
                            <span><strong>Question:</strong> </span> " . htmlspecialchars($doubt['question']) . "<br>
                            <span><strong>Status:</strong> </span> " . htmlspecialchars($doubt['status']) . "<br>
                          </div>";
                }
            }
            ?>
        </div>

        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>