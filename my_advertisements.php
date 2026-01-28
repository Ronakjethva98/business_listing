<?php
include "auth.php";
include "db.php";

/* BLOCK NON-COMPANY USERS */
if ($_SESSION['role'] !== 'company') {
    header("Location: index.php");
    exit();
}

$company_id = $_SESSION['user_id'];

/* FETCH COMPANY'S ADVERTISEMENTS */
$sql = "SELECT * FROM advertisements WHERE company_id='$company_id' ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

/* CALCULATE STATISTICS */
$total_ads = mysqli_num_rows($result);
$pending_count = 0;
$approved_count = 0;
$rejected_count = 0;

mysqli_data_seek($result, 0); // Reset pointer
while ($row = mysqli_fetch_assoc($result)) {
    if ($row['status'] == 'pending') $pending_count++;
    elseif ($row['status'] == 'approved') $approved_count++;
    elseif ($row['status'] == 'rejected') $rejected_count++;
}
mysqli_data_seek($result, 0); // Reset pointer again
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Advertisements - Business Listing Portal</title>
    <meta name="description" content="View and manage your advertisements">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 24px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .stat-card h3 {
            font-size: 36px;
            margin: 0 0 8px 0;
        }
        .stat-card p {
            margin: 0;
            opacity: 0.9;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-pending {
            background: #fef3c7;
            color: #d97706;
        }
        .status-approved {
            background: #d1fae5;
            color: #059669;
        }
        .status-rejected {
            background: #fee2e2;
            color: #dc2626;
        }
        .ad-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .ad-table th {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            color: white;
            padding: 16px;
            text-align: left;
            font-weight: 600;
        }
        .ad-table td {
            padding: 16px;
            border-bottom: 1px solid #e5e7eb;
        }
        .ad-table tr:hover {
            background: #f9fafb;
        }
        .ad-thumbnail {
            width: 100px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }
        .admin-notes {
            background: #fef3c7;
            padding: 12px;
            border-radius: 8px;
            border-left: 4px solid #d97706;
            margin-top: 8px;
            font-size: 14px;
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
        📢 My Advertisements
    </div>
</div>

<!-- CONTENT -->
<div class="content">

    <!-- STATISTICS -->
    <div class="stats-grid">
        <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <h3><?php echo $total_ads; ?></h3>
            <p>Total Ads</p>
        </div>
        <div class="stat-card" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
            <h3><?php echo $pending_count; ?></h3>
            <p>Pending Review</p>
        </div>
        <div class="stat-card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <h3><?php echo $approved_count; ?></h3>
            <p>Approved</p>
        </div>
        <div class="stat-card" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
            <h3><?php echo $rejected_count; ?></h3>
            <p>Rejected</p>
        </div>
    </div>

    <!-- SUBMIT NEW AD BUTTON -->
    <div style="margin-bottom: 24px;">
        <a href="submit_advertisement.php">
            <button style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 12px 24px;">
                ➕ Submit New Advertisement
            </button>
        </a>
    </div>

    <!-- ADVERTISEMENTS TABLE -->
    <div class="box">
        <?php if ($total_ads > 0): ?>
            <table class="ad-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($ad = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td>
                                <img src="<?php echo htmlspecialchars($ad['image_path']); ?>" 
                                     alt="<?php echo htmlspecialchars($ad['title']); ?>" 
                                     class="ad-thumbnail">
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($ad['title']); ?></strong>
                                <?php if ($ad['description']): ?>
                                    <br><small style="color: #666;"><?php echo htmlspecialchars(substr($ad['description'], 0, 100)); ?><?php echo strlen($ad['description']) > 100 ? '...' : ''; ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $ad['status']; ?>">
                                    <?php 
                                        if ($ad['status'] == 'pending') echo '⏳ Pending';
                                        elseif ($ad['status'] == 'approved') echo '✓ Approved';
                                        elseif ($ad['status'] == 'rejected') echo '✗ Rejected';
                                    ?>
                                </span>
                            </td>
                            <td>
                                <?php echo date('M d, Y', strtotime($ad['created_at'])); ?>
                                <br><small style="color: #666;"><?php echo date('h:i A', strtotime($ad['created_at'])); ?></small>
                            </td>
                            <td>
                                <?php if ($ad['link_url']): ?>
                                    <a href="<?php echo htmlspecialchars($ad['link_url']); ?>" target="_blank" style="color: #3b82f6; text-decoration: none;">
                                        🔗 View Link
                                    </a>
                                    <br>
                                <?php endif; ?>
                                
                                <?php if ($ad['status'] == 'rejected' && $ad['admin_notes']): ?>
                                    <div class="admin-notes">
                                        <strong>Admin Notes:</strong><br>
                                        <?php echo htmlspecialchars($ad['admin_notes']); ?>
                                    </div>
                                <?php elseif ($ad['status'] == 'approved'): ?>
                                    <small style="color: #059669;">
                                        ✓ Approved on <?php echo date('M d, Y', strtotime($ad['approved_at'])); ?>
                                    </small>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center; color: #666; padding: 40px 20px;">
                No advertisements submitted yet.
                <br><br>
                <a href="submit_advertisement.php">
                    <button style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        ➕ Submit Your First Ad
                    </button>
                </a>
            </p>
        <?php endif; ?>
    </div>

</div>

</body>
</html>
