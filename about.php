<?php
session_start();
include "db.php";

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userRole = $_SESSION['role'] ?? 'normal';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - Business Listing Portal</title>
    <meta name="description" content="Learn how to use the Business Listing Portal">
    <link rel="stylesheet" href="style.css">
    <style>
        .about-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .about-section {
            background: #fff;
            border-radius: 16px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
        }

        .about-section h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 28px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .about-section h3 {
            font-family: 'Poppins', sans-serif;
            font-size: 20px;
            font-weight: 600;
            color: #334155;
            margin-top: 30px;
            margin-bottom: 15px;
        }

        .about-section p {
            color: #64748b;
            line-height: 1.8;
            margin-bottom: 15px;
            font-size: 15px;
        }

        .user-type-card {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }

        .user-type-card h4 {
            font-family: 'Poppins', sans-serif;
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-type-card ul {
            margin: 10px 0;
            padding-left: 20px;
        }

        .user-type-card li {
            color: #475569;
            margin-bottom: 8px;
            line-height: 1.6;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .feature-item {
            background: #fff;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .feature-item:hover {
            border-color: #667eea;
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.2);
        }

        .feature-icon {
            font-size: 36px;
            margin-bottom: 12px;
        }

        .feature-item h5 {
            font-family: 'Poppins', sans-serif;
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .feature-item p {
            font-size: 14px;
            color: #64748b;
            margin: 0;
        }

        .steps-list {
            counter-reset: step-counter;
            list-style: none;
            padding: 0;
        }

        .steps-list li {
            counter-increment: step-counter;
            position: relative;
            padding-left: 50px;
            margin-bottom: 20px;
            color: #475569;
            line-height: 1.6;
        }

        .steps-list li::before {
            content: counter(step-counter);
            position: absolute;
            left: 0;
            top: 0;
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="navbar-container">
        <div class="navbar-header">
            <div class="navbar-brand">Business Portal</div>
            <?php if ($isLoggedIn) { ?>
                <div class="navbar-user">👤 <?php echo ucfirst($userRole); ?></div>
            <?php } ?>
        </div>
        <div class="navbar-menu">
            <?php if (!$isLoggedIn) { ?>
                <a href="visitor.php">🏠 Home</a>
                <a href="login.php?role=company">🏢 Company Login</a>
                <a href="register.php">📝 Register</a>
                <a href="login.php?role=admin">👑 Admin Login</a>
                <a href="about.php">ℹ️ About</a>
            <?php } elseif ($userRole === 'company') { ?>
                <a href="dashboard.php">🏠 Home</a>
                <a href="add_business.php">➕ Add Business</a>
                <a href="view_inquiries.php">📨 View Inquiries</a>
                <a href="about.php">ℹ️ About</a>
                <a href="logout.php" class="logout-btn">🚪 Logout</a>
            <?php } elseif ($userRole === 'admin') { ?>
                <a href="dashboard.php">🏠 Home</a>
                <a href="manage_users.php">👥 Manage Users</a>
                <a href="view_admin.php">👤 View Admin</a>
                <a href="add_admin.php">➕ Add Admin</a>
                <a href="about.php">ℹ️ About</a>
                <a href="logout.php" class="logout-btn">🚪 Logout</a>
            <?php } ?>
        </div>
    </div>
</nav>

<!-- TOPBAR -->
<div class="topbar">
    <div class="topbar-container">
        ℹ️ About Us - How to Use
    </div>
</div>

<!-- CONTENT -->
<div class="content">
    <div class="about-container">
        
        <!-- Welcome Section -->
        <div class="about-section">
            <h2>Welcome to Business Listing Portal</h2>
            <p>
                Our Business Listing Portal is a comprehensive platform designed to connect businesses with potential customers. 
                Whether you're looking for services, managing your business presence, or administering the platform, 
                we've made it simple and intuitive for everyone.
            </p>
        </div>

        <!-- Key Features -->
        <div class="about-section">
            <h2>🌟 Key Features</h2>
            <div class="feature-grid">
                <div class="feature-item">
                    <div class="feature-icon">🔍</div>
                    <h5>Search & Discover</h5>
                    <p>Find businesses by name or category easily</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">✉️</div>
                    <h5>Send Inquiries</h5>
                    <p>Contact businesses directly through the platform</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">📊</div>
                    <h5>Manage Listings</h5>
                    <p>Companies can add and update their business info</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">👥</div>
                    <h5>User Management</h5>
                    <p>Admins can manage all users and content</p>
                </div>
            </div>
        </div>

        <!-- User Types -->
        <div class="about-section">
            <h2>👤 User Types & Capabilities</h2>

            <div class="user-type-card">
                <h4>🏠 Normal Visitors</h4>
                <p><strong>What you can do:</strong></p>
                <ul>
                    <li>Browse all business listings</li>
                    <li>Search businesses by name or filter by category</li>
                    <li>View detailed business information (address, phone, description)</li>
                    <li>Send inquiries to businesses you're interested in</li>
                    <li>No registration required for basic browsing</li>
                </ul>
            </div>

            <div class="user-type-card">
                <h4>🏢 Company Users</h4>
                <p><strong>What you can do:</strong></p>
                <ul>
                    <li>Create an account by clicking "Company Login" and then registering</li>
                    <li>Add your business listings with images and details</li>
                    <li>Edit and update your business information anytime</li>
                    <li>Delete your business listings if needed</li>
                    <li>Manage multiple businesses under one account</li>
                    <li><strong>View all customer inquiries</strong> sent to your businesses</li>
                </ul>
            </div>

            <div class="user-type-card">
                <h4>👑 Admin Users</h4>
                <p><strong>What you can do:</strong></p>
                <ul>
                    <li>View and manage all business listings</li>
                    <li>Manage company user accounts (view and delete)</li>
                    <li><strong>View all customer inquiries</strong> from all businesses</li>
                    <li>Delete inquiries, inappropriate content, or spam users</li>
                    <li>Full control over the platform</li>
                </ul>
            </div>
        </div>

        <!-- How to Use Section -->
        <div class="about-section">
            <h2>📖 How to Use the Platform</h2>

            <h3>For Visitors (Finding Businesses)</h3>
            <ol class="steps-list">
                <li>Go to the <strong>Home</strong> page to see all businesses</li>
                <li>Use the <strong>Search Bar</strong> to find businesses by name</li>
                <li>Use the <strong>Category Filter</strong> to browse specific types of businesses</li>
                <li>Click <strong>"Send Inquiry"</strong> on any business card to contact them</li>
                <li>Fill out the inquiry form with your name, email, phone (optional), and message</li>
            </ol>

            <h3>For Companies (Listing Your Business)</h3>
            <ol class="steps-list">
                <li>Click <strong>"Company Login"</strong> in the sidebar</li>
                <li>If you're new, click <strong>"Register here"</strong> to create an account</li>
                <li>Once logged in, click <strong>"Add Business"</strong> to create a new listing</li>
                <li>Fill in your business details: name, category, address, phone, description, and upload an image</li>
                <li>Click <strong>"Save Business"</strong> to publish your listing</li>
                <li>View all your businesses in <strong>"My Businesses"</strong></li>
                <li>Click <strong>"Edit"</strong> to update or <strong>"Delete"</strong> to remove a listing</li>
                <li>Use <strong>"View Inquiries"</strong> to see all customer inquiries for your businesses</li>
            </ol>

            <h3>For Administrators</h3>
            <ol class="steps-list">
                <li>Click <strong>"Admin Login"</strong> in the sidebar</li>
                <li>Enter your admin credentials</li>
                <li>Use <strong>"All Businesses"</strong> to view and manage all listings</li>
                <li>Use <strong>"Manage Users"</strong> to view and delete company accounts</li>
                <li>Use <strong>"View Inquiries"</strong> to see all customer inquiries from all businesses</li>
                <li>Delete any inappropriate content or spam users</li>
            </ol>
        </div>

        <!-- Tips Section -->
        <div class="about-section">
            <h2>💡 Tips for Best Experience</h2>
            <ul>
                <li><strong>Companies:</strong> Add high-quality images and detailed descriptions to attract more customers</li>
                <li><strong>Companies:</strong> Update your contact information regularly</li>
                <li><strong>Visitors:</strong> Be specific in your inquiry messages to get better responses</li>
                <li><strong>All Users:</strong> Use the search and filter features to quickly find what you need</li>
                <li><strong>Companies:</strong> Respond promptly to customer inquiries for better engagement</li>
            </ul>
        </div>

        <!-- Contact Section -->
        <div class="about-section">
            <h2>📞 Need Help?</h2>
            <p>
                If you encounter any issues or have questions about using the platform, 
                please contact our support team or refer to this guide anytime by clicking 
                the <strong>"About"</strong> link in the sidebar.
            </p>
        </div>

    </div>
</div>

</body>
</html>
