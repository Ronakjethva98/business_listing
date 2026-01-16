<?php
include "auth.php";
include "db.php";

/* ONLY ADMIN CAN ACCESS */
if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

/* DELETE USER */
if (isset($_GET['delete'])) {
    $uid = intval($_GET['delete']);

    // prevent deleting admin
    mysqli_query($conn,
        "DELETE FROM users 
         WHERE id='$uid' AND role='company'"
    );

    header("Location: manage_users.php");
    exit();
}

/* FETCH ONLY COMPANY USERS */
$result = mysqli_query($conn,
    "SELECT id, username, role 
     FROM users 
     WHERE role='company'"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Accounts - Business Listing Portal</title>
    <meta name="description" content="Manage company user accounts">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>Admin</h2>
    <a href="dashboard.php">🏠 Home</a>
    <a href="manage_users.php">👥 Manage Accounts</a>
    <a href="view_admin.php">👤 View Admin</a>
    <a href="add_admin.php">➕ Add Admin</a>
    <a href="about.php">ℹ️ About</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<!-- TOPBAR -->
<div class="topbar">
    👥 Manage Company Accounts
</div>

<!-- CONTENT -->
<div class="content">

    <div class="users-table-container">
        <div class="users-table-header">
            <h2>Company Users</h2>
            <span class="users-count"><?php echo mysqli_num_rows($result); ?> Users</span>
        </div>

        <?php if (mysqli_num_rows($result) > 0) { ?>
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>👤 Username</th>
                        <th>🏷️ Role</th>
                        <th>⚙️ Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($u = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td class="username-cell"><?php echo htmlspecialchars($u['username']); ?></td>
                            <td>
                                <span class="role-badge"><?php echo ucfirst($u['role']); ?></span>
                            </td>
                            <td>
                                <a href="manage_users.php?delete=<?php echo $u['id']; ?>"
                                   class="delete-btn"
                                   onclick="return confirm('Are you sure you want to delete this company user?')">
                                    🗑️ Delete
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <div class="empty-state">
                <div class="empty-state-icon">👥</div>
                <p>No company users found.</p>
            </div>
        <?php } ?>
    </div>

</div>

</body>
</html>
