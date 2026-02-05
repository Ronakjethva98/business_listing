<?php
include "auth.php";
include "db.php";

/* ONLY ADMIN */
if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

$admin_id = intval($_GET['id']);
$error = "";
$success = "";

/* FETCH ADMIN DATA */
$query = mysqli_query($conn, "SELECT * FROM users WHERE id='$admin_id' AND role='admin'");
$admin = mysqli_fetch_assoc($query);

if (!$admin) {
    header("Location: view_admin.php");
    exit();
}

/* UPDATE ADMIN */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    
    // Check if username is already taken by another user
    $check = mysqli_query($conn, "SELECT id FROM users WHERE username='$username' AND id != '$admin_id'");
    
    if (mysqli_num_rows($check) > 0) {
        $error = "Username already exists!";
    } else {
        if (!empty($password)) {
            // Update with new password
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $sql = "UPDATE users SET username='$username', password='$hashed' WHERE id='$admin_id'";
        } else {
            // Update without changing password
            $sql = "UPDATE users SET username='$username' WHERE id='$admin_id'";
        }
        
        if (mysqli_query($conn, $sql)) {
            $success = "Administrator updated successfully!";
            // Refresh admin data
            $query = mysqli_query($conn, "SELECT * FROM users WHERE id='$admin_id' AND role='admin'");
            $admin = mysqli_fetch_assoc($query);
        } else {
            $error = "Failed to update administrator!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Administrator - Business Listing Portal</title>
    <meta name="description" content="Edit administrator account details">
    <link rel="stylesheet" href="style.css">
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
                
                <?php if ($_SESSION['role'] === 'company') { ?>
                    <a href="add_business.php">Add Business</a>
                    <a href="my_advertisements.php">My Ads</a>
                    <a href="submit_advertisement.php">Submit Ad</a>
                    <a href="view_inquiries.php">View Inquiries</a>
                    <a href="about.php">About</a>
                <?php } elseif ($_SESSION['role'] === 'admin') { ?>
                    <a href="manage_users.php">Manage Users</a>
                    <a href="manage_advertisements.php">Manage Ads</a>
                    <a href="view_inquiries.php">View Inquiries</a>
                    <a href="view_admin.php">View Admin</a>
                    <a href="add_admin.php">Add Admin</a>
                    <a href="about.php">About</a>
                <?php } ?>
                
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </div>
</nav>

<!-- TOPBAR -->
<div class="topbar">
    <div class="topbar-container">
        Edit Administrator
    </div>
</div>

<!-- CONTENT -->
<div class="content content-center">

    <form method="POST" class="form">
        <h2>Edit Administrator Account</h2>

        <?php if ($success != "") { ?>
            <p style="color: #10b981; text-align: center; margin-bottom: 15px; padding: 12px; background: #d1fae5; border-radius: 10px; font-size: 14px;">
                <?php echo $success; ?>
            </p>
        <?php } ?>

        <?php if ($error != "") { ?>
            <p style="color: #ef4444; text-align: center; margin-bottom: 15px; padding: 12px; background: #fee2e2; border-radius: 10px; font-size: 14px;">
                <?php echo $error; ?>
            </p>
        <?php } ?>

        <input
            type="text"
            name="username"
            placeholder="Username"
            value="<?php echo htmlspecialchars($admin['username']); ?>"
            required
        >

        <input
            type="password"
            name="password"
            placeholder="New Password (leave blank to keep current)"
        >

        <button type="submit">Update Administrator</button>
        
        <a href="view_admin.php" style="text-decoration: none; display: block; margin-top: 16px;">
            <button type="button" style="width: 100%; background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%);">
                ← Back to View Admin
            </button>
        </a>
    </form>

</div>

<?php include "footer.php"; ?>

</body>
</html>
