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
    header("Location: user_login.php");
    exit();
}

$userid = $_SESSION['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $_SESSION['notification'] = [
            'type' => 'error',
            'message' => 'Please fill in all fields.'
        ];
    } elseif ($new_password !== $confirm_password) {
        $_SESSION['notification'] = [
            'type' => 'error',
            'message' => 'New passwords do not match.'
        ];
    } elseif (strlen($new_password) < 6) {
        $_SESSION['notification'] = [
            'type' => 'error',
            'message' => 'Password must be at least 6 characters long.'
        ];
    } else {
        // Verify current password
        $stmt = $conn->prepare("SELECT password FROM userlist WHERE id = ?");
        $stmt->bind_param("i", $userid);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            if (password_verify($current_password, $user['password'])) {
                // Hash new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                // Update password
                $update_stmt = $conn->prepare("UPDATE userlist SET password = ? WHERE id = ?");
                $update_stmt->bind_param("si", $hashed_password, $userid);
                
                if ($update_stmt->execute()) {
                    $_SESSION['notification'] = [
                        'type' => 'success',
                        'message' => 'Password changed successfully!'
                    ];
                } else {
                    $_SESSION['notification'] = [
                        'type' => 'error',
                        'message' => 'Failed to update password. Please try again.'
                    ];
                }
                $update_stmt->close();
            } else {
                $_SESSION['notification'] = [
                    'type' => 'error',
                    'message' => 'Current password is incorrect.'
                ];
            }
        }
        $stmt->close();
    }
    
    header("Location: change_password.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <style>
        * {
            box-sizing: border-box;
        }
        
        body {
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #ff8c00, #ffd700);
            font-family: Arial, sans-serif;
        }
        
        .container {
            width: 100%;
            max-width: 450px;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .notification {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }
        
        .notification.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .notification.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        h2 {
            text-align: center;
            margin-bottom: 10px;
            color: #333;
        }
        
        .description {
            text-align: center;
            color: #666;
            margin-bottom: 25px;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .form-group input:focus {
            border-color: #ff8c00;
            outline: none;
        }
        
        .password-requirements {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        
        .form-group button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #ff8c00, #ffd700);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
            font-size: 16px;
            font-weight: bold;
        }
        
        .form-group button:hover {
            background: linear-gradient(135deg, #007bff, #00c9ff);
        }
        
        .link {
            text-align: center;
            margin-top: 15px;
        }
        
        .link a {
            color: #ff7e5f;
            text-decoration: none;
        }
        
        .link a:hover {
            text-decoration: underline;
        }

        .forgot-password-link {
            text-align: center;
            margin-top: 15px;
            font-size: 13px;
        }

        .forgot-password-link a {
            color: #007bff;
            text-decoration: none;
        }

        .forgot-password-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="container">
        <?php
        if (isset($_SESSION['notification'])):
            $notification = $_SESSION['notification'];
            unset($_SESSION['notification']);
        ?>
            <div class="notification <?= $notification['type']; ?>">
                <?= $notification['message']; ?>
            </div>
        <?php endif; ?>

        <h2>Change Password</h2>
        <p class="description">Enter your current password and choose a new password.</p>
        
        <form action="change_password.php" method="POST">
            <div class="form-group">
                <label for="current_password">Current Password:</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
            
            <div class="form-group">
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required>
                <div class="password-requirements">Password must be at least 6 characters long</div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <div class="form-group">
                <button type="submit">Change Password</button>
            </div>

            <div class="forgot-password-link">
                <p>Forgot your current password? <a href="forgot_password.php">Reset via Email</a></p>
            </div>
            
            <div class="link">
                <p><a href="profile.php">← Back to Profile</a></p>
            </div>
        </form>
    </div>

</body>
</html>