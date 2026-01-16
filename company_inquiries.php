<?php
include "auth.php";
include "db.php";

/* ONLY COMPANY USER */
if ($_SESSION['role'] !== 'company') {
    header("Location: dashboard.php");
    exit();
}

$uid = $_SESSION['user_id'];

/* DELETE INQUIRY - Only if it belongs to company's business */
if (isset($_GET['delete'])) {
    $inquiry_id = intval($_GET['delete']);
    
    // Get company's business names first
    $bizCheck = mysqli_query($conn,
        "SELECT name FROM businesses WHERE user_id='$uid'"
    );
    
    $bizNames = [];
    while ($b = mysqli_fetch_assoc($bizCheck)) {
        $bizNames[] = "'" . mysqli_real_escape_string($conn, $b['name']) . "'";
    }
    
    if (count($bizNames) > 0) {
        $bizList = implode(",", $bizNames);
        
        // Delete only if inquiry belongs to company's business
        mysqli_query($conn,
            "DELETE FROM inquiries 
             WHERE id='$inquiry_id' 
             AND business_name IN ($bizList)"
        );
    }
    
    header("Location: company_inquiries.php");
    exit();
}

/* GET COMPANY BUSINESS NAMES */
$bizResult = mysqli_query($conn,
    "SELECT name FROM businesses WHERE user_id='$uid'"
);

$businesses = [];
while ($b = mysqli_fetch_assoc($bizResult)) {
    $businesses[] = "'" . mysqli_real_escape_string($conn, $b['name']) . "'";
}

if (count($businesses) > 0) {
    $bizList = implode(",", $businesses);

    $result = mysqli_query($conn,
        "SELECT * FROM inquiries
         WHERE business_name IN ($bizList)
         ORDER BY id DESC"
    );
} else {
    $result = false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Business Inquiries - Business Listing Portal</title>
    <meta name="description" content="View and manage inquiries for your businesses">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>Company</h2>
    <a href="dashboard.php">🏠 Home</a>
    <a href="add_business.php">➕ Add Business</a>
    <a href="company_inquiries.php">📨 View Inquiries</a>
    <a href="about.php">ℹ️ About</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<!-- TOPBAR -->
<div class="topbar">
    📨 My Business Inquiries
</div>

<!-- CONTENT -->
<div class="content">

    <div class="inquiries-table-container">
        <div class="inquiries-table-header">
            <h2>All Inquiries</h2>
            <span class="inquiries-count"><?php echo $result ? mysqli_num_rows($result) : 0; ?> Total</span>
        </div>

        <?php if ($result && mysqli_num_rows($result) > 0) { ?>
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
                                <a href="company_inquiries.php?delete=<?php echo $row['id']; ?>"
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
                <p>No inquiries found for your businesses.</p>
            </div>
        <?php } ?>
    </div>

</div>

</body>
</html>
