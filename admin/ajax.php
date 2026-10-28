<?php
ob_start();
$action = $_GET['action'];
include 'admin_class.php';
include 'db_connect.php'; // Make sure this path is correct
$crud = new Action();

if($action == 'login'){
	$login = $crud->login();
	if($login)
		echo $login;
}
if($action == 'login2'){
	$login = $crud->login2();
	if($login)
		echo $login;
}
if($action == 'logout'){
	$logout = $crud->logout();
	if($logout)
		echo $logout;
}
if($action == 'logout2'){
	$logout = $crud->logout2();
	if($logout)
		echo $logout;
}
if($action == 'save_user'){
	// Handle profile image upload
	$id = $_POST['id'];
	if(isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
		$upload_dir = 'assets/img/profile_images/';
		
		// Create directory if it doesn't exist
		if(!is_dir($upload_dir)) {
			mkdir($upload_dir, 0777, true);
		}
		
		$file_ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
		$filename = 'user_'.$id.'_'.time().'.'.$file_ext;
		
		// Move uploaded file
		if(move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_dir.$filename)) {
			// Update profile_image in the database
			$conn->query("UPDATE users SET profile_image = '$filename' WHERE id = ".$id);
		}
	}
	
	// Now proceed with the regular save_user functionality
	$save = $crud->save_user();
	if($save)
		echo $save;
}
if($action == 'delete_user'){
	$save = $crud->delete_user();
	if($save)
		echo $save;
}
if($action == 'signup'){
	$save = $crud->signup();
	if($save)
		echo $save;
}
if($action == "save_settings"){
	$save = $crud->save_settings();
	if($save)
		echo $save;
}
if($action == "save_recruitment_status"){
	$save = $crud->save_recruitment_status();
	if($save)
		echo $save;
}
if($action == "delete_recruitment_status"){
	$save = $crud->delete_recruitment_status();
	if($save)
		echo $save;
}
if($action == "save_vacancy"){
	$save = $crud->save_vacancy();
	if($save)
		echo $save;
}
if($action == "delete_vacancy"){
	$save = $crud->delete_vacancy();
	if($save)
		echo $save;
}
if($action == "save_application"){
	$save = $crud->save_application();
	if($save)
		echo $save;
}
if($action == "delete_application"){
	$save = $crud->delete_application();
	if($save)
		echo $save;
}


if ($_GET['action'] == 'send_notification') {
    $email = $_POST['email'];
    $message = $_POST['message'];

    // Check if notification exists
    $checkQuery = "SELECT * FROM notifications WHERE email = '$email'";
    $result = $conn->query($checkQuery);

    if ($result->num_rows > 0) {
        // Update
        $updateQuery = "UPDATE notifications SET message = '$message', status = 'unread', date_created = NOW() WHERE email = '$email'";
        if ($conn->query($updateQuery)) {
            echo "1";
        } else {
            echo "0";
        }
    } else {
        // Insert
        $insertQuery = "INSERT INTO notifications (email, message, status, date_created) 
                        VALUES ('$email', '$message', 'unread', NOW())";
        if ($conn->query($insertQuery)) {
            echo "1";
        } else {
            echo "0";
        }
    }
    exit;
}
?>