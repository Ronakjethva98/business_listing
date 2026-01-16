<?php
include "auth.php";
include "db.php";

/* ONLY ADMIN */
if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

$error = "";
$success = "";

/* ADD NEW ADMIN */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required!";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters!";
    } else {
        // Check if username already exists
        $check = mysqli_query($conn, "SELECT id FROM users WHERE username='$username'");
        
        if (mysqli_num_rows($check) > 0) {
            $error = "Username already exists!";
        } else {
            // Create new admin
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$hashed', 'admin')";
            
            if (mysqli_query($conn, $sql)) {
                $success = "New administrator created successfully!";
                // Clear form
                $_POST = array();
            } else {
                $error = "Failed to create administrator!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Admin - Business Listing Portal</title>
    <meta name="description" content="Create a new administrator account">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>Admin</h2>
    <a href="visitor.php">🏠 Home</a>
    <a href="manage_users.php">👥 Manage Users</a>
    <a href="view_admin.php">👤 View Admin</a>
    <a href="add_admin.php">➕ Add Admin</a>
    <a href="about.php">ℹ️ About</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<!-- TOPBAR -->
<div class="topbar">
    ➕ Add New Administrator
</div>

<!-- CONTENT -->
<div class="content content-center">

    <form method="POST" class="form">
        <h2>Create Administrator Account</h2>

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
            value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
            required
        >

        <input
            type="password"
            name="password"
            placeholder="Password (minimum 6 characters)"
            required
        >

        <input
            type="password"
            name="confirm_password"
            placeholder="Confirm Password"
            required
        >

        <button type="submit">➕ Create Administrator</button>
        
        <a href="view_admin.php" style="text-decoration: none; display: block; margin-top: 16px;">
            <button type="button" style="width: 100%; background: linear-gradient(135deg, #64748b 0%, #475569 100%);">
                ← Back to Admin Panel
            </button>
        </a>
    </form>

</div>

</body>
</html>
