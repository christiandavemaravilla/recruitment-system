<?php 
include 'db_connect.php';

// Validate the 'id' parameter
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid request.");
}

$id = intval($_GET['id']);
$qry = $conn->query("SELECT * FROM application WHERE id = $id");
if ($qry->num_rows <= 0) {
    die("Application not found.");
}

$row = $qry->fetch_array();
$resume_path = $row['resume_path']; // This should be something like "uploads/1742282987_resume.pdf"

// Use the resume_path directly as the file location (adjust if needed)
$file = $resume_path;

// Check if the file exists
if (!file_exists($file)) {
    die("File not found.");
}

// Extract the original filename by removing the timestamp prefix
$baseName = basename($file);
$parts = explode('_', $baseName, 2); // Split into 2 parts: timestamp and the original filename
$fname = (count($parts) > 1) ? $parts[1] : $baseName;

// Get the MIME type of the file
$contentType = function_exists('mime_content_type') ? mime_content_type($file) : 'application/octet-stream';

// Set headers to force download
header("Content-Description: File Transfer");
header("Content-Type: " . $contentType);
header("Content-Length: " . filesize($file));
header("Content-Disposition: attachment; filename=\"" . $fname . "\"");
header("Expires: 0");
header("Cache-Control: must-revalidate");
header("Pragma: public");

// Read and output the file
readfile($file);
exit;
?>
