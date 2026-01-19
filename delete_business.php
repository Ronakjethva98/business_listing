<?php
session_start();

// Check authentication
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

// Only allow company and admin
if ($_SESSION['role'] !== 'company' && $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Include database
include "db.php";

// Get and validate ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$id = intval($_GET['id']);

// Get business info
$check_query = "SELECT id, name, user_id FROM businesses WHERE id = $id";
$check_result = mysqli_query($conn, $check_query);

if (!$check_result || mysqli_num_rows($check_result) == 0) {
    header("Location: dashboard.php?error=notfound");
    exit();
}

$business = mysqli_fetch_assoc($check_result);

// Check authorization for company users
if ($_SESSION['role'] === 'company' && $business['user_id'] != $_SESSION['user_id']) {
    header("Location: dashboard.php?error=unauthorized");
    exit();
}

// Delete related inquiries first
$business_name = mysqli_real_escape_string($conn, $business['name']);
mysqli_query($conn, "DELETE FROM inquiries WHERE business_name = '$business_name'");

// Delete the business
$delete_query = "DELETE FROM businesses WHERE id = $id";
mysqli_query($conn, $delete_query);

// Redirect with success message
header("Location: dashboard.php?msg=deleted");
exit();
