<?php
require 'config.php';
session_start();

// Check if the delete request is made
if (isset($_GET['delete'])) {
    $doubt_id = $_GET['delete'];

    // Ensure that the logged-in user is the one who created the doubt
    $user_id = $_SESSION['user_id'];

    // Delete the doubt from the database if the user is authorized
    $sql = "DELETE FROM doubts WHERE id = :doubt_id AND user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['doubt_id' => $doubt_id, 'user_id' => $user_id]);

    // Redirect to the page after deletion
    header("Location: doubts.php");
    exit();
}

// Fetch doubts for the logged-in user
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM doubts WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);

$doubts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission for new doubts
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the form data
    $subject = $_POST['subject'];
    $question = $_POST['question'];
    $user_id = $_SESSION['user_id'];

    // Check if file is uploaded
    $file = $_FILES['attachment'];
    $file_path = null;
    if ($file['error'] == 0) {
        // Save the uploaded file
        $file_path = 'uploads/' . basename($file['name']);
        move_uploaded_file($file['tmp_name'], $file_path);
    }

    // Insert the new doubt into the database with 'Unsolved' status
    $sql = "INSERT INTO doubts (user_id, subject, question, attachment, status) VALUES (:user_id, :subject, :question, :attachment, 'Unsolved')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $user_id, 'subject' => $subject, 'question' => $question, 'attachment' => $file_path]);

    // Redirect to the same page after submitting the form
    header("Location: doubts.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit a Doubt</title>
    <link rel="stylesheet" href="doubts.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <h3>Submit Your Doubts</h3>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link  d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle" ></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end " aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="home.html">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <section class="doubt-form">
            <h2>New Doubt Submission</h2>
            <form id="doubt-form" action="doubts.php" method="POST" enctype="multipart/form-data">
                <label for="subject">Subject</label>
                <select id="subject" name="subject" required>
                    <option value="math">Math</option>
                    <option value="science">Science</option>
                    <option value="history">History</option>
                </select>

                <label for="question">Your Question</label>
                <textarea id="question" name="question" rows="4" required></textarea>

                <label for="attachment">Attach File (Optional)</label>
                <input type="file" id="attachment" name="attachment">

                <center><button type="submit" class="btn btn-primary">Submit</button></center>
            </form>
        </section>

        <section class="submitted-doubts">
            <h2>Recent Doubts</h2>
            <ul id="doubts-list" class="list-group">
                <?php foreach ($doubts as $doubt): ?>
                    <li class="list-group-item">
                        <strong>Subject: </strong><?php echo htmlspecialchars($doubt['subject']); ?><br>
                        <strong>Question: </strong><?php echo htmlspecialchars($doubt['question']); ?><br>
                        <strong>Status: </strong><?php echo htmlspecialchars($doubt['status']); ?><br>

                        <?php if ($doubt['attachment']): ?>
                            <a href="<?php echo htmlspecialchars($doubt['attachment']); ?>" target="_blank" class="btn btn-info btn-sm">View Attachment</a>
                        <?php endif; ?>

                        <a href="doubts.php?delete=<?php echo $doubt['id']; ?>" class="btn btn-danger btn-sm float-end" onclick="return confirm('Are you sure you want to delete this doubt?');">Delete</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
