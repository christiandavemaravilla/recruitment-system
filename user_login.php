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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $_SESSION['notification'] = [
            'type' => 'error',
            'message' => 'Please fill in all fields.'
        ];
        header("Location: user_login.php");
        exit();
    }

    // Check if username exists
    $stmt = $conn->prepare("SELECT * FROM userlist WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];

            // Redirect to home page
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['notification'] = [
                'type' => 'error',
                'message' => 'Invalid password. Please try again.'
            ];
        }
    } else {
        $_SESSION['notification'] = [
            'type' => 'error',
            'message' => 'User not found. Please check your username.'
        ];
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            background: linear-gradient(135deg, #ff8c00, #ffd700);
            font-family: Arial, sans-serif;
        }
        .container {
            width: 400px;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-left: 50px;
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
        .logo-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-left: 100px;
        }
        .logo-container img {
            max-width: 400px;
            height: auto;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
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
        }
        .form-group button:hover {
            background: linear-gradient(135deg, #007bff, #00c9ff);
        }
        .link {
            text-align: center;
            margin-top: 10px;
        }
        .link a {
            color: #ff7e5f;
            text-decoration: none;
        }
        .link a:hover {
            text-decoration: underline;
        }
        .forgot-password {
            text-align: right;
            margin-top: -10px;
            margin-bottom: 15px;
        }
        .forgot-password a {
            color: #ff7e5f;
            text-decoration: none;
            font-size: 14px;
        }
        .forgot-password a:hover {
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

        <h2>User Login</h2>
        <form id="login-form" action="user_login.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="forgot-password">
                <a href="forgot_password.php">Forgot Password?</a>
            </div>
            <div class="form-group">
                <button type="submit">Login</button>
            </div>
            <div class="link">
                <p>Log in as Admin <a href="admin/login.php">Login</a>.</p>
                <p>Don't have an account? <a href="register.php">Register</a>.</p>
            </div>
        </form>
    </div>

    <div class="logo-container">
        <img src="assets/img/smarthire.png" alt="SmartHire Logo">
    </div>

</body>
</html>