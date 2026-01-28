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
    <style>
        .tabs {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
            border-bottom: 2px solid #e5e7eb;
        }
        .tab {
            padding: 12px 24px;
            background: transparent;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            font-weight: 600;
            color: #6b7280;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        .tab:hover {
            color: #111827;
            background: #f3f4f6;
        }
        .tab.active {
            color: #3b82f6;
            border-bottom-color: #3b82f6;
        }
        .ad-grid {
            display: grid;
            gap: 24px;
        }
        .ad-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: grid;
            grid-template-columns: 200px 1fr auto;
            gap: 24px;
            align-items: start;
        }
        .ad-image {
            width: 200px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
        }
        .ad-info h3 {
            margin: 0 0 8px 0;
            color: #111827;
        }
        .ad-meta {
            color: #6b7280;
            font-size: 14px;
            margin: 4px 0;
        }
        .ad-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 150px;
        }
        .btn {
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            text-decoration: none;
            display: inline-block;
        }
        .btn-approve {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
        .btn-approve:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
        .btn-reject {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }
        .btn-reject:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }
        .btn-delete {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            color: white;
        }
        .stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
        }
        .stat-box h2 {
            font-size: 32px;
            margin: 0 0 8px 0;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
        }
        .modal-content {
            background: white;
            margin: 10% auto;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
        }
        .modal-close {
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            color: #6b7280;
        }
        .modal-close:hover {
            color: #111827;
        }
    </style>
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
        📢 Manage Advertisements
    </div>
</div>

<!-- CONTENT -->
<div class="content">

    <!-- SUCCESS MESSAGE -->
    <?php if (isset($_GET['success'])): ?>
        <div style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; color: #059669; font-weight: 600; text-align: center; border: 2px solid #10b981;">
            <?php if ($_GET['success'] == 'approved'): ?>
                ✓ Advertisement approved successfully!
            <?php elseif ($_GET['success'] == 'rejected'): ?>
                ✓ Advertisement rejected!
            <?php elseif ($_GET['success'] == 'deleted'): ?>
                ✓ Advertisement deleted!
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- STATISTICS -->
    <div class="stats-row">
        <div class="stat-box" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
            <h2><?php echo $pending_count; ?></h2>
            <p>Pending Review</p>
        </div>
        <div class="stat-box" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <h2><?php echo $approved_count; ?></h2>
            <p>Approved</p>
        </div>
        <div class="stat-box" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
            <h2><?php echo $rejected_count; ?></h2>
            <p>Rejected</p>
        </div>
    </div>

    <!-- TABS -->
    <div class="tabs">
        <a href="?filter=pending" class="tab <?php echo $filter == 'pending' ? 'active' : ''; ?>">
            ⏳ Pending (<?php echo $pending_count; ?>)
        </a>
        <a href="?filter=approved" class="tab <?php echo $filter == 'approved' ? 'active' : ''; ?>">
            ✓ Approved (<?php echo $approved_count; ?>)
        </a>
        <a href="?filter=rejected" class="tab <?php echo $filter == 'rejected' ? 'active' : ''; ?>">
            ✗ Rejected (<?php echo $rejected_count; ?>)
        </a>
    </div>

    <!-- ADVERTISEMENTS LIST -->
    <div class="ad-grid">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($ad = mysqli_fetch_assoc($result)): ?>
                <div class="ad-card">
                    <img src="<?php echo htmlspecialchars($ad['image_path']); ?>" 
                         alt="<?php echo htmlspecialchars($ad['title']); ?>" 
                         class="ad-image">
                    
                    <div class="ad-info">
                        <h3><?php echo htmlspecialchars($ad['title']); ?></h3>
                        <p class="ad-meta">
                            <strong>Company:</strong> <?php echo htmlspecialchars($ad['company_name']); ?>
                        </p>
                        <p class="ad-meta">
                            <strong>Submitted:</strong> <?php echo date('M d, Y h:i A', strtotime($ad['created_at'])); ?>
                        </p>
                        <?php if ($ad['description']): ?>
                            <p class="ad-meta">
                                <strong>Description:</strong><br>
                                <?php echo htmlspecialchars($ad['description']); ?>
                            </p>
                        <?php endif; ?>
                        <?php if ($ad['link_url']): ?>
                            <p class="ad-meta">
                                <strong>Link:</strong> 
                                <a href="<?php echo htmlspecialchars($ad['link_url']); ?>" target="_blank" style="color: #3b82f6;">
                                    <?php echo htmlspecialchars($ad['link_url']); ?>
                                </a>
                            </p>
                        <?php endif; ?>
                        <?php if ($ad['admin_notes']): ?>
                            <p class="ad-meta" style="background: #fef3c7; padding: 12px; border-radius: 8px; border-left: 4px solid #d97706;">
                                <strong>Admin Notes:</strong><br>
                                <?php echo htmlspecialchars($ad['admin_notes']); ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div class="ad-actions">
                        <?php if ($ad['status'] == 'pending'): ?>
                            <form action="update_advertisement_status.php" method="POST" style="margin: 0;">
                                <input type="hidden" name="ad_id" value="<?php echo $ad['id']; ?>">
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" class="btn btn-approve">✓ Approve</button>
                            </form>
                            <button class="btn btn-reject" onclick="openRejectModal(<?php echo $ad['id']; ?>)">✗ Reject</button>
                        <?php endif; ?>
                        
                        <form action="update_advertisement_status.php" method="POST" style="margin: 0;" 
                              onsubmit="return confirm('Are you sure you want to delete this advertisement?');">
                            <input type="hidden" name="ad_id" value="<?php echo $ad['id']; ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="btn btn-delete">🗑️ Delete</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="box" style="text-align: center; padding: 40px; color: #6b7280;">
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
        <form action="update_advertisement_status.php" method="POST">
            <input type="hidden" name="ad_id" id="reject_ad_id">
            <input type="hidden" name="action" value="reject">
            
            <label>Reason for Rejection *</label>
            <textarea name="admin_notes" rows="4" required placeholder="Enter reason for rejection..."></textarea>
            
            <button type="submit" class="btn btn-reject" style="width: 100%; margin-top: 12px;">
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

</body>
</html>
