<?php
session_start();
include "db.php";

$error = "";

/* KEEP ROLE FROM GET OR POST */
$role = $_GET['role'] ?? ($_POST['role'] ?? '');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $u = mysqli_real_escape_string($conn, $_POST['username']);
    $p = $_POST['password'];

    // Case-sensitive username matching
    $q = mysqli_query($conn, "SELECT * FROM users WHERE username='$u'");

    if (mysqli_num_rows($q) == 1) {
        $row = mysqli_fetch_assoc($q);

        if (password_verify($p, $row['password'])) {

            if ($row['role'] === 'normal') {
                $error = "Normal users do not need to login.";
            } else {
                // Check if the user's role matches the login page type
                if ($role === 'company' && $row['role'] !== 'company') {
                    $error = "Invalid credentials. Please use the Admin Login page if you are an administrator.";
                } elseif ($role === 'admin' && $row['role'] !== 'admin') {
                    $error = "Invalid credentials. Please use the Company Login page if you are a company user.";
                } else {
                    // Role matches, proceed with login
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['role']    = $row['role'];

                    header("Location: dashboard.php");
                    exit();
                }
            }
        }
    }

    if ($error == "") {
        $error = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $role === 'admin' ? 'Admin Login' : ($role === 'company' ? 'Company Login' : 'Login'); ?> - Business Listing Portal</title>
    <meta name="description" content="Login to your business listing account">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body class="auth-page">

<!-- NAVBAR -->
<nav class="navbar">
    <div class="navbar-container">
        <div class="navbar-header">
            <div class="navbar-brand">
                <img src="assets/logo.png" alt="Logo" class="navbar-logo">
                Business Portal
            </div>
            <div class="navbar-menu">
                <a href="index.php">Home</a>
                <a href="login.php?role=company">Company Login</a>
                <a href="login.php?role=admin">Admin Login</a>
                <a href="about.php">About</a>
            </div>
        </div>
    </div>
</nav>

<!-- TOPBAR -->
<div class="topbar">
    <div class="topbar-container">
        <?php echo $role === 'admin' ? '🔑 Admin Login Portal' : ($role === 'company' ? '💼 Company Login Portal' : '👤 Login to Your Account'); ?>
    </div>
</div>

<div class="content content-center">
    <form method="POST" class="form">
        <h2><?php echo $role === 'admin' ? 'Admin Login' : ($role === 'company' ? 'Company Login' : 'Login'); ?></h2>

        <!-- PRESERVE ROLE -->
        <input type="hidden" name="role" value="<?php echo htmlspecialchars($role); ?>">

        <input type="text" name="username" placeholder="Username" required autofocus>
        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">Login</button>

        <!-- SHOW REGISTER LINK ONLY FOR COMPANY USER -->
        <?php if ($role === 'company') { ?>
            <p class="form-link">
                New company?
                <a href="register.php">
                    Register here
                </a>
            </p>
        <?php } ?>

        <?php if ($error != "") { ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php } ?>
    </form>
</div>

<?php include "footer.php"; ?>

</body>
</html>
