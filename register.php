<?php
include "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    /* CHECK IF USERNAME EXISTS (case-sensitive) */
    $check = mysqli_query($conn,
        "SELECT id FROM users WHERE username='$username'"
    );

    if (mysqli_num_rows($check) > 0) {
        $error = "Username already exists";
    } else {

        $hash = password_hash($password, PASSWORD_DEFAULT);

        /* REGISTER AS COMPANY USER */
        mysqli_query($conn,
            "INSERT INTO users (username, password, role)
             VALUES ('$username', '$hash', 'company')"
        );

        header("Location: login.php?role=company");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Registration - Business Listing Portal</title>
    <meta name="description" content="Register your company to list your business">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body class="auth-page">

<!-- NAVBAR -->
<nav class="navbar">
    <div class="navbar-container">
        <div class="navbar-header">
            <div class="navbar-brand">Business Portal</div>
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
        Company Registration
    </div>
</div>

<!-- CONTENT -->
<div class="content content-center">

    <form method="POST" class="form">
        <h2>Register as Company</h2>

        <input
            type="text"
            name="username"
            placeholder="Company Username"
            required
            autofocus
        >

        <input
            type="password"
            name="password"
            placeholder="Password"
            required
            minlength="6"
        >

        <button type="submit">Register</button>

        <?php if ($error != "") { ?>
            <p class="error-message">
                <?php echo $error; ?>
            </p>
        <?php } ?>
    </form>

</div>

</body>
</html>
