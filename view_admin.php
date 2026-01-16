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
    <link rel="stylesheet" href="style.css">
    <style>
        /* Modern Table Styles */
        .admins-table-container {
            background: #fff;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            overflow-x: auto;
            margin-bottom: 30px;
        }

        .admins-table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 24px;
            border-bottom: 2px solid #e2e8f0;
        }

        .admins-table-header h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 26px;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .admins-count {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            padding: 8px 18px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .modern-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 15px;
        }

        .modern-table thead tr {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }

        .modern-table th {
            padding: 16px;
            text-align: left;
            font-weight: 600;
            color: #334155;
            font-family: 'Poppins', sans-serif;
            border-bottom: 2px solid #cbd5e1;
        }

        .modern-table th:first-child {
            border-top-left-radius: 10px;
            padding-left: 20px;
        }

        .modern-table th:last-child {
            border-top-right-radius: 10px;
            padding-right: 20px;
        }

        .modern-table tbody tr {
            transition: all 0.3s ease;
            border-bottom: 1px solid #e2e8f0;
        }

        .modern-table tbody tr:hover {
            background: #f8fafc;
        }

        .modern-table td {
            padding: 16px;
            color: #475569;
            vertical-align: middle;
        }

        .modern-table td:first-child {
            padding-left: 20px;
        }

        .modern-table td:last-child {
            padding-right: 20px;
        }

        .username-cell {
            font-weight: 600;
            color: #1e293b;
        }

        .role-badge {
            display: inline-block;
            padding: 6px 14px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .current-user-badge {
            display: inline-block;
            padding: 4px 10px;
            background: #10b981;
            color: white;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            margin-left: 8px;
        }

        .action-edit {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 16px;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: #fff;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            white-space: nowrap;
            margin-right: 8px;
        }

        .action-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(59, 130, 246, 0.4);
        }

        .action-delete {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 16px;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: #fff;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
            white-space: nowrap;
        }

        .action-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(239, 68, 68, 0.4);
        }

        .action-delete.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #64748b;
            font-size: 16px;
        }

        .empty-state-icon {
            font-size: 56px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>Admin</h2>
    <a href="visitor.php">🏠 Home</a>
    <a href="manage_users.php">👥 Manage Users</a>
    <a href="view_admin.php">👤 View Admin</a>
    <a href="add_admin.php">➕ Add Admin</a>
    <a href="about.php">ℹ️ About</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<!-- TOPBAR -->
<div class="topbar">
    👤 View Admin - Manage Administrators
</div>

<!-- CONTENT -->
<div class="content">

    <div class="admins-table-container">
        <div class="admins-table-header">
            <h2>Administrator Accounts</h2>
            <span class="admins-count"><?php echo mysqli_num_rows($result); ?> Admins</span>
        </div>

        <?php if (mysqli_num_rows($result) > 0) { ?>
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>👤 Username</th>
                        <th>🏷️ Role</th>
                        <th>⚙️ Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($u = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td class="username-cell">
                                <?php echo htmlspecialchars($u['username']); ?>
                                <?php if ($u['id'] == $_SESSION['user_id']) { ?>
                                    <span class="current-user-badge">You</span>
                                <?php } ?>
                            </td>
                            <td>
                                <span class="role-badge"><?php echo ucfirst($u['role']); ?></span>
                            </td>
                            <td>
                                <a href="edit_admin.php?id=<?php echo $u['id']; ?>" class="action-edit">
                                    ✏️ Edit
                                </a>
                                
                                <?php if ($u['id'] != $_SESSION['user_id']) { ?>
                                    <a href="view_admin.php?delete=<?php echo $u['id']; ?>"
                                       class="action-delete"
                                       onclick="return confirm('Are you sure you want to delete this administrator?')">
                                        🗑️ Delete
                                    </a>
                                <?php } else { ?>
                                    <a class="action-delete disabled" title="You cannot delete yourself">
                                        🗑️ Delete
                                    </a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <div class="empty-state">
                <div class="empty-state-icon">👥</div>
                <p>No administrators found.</p>
            </div>
        <?php } ?>
    </div>

</div>

</body>
</html>
