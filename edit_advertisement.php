<?php
include "auth.php";
include "db.php";

/* GET AD ID */
$ad_id = $_GET['id'] ?? 0;

/* FETCH ADVERTISEMENT */
$sql = "SELECT a.*, u.username as company_name 
        FROM advertisements a 
        JOIN users u ON a.company_id = u.id 
        WHERE a.id = '$ad_id'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    header("Location: dashboard.php");
    exit();
}

$ad = mysqli_fetch_assoc($result);

/* CHECK PERMISSIONS */
if ($_SESSION['role'] === 'company' && $ad['company_id'] != $_SESSION['user_id']) {
    // Company users can only edit their own ads
    header("Location: my_advertisements.php");
    exit();
}
// Admins can edit any ad

$error = "";
$success = "";

/* HANDLE UPDATE */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $link_url = mysqli_real_escape_string($conn, $_POST['link_url']);
    
    // Handle image upload if new image provided
    $image_path = $ad['image_path']; // Keep existing image by default
    $end_date = $ad['end_date'];
    $is_paid = $ad['is_paid'];
    $status = $ad['status'];
    $total_cost = $ad['total_cost'];
    $days_duration = $ad['days_duration'];
    $admin_notes = $ad['admin_notes'];
    
    // Handle Renewal
    if (isset($_POST['renew_ad']) && $_POST['renew_ad'] == '1') {
        $new_end_date = mysqli_real_escape_string($conn, $_POST['new_end_date']);
        $extra_days = intval($_POST['extra_days']);
        $renewal_cost = $extra_days * 100; // Rate is 100 per day
        
        $end_date = $new_end_date;
        $days_duration += $extra_days;
        $total_cost += $renewal_cost;
        $is_paid = 0; // Reset paid status for the renewal
        $status = 'pending'; // Ad needs re-approval after renewal

        // Update admin notes with renewal information
        $admin_notes = "🔄 RENEWAL REQUEST: Extra ₹$renewal_cost paid for $extra_days days (New End: " . date('d-m-Y', strtotime($new_end_date)) . ")";
        $admin_notes = mysqli_real_escape_string($conn, $admin_notes);
    }
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = 'ad_' . time() . '_' . uniqid() . '.' . $ext;
            $upload_path = 'uploads/advertisements/' . $new_filename;
            
            // Create directory if it doesn't exist
            if (!file_exists('uploads/advertisements')) {
                mkdir('uploads/advertisements', 0777, true);
            }
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                // Delete old image if it exists
                if (file_exists($ad['image_path'])) {
                    unlink($ad['image_path']);
                }
                $image_path = $upload_path;
            } else {
                $error = "Failed to upload new image!";
            }
        } else {
            $error = "Invalid image format! Allowed: JPG, JPEG, PNG, GIF, WEBP";
        }
    }
    
    if ($error == "") {
        // Update advertisement
        $update_sql = "UPDATE advertisements SET 
                       title = '$title',
                       description = '$description',
                       link_url = '$link_url',
                       image_path = '$image_path',
                       end_date = '$end_date',
                       days_duration = '$days_duration',
                       total_cost = '$total_cost',
                       is_paid = '$is_paid',
                       status = '$status',
                       admin_notes = '$admin_notes',
                       updated_at = NOW()
                       WHERE id = '$ad_id'";
        
        if (mysqli_query($conn, $update_sql)) {
            $success = "Advertisement updated successfully!";
            // Refresh ad data
            $result = mysqli_query($conn, $sql);
            $ad = mysqli_fetch_assoc($result);
        } else {
            $error = "Failed to update advertisement!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Advertisement - Business Listing Portal</title>
    <meta name="description" content="Edit your advertisement">
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
        📝 Edit Advertisement
    </div>
</div>

<!-- CONTENT -->
<div class="content content-center">

    <form method="POST" enctype="multipart/form-data" class="form" style="max-width: 600px;">
        <h2>✏️ Edit Advertisement</h2>

        <?php if ($success != "") { ?>
            <p class="success-message"><?php echo $success; ?></p>
        <?php } ?>

        <?php if ($error != "") { ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php } ?>

        <div style="margin-bottom: 20px; text-align: center;">
            <img src="<?php echo htmlspecialchars($ad['image_path']); ?>" 
                 alt="Current Image" 
                 style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        </div>

        <label>📝 Advertisement Title *</label>
        <input
            type="text"
            name="title"
            value="<?php echo htmlspecialchars($ad['title']); ?>"
            required
            placeholder="Enter advertisement title"
        >

        <label>ℹ️ Description</label>
        <textarea
            name="description"
            rows="4"
            placeholder="Enter advertisement description (optional)"
        ><?php echo htmlspecialchars($ad['description']); ?></textarea>

        <label>🔗 Link URL</label>
        <input
            type="url"
            name="link_url"
            value="<?php echo htmlspecialchars($ad['link_url']); ?>"
            placeholder="https://example.com (optional)"
        >

        <label>🖼️ Change Image (optional)</label>
        <input
            type="file"
            name="image"
            accept="image/*"
        >
        <small style="color: #666; display: block; margin-top: -12px; margin-bottom: 24px;">
            Leave empty to keep current image. Allowed: JPG, PNG, GIF, WEBP
        </small>

        <!-- RENEWAL SECTION -->
        <div style="background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 24px;">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-weight: 600; color: #1e293b;">
                <input type="checkbox" name="renew_ad" id="renew_ad" value="1" style="width: 20px; height: 20px;">
                🔄 Renew / Extend Advertisement
            </label>
            
            <div id="renewalFields" style="display: none; margin-top: 15px;">
                <label>📅 Current End Date</label>
                <input type="text" value="<?php echo date('d-m-Y', strtotime($ad['end_date'])); ?>" disabled style="background: #eee;">
                
                <label>🆕 New End Date *</label>
                <input type="date" name="new_end_date" id="new_end_date" min="<?php echo date('Y-m-d', strtotime($ad['end_date'] . ' +1 day')); ?>">
                
                <!-- Cost Calculation Box (Hidden until date selected) -->
                <div id="costBox" style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); padding: 20px; border-radius: 12px; margin-top: 20px; border: 2px solid #0ea5e9; display: none;">
                    <h3 style="margin: 0 0 12px 0; color: #0369a1; font-size: 18px;">💰 Renewal Payment</h3>
                    <div style="background: white; padding: 16px; border-radius: 8px; margin-bottom: 12px;">
                        <p style="margin: 0 0 8px 0; color: #666; font-size: 14px;">Extension: <strong id="extraDaysText" style="color: #1e293b;">-</strong> extra days</p>
                        <p style="margin: 0; font-size: 20px; font-weight: 700; color: #059669;">Renewal Cost: ₹<span id="renewalCostText">0</span></p>
                        <input type="hidden" name="extra_days" id="extra_days" value="0">
                    </div>
                    
                    <div style="background: #fef3c7; padding: 14px; border-radius: 8px; border-left: 4px solid #f59e0b;">
                        <p style="margin: 0 0 10px 0; font-weight: 600; color: #92400e; font-size: 15px;">Scan to Pay & Extend:</p>
                        <div style="text-align: center; background: white; padding: 10px; border-radius: 8px;">
                            <img src="assets/payment_qr.png" alt="Payment Scanner" style="max-width: 150px; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        </div>
                        <p style="margin: 10px 0 0 0; color: #78350f; font-size: 12px; text-align: center;">Phone: +91 9327060890</p>
                    </div>
                </div>
            </div>
        </div>

        <script>
            const renewCheckbox = document.getElementById('renew_ad');
            const renewalFields = document.getElementById('renewalFields');
            const newEndDateInput = document.getElementById('new_end_date');
            const costBox = document.getElementById('costBox');
            const extraDaysText = document.getElementById('extra_days_text'); // Wait, ID mismatch below
            const extraDaysInput = document.getElementById('extra_days');
            const renewalCostText = document.getElementById('renewalCostText');
            
            // Fix references
            const extraDaysSpan = document.getElementById('extraDaysText');
            
            const currentEndDate = new Date("<?php echo $ad['end_date']; ?>");
            const ratePerDay = 100;

            renewCheckbox.addEventListener('change', function() {
                renewalFields.style.display = this.checked ? 'block' : 'none';
                if (!this.checked) {
                    costBox.style.display = 'none';
                    newEndDateInput.required = false;
                } else {
                    newEndDateInput.required = true;
                }
            });

            newEndDateInput.addEventListener('change', function() {
                const newDate = new Date(this.value);
                if (newDate > currentEndDate) {
                    const diffTime = Math.abs(newDate - currentEndDate);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    
                    extraDaysSpan.textContent = diffDays;
                    renewalCostText.textContent = (diffDays * ratePerDay).toLocaleString('en-IN');
                    extraDaysInput.value = diffDays;
                    costBox.style.display = 'block';
                } else {
                    costBox.style.display = 'none';
                }
            });
        </script>

        <button type="submit">💾 Update Advertisement</button>

        <div style="margin-top: 16px;">
            <a href="<?php echo $_SESSION['role'] === 'admin' ? 'manage_advertisements.php' : 'my_advertisements.php'; ?>" 
               style="text-decoration: none;">
                <button type="button" class="btn-back">← Back</button>
            </a>
        </div>
    </form>

</div>

<?php include "footer.php"; ?>

</body>
</html>
