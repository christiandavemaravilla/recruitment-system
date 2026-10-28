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

        /* Preloader styling */
        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #preloader:after {
            content: '';
            width: 50px;
            height: 50px;
            border: 5px solid #ff8c00;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>

    <nav class="navbar navbar-expand-lg navbar-light fixed-top" style="padding:10px; background: linear-gradient(to right, #ff8c00, #ffd700); border-bottom-left-radius: 10px; border-bottom-right-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);">
        <div class="container-fluid">
            <!-- Left Section: Logo -->
            <a class="navbar-brand text-white" href="#">
                <img src="assets/img/smarthire.png" alt="SmartHire Logo" style="height:35px; width:auto;">
            </a>

            <!-- Toggle for Mobile View -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Right Section -->
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
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="accountDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php
                                $userid = $_SESSION['id'];
                                $result = $conn->query("SELECT profile_pic FROM userlist WHERE id = '$userid'");
                                $user = $result->fetch_assoc();

                                // If there's a profile picture, display it, otherwise display a default image
                                $profile_pic = !empty($user['profile_pic']) ? $user['profile_pic'] : 'uploads/default.png';  // Default if no profile pic
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

    <!-- Preloader -->
    <div id="preloader"></div>

    <!-- Modals -->
    <div class="modal fade" id="confirm_modal" role='dialog'>
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmation</h5>
                </div>
                <div class="modal-body">
                    <div id="delete_content"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id='confirm' onclick="">Continue</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="uni_modal" role='dialog'>
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id='submit' onclick="$('#uni_modal form').submit()">Save</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="uni_modal_right" role='dialog'>
        <div class="modal-dialog modal-full-height modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span class="fa fa-arrow-right"></span>
                    </button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#preloader').fadeOut('fast', function () {
                $(this).remove();
            });
        });
    </script>

    <?php $conn->close(); ?>
</html>
