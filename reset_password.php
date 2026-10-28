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

$token = isset($_GET['token']) ? $_GET['token'] : '';
$valid_token = false;
$user_email = '';

// Verify token
if (!empty($token)) {
    $stmt = $conn->prepare("SELECT email, reset_token_expiry FROM userlist WHERE reset_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $user_email = $user['email'];
        
        // Check if token has expired
        if (strtotime($user['reset_token_expiry']) > time()) {
            $valid_token = true;
        } else {
            $_SESSION['notification'] = [
                'type' => 'error',
                'message' => 'This password reset link has expired. Please request a new one.'
            ];
        }
    } else {
        $_SESSION['notification'] = [
            'type' => 'error',
            'message' => 'Invalid password reset link.'
        ];
    }
    $stmt->close();
}

// Handle password reset form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && $valid_token) {
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($new_password) || empty($confirm_password)) {
        $_SESSION['notification'] = [
            'type' => 'error',
            'message' => 'Please fill in all fields.'
        ];
    } elseif ($new_password !== $confirm_password) {
        $_SESSION['notification'] = [
            'type' => 'error',
            'message' => 'Passwords do not match.'
        ];
    } elseif (strlen($new_password) < 6) {
        $_SESSION['notification'] = [
            'type' => 'error',
            'message' => 'Password must be at least 6 characters long.'
        ];
    } else {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update password and clear reset token
        $update_stmt = $conn->prepare("UPDATE userlist SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ?");
        $update_stmt->bind_param("ss", $hashed_password, $token);
        
        if ($update_stmt->execute()) {
            $_SESSION['notification'] = [
                'type' => 'success',
                'message' => 'Your password has been reset successfully. You can now login with your new password.'
            ];
            $update_stmt->close();
            $conn->close();
            header("Location: user_login.php");
            exit();
        } else {
            $_SESSION['notification'] = [
                'type' => 'error',
                'message' => 'An error occurred. Please try again.'
            ];
        }
        $update_stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #ff8c00, #ffd700);
            font-family: Arial, sans-serif;
        }
        .container {
            width: 400px;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .notification {
            padding: 10px;
            margin-bottom: 15px;
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
        }
        .description {
            text-align: center;
            color: #666;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input {
            width: 95%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-group input:focus {
            border-color: #feb47b;
            outline: none;
        }
        .form-group button {
            width: 100%;
            padding: 10px;
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
        .password-requirements {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        .error-container {
            text-align: center;
            padding: 20px;
        }
        .error-container a {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background: linear-gradient(135deg, #ff8c00, #ffd700);
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .error-container a:hover {
            background: linear-gradient(135deg, #007bff, #00c9ff);
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

        <?php if ($valid_token): ?>
            <h2>Reset Password</h2>
            <p class="description">Please enter your new password below.</p>
            
            <form action="reset_password.php?token=<?= htmlspecialchars($token); ?>" method="POST">
                <div class="form-group">
                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password" required>
                    <div class="password-requirements">Password must be at least 6 characters long</div>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <div class="form-group">
                    <button type="submit">Reset Password</button>
                </div>
                <div class="link">
                    <p><a href="user_login.php">Back to Login</a></p>
                </div>
            </form>
        <?php else: ?>
            <div class="error-container">
                <h2>Invalid or Expired Link</h2>
                <p>This password reset link is invalid or has expired.</p>
                <a href="forgot_password.php">Request New Reset Link</a>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>