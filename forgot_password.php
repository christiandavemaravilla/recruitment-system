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

// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Make sure you have PHPMailer installed via Composer

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    if (empty($email)) {
        $_SESSION['notification'] = [
            'type' => 'error',
            'message' => 'Please enter your email address.'
        ];
        header("Location: forgot_password.php");
        exit();
    }

    // Check if email exists in database
    $stmt = $conn->prepare("SELECT id, username, email FROM userlist WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Generate unique token
        $token = bin2hex(random_bytes(32));
        $token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token valid for 1 hour

        // Store token in database
        $update_stmt = $conn->prepare("UPDATE userlist SET reset_token = ?, reset_token_expiry = ? WHERE email = ?");
        $update_stmt->bind_param("sss", $token, $token_expiry, $email);
        
        if ($update_stmt->execute()) {
            // Send email with reset link
            $mail = new PHPMailer(true);

            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Change to your SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = 'smarthire.noreply@gmail.com'; // Your email
                $mail->Password = 'kxbl nahy yxkp azlc'; // Your email app password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Recipients
                $mail->setFrom('smarthire.noreply@gmail.com', 'SmartHire');
                $mail->addAddress($email, $user['username']);

                // Content
                $reset_link = "htdocs/smarthire/reset_password.php?token=" . $token;
                
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Request - SmartHire';
                $mail->Body = "
                    <h2>Password Reset Request</h2>
                    <p>Hello {$user['username']},</p>
                    <p>We received a request to reset your password. Click the link below to reset your password:</p>
                    <p><a href='{$reset_link}' style='background-color: #ff8c00; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>Reset Password</a></p>
                    <p>Or copy and paste this link into your browser:</p>
                    <p>{$reset_link}</p>
                    <p>This link will expire in 1 hour.</p>
                    <p>If you didn't request a password reset, please ignore this email.</p>
                    <br>
                    <p>Best regards,<br>SmartHire Team</p>
                ";
                $mail->AltBody = "Hello {$user['username']}, Click this link to reset your password: {$reset_link} This link will expire in 1 hour.";

                $mail->send();
                
                $_SESSION['notification'] = [
                    'type' => 'success',
                    'message' => 'Password reset link has been sent to your email address.'
                ];
            } catch (Exception $e) {
                $_SESSION['notification'] = [
                    'type' => 'error',
                    'message' => 'Failed to send email. Please try again later.'
                ];
            }
        } else {
            $_SESSION['notification'] = [
                'type' => 'error',
                'message' => 'An error occurred. Please try again.'
            ];
        }
        
        $update_stmt->close();
    } else {
        // Don't reveal if email exists or not for security
        $_SESSION['notification'] = [
            'type' => 'success',
            'message' => 'If that email address is in our system, we have sent a password reset link.'
        ];
    }

    $stmt->close();
    header("Location: forgot_password.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
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

        <h2>Forgot Password</h2>
        <p class="description">Enter your email address and we'll send you a link to reset your password.</p>
        
        <form action="forgot_password.php" method="POST">
            <div class="form-group">
                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <button type="submit">Send Reset Link</button>
            </div>
            <div class="link">
                <p>Remember your password? <a href="user_login.php">Back to Login</a></p>
            </div>
        </form>
    </div>

</body>
</html>