<?php
// Start session
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h3>Debug Info:</h3>";
echo "Session User ID: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NOT SET') . "<br>";
echo "Session Role: " . (isset($_SESSION['role']) ? $_SESSION['role'] : 'NOT SET') . "<br>";
echo "Business ID from URL: " . (isset($_GET['id']) ? $_GET['id'] : 'NOT SET') . "<br><br>";

// Check authentication
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    die("<p style='color:red;'>ERROR: You are not logged in! <a href='login.php'>Login here</a></p>");
}

// Only allow company and admin
if ($_SESSION['role'] !== 'company' && $_SESSION['role'] !== 'admin') {
    die("<p style='color:red;'>ERROR: Access denied. Only company and admin can delete. Your role: " . $_SESSION['role'] . "</p>");
}

// Include database
include "db.php";

if (!$conn) {
    die("<p style='color:red;'>ERROR: Database connection failed!</p>");
}
echo "<p style='color:green;'>✓ Database connected successfully</p>";

// Get and validate ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("<p style='color:red;'>ERROR: No business ID provided! <a href='dashboard.php'>Go back</a></p>");
}

$id = intval($_GET['id']);
echo "<p>Processing delete for business ID: $id</p>";

if ($id <= 0) {
    die("<p style='color:red;'>ERROR: Invalid business ID! <a href='dashboard.php'>Go back</a></p>");
}

// Get business info first
$check_query = "SELECT id, name, user_id FROM businesses WHERE id = $id";
echo "<p>Running query: $check_query</p>";

$check_result = mysqli_query($conn, $check_query);

if (!$check_result) {
    die("<p style='color:red;'>Database query error: " . mysqli_error($conn) . "</p>");
}

echo "<p style='color:green;'>✓ Query executed successfully</p>";

if (mysqli_num_rows($check_result) == 0) {
    die("<p style='color:red;'>ERROR: Business with ID $id not found in database! <a href='dashboard.php'>Go back</a></p>");
}

$business = mysqli_fetch_assoc($check_result);
echo "<p style='color:green;'>✓ Found business: " . htmlspecialchars($business['name']) . "</p>";
echo "<p>Business owner ID: " . $business['user_id'] . "</p>";

// Check authorization for company users
if ($_SESSION['role'] === 'company') {
    echo "<p>Checking authorization for company user...</p>";
    if ($business['user_id'] != $_SESSION['user_id']) {
        die("<p style='color:red;'>ERROR: This business belongs to user ID " . $business['user_id'] . " but you are user ID " . $_SESSION['user_id'] . ". You can only delete your own businesses! <a href='dashboard.php'>Go back</a></p>");
    }
    echo "<p style='color:green;'>✓ Authorization passed - you own this business</p>";
} else {
    echo "<p style='color:green;'>✓ Admin user - can delete any business</p>";
}

// Delete related inquiries first
$business_name = mysqli_real_escape_string($conn, $business['name']);
$delete_inquiries = "DELETE FROM inquiries WHERE business_name = '$business_name'";
echo "<p>Deleting related inquiries: $delete_inquiries</p>";
$inquiry_result = mysqli_query($conn, $delete_inquiries);
if ($inquiry_result) {
    $deleted_inquiries = mysqli_affected_rows($conn);
    echo "<p style='color:green;'>✓ Deleted $deleted_inquiries related inquiries</p>";
} else {
    echo "<p style='color:orange;'>Warning: Could not delete inquiries: " . mysqli_error($conn) . "</p>";
}

// Now delete the business
$delete_query = "DELETE FROM businesses WHERE id = $id";
echo "<p>Deleting business: $delete_query</p>";
$delete_result = mysqli_query($conn, $delete_query);

if (!$delete_result) {
    die("<p style='color:red;'>Delete query failed: " . mysqli_error($conn) . "</p>");
}

$affected = mysqli_affected_rows($conn);
echo "<p>Rows affected: $affected</p>";

if ($affected > 0) {
    echo "<p style='color:green; font-size:20px; font-weight:bold;'>✓ SUCCESS! Business deleted!</p>";
    echo "<p>Redirecting to dashboard in 3 seconds...</p>";
    echo "<meta http-equiv='refresh' content='3;url=dashboard.php?msg=deleted'>";
} else {
    die("<p style='color:red;'>ERROR: No rows were deleted! <a href='dashboard.php'>Go back</a></p>");
}
