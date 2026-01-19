<?php
session_start();

// Check authentication
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    die("Error: Not logged in. <a href='login.php'>Login here</a>");
}

// Only allow company and admin
if ($_SESSION['role'] !== 'company' && $_SESSION['role'] !== 'admin') {
    die("Error: Access denied. Only company users and admins can delete businesses.");
}

// Include database
include "db.php";

// Get and validate ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: No business ID provided. <a href='dashboard.php'>Go back</a>");
}

$id = intval($_GET['id']);

if ($id <= 0) {
    die("Error: Invalid business ID. <a href='dashboard.php'>Go back</a>");
}

// Get business info first
$check_query = "SELECT id, name, user_id FROM businesses WHERE id = $id";
$check_result = mysqli_query($conn, $check_query);

if (!$check_result) {
    die("Database error: " . mysqli_error($conn));
}

if (mysqli_num_rows($check_result) == 0) {
    die("Error: Business with ID $id not found. <a href='dashboard.php'>Go back</a>");
}

$business = mysqli_fetch_assoc($check_result);

// Check authorization for company users
if ($_SESSION['role'] === 'company') {
    if ($business['user_id'] != $_SESSION['user_id']) {
        die("Error: You don't have permission to delete this business. <a href='dashboard.php'>Go back</a>");
    }
}

// Delete related inquiries first
$business_name = mysqli_real_escape_string($conn, $business['name']);
$delete_inquiries = "DELETE FROM inquiries WHERE business_name = '$business_name'";
mysqli_query($conn, $delete_inquiries);

// Now delete the business
$delete_query = "DELETE FROM businesses WHERE id = $id";
$delete_result = mysqli_query($conn, $delete_query);

if (!$delete_result) {
    die("Delete failed: " . mysqli_error($conn));
}

if (mysqli_affected_rows($conn) > 0) {
    // Success - redirect with message
    header("Location: dashboard.php?msg=deleted");
    exit();
} else {
    die("Error: No rows were deleted. <a href='dashboard.php'>Go back</a>");
}
