<?php include "auth.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Business - Business Listing Portal</title>
    <meta name="description" content="Add a new business listing">
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
                
                <?php if ($_SESSION['role'] === 'company') { ?>
                    <a href="add_business.php">Add Business</a>
                    <a href="my_advertisements.php">My Ads</a>
                    <a href="submit_advertisement.php">Submit Ad</a>
                    <a href="view_inquiries.php">View Inquiries</a>
                    <a href="about.php">About</a>
                <?php } elseif ($_SESSION['role'] === 'admin') { ?>
                    <a href="manage_users.php">Manage Users</a>
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
        ➕ Add New Business
    </div>
</div>

<!-- CONTENT -->
<div class="content content-center">

    <form method="POST"
          action="save_business.php"
          enctype="multipart/form-data"
          class="form">

        <h2>Add Business</h2>

        <input
            type="text"
            name="name"
            placeholder="Business Name"
            required
        >

        <input
            type="text"
            name="category"
            placeholder="Category"
            required
        >

        <input
            type="text"
            name="address"
            placeholder="Address"
            required
        >

        <input
            type="text"
            name="phone"
            placeholder="Phone"
            required
        >

        <textarea
            name="description"
            placeholder="Description"
        ></textarea>

        <input
            type="file"
            name="image"
            required
        >

        <button type="submit">💾 Save Business</button>

    </form>

</div>

<?php include "footer.php"; ?>

</body>
</html>
