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
        // Check if username already exists (case-sensitive)
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
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="navbar-container">
        <div class="navbar-header">
            <div class="navbar-brand">Business Portal</div>
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
        Add New Administrator
    </div>
</div>

<!-- CONTENT -->
<div class="content content-center">

    <form method="POST" class="form">
        <h2>Create Administrator Account</h2>

        <?php if ($success != "") { ?>
            <p class="success-message">
                <?php echo $success; ?>
            </p>
        <?php } ?>

        <?php if ($error != "") { ?>
            <p class="error-message">
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

        <button type="submit">Create Administrator</button>
        
        <div style="margin-top: 16px;">
            <a href="view_admin.php" style="text-decoration: none;">
                <button type="button" class="btn-back">← Back to Admin Panel</button>
            </a>
        </div>
    </form>

</div>

</body>
</html>
