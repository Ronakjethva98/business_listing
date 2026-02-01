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
$start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
$end_date = mysqli_real_escape_string($conn, $_POST['end_date']);

/* VALIDATE REQUIRED FIELDS */
if (empty($title) || empty($start_date) || empty($end_date)) {
    header("Location: submit_advertisement.php?error=missing_fields");
    exit();
}

/* VALIDATE DATES */
$start = new DateTime($start_date);
$end = new DateTime($end_date);
$today = new DateTime();
$today->setTime(0,0,0);

if ($start < $today) {
    header("Location: submit_advertisement.php?error=invalid_start_date");
    exit();
}

if ($end < $start) {
    header("Location: submit_advertisement.php?error=invalid_end_date");
    exit();
}

/* CALCULATE COST */
$days_duration = $start->diff($end)->days + 1; // +1 to include both days
$cost_per_day = 100.00;
$total_cost = $days_duration * $cost_per_day;

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
$sql = "INSERT INTO advertisements (company_id, title, description, image_path, link_url, start_date, end_date, days_duration, cost_per_day, total_cost, is_paid, status) 
        VALUES ('$company_id', '$title', '$description', '$upload_path', '$link_url', '$start_date', '$end_date', '$days_duration', '$cost_per_day', '$total_cost', 0, 'pending')";

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
