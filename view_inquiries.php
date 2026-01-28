<?php
include "auth.php";
include "db.php";

/* ALLOW ADMIN AND COMPANY USERS */
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'company') {
    header("Location: dashboard.php");
    exit();
}

/* DELETE INQUIRY */
if (isset($_GET['delete'])) {
    $inquiry_id = intval($_GET['delete']);
    
    // For company users, only allow deleting inquiries for their own businesses
    if ($_SESSION['role'] === 'company') {
        $user_id = $_SESSION['user_id'];
        // Get list of business names owned by this user
        $businesses = mysqli_query($conn, "SELECT name FROM businesses WHERE user_id='$user_id'");
        $business_names = [];
        while ($row = mysqli_fetch_assoc($businesses)) {
            $business_names[] = "'" . mysqli_real_escape_string($conn, $row['name']) . "'";
        }
        
        if (count($business_names) > 0) {
            $names_list = implode(',', $business_names);
            mysqli_query($conn, "DELETE FROM inquiries WHERE id='$inquiry_id' AND business_name IN ($names_list)");
        }
    } else {
        // Admin can delete any inquiry
        mysqli_query($conn, "DELETE FROM inquiries WHERE id='$inquiry_id'");
    }
    
    header("Location: view_inquiries.php");
    exit();
}

/* FETCH INQUIRIES */
if ($_SESSION['role'] === 'company') {
    // Company users see only inquiries for their own businesses
    $user_id = $_SESSION['user_id'];
    
    // Get list of business names owned by this user
    $businesses = mysqli_query($conn, "SELECT name FROM businesses WHERE user_id='$user_id'");
    $business_names = [];
    while ($row = mysqli_fetch_assoc($businesses)) {
        $business_names[] = "'" . mysqli_real_escape_string($conn, $row['name']) . "'";
    }
    
    if (count($business_names) > 0) {
        $names_list = implode(',', $business_names);
        $result = mysqli_query($conn,
            "SELECT * FROM inquiries 
             WHERE business_name IN ($names_list)
             ORDER BY id DESC"
        );
    } else {
        // No businesses, empty result
        $result = mysqli_query($conn, "SELECT * FROM inquiries WHERE 1=0");
    }
} else {
    // Admin sees all inquiries
    $result = mysqli_query($conn,
        "SELECT * FROM inquiries ORDER BY id DESC"
    );
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Inquiries - Business Listing Portal</title>
    <meta name="description" content="View and manage all customer inquiries">
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
        📨 Customer Inquiries
    </div>
</div>

<!-- CONTENT -->
<div class="content">

    <!-- ADVERTISEMENTS -->
    <?php include "display_ads.php"; ?>

    <div class="inquiries-table-container">
        <div class="inquiries-table-header">
            <h2><?php echo ($_SESSION['role'] === 'company') ? 'My Business Inquiries' : 'All Inquiries'; ?></h2>
            <span class="inquiries-count"><?php echo mysqli_num_rows($result); ?> Total</span>
        </div>

        <?php if (mysqli_num_rows($result) > 0) { ?>
            <table class="modern-inquiries-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>🏢 Business</th>
                        <th>👤 Customer Name</th>
                        <th>📧 Email</th>
                        <th>📱 Phone</th>
                        <th>💬 Message</th>
                        <th>📅 Date</th>
                        <th>⚙️ Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td class="inquiry-id"><?php echo $i++; ?></td>
                            <td class="business-name"><?php echo htmlspecialchars($row['business_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td class="email-cell"><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone'] ?: '-'); ?></td>
                            <td class="message-cell" title="<?php echo htmlspecialchars($row['message']); ?>">
                                <?php echo htmlspecialchars($row['message']); ?>
                            </td>
                            <td>
                                <span class="date-badge">
                                    <?php
                                    echo isset($row['created_at'])
                                        ? date("d M Y", strtotime($row['created_at']))
                                        : '-';
                                    ?>
                                </span>
                            </td>
                            <td>
                                <a href="view_inquiries.php?delete=<?php echo $row['id']; ?>"
                                   class="delete-inquiry-btn"
                                   onclick="return confirm('Are you sure you want to delete this inquiry?')">
                                    🗑️ Delete
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <div class="empty-state">
                <div class="empty-state-icon">📨</div>
                <p>No inquiries found.</p>
            </div>
        <?php } ?>
    </div>

</div>

</body>
</html>
