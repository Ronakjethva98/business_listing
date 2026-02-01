<?php
include "auth.php";
include "db.php";

/* GET AD ID */
$ad_id = $_GET['id'] ?? 0;

/* FETCH ADVERTISEMENT */
$sql = "SELECT a.*, u.username as company_name 
        FROM advertisements a 
        JOIN users u ON a.company_id = u.id 
        WHERE a.id = '$ad_id'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    header("Location: dashboard.php");
    exit();
}

$ad = mysqli_fetch_assoc($result);

/* CHECK PERMISSIONS */
if ($_SESSION['role'] === 'company' && $ad['company_id'] != $_SESSION['user_id']) {
    // Company users can only edit their own ads
    header("Location: my_advertisements.php");
    exit();
}
// Admins can edit any ad

$error = "";
$success = "";

/* HANDLE UPDATE */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $link_url = mysqli_real_escape_string($conn, $_POST['link_url']);
    
    // Handle image upload if new image provided
    $image_path = $ad['image_path']; // Keep existing image by default
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = 'ad_' . time() . '_' . uniqid() . '.' . $ext;
            $upload_path = 'uploads/advertisements/' . $new_filename;
            
            // Create directory if it doesn't exist
            if (!file_exists('uploads/advertisements')) {
                mkdir('uploads/advertisements', 0777, true);
            }
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                // Delete old image if it exists
                if (file_exists($ad['image_path'])) {
                    unlink($ad['image_path']);
                }
                $image_path = $upload_path;
            } else {
                $error = "Failed to upload new image!";
            }
        } else {
            $error = "Invalid image format! Allowed: JPG, JPEG, PNG, GIF, WEBP";
        }
    }
    
    if ($error == "") {
        // Update advertisement
        $update_sql = "UPDATE advertisements SET 
                       title = '$title',
                       description = '$description',
                       link_url = '$link_url',
                       image_path = '$image_path',
                       updated_at = NOW()
                       WHERE id = '$ad_id'";
        
        if (mysqli_query($conn, $update_sql)) {
            $success = "Advertisement updated successfully!";
            // Refresh ad data
            $result = mysqli_query($conn, $sql);
            $ad = mysqli_fetch_assoc($result);
        } else {
            $error = "Failed to update advertisement!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Advertisement - Business Listing Portal</title>
    <meta name="description" content="Edit your advertisement">
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
        Edit Advertisement
    </div>
</div>

<!-- CONTENT -->
<div class="content content-center">

    <form method="POST" enctype="multipart/form-data" class="form" style="max-width: 600px;">
        <h2>Edit Advertisement</h2>

        <?php if ($success != "") { ?>
            <p class="success-message"><?php echo $success; ?></p>
        <?php } ?>

        <?php if ($error != "") { ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php } ?>

        <div style="margin-bottom: 20px; text-align: center;">
            <img src="<?php echo htmlspecialchars($ad['image_path']); ?>" 
                 alt="Current Image" 
                 style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        </div>

        <label>Advertisement Title *</label>
        <input
            type="text"
            name="title"
            value="<?php echo htmlspecialchars($ad['title']); ?>"
            required
            placeholder="Enter advertisement title"
        >

        <label>Description</label>
        <textarea
            name="description"
            rows="4"
            placeholder="Enter advertisement description (optional)"
        ><?php echo htmlspecialchars($ad['description']); ?></textarea>

        <label>Link URL</label>
        <input
            type="url"
            name="link_url"
            value="<?php echo htmlspecialchars($ad['link_url']); ?>"
            placeholder="https://example.com (optional)"
        >

        <label>Change Image (optional)</label>
        <input
            type="file"
            name="image"
            accept="image/*"
        >
        <small style="color: #666; display: block; margin-top: -12px; margin-bottom: 16px;">
            Leave empty to keep current image. Allowed: JPG, PNG, GIF, WEBP
        </small>

        <button type="submit">Update Advertisement</button>

        <div style="margin-top: 16px;">
            <a href="<?php echo $_SESSION['role'] === 'admin' ? 'manage_advertisements.php' : 'my_advertisements.php'; ?>" 
               style="text-decoration: none;">
                <button type="button" class="btn-back">← Back</button>
            </a>
        </div>
    </form>

</div>

</body>
</html>
