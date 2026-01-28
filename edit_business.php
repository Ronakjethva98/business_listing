<?php
include "auth.php";
include "db.php";

$id = $_GET['id'];
$q  = mysqli_query($conn, "SELECT * FROM businesses WHERE id='$id'");
$b  = mysqli_fetch_assoc($q);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Business - Business Listing Portal</title>
    <meta name="description" content="Edit business listing details">
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
        Edit Business
    </div>
</div>

<!-- CONTENT -->
<div class="content content-center">

    <form method="POST" action="update_business.php" class="form" enctype="multipart/form-data">
        <h2>Edit Business</h2>

        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <input type="hidden" name="old_image" value="<?php echo $b['image']; ?>">

        <input
            type="text"
            name="name"
            placeholder="Business Name"
            value="<?php echo $b['name']; ?>"
            required
        >

        <input
            type="text"
            name="category"
            placeholder="Category"
            value="<?php echo $b['category']; ?>"
            required
        >

        <input
            type="text"
            name="address"
            placeholder="Address"
            value="<?php echo $b['address']; ?>"
            required
        >

        <input
            type="text"
            name="phone"
            placeholder="Phone Number"
            value="<?php echo $b['phone']; ?>"
            required
        >

        <textarea
            name="description"
            placeholder="Description"
        ><?php echo $b['description']; ?></textarea>

        <?php if (!empty($b['image'])) { 
            // Handle both 'filename.jpg' and 'uploads/filename.jpg' formats
            $image_src = (strpos($b['image'], 'uploads/') === 0) ? $b['image'] : 'uploads/' . $b['image'];
        ?>
            <div style="margin-bottom: 16px; text-align: center;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155;">Current Image:</label>
                <img src="<?php echo $image_src; ?>" alt="Current business image" style="max-width: 100%; max-height: 200px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
            </div>
        <?php } ?>

        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155; margin-top: 16px;">
            <?php echo !empty($b['image']) ? 'Change Image (optional):' : 'Upload Image:'; ?>
        </label>
        <input
            type="file"
            name="image"
            accept="image/*"
            style="display: block; width: 100%; padding: 12px; margin-bottom: 16px; border: 2px solid #e2e8f0; border-radius: 10px; background: #fff; font-size: 14px; cursor: pointer;"
        >

        <button type="submit">Update Business</button>
    </form>

</div>

</body>
</html>
