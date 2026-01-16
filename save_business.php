<?php
session_start();
include "db.php";

/* SECURITY CHECK */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$name = mysqli_real_escape_string($conn, $_POST['name']);
$category = mysqli_real_escape_string($conn, $_POST['category']);
$address = mysqli_real_escape_string($conn, $_POST['address']);
$phone = mysqli_real_escape_string($conn, $_POST['phone']);
$description = mysqli_real_escape_string($conn, $_POST['description']);

$image_path = '';

// Handle image upload
if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $target_dir = "uploads/";
    
    // Create uploads directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '_' . time() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        $image_path = $target_file; // Save as 'uploads/filename.jpg'
    }
}

$uid = $_SESSION['user_id'];

$sql = "INSERT INTO businesses 
(user_id, name, category, address, phone, description, image)
VALUES 
('$uid', '$name', '$category', '$address', '$phone', '$description', '$image_path')";

mysqli_query($conn, $sql);

header("Location: dashboard.php");
exit();
