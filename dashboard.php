<?php
include "auth.php";
include "db.php";

/* BLOCK NORMAL USER */
if ($_SESSION['role'] === 'normal') {
    header("Location: index.php");
    exit();
}

$search   = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

/* FETCH DISTINCT CATEGORIES */
$catResult = mysqli_query($conn, "SELECT DISTINCT category FROM businesses");

/* BASE QUERY BY ROLE */
if ($_SESSION['role'] === 'admin') {
    $sql = "SELECT * FROM businesses WHERE 1";
} else {
    $uid = $_SESSION['user_id'];
    $sql = "SELECT * FROM businesses WHERE user_id='$uid'";
}

/* APPLY SEARCH BY NAME */
if ($search != '') {
    $sql .= " AND name LIKE '%$search%'";
}

/* APPLY CATEGORY FILTER */
if ($category != '') {
    $sql .= " AND category = '$category'";
}

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Business Listing Portal</title>
    <meta name="description" content="Manage your business listings">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="navbar-container">
        <div class="navbar-header">
            <div class="navbar-brand">Business Portal</div>
            <div class="navbar-menu">
                <div class="navbar-user">👤 <?php echo ucfirst($_SESSION['role']); ?></div>
                <a href="dashboard.php">🏠 Home</a>
                
                <?php if ($_SESSION['role'] === 'company') { ?>
                    <a href="add_business.php">➕ Add Business</a>
                    <a href="view_inquiries.php">📨 View Inquiries</a>
                    <a href="about.php">ℹ️ About</a>
                <?php } elseif ($_SESSION['role'] === 'admin') { ?>
                    <a href="manage_users.php">👥 Manage Users</a>
                    <a href="view_admin.php">👤 View Admin</a>
                    <a href="add_admin.php">➕ Add Admin</a>
                    <a href="about.php">ℹ️ About</a>
                <?php } ?>
                
                <a href="logout.php" class="logout-btn">🚪 Logout</a>
            </div>
        </div>
    </div>
</nav>

<!-- TOPBAR -->
<div class="topbar">
    <div class="topbar-container">
        Welcome, <?php echo ucfirst($_SESSION['role']); ?> 👋
    </div>
</div>

<!-- CONTENT -->
<div class="content">
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <div style="background: #d1fae5; padding: 15px; border-radius: 10px; margin-bottom: 20px; color: #059669; font-weight: 600; text-align: center; border: 2px solid #10b981;">
            ✓ Business deleted successfully!
        </div>
    <?php endif; ?>
    
    <!-- SEARCH + CATEGORY FILTER -->
    <form method="GET" class="search-form">
        <input
            type="text"
            name="search"
            placeholder="Search business name"
            value="<?php echo htmlspecialchars($search); ?>"
        >

        <select name="category">
            <option value="">All Categories</option>
            <?php while ($c = mysqli_fetch_assoc($catResult)) { ?>
                <option value="<?php echo $c['category']; ?>"
                    <?php if ($category == $c['category']) echo 'selected'; ?>>
                    <?php echo $c['category']; ?>
                </option>
            <?php } ?>
        </select>

        <button type="submit">🔍 Search</button>
        
        <a href="dashboard.php">
        <button type="button">🔄 Reset</button></a>
    </form>

    <!-- BUSINESS LIST -->
    <div class="card-grid">
        <?php if (mysqli_num_rows($result) > 0) { ?>
            <?php while ($b = mysqli_fetch_assoc($result)) { ?>
                <div class="card">
                    <?php 
                        // Handle both 'filename.jpg' and 'uploads/filename.jpg' formats
                        $image_src = (strpos($b['image'], 'uploads/') === 0) ? $b['image'] : 'uploads/' . $b['image'];
                    ?>
                    <img src="<?php echo $image_src; ?>" alt="<?php echo htmlspecialchars($b['name']); ?>">

                    <div class="card-content">
                        <h3><?php echo $b['name']; ?></h3>
                        <p><b>Category:</b> <?php echo $b['category']; ?></p>
                        <p><?php echo $b['address']; ?></p>
                        <p><?php echo $b['phone']; ?></p>

                        <div class="action-buttons">
                            <a href="edit_business.php?id=<?php echo $b['id']; ?>" class="action-edit">
                                ✏️ Edit
                            </a>

                            <a href="delete_business.php?id=<?php echo $b['id']; ?>"
                               class="action-delete"
                               onclick="return confirm('Are you sure you want to delete this business?')">
                                🗑️ Delete
                            </a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="box">No businesses found.</div>
        <?php } ?>
    </div>

</div>

</body>
</html>
