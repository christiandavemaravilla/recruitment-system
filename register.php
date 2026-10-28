<?php
session_start();

$host = "localhost";
$db_username = "root";
$db_password = "";
$db_name = "recruitment_db";

$conn = new mysqli($host, $db_username, $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    // Get user input
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        $_SESSION['notification'] = [
            'type' => 'error',
            'message' => 'Passwords do not match. Please try again.',
        ];
        header("Location: register.php");
        exit();
    }

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if the username or email already exists
    $checkQuery = "SELECT * FROM userlist WHERE username = '$username' OR email = '$email'";
    $result = $conn->query($checkQuery);

    if ($result->num_rows > 0) {
        $_SESSION['notification'] = [
            'type' => 'error',
            'message' => 'Username or email already exists.',
        ];
    } else {
        // Insert new user into the database
        $insertQuery = "INSERT INTO userlist (username, email, password) VALUES ('$username', '$email', '$hashed_password')";

        if ($conn->query($insertQuery) === TRUE) {
            $_SESSION['notification'] = [
                'type' => 'success',
                'message' => 'Registration successful. You can now <a href="user_login.php">login</a>.',
            ];
        } else {
            $_SESSION['notification'] = [
                'type' => 'error',
                'message' => 'Error: ' . $conn->error,
            ];
        }
    }

    // Close the database connection and redirect
    $conn->close();
    header("Location: register.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
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
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
          
        }

        .notification {
            padding: 10px;
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
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($_SESSION['notification'])): ?>
            <div class="notification <?= $_SESSION['notification']['type']; ?>">
                <?= $_SESSION['notification']['message']; ?>
            </div>
            <?php unset($_SESSION['notification']); ?>
        <?php endif; ?>

        <h2>User Registration</h2>
        <form action="register.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>
            <div class="form-group">
                <button type="submit" name="register">Register</button>
            </div>
            <div class="link">
                <p>Already have an account? <a href="user_login.php">Login</a>.</p>
            </div>
        </form>
    </div>
</body>
</html>