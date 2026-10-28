<?php
session_start();
include 'db_connect.php';

// Ensure user is logged in
if (!isset($_SESSION['email'])) {
    echo "You must be logged in to view notifications.";
    exit;
}

$email = $_SESSION['email']; // Get logged-in user's email

// Fetch notifications only for the logged-in user
$query = "
    SELECT id, message, date_created
    FROM notifications
    WHERE email = ?
    ORDER BY date_created DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; }
        .notification-container { width: 60%; margin: 50px auto; padding: 20px; background-color: #fff; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h3 { margin-bottom: 20px; color: #333; }
        ul { list-style-type: none; padding: 0; }
        li { background-color: #e8e8f0; margin: 10px 0; padding: 10px; border-radius: 5px; }
        .no-notifications { text-align: center; color: #888; font-style: italic; }
    </style>
</head>
<body>
    <div class="notification-container">
        <h3>Your Notifications</h3>
        <ul>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <li><?= htmlspecialchars($row['message']); ?> <br>
                        <small><strong><?= $row['date_created']; ?></strong></small>
                    </li>
                <?php endwhile; ?>
            <?php else: ?>
                <li class="no-notifications">No notifications available.</li>
            <?php endif; ?>
        </ul>
    </div>
</body>
</html>
