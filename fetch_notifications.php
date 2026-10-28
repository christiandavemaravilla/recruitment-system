<?php
session_start();
$conn = new mysqli("localhost", "root", "", "recruitment_db");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    // Fetch notifications based on email
    $query = $conn->prepare("SELECT message, date_created FROM notifications WHERE email = ? ORDER BY created_at DESC LIMIT 5");
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();

    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $notifications[] = [
            'message' => $row['message'],
            'created_at' => $row['created_at']
        ];
    }

    echo json_encode($notifications);
} else {
    echo json_encode(["error" => "User not logged in."]);
}

$conn->close();
?>