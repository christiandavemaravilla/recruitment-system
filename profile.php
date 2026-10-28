<?php
session_start();

// Database connection
$host = "localhost";
$db_username = "root";
$db_password = "";
$db_name = "recruitment_db";

$conn = new mysqli($host, $db_username, $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: user_login.php?error=unauthorized");
    exit();
}

// Retrieve the user's information
$userid = $_SESSION['id'];
$stmt = $conn->prepare("SELECT id, username, email, created_at, profile_pic FROM userlist WHERE id = ?");
$stmt->bind_param("i", $userid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "<script>alert('No profile found!'); window.location.href='index.php';</script>";
    exit();
}
$stmt->close();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $profile_pic = $user['profile_pic'];
    $oldEmail = $user['email'];

    // Check if username or email already exists (excluding current user)
    $checkStmt = $conn->prepare("SELECT id FROM userlist WHERE (username = ? OR email = ?) AND id != ?");
    $checkStmt->bind_param("ssi", $username, $email, $userid);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        echo "<script>alert('Username or Email already exists!'); window.location.href='profile.php';</script>";
        exit();
    }
    $checkStmt->close();

    // Handle profile picture upload
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $upload_dir = 'uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $target_file = $upload_dir . basename($_FILES["profile_pic"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif']) && getimagesize($_FILES["profile_pic"]["tmp_name"])) {
            if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
                $profile_pic = $target_file;
            } else {
                echo "<script>alert('Error uploading file.');</script>";
            }
        } else {
            echo "<script>alert('Invalid image file.');</script>";
        }
    }

    // If email changed, update all references
    if ($email !== $oldEmail) {
        $conn->begin_transaction(); // Start transaction

        try {
            // Temporarily disable foreign key checks
            $conn->query("SET FOREIGN_KEY_CHECKS=0;");

            // Update email in application table first
            $updateApp = $conn->prepare("UPDATE application SET email = ? WHERE email = ?");
            $updateApp->bind_param("ss", $email, $oldEmail);
            $updateApp->execute();
            $updateApp->close();

            // Update email in notifications table
            $updateNotif = $conn->prepare("UPDATE notifications SET email = ? WHERE email = ?");
            $updateNotif->bind_param("ss", $email, $oldEmail);
            $updateNotif->execute();
            $updateNotif->close();

            // Finally, update the email in userlist
            $updateUser = $conn->prepare("UPDATE userlist SET username= ?, email= ?, profile_pic= ? WHERE id= ?");
            $updateUser->bind_param("sssi", $username, $email, $profile_pic, $userid);
            $updateUser->execute();
            $updateUser->close();

            // Re-enable foreign key checks
            $conn->query("SET FOREIGN_KEY_CHECKS=1;");

            // Commit transaction if all updates succeed
            $conn->commit();
            
            // Update session email
            $_SESSION['email'] = $email;
            
            echo "<script>alert('Profile updated successfully!'); window.location.href='profile.php';</script>";
        } catch (Exception $e) {
            $conn->rollback(); // Revert changes if any update fails
            echo "<script>alert('Profile update failed: " . $e->getMessage() . "');</script>";
        }
    } else {
        // If email didn't change, just update username and profile picture
        $stmt = $conn->prepare("UPDATE userlist SET username= ?, profile_pic= ? WHERE id= ?");
        $stmt->bind_param("ssi", $username, $profile_pic, $userid);
        if ($stmt->execute()) {
            echo "<script>alert('Profile updated successfully!'); window.location.href='profile.php';</script>";
        } else {
            echo "<script>alert('Profile update failed.');</script>";
        }
        $stmt->close();
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile</title>
    <style>
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #ff8c00, #ffd700);
            color: #000000;
            margin: 0;
            padding: 20px;
        }

        .profile-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: #ffffff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3);
            border-radius: 8px;
        }

        h1 {
            text-align: center;
            margin-top: 0;
            margin-bottom: 20px;
            color: #333;
        }

        h2 {
            color: #333;
            margin-bottom: 15px;
            border-bottom: 2px solid #ff8c00;
            padding-bottom: 10px;
        }

        .profile-details p {
            margin: 10px 0;
            font-size: 16px;
            color: #212629;
        }

        .profile-pic-frame {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .profile-pic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background-color: #ddd;
            background-image: url('<?php echo htmlspecialchars($user['profile_pic']); ?>');
            background-size: cover;
            background-position: center;
            display: inline-block;
            border: 4px solid #ff8c00;
        }

        .edit-form {
            margin-top: 30px;
        }

        .edit-form input[type="text"],
        .edit-form input[type="email"],
        .edit-form input[type="file"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        .edit-form input:focus {
            outline: none;
            border-color: #ff8c00;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .edit-form button {
            flex: 1;
            background: linear-gradient(135deg, #ff8c00, #ffd700);
            color: black;
            border: none;
            border-radius: 5px;
            padding: 12px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
            transition: background 0.3s ease;
        }

        .edit-form button:hover {
            background: linear-gradient(135deg, #007bff, #00c9ff);
        }

        .change-password-btn {
            background: linear-gradient(135deg, #6c757d, #95a5a6) !important;
            color: white !important;
        }

        .change-password-btn:hover {
            background: linear-gradient(135deg, #495057, #6c757d) !important;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #ff7e5f;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="profile-container">
        <h1>MY PROFILE</h1>

        <div class="profile-pic-frame">
            <div class="profile-pic" id="profilePicPreview"></div>
        </div>

        <div class="profile-details">
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Member Since:</strong> <?php echo htmlspecialchars(date('F j, Y', strtotime($user['created_at']))); ?></p>
        </div>

        <div class="edit-form">
            <h2>Edit Profile</h2>
            <form action="profile.php" method="POST" enctype="multipart/form-data">
                <label for="name"><strong>Username:</strong></label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                
                <label for="email"><strong>Email:</strong></label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                
                <label for="profile_pic"><strong>Profile Picture:</strong></label>
                <input type="file" id="profile_pic" name="profile_pic" accept="image/*">
                
                <div class="button-group">
                    <button type="submit">Save Changes</button>
                    <button type="button" class="change-password-btn" onclick="window.location.href='change_password.php'">Change Password</button>
                </div>
            </form>
        </div>

        <div class="back-link">
            <a href="index.php">← Back to Home</a>
        </div>
    </div>

</body>
</html>