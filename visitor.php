<?php
session_start();
include "db.php";

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userRole = $_SESSION['role'] ?? 'normal';

$search   = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

/* FETCH DISTINCT CATEGORIES */
$catResult = mysqli_query($conn, "SELECT DISTINCT category FROM businesses");

/* BUILD QUERY */
$sql = "SELECT * FROM businesses WHERE 1";

if ($search != '') {
    $sql .= " AND name LIKE '%$search%'";
}

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
    <title>Browse Businesses - Business Listing Portal</title>
    <meta name="description" content="Explore and discover businesses, search by category and send inquiries">
    <link rel="stylesheet" href="style.css">
    <style>
        .inquiry-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .inquiry-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(16, 185, 129, 0.4);
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>Business Portal</h2>
    
    <?php if (!$isLoggedIn) { ?>
        <!-- Normal User / Not Logged In -->
        <a href="visitor.php">🏠 Home</a>
        <a href="login.php?role=company">🏢 Company Login</a>
        <a href="login.php?role=admin">👑 Admin Login</a>
         <a href="about.php">ℹ️ About</a>
    <?php } elseif ($userRole === 'company') { ?>
        <!-- Company User -->
        <a href="visitor.php">🏠 Home</a>
        <a href="dashboard.php">📊 My Businesses</a>
        <a href="add_business.php">➕ Add Business</a>
        <a href="view_inquiries.php">📨 View Inquiries</a>
        <a href="about.php">ℹ️ About</a>
        <a href="logout.php">🚪 Logout</a>
    <?php } elseif ($userRole === 'admin') { ?>
        <!-- Admin User -->
        <a href="visitor.php">🏠 Home</a>
        <a href="manage_users.php">👥 Manage Users</a>
        <a href="view_admin.php">👤 View Admin</a>
        <a href="add_admin.php">➕ Add Admin</a>
        <a href="about.php">ℹ️ About</a>
        <a href="logout.php">🚪 Logout</a>
    <?php } ?>
</div>

<!-- TOPBAR -->
<div class="topbar">
    🏪 Browse Businesses
</div>

<!-- CONTENT -->
<div class="content">

    <!-- SEARCH + CATEGORY FILTER -->
    <form method="GET" class="search-form">

        <input
            type="text"
            name="search"
            placeholder="Search by business name"
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

        <a href="visitor.php">
            <button type="button">🔄 Reset</button>
        </a>

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

                    <!-- CARD CONTENT -->
                    <div class="card-content">

                        <!-- BUSINESS INFO -->
                        <div>
                            <h3><?php echo $b['name']; ?></h3>
                            <p><b>Category:</b> <?php echo $b['category']; ?></p>
                            <p><b>Address:</b> <?php echo $b['address']; ?></p>
                            <p><b>Phone:</b> <?php echo $b['phone']; ?></p>
                            <p><?php echo $b['description']; ?></p>
                        </div>

                        <!-- SEND INQUIRY BUTTON (ALIGNED BOTTOM) -->
                        <div style="margin-top:auto; padding-top: 12px;">
                            <a href="inquiry.php?business=<?php echo urlencode($b['name']); ?>" class="inquiry-btn">
                                ✉️ Send Inquiry
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
