<?php
session_start();
include 'admin/db_connect.php';

// Check if 'id' is passed in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $job_id = (int) $_GET['id']; // Cast to integer for safety

    // Fetch job details from the vacancy table
    $qry = $conn->query("SELECT * FROM vacancy WHERE id = $job_id");

    if ($qry && $qry->num_rows > 0) {
        $row = $qry->fetch_array();
        $position = $row['position']; // Get the job position
    } else {
        die("No job found for the provided job ID.");
    }
} else {
    die("Missing or invalid job ID in the URL.");
}

// Get logged-in user's email from session
$user_email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lastname = htmlspecialchars($_POST['lastname']);
    $firstname = htmlspecialchars($_POST['firstname']);
    $middlename = htmlspecialchars($_POST['middlename']);
    $gender = htmlspecialchars($_POST['gender']);
    $email = htmlspecialchars($_POST['email']);
    $contact = htmlspecialchars($_POST['contact']);
    $address = htmlspecialchars($_POST['address']);
    $cover_letter = htmlspecialchars($_POST['cover_letter']);
    $position_id = $_POST['position_id']; // This will hold the job ID submitted via the form

    // Handle resume file upload
    $resume_name = $_FILES['resume']['name'];
    $resume_tmp = $_FILES['resume']['tmp_name'];
    $resume_size = $_FILES['resume']['size'];
    $resume_error = $_FILES['resume']['error'];

    // Allowed file types for the resume
    $allowed_file_types = ['application/msword', 'text/plain', 'application/pdf'];
    $upload_dir = 'uploads/resumes/';
    $resume_path = $upload_dir . basename($resume_name);

    if (in_array($_FILES['resume']['type'], $allowed_file_types) && $resume_error === 0) {
        if (move_uploaded_file($resume_tmp, $resume_path)) {
            // Prepare SQL to insert the application into the applications table
            $stmt = $conn->prepare("INSERT INTO application (position_id, lastname, firstname, middlename, gender, email, contact, address, cover_letter, resume_path) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            // Correct the bind_param to match the number of variables and types
            $stmt->bind_param(
                "isssssssss", 
                $position_id, 
                $lastname, 
                $firstname, 
                $middlename, 
                $gender, 
                $email, 
                $contact, 
                $address, 
                $cover_letter, 
                $resume_path
            );

            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Application submitted successfully!</div>";
            } else {
                echo "<div class='alert alert-danger'>Error submitting your application. Please try again.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Error uploading the resume. Please try again.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Invalid file type or error uploading the file. Please upload a valid document.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Form for <?php echo htmlspecialchars($position); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom styling */
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #ff8c00, #ffd700);
            color: #000000;
        }
        .container-fluid {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #ffffff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3);
            border-radius: 8px;
        }
        h1 {
            text-align: center;
            margin-top: 10px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <h1>Application Form for <?php echo htmlspecialchars($position); ?></h1>

        <!-- Form with corrected action -->
        <form id="applicationForm" method="POST" action="submit_application.php?id=<?php echo $job_id; ?>" enctype="multipart/form-data">
            <input type="hidden" name="position_id" value="<?php echo $job_id; ?>">

            <div class="form-group">
                <label for="lastname">Last Name</label>
                <input type="text" class="form-control" name="lastname" required>
            </div>
            <div class="form-group">
                <label for="firstname">First Name</label>
                <input type="text" class="form-control" name="firstname" required>
            </div>
            <div class="form-group">
                <label for="middlename">Middle Name</label>
                <input type="text" class="form-control" name="middlename" required>
            </div>
            <div class="form-group">
                <label for="gender">Gender</label>
                <select name="gender" class="form-control" required>
                    <option>Male</option>
                    <option>Female</option>
                </select>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" name="email" value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>" required>

            </div>
            <div class="form-group">
                <label for="contact">Contact</label>
                <input type="text" class="form-control" name="contact" required>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <textarea name="address" class="form-control" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="cover_letter">Cover Letter (Optional)</label>
                <textarea name="cover_letter" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="resume">Resume</label>
                <input type="file" class="form-control" name="resume" accept="application/msword,application/pdf,text/plain" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Submit Application</button>
        </form>

        <a href="index.php" class="btn btn-secondary">Back to Home</a>
    </div>
</body>
</html>
