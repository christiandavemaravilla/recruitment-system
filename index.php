<!DOCTYPE html>
<html lang="en">
<?php
session_start(); // Start the session
include('admin/db_connect.php');
ob_start();

// Load system settings into session variables
$query = $conn->query("SELECT * FROM system_settings LIMIT 1")->fetch_array();
foreach ($query as $key => $value) {
    if (!is_numeric($key))
        $_SESSION['setting_' . $key] = $value;
}
ob_end_flush();
        
// Update email session
if (isset($_SESSION['id'])) {
    $userid = $_SESSION['id'];
    $result = $conn->query("SELECT email FROM userlist WHERE id = '$userid'");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['email'] = $user['email'];
    }
}

include('header.php');
?>

<style>
    /* Navbar styling */
    a.nav-link {
        color: white;
        text-decoration: none;
        margin: 0 10px;
        font-weight: bold;
    }
    a.nav-link:hover {
        text-decoration: none;
    }
</style>

<nav class="navbar navbar-expand-lg navbar-light fixed-top" style="padding:10px; background: linear-gradient(to right, #ff8c00, #ffd700); border-bottom-left-radius: 10px; border-bottom-right-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);">
    <div class="container-fluid">
        <a class="navbar-brand text-white" href="#">
            <img src="assets/img/smarthire.png" alt="SmartHire Logo" style="height:35px; width:auto;">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav align-items-center">
                <?php if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) : ?>
                    <li class="nav-item">
                        <a href="user_login.php" class="nav-link">Login</a>
                    </li>
                    <li class="nav-item">
                        <a href="register.php" class="nav-link">Register</a>
                    </li>
                <?php else : ?>
                    <!-- 🔔 Notification Bell Icon -->
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="view_notification.php">
                            <i class="fa fa-bell text-white" style="font-size: 20px;"></i>
                        </a>
                    </li>

                    <!-- Account Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="accountDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php
                            $userid = $_SESSION['id'];
                            $result = $conn->query("SELECT profile_pic FROM userlist WHERE id = '$userid'");
                            $user = $result->fetch_assoc();
                            $profile_pic = !empty($user['profile_pic']) ? $user['profile_pic'] : 'uploads/default.png';
                            ?>
                            <img src="<?php echo $profile_pic; ?>" alt="Profile Picture" style="height:35px; width:35px; border-radius:50%; object-fit:cover;" class="me-2">
                            Account
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountDropdown">
                            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<?php 
$page = isset($_GET['page']) ? $_GET['page'] : "home";
include $page . '.php';
?>

<?php $conn->close(); ?>
</html>
