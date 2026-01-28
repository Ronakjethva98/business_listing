<?php
include "auth.php";
include "db.php";

/* BLOCK NON-COMPANY USERS */
if ($_SESSION['role'] !== 'company') {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Advertisement - Business Listing Portal</title>
    <meta name="description" content="Submit your advertisement for review">
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
        📢 Submit New Advertisement
    </div>
</div>

<!-- CONTENT -->
<div class="content content-center">

    <form method="POST" action="save_advertisement.php" enctype="multipart/form-data" class="form">
        
        <h2>Submit Advertisement</h2>
        
        <!-- SUCCESS/ERROR MESSAGES -->
        <?php if (isset($_GET['success'])): ?>
            <div style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; color: #059669; font-weight: 600; text-align: center; border: 2px solid #10b981; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);">
                ✓ Advertisement submitted successfully! It will be reviewed by an admin.
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div style="background: #fee2e2; padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; color: #dc2626; font-weight: 600; text-align: center; border: 2px solid #ef4444;">
                <?php if ($_GET['error'] == 'invalid_file'): ?>
                    ✗ Invalid file type. Please upload JPEG or PNG images only.
                <?php elseif ($_GET['error'] == 'file_too_large'): ?>
                    ✗ File size too large. Maximum 2MB allowed.
                <?php elseif ($_GET['error'] == 'upload_failed'): ?>
                    ✗ File upload failed. Please try again.
                <?php else: ?>
                    ✗ An error occurred. Please try again.
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <p style="color: #666; margin-bottom: 24px; text-align: center;">
            Submit your advertisement for admin approval. Once approved, it will appear across the website.
        </p>
        
        <label>Advertisement Title *</label>
        <input type="text" name="title" required maxlength="255" placeholder="Enter advertisement title">
        
        <label>Description (Optional)</label>
        <textarea name="description" rows="4" placeholder="Enter advertisement description"></textarea>
        
        <label>Advertisement Image * (Max 2MB, JPEG/PNG)</label>
        <input type="file" name="ad_image" accept="image/jpeg,image/png,image/jpg" required>
        <small style="color: #666; display: block; margin-top: 8px; margin-bottom: 16px;">Recommended size: 800x400px for best display</small>
        
        <label>Link URL (Optional)</label>
        <input type="url" name="link_url" placeholder="https://example.com">
        <small style="color: #666; display: block; margin-top: 8px; margin-bottom: 16px;">Where should users go when they click your ad?</small>
        
        <button type="submit" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            📤 Submit Advertisement
        </button>
        
        <a href="my_advertisements.php">
            <button type="button" style="background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%); margin-top: 12px; width: 100%;">
                📢 View My Ads
            </button>
        </a>
    </form>

</div>

</body>
</html>
