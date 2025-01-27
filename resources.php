<?php
// Include your database connection
include('config.php');
session_start();

// Handle file upload (sharing)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf-file'])) {
    $domain = $_POST['domain'];
    $title = $_POST['pdf-upload-title'];
    $file = $_FILES['pdf-file'];

    // Check if the file was uploaded successfully
    if ($file['error'] === UPLOAD_ERR_OK) {
        // Define the upload directory and the filename
        $uploadDir = 'uploads/';
        $fileName = time() . '_' . basename($file['name']);
        $uploadFilePath = $uploadDir . $fileName;

        // Move the uploaded file to the 'uploads' directory
        if (move_uploaded_file($file['tmp_name'], $uploadFilePath)) {
            // Insert the resource details into the database
            $stmt = $pdo->prepare("INSERT INTO resources (domain, title, file_name) VALUES (?, ?, ?)");
            $stmt->execute([$domain, $title, $fileName]);

            // Redirect to refresh the page after upload
            header('Location: resources.php');
            exit;
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "Error uploading file.";
    }
}

// Handle file download
if (isset($_GET['download'])) {
    $fileId = $_GET['download'];

    // Fetch the file from the database
    $stmt = $pdo->prepare("SELECT * FROM resources WHERE id = ?");
    $stmt->execute([$fileId]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($file) {
        $filePath = 'uploads/' . $file['file_name'];
        if (file_exists($filePath)) {
            // Send the file to the browser for download
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file['file_name']) . '"');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
            exit;
        } else {
            echo "File not found.";
        }
    } else {
        echo "File not found in database.";
    }
}

// Handle file deletion
if (isset($_GET['delete'])) {
    $fileId = $_GET['delete'];

    // Fetch the file details from the database
    $stmt = $pdo->prepare("SELECT * FROM resources WHERE id = ?");
    $stmt->execute([$fileId]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($file) {
        // Delete the file from the server
        $filePath = 'uploads/' . $file['file_name'];
        if (file_exists($filePath)) {
            unlink($filePath);  // Delete the file from the server
        }

        // Delete the record from the database
        $stmt = $pdo->prepare("DELETE FROM resources WHERE id = ?");
        $stmt->execute([$fileId]);

        // Redirect to the resources page after deletion
        header('Location: resources.php');
        exit;
    } else {
        echo "Resource not found.";
    }
}

// Handle search functionality
$files = [];
$all_files = [];  // Variable to hold all resources
$error_message = '';  // Error message to display for search

// Fetch all resources if no search is done
$stmt = $pdo->query("SELECT * FROM resources ORDER BY id DESC");
$all_files = $stmt->fetchAll(PDO::FETCH_ASSOC);

// If there is a search query, filter the resources
if (isset($_GET['domain']) || isset($_GET['title'])) {
    $domain = $_GET['domain'] ?? '';
    $title = $_GET['title'] ?? '';

    // Prepare search query
    $sql = "SELECT * FROM resources WHERE 1";
    $params = [];
    
    if ($domain) {
        $sql .= " AND domain = ?";
        $params[] = $domain;
    }
    
    if ($title) {
        $sql .= " AND title LIKE ?";
        $params[] = "%" . $title . "%";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // If no files are found, set the error message
    if (empty($files)) {
        $error_message = "No resources found for the given title and domain. Please check your inputs.";
    }
} else {
    // If no search is performed, show all resources
    $files = $all_files;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resource Sharing Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        .navbar {
            padding: 10px 20px;
            background-color: #483A23;
            box-shadow: 0 2px 4px rgba(0,0, 0, 0.2);
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

        .bi-person-circle {
            font-size: 23px; 
            color: white;
        }

        .container {
            margin-top: 20px;
        }

        .section-title {
            margin-bottom: 20px;
        }

        .dropdown-container {
            margin-bottom: 15px;
        }

        .pdf-list {
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">PeerPrep</a>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <!-- Upload Form -->
        <h2>Share a Resource</h2>
        <form action="resources.php" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <label for="share-domain" class="form-label">Select Domain</label>
                    <select class="form-select" id="share-domain" name="domain" required>
                        <option value="">Choose a Domain...</option>
                        <option value="Science">Science</option>
                        <option value="Maths">Maths</option>
                        <option value="Humanities">Humanities</option>
                        <option value="Technology">Technology</option>
                        <option value="Coding">Coding</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="pdf-upload-title" class="form-label">PDF Title</label>
                    <input type="text" class="form-control" id="pdf-upload-title" name="pdf-upload-title" placeholder="Enter PDF title..." required>
                </div>
            </div>
            <div class="mb-3">
                <label for="pdf-file" class="form-label">Upload File</label>
                <input type="file" class="form-control" id="pdf-file" name="pdf-file" accept="application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.ms-powerpoint, application/vnd.openxmlformats-officedocument.presentationml.presentation" required>
            </div>
            <button type="submit" class="btn btn-success">Submit Resource</button>
        </form>

        <!-- Search Form -->
        <h2 class="mt-4">Search for Resources</h2>
        <form method="get" action="resources.php">
            <div class="row">
                <div class="col-md-6">
                    <label for="domain" class="form-label">Select Domain</label>
                    <select class="form-select" id="domain" name="domain">
                        <option value="">Choose a Domain...</option>
                        <option value="Science">Science</option>
                        <option value="Maths">Maths</option>
                        <option value="Humanities">Humanities</option>
                        <option value="Technology">Technology</option>
                        <option value="Coding">Coding</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="title" class="form-label">Search by Title</label>
                    <input type="text" class="form-control" id="title" name="title" placeholder="Enter title...">
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Search</button>
        </form>

        <!-- Show Error if No Results Found -->
        <?php if ($error_message): ?>
            <div class="alert alert-warning mt-3"><?= $error_message ?></div>
        <?php endif; ?>

        <!-- Display Resources -->
        <h2 class="mt-4">Available Resources</h2>
        <ul class="list-group">
            <?php
            if (empty($files)) {
                echo "<li class='list-group-item'>No resources found.</li>";
            } else {
                foreach ($files as $file) {
                    echo "<li class='list-group-item'>
                            <strong>Domain:</strong> {$file['domain']} <br>
                            <strong>Title:</strong> {$file['title']}
                            <a href='resources.php?download={$file['id']}' class='btn btn-sm btn-primary float-end ms-2'>Download</a>
                            <a href='resources.php?delete={$file['id']}' class='btn btn-sm btn-danger float-end ms-2' onclick='return confirm(\"Are you sure you want to delete this resource?\");'>Delete</a>
                          </li>";
                }
            }
            ?>
        </ul>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
