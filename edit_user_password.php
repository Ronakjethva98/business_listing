<?php
include "auth.php";
include "db.php";

/* ONLY ADMIN CAN ACCESS */
if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

$uid = intval($_GET['id'] ?? 0);
$error = "";
$success = "";

/* FETCH USER DETAILS TO ENSURE IT'S A COMPANY USER */
$user_res = mysqli_query($conn, "SELECT username, role FROM users WHERE id='$uid' AND role='company'");
if (mysqli_num_rows($user_res) == 0) {
    header("Location: manage_users.php");
    exit();
}

$user_data = mysqli_fetch_assoc($user_res);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $hash = password_hash($new_password, PASSWORD_DEFAULT);
        $update_query = "UPDATE users SET password='$hash' WHERE id='$uid'";
        if (mysqli_query($conn, $update_query)) {
            $success = "Password updated successfully for " . htmlspecialchars($user_data['username']);
        } else {
            $error = "Failed to update password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change User Password - Business Listing Portal</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="navbar-container">
        <div class="navbar-header">
            <div class="navbar-brand">
                <img src="assets/logo.png" alt="Logo" class="navbar-logo">
                Business Portal
            </div>
            <div class="navbar-menu">
                <div class="navbar-user"><?php echo ucfirst($_SESSION['role']); ?></div>
                    <a href="dashboard.php">Home</a>
                    <a href="manage_users.php">Manage Users</a>
                    <a href="manage_advertisements.php">Manage Ads</a>
                    <a href="view_inquiries.php">View Inquiries</a>
                    <a href="view_admin.php">View Admin</a>
                    <a href="add_admin.php">Add Admin</a>
                    <a href="about.php">About</a>
                    <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>
</nav>

<!-- TOPBAR -->
<div class="topbar">
    <div class="topbar-container">
        Change Password for <?php echo htmlspecialchars($user_data['username']); ?>
    </div>
</div>

<!-- CONTENT -->
<div class="content content-center">

    <form method="POST" class="form">
        <h2>Set New Password</h2>
        <p style="text-align: center; color: #666; margin-bottom: 20px;">
            Changing password for: <strong><?php echo htmlspecialchars($user_data['username']); ?></strong>
        </p>

        <?php if ($error != "") { ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php } ?>

        <?php if ($success != "") { ?>
            <p class="success-message"><?php echo $success; ?></p>
        <?php } ?>

        <label>New Password</label>
        <input type="password" name="new_password" placeholder="Enter new password" required minlength="6">

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" placeholder="Confirm new password" required minlength="6">

        <button type="submit">Update Password</button>

        <div style="margin-top: 20px;">
            <a href="manage_users.php" style="text-decoration: none;">
                <button type="button" class="btn-back">← Back to Manage Users</button>
            </a>
        </div>
    </form>

</div>

<?php include "footer.php"; ?>

</body>
</html>
