<?php
include "auth.php";
include "db.php";

/* BLOCK NON-COMPANY USERS */
if ($_SESSION['role'] !== 'company') {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Advertisement - Business Listing Portal</title>
    <meta name="description" content="Submit your advertisement for review">
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
        📢 Submit New Advertisement
    </div>
</div>

<!-- CONTENT -->
<div class="content content-center">

    <form method="POST" action="save_advertisement.php" enctype="multipart/form-data" class="form">
        
        <h2>Submit Advertisement</h2>
        
        <!-- SUCCESS/ERROR MESSAGES -->
        <?php if (isset($_GET['success'])): ?>
            <div style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; color: #059669; font-weight: 600; text-align: center; border: 2px solid #10b981; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);">
                ✓ Advertisement submitted successfully! It will be reviewed by an admin.
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div style="background: #fee2e2; padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; color: #dc2626; font-weight: 600; text-align: center; border: 2px solid #ef4444;">
                <?php if ($_GET['error'] == 'invalid_file'): ?>
                    ✗ Invalid file type. Please upload JPEG or PNG images only.
                <?php elseif ($_GET['error'] == 'file_too_large'): ?>
                    ✗ File size too large. Maximum 2MB allowed.
                <?php elseif ($_GET['error'] == 'upload_failed'): ?>
                    ✗ File upload failed. Please try again.
                <?php else: ?>
                    ✗ An error occurred. Please try again.
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <p style="color: #666; margin-bottom: 24px; text-align: center;">
            Submit your advertisement for admin approval. Once approved, it will appear across the website.
        </p>
        
        <label>📝 Advertisement Title *</label>
        <input type="text" name="title" required maxlength="255" placeholder="Enter advertisement title">
        
        <label>ℹ️ Description (Optional)</label>
        <textarea name="description" rows="4" placeholder="Enter advertisement description"></textarea>
        
        <label>🖼️ Advertisement Image * (Max 2MB, JPEG/PNG)</label>
        <input type="file" name="ad_image" accept="image/jpeg,image/png,image/jpg" required>
        <small style="color: #666; display: block; margin-top: 8px; margin-bottom: 16px;">Recommended size: 800x400px for best display</small>
        
        <label>🔗 Link URL (Optional)</label>
        <input type="url" name="link_url" id="link_url" placeholder="https://example.com">
        <small style="color: #666; display: block; margin-top: 8px; margin-bottom: 16px;">Where should users go when they click your ad?</small>
        
        <label>📅 Advertisement Start Date *</label>
        <input type="date" name="start_date" id="start_date" required min="<?php echo date('Y-m-d'); ?>">
        
        <label>📆 Advertisement End Date *</label>
        <input type="date" name="end_date" id="end_date" required>
        <small style="color: #666; display: block; margin-top: 8px; margin-bottom: 16px;">Select when your advertisement should stop displaying</small>
        
        <!-- Cost Calculation Box -->
        <div id="costBox" style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); padding: 20px; border-radius: 12px; margin: 20px 0; border: 2px solid #0ea5e9; display: none;">
            <h3 style="margin: 0 0 12px 0; color: #0369a1; font-size: 18px;">💰 Payment Information</h3>
            <div style="background: white; padding: 16px; border-radius: 8px; margin-bottom: 12px;">
                <p style="margin: 0 0 8px 0; color: #666; font-size: 14px;">Duration: <strong id="durationDays" style="color: #1e293b;">-</strong> days</p>
                <p style="margin: 0 0 8px 0; color: #666; font-size: 14px;">Rate: <strong style="color: #1e293b;">₹100 per day</strong></p>
                <p style="margin: 0; font-size: 20px; font-weight: 700; color: #059669;">Total Cost: ₹<span id="totalCost">0</span></p>
            </div>
            <div style="background: #fef3c7; padding: 14px; border-radius: 8px; border-left: 4px solid #f59e0b;">
                <p style="margin: 0 0 10px 0; font-weight: 600; color: #92400e; font-size: 15px;">To activate your advertisement:</p>
                <div style="color: #78350f; font-size: 14px;">
                    <p style="margin: 0 0 6px 0;"><strong>📞 Phone:</strong> +91 9327060890</p>
                    <p style="margin: 0 0 10px 0;"><strong>📧 Email:</strong> jethvaronak98@gmail.com</p>
                    <div style="text-align: center; margin: 10px 0; background: white; padding: 10px; border-radius: 8px;">
                        <p style="margin: 0 0 5px 0; font-weight: 600; color: #1e293b; font-size: 12px;">Scan to Pay</p>
                        <img src="assets/payment_qr.png" alt="Payment Scanner" style="max-width: 150px; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    </div>
                </div>
                <p style="margin: 0; color: #78350f; font-size: 13px; font-style: italic; opacity: 0.9;">Payment via Mobile no, Email-id, UPI or Scanner</p>
            </div>
        </div>
        
        <script>
        // Calculate cost dynamically
        const startInput = document.getElementById('start_date');
        const endInput = document.getElementById('end_date');
        const costBox = document.getElementById('costBox');
        const durationSpan = document.getElementById('durationDays');
        const totalCostSpan = document.getElementById('totalCost');
        const costPerDay = 100;
        
        function calculateCost() {
            const startDate = new Date(startInput.value);
            const endDate = new Date(endInput.value);
            
            if (startInput.value && endInput.value && endDate >= startDate) {
                // Calculate days (inclusive of both start and end)
                const timeDiff = endDate.getTime() - startDate.getTime();
                const days = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1;
                const total = days * costPerDay;
                
                durationSpan.textContent = days;
                totalCostSpan.textContent = total.toLocaleString('en-IN');
                costBox.style.display = 'block';
                
                // Set minimum end date
                endInput.min = startInput.value;
            } else {
                costBox.style.display = 'none';
            }
        }
        
        startInput.addEventListener('change', calculateCost);
        endInput.addEventListener('change', calculateCost);
        </script>
        
        <button type="submit" class="btn-primary-ad" style="width: 100%;">
            📤 Submit Advertisement
        </button>
        
        <a href="my_advertisements.php">
            <button type="button" class="btn-delete-ad" style="margin-top: 12px; width: 100%; border-radius: 12px;">
                📢 View My Ads
            </button>
        </a>
    </form>

</div>

<?php include "footer.php"; ?>

</body>
</html>
