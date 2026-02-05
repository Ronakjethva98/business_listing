<?php
include "auth.php";
include "db.php";

/* ONLY ADMIN */
if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

/* DELETE ADMIN */
if (isset($_GET['delete'])) {
    $uid = intval($_GET['delete']);
    
    // Prevent deleting yourself
    if ($uid != $_SESSION['user_id']) {
        mysqli_query($conn,
            "DELETE FROM users 
             WHERE id='$uid' AND role='admin'"
        );
    }
    
    header("Location: view_admin.php");
    exit();
}

/* FETCH ONLY ADMIN USERS */
$result = mysqli_query($conn,
    "SELECT id, username, role 
     FROM users 
     WHERE role='admin'"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Admin - Business Listing Portal</title>
    <meta name="description" content="Manage administrator accounts">
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
        View Admin - Manage Administrators
    </div>
</div>

<!-- CONTENT -->
<div class="content">

    <!-- ADVERTISEMENTS -->
    <?php include "display_ads.php"; ?>

    <div class="admins-table-container">
        <div class="admins-table-header">
            <h2>Administrator Accounts</h2>
            <span class="admins-count"><?php echo mysqli_num_rows($result); ?> Admins</span>
        </div>

        <?php if (mysqli_num_rows($result) > 0) { ?>
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($u = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td class="username-cell" data-label="Username">
                                <?php echo htmlspecialchars($u['username']); ?>
                                <?php if ($u['id'] == $_SESSION['user_id']) { ?>
                                    <span class="current-user-badge">You</span>
                                <?php } ?>
                            </td>
                            <td data-label="Role">
                                <span class="role-badge"><?php echo ucfirst($u['role']); ?></span>
                            </td>
                            <td data-label="Actions">
                                <a href="edit_admin.php?id=<?php echo $u['id']; ?>" class="action-edit">
                                    Edit
                                </a>
                                
                                <?php if ($u['id'] != $_SESSION['user_id']) { ?>
                                    <a href="view_admin.php?delete=<?php echo $u['id']; ?>" 
                                       class="action-delete"
                                       onclick="return confirm('Are you sure you want to delete this administrator?')">
                                        Delete
                                    </a>
                                <?php } else { ?>
                                    <a class="action-delete disabled" title="You cannot delete yourself">
                                        Delete
                                    </a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <div class="empty-state">
                <div class="empty-state-icon"></div>
                <p>No administrators found.</p>
            </div>
        <?php } ?>
    </div>

</div>

<?php include "footer.php"; ?>

</body>
</html>
