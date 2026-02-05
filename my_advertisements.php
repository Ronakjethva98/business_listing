<?php
include "auth.php";
include "db.php";

/* BLOCK NON-COMPANY USERS */
if ($_SESSION['role'] !== 'company') {
    header("Location: index.php");
    exit();
}

$company_id = $_SESSION['user_id'];

/* GET FILTER */
$filter = $_GET['filter'] ?? 'pending';
if (!in_array($filter, ['pending', 'approved', 'rejected'])) {
    $filter = 'pending';
}

/* COUNT STATISTICS FOR TABS - SPECIFIC TO CURRENT COMPANY */
$pending_sql = "SELECT COUNT(*) as count FROM advertisements WHERE company_id='$company_id' AND status='pending'";
$approved_sql = "SELECT COUNT(*) as count FROM advertisements WHERE company_id='$company_id' AND status='approved'";
$rejected_sql = "SELECT COUNT(*) as count FROM advertisements WHERE company_id='$company_id' AND status='rejected'";

$pending_count = mysqli_fetch_assoc(mysqli_query($conn, $pending_sql))['count'];
$approved_count = mysqli_fetch_assoc(mysqli_query($conn, $approved_sql))['count'];
$rejected_count = mysqli_fetch_assoc(mysqli_query($conn, $rejected_sql))['count'];

/* FETCH FILTERED ADVERTISEMENTS */
$sql = "SELECT * FROM advertisements 
        WHERE company_id='$company_id' AND status='$filter' 
        ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Advertisements - Business Listing Portal</title>
    <meta name="description" content="View and manage your advertisements">
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
        📢 My Advertisements
    </div>
</div>

<!-- CONTENT -->
<div class="content">

    <!-- MESSAGES -->
    <?php if (isset($_GET['success']) && $_GET['success'] == 'deleted'): ?>
        <div class="success-message">✓ Advertisement deleted successfully!</div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="error-message">✗ Failed to delete advertisement.</div>
    <?php endif; ?>

    <!-- TABS -->
    <div class="ad-tabs">
        <a href="?filter=pending" class="ad-tab <?php echo $filter == 'pending' ? 'active' : ''; ?>">
            Pending (<?php echo $pending_count; ?>)
        </a>
        <a href="?filter=approved" class="ad-tab <?php echo $filter == 'approved' ? 'active' : ''; ?>">
            Approved (<?php echo $approved_count; ?>)
        </a>
        <a href="?filter=rejected" class="ad-tab <?php echo $filter == 'rejected' ? 'active' : ''; ?>">
            Rejected (<?php echo $rejected_count; ?>)
        </a>
    </div>

    <!-- SUBMIT NEW AD BUTTON -->
    <div style="margin-bottom: 24px;">
        <a href="submit_advertisement.php" class="btn-submit-ad-container">
            <button type="button" class="btn-primary-ad">
                <span class="icon">➕</span>
                <span class="text">Submit New Advertisement</span>
            </button>
        </a>
    </div>

    <!-- ADVERTISEMENTS LIST -->
    <div class="ad-grid">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($ad = mysqli_fetch_assoc($result)): ?>
                <div class="ad-card">
                    <?php if ($ad['link_url']): ?>
                        <a href="<?php echo htmlspecialchars($ad['link_url']); ?>" target="_blank" class="ad-image-link">
                    <?php endif; ?>
                        <img src="<?php echo htmlspecialchars($ad['image_path']); ?>" 
                             alt="<?php echo htmlspecialchars($ad['title']); ?>" 
                             class="ad-image">
                    <?php if ($ad['link_url']): ?>
                        </a>
                    <?php endif; ?>
                    
                    <div class="ad-info">
                        <h3><?php echo htmlspecialchars($ad['title']); ?></h3>
                        
                        <div class="ad-details">
                            <div class="ad-detail-row">
                                <strong>Status:</strong>
                                <span>
                                    <span class="status-badge status-<?php echo $ad['status']; ?>">
                                        <?php 
                                            if ($ad['status'] == 'pending') echo '⏳ Pending';
                                            elseif ($ad['status'] == 'approved') echo '✅ Approved';
                                            elseif ($ad['status'] == 'rejected') echo '❌ Rejected';
                                        ?>
                                    </span>
                                </span>
                            </div>
                            
                            <?php if ($ad['description']): ?>
                                <div class="ad-detail-row">
                                    <strong>Description:</strong>
                                    <span><?php echo htmlspecialchars($ad['description']); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="ad-detail-row">
                                <strong>Schedule:</strong>
                                <span>
                                    <?php echo date('M d, Y', strtotime($ad['start_date'])); ?> - 
                                    <?php echo date('M d, Y', strtotime($ad['end_date'])); ?> 
                                    (<?php echo $ad['days_duration']; ?> days)
                                </span>
                            </div>
                            
                            <div class="ad-detail-row">
                                <strong>Cost:</strong>
                                <span>₹<?php echo number_format($ad['total_cost'], 2); ?></span>
                            </div>
                            
                            <div class="ad-detail-row">
                                <strong>Payment:</strong>
                                <span>
                                    <?php if ($ad['is_paid']): ?>
                                        <span class="status-badge status-approved">PAID</span>
                                    <?php else: ?>
                                        <span class="status-badge status-rejected">UNPAID</span>
                                        <small style="display: block; color: var(--gray-500); margin-top: 4px;">Wait for admin to mark as paid after verification</small>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>
                        
                        <?php if ($ad['admin_notes']): ?>
                            <div class="ad-notes">
                                <strong>Admin Notes:</strong><br>
                                <?php echo htmlspecialchars($ad['admin_notes']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="ad-actions">
                        <a href="edit_advertisement.php?id=<?php echo $ad['id']; ?>" class="btn-edit">
                            ✏️ Edit
                        </a>
                        
                        <form action="update_advertisement_status.php" method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this advertisement?');">
                            <input type="hidden" name="ad_id" value="<?php echo $ad['id']; ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="btn-delete-ad">🗑️ Delete</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="box" style="padding: 40px; text-align: center;">
                <p style="color: #666; margin-bottom: 20px;">No <?php echo $filter; ?> advertisements found.</p>
                <?php if ($filter !== 'pending'): ?>
                    <a href="?filter=pending" style="color: var(--primary); font-weight: 600;">View Pending Ads</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php include "footer.php"; ?>

</body>
</html>
