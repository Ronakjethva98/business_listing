<?php
session_start();
include "db.php";

$error = "";

/* KEEP ROLE FROM GET OR POST */
$role = $_GET['role'] ?? ($_POST['role'] ?? '');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $u = $_POST['username'];
    $p = $_POST['password'];

    $q = mysqli_query($conn, "SELECT * FROM users WHERE username='$u'");

    if (mysqli_num_rows($q) == 1) {
        $row = mysqli_fetch_assoc($q);

        if (password_verify($p, $row['password'])) {

            if ($row['role'] === 'normal') {
                $error = "Normal users do not need to login.";
            } else {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['role']    = $row['role'];

                header("Location: dashboard.php");
                exit();
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
    <title>Login - Business Listing Portal</title>
    <meta name="description" content="Login to your business listing account">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="sidebar">
    <h2>Business</h2>
    <a href="visitor.php">🏠 Home</a>
    <a href="login.php?role=company">🏢 Company Login</a>
    <a href="login.php?role=admin">👑 Admin Login</a>
    <a href="about.php">ℹ️ About</a>
</div>

<div class="topbar">🔑 Login to Your Account</div>

<div class="content content-center">
    <form method="POST" class="form">
        <h2>Login</h2>

        <!-- PRESERVE ROLE -->
        <input type="hidden" name="role" value="<?php echo htmlspecialchars($role); ?>">

        <input type="text" name="username" placeholder="Username" required autofocus>
        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">🔓 Login</button>

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

</body>
</html>
