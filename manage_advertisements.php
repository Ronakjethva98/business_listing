<?php
include "auth.php";
include "db.php";

/* BLOCK NON-ADMIN USERS */
if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

/* GET FILTER */
$filter = $_GET['filter'] ?? 'pending';
if (!in_array($filter, ['pending', 'approved', 'rejected'])) {
    $filter = 'pending';
}

/* FETCH ADVERTISEMENTS WITH COMPANY INFO */
$sql = "SELECT a.*, u.username as company_name 
        FROM advertisements a 
        JOIN users u ON a.company_id = u.id 
        WHERE a.status = '$filter' 
        ORDER BY a.created_at DESC";
$result = mysqli_query($conn, $sql);

/* COUNT STATISTICS */
$pending_sql = "SELECT COUNT(*) as count FROM advertisements WHERE status='pending'";
$approved_sql = "SELECT COUNT(*) as count FROM advertisements WHERE status='approved'";
$rejected_sql = "SELECT COUNT(*) as count FROM advertisements WHERE status='rejected'";

$pending_count = mysqli_fetch_assoc(mysqli_query($conn, $pending_sql))['count'];
$approved_count = mysqli_fetch_assoc(mysqli_query($conn, $approved_sql))['count'];
$rejected_count = mysqli_fetch_assoc(mysqli_query($conn, $rejected_sql))['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Advertisements - Business Listing Portal</title>
    <meta name="description" content="Review and approve advertisements">
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
        Manage Advertisements
    </div>
</div>

<!-- CONTENT -->
<div class="content">

    <!-- SUCCESS MESSAGE -->
    <?php if (isset($_GET['success'])): ?>
        <div class="success-message">
            <?php if ($_GET['success'] == 'approved'): ?>
                ✓ Advertisement approved successfully!
            <?php elseif ($_GET['success'] == 'rejected'): ?>
                ✓ Advertisement rejected!
            <?php elseif ($_GET['success'] == 'deleted'): ?>
                ✓ Advertisement deleted!
            <?php elseif ($_GET['success'] == 'payment_updated'): ?>
                ✓ Advertisement marked as PAID successfully!
            <?php endif; ?>

    <!-- ERROR MESSAGE -->
    <?php if (isset($_GET['error'])): ?>
        <div class="error-message">
            <?php if ($_GET['error'] == 'payment_update_failed'): ?>
                ✗ Failed to update payment status.
            <?php else: ?>
                ✗ An error occurred while processing your request.
            <?php endif; ?>
        </div>
    <?php endif; ?>
        </div>
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
                                <strong>Company:</strong> 
                                <span><?php echo htmlspecialchars($ad['company_name']); ?></span>
                            </div>
                            
                            <div class="ad-detail-row">
                                <strong>Status:</strong>
                                <span>
                                    <span class="status-badge status-<?php echo $ad['status']; ?>">
                                        <?php if ($ad['status'] == 'pending') echo '⏳ '; elseif ($ad['status'] == 'approved') echo '✅ '; else echo '❌ '; ?><?php echo ucfirst($ad['status']); ?>
                                    </span>
                                </span>
                            </div>
                            
                            <?php if ($ad['description']): ?>
                                <div class="ad-detail-row">
                                    <strong>Description:</strong>
                                    <span><?php echo htmlspecialchars($ad['description']); ?></span>
                                </div>
                            <?php endif; ?>
                            

                            
                            <?php if ($ad['start_date'] && $ad['end_date']): ?>
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
                                            <form action="mark_as_paid.php" method="POST" style="display: inline-block; margin-left: 10px;">
                                                <input type="hidden" name="ad_id" value="<?php echo $ad['id']; ?>">
                                                <button type="submit" class="btn-approve" style="padding: 4px 10px; font-size: 12px;" 
                                                        onclick="return confirm('Mark this advertisement as PAID?')">
                                                    Mark as Paid
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($ad['admin_notes']): ?>
                            <div class="ad-notes">
                                <strong>Admin Notes:</strong> <?php echo htmlspecialchars($ad['admin_notes']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="ad-actions">
                        <?php if ($ad['status'] == 'pending'): ?>
                            <form action="update_advertisement_status.php" method="POST">
                                <input type="hidden" name="ad_id" value="<?php echo $ad['id']; ?>">
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" class="btn-approve">Approve</button>
                            </form>
                            <button class="btn-reject" onclick="openRejectModal(<?php echo $ad['id']; ?>)">
                                Reject
                            </button>
                        <?php endif; ?>
                        
                        <a href="edit_advertisement.php?id=<?php echo $ad['id']; ?>" class="btn-edit">Edit</a>
                        
                        <form action="update_advertisement_status.php" method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this advertisement?');">
                            <input type="hidden" name="ad_id" value="<?php echo $ad['id']; ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="btn-delete-ad">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="box">
                No <?php echo $filter; ?> advertisements found.
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- REJECT MODAL -->
<div id="rejectModal" class="modal">
    <div class="modal-content">
        <span class="modal-close" onclick="closeRejectModal()">&times;</span>
        <h2>Reject Advertisement</h2>
        <form action="update_advertisement_status.php" method="POST" class="form">
            <input type="hidden" name="ad_id" id="reject_ad_id">
            <input type="hidden" name="action" value="reject">
            
            <label>Reason for Rejection *</label>
            <textarea name="admin_notes" rows="4" required placeholder="Enter reason for rejection..."></textarea>
            
            <button type="submit" class="btn-reject" style="width: 100%; margin-top: 12px;">
                ✗ Reject Advertisement
            </button>
        </form>
    </div>
</div>

<script>
function openRejectModal(adId) {
    document.getElementById('reject_ad_id').value = adId;
    document.getElementById('rejectModal').style.display = 'block';
}

function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('rejectModal');
    if (event.target == modal) {
        closeRejectModal();
    }
}
</script>

<?php include "footer.php"; ?>

</body>
</html>
