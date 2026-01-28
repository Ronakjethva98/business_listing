<?php
include "auth.php";
include "db.php";

/* BLOCK NON-COMPANY USERS */
if ($_SESSION['role'] !== 'company') {
    header("Location: index.php");
    exit();
}

/* VALIDATE POST REQUEST */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: submit_advertisement.php");
    exit();
}

/* GET FORM DATA */
$company_id = $_SESSION['user_id'];
$title = mysqli_real_escape_string($conn, trim($_POST['title']));
$description = mysqli_real_escape_string($conn, trim($_POST['description']));
$link_url = mysqli_real_escape_string($conn, trim($_POST['link_url']));

/* VALIDATE REQUIRED FIELDS */
if (empty($title)) {
    header("Location: submit_advertisement.php?error=missing_fields");
    exit();
}

/* HANDLE FILE UPLOAD */
if (!isset($_FILES['ad_image']) || $_FILES['ad_image']['error'] !== UPLOAD_ERR_OK) {
    header("Location: submit_advertisement.php?error=upload_failed");
    exit();
}

$file = $_FILES['ad_image'];
$file_name = $file['name'];
$file_tmp = $file['tmp_name'];
$file_size = $file['size'];
$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

/* VALIDATE FILE TYPE */
$allowed_extensions = ['jpg', 'jpeg', 'png'];
if (!in_array($file_ext, $allowed_extensions)) {
    header("Location: submit_advertisement.php?error=invalid_file");
    exit();
}

/* VALIDATE FILE SIZE (2MB MAX) */
if ($file_size > 2097152) {
    header("Location: submit_advertisement.php?error=file_too_large");
    exit();
}

/* GENERATE UNIQUE FILENAME */
$new_filename = uniqid('ad_', true) . '.' . $file_ext;
$upload_path = 'uploads/ads/' . $new_filename;

/* MOVE FILE TO UPLOADS DIRECTORY */
if (!move_uploaded_file($file_tmp, $upload_path)) {
    header("Location: submit_advertisement.php?error=upload_failed");
    exit();
}

/* INSERT INTO DATABASE */
$sql = "INSERT INTO advertisements (company_id, title, description, image_path, link_url, status) 
        VALUES ('$company_id', '$title', '$description', '$upload_path', '$link_url', 'pending')";

if (mysqli_query($conn, $sql)) {
    header("Location: submit_advertisement.php?success=1");
} else {
    // Delete uploaded file if database insert fails
    if (file_exists($upload_path)) {
        unlink($upload_path);
    }
    header("Location: submit_advertisement.php?error=database");
}
exit();
?>
