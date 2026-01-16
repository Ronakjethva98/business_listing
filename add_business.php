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

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>Dashboard</h2>
    <a href="dashboard.php">🏠 Home</a>
    <a href="add_business.php">➕ Add Business</a>
    <a href="company_inquiries.php">📨 View Inquiries</a>
    <a href="about.php">ℹ️ About</a>
    <a href="logout.php">🚪 Logout</a>

</div>

<!-- TOPBAR -->
<div class="topbar">
    ➕ Add New Business
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

</body>
</html>
