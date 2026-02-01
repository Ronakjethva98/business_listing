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
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
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
            <div class="navbar-menu">
                <?php if (!$isLoggedIn) { ?>
                    <a href="index.php">Home</a>
                    <a href="login.php?role=company">Company Login</a>
                    <a href="login.php?role=admin">Admin Login</a>
                    <a href="about.php">About</a>
                <?php } elseif ($userRole === 'company') { ?>
                    <?php if ($isLoggedIn) { ?>
                        <div class="navbar-user"><?php echo ucfirst($userRole); ?></div>
                    <?php } ?>
                    <a href="dashboard.php">Home</a>
                    <a href="add_business.php">Add Business</a>
                    <a href="my_advertisements.php">My Ads</a>
                    <a href="submit_advertisement.php">Submit Ad</a>
                    <a href="view_inquiries.php">View Inquiries</a>
                    <a href="about.php">About</a>
                    <a href="logout.php" class="logout-btn">Logout</a>
                <?php } elseif ($userRole === 'admin') { ?>
                    <?php if ($isLoggedIn) { ?>
                        <div class="navbar-user"><?php echo ucfirst($userRole); ?></div>
                    <?php } ?>
                    <a href="dashboard.php">Home</a>
                    <a href="manage_users.php">Manage Users</a>
                    <a href="manage_advertisements.php">Manage Ads</a>
                    <a href="view_inquiries.php">View Inquiries</a>
                    <a href="view_admin.php">View Admin</a>
                    <a href="add_admin.php">Add Admin</a>
                    <a href="about.php">About</a>
                    <a href="logout.php" class="logout-btn">Logout</a>
                <?php } ?>
            </div>
        </div>
    </div>
</nav>

<!-- TOPBAR -->
<div class="topbar">
    <div class="topbar-container">
        About Us - How to Use
    </div>
</div>

<!-- CONTENT -->
<div class="content">
    
    <!-- ADVERTISEMENTS -->
    <?php include "display_ads.php"; ?>
    
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
                    <h5>Advanced Search & Filter</h5>
                    <p>Find businesses by name or filter by category with instant results</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">✉️</div>
                    <h5>Inquiry System</h5>
                    <p>Send inquiries to businesses; companies and admins can view all inquiries</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">🖼️</div>
                    <h5>Business Listings with Images</h5>
                    <p>Create, edit, delete listings with image upload support</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">👥</div>
                    <h5>User Management</h5>
                    <p>Admins can view and manage company users</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">👑</div>
                    <h5>Admin Management</h5>
                    <p>Add, view, edit, and manage multiple admin accounts</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">📢</div>
                    <h5>Advertisement System</h5>
                    <p>Companies can submit ads; admins approve them for website-wide display</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">🔐</div>
                    <h5>Role-Based Access</h5>
                    <p>Secure authentication with distinct permissions for visitors, companies, and admins</p>
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
                    <li>Access a personalized dashboard showing only your business listings</li>
                    <li>Add business listings with images, name, category, address, phone, and description</li>
                    <li>Upload and manage business images (supports image file uploads)</li>
                    <li>Edit and update your business information and images anytime</li>
                    <li>Delete your business listings if needed</li>
                    <li>Search and filter your own businesses by name or category</li>
                    <li>Manage multiple businesses under one account</li>
                    <li><strong>Submit advertisements</strong> for admin approval</li>
                    <li><strong>View your advertisement status</strong> (pending, approved, rejected)</li>
                    <li><strong>View all customer inquiries</strong> sent to your businesses</li>
                    <li>Navigate easily with role-specific navigation menu</li>
                </ul>
            </div>

            <div class="user-type-card">
                <h4>👑 Admin Users</h4>
                <p><strong>What you can do:</strong></p>
                <ul>
                    <li>View and manage all business listings from all companies</li>
                    <li>Search and filter all businesses across the platform</li>
                    <li>Access "Manage Users" to view all company accounts</li>
                    <li>Delete company user accounts when necessary</li>
                    <li><strong>View Admin</strong> - See all administrator accounts in the system</li>
                    <li><strong>Add Admin</strong> - Create new administrator accounts</li>
                    <li><strong>Edit Admin</strong> - Modify existing admin account details</li>
                    <li>Delete admin accounts (except your own)</li>
                    <li><strong>View all customer inquiries</strong> from all businesses across the platform</li>
                    <li>Delete inappropriate content, spam, or unwanted inquiries</li>
                    <li>Edit and delete any business listing</li>
                    <li><strong>Manage Advertisements</strong> - Review, approve, or reject company ad submissions</li>
                    <li>View all advertisements with filtering by status (pending/approved/rejected)</li>
                    <li>Full administrative control over the entire platform</li>
                    <li>Role-specific admin navigation and dashboard</li>
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
                <li>Click <strong>"Company Login"</strong> in the navigation menu</li>
                <li>If you're new, click <strong>"Register here"</strong> to create an account</li>
                <li>Once logged in, you'll see your personalized dashboard with all your businesses</li>
                <li>Click <strong>"Add Business"</strong> in the navigation to create a new listing</li>
                <li>Fill in your business details: name, category, address, phone, and description</li>
                <li>Click <strong>"Choose File"</strong> to upload a business image (JPG, PNG, etc.)</li>
                <li>Click <strong>"Save Business"</strong> to publish your listing</li>
                <li>View all your businesses in your dashboard (Home)</li>
                <li>Use the <strong>Search Bar</strong> and <strong>Category Filter</strong> to find specific businesses</li>
                <li>Click <strong>"Edit"</strong> on any business card to update information or change the image</li>
                <li>Click <strong>"Delete"</strong> to remove a listing (you'll see a confirmation page)</li>
                <li>Click <strong>"View Inquiries"</strong> in the navigation to see all customer inquiries for your businesses</li>
                <li>Review inquiry details including customer name, email, phone, message, and submission date</li>
                <li>Click <strong>"Submit Ad"</strong> to create a new advertisement submission</li>
                <li>Fill in advertisement details (title, description) and upload an image</li>
                <li>Click <strong>"My Ads"</strong> to view the status of your advertisements (pending, approved, rejected)</li>
            </ol>

            <h3>For Administrators</h3>
            <ol class="steps-list">
                <li>Click <strong>"Admin Login"</strong> in the navigation menu</li>
                <li>Enter your admin credentials</li>
                <li>Access your admin dashboard to view all businesses across the platform</li>
                <li>Use <strong>"Manage Users"</strong> to view all company accounts and their details</li>
                <li>Delete company accounts if necessary from the Manage Users page</li>
                <li>Click <strong>"View Admin"</strong> to see all administrator accounts in the system</li>
                <li>View admin details including username and current user indicator</li>
                <li>Click <strong>"Edit"</strong> on any admin account to modify their details (username, password)</li>
                <li>Click <strong>"Delete"</strong> to remove admin accounts (you cannot delete your own account)</li>
                <li>Use <strong>"Add Admin"</strong> to create new administrator accounts</li>
                <li>Fill in username, password, and confirm password for new admins</li>
                <li>Use <strong>Search and Category Filter</strong> to find specific businesses</li>
                <li>Click <strong>"Edit"</strong> on any business to modify its details</li>
                <li>Click <strong>"Delete"</strong> to remove inappropriate or spam business listings</li>
                <li>Use <strong>"View Inquiries"</strong> to see all customer inquiries from all businesses</li>
                <li>Delete inappropriate inquiries or spam messages as needed</li>
                <li>Click <strong>"Manage Ads"</strong> to review company advertisement submissions</li>
                <li>Use tabs to view <strong>Pending</strong>, <strong>Approved</strong>, or <strong>Rejected</strong> advertisements</li>
                <li>Click <strong>"Approve"</strong> to publish advertisements across the website</li>
                <li>Click <strong>"Reject"</strong> and add notes explaining why an ad was rejected</li>
                <li>Monitor and maintain the overall quality and security of the platform</li>
            </ol>
        </div>

        <!-- Tips Section -->
        <div class="about-section">
            <h2>💡 Tips for Best Experience</h2>
            <ul>
                <li><strong>Companies:</strong> Add high-quality, clear images to attract more customers to your listings</li>
                <li><strong>Companies:</strong> Provide detailed, accurate descriptions of your services or products</li>
                <li><strong>Companies:</strong> Keep your contact information (phone, address) updated regularly</li>
                <li><strong>Companies:</strong> Use the search and filter in your dashboard to quickly manage multiple businesses</li>
                <li><strong>Companies:</strong> Check "View Inquiries" regularly and respond promptly for better customer engagement</li>
                <li><strong>Companies:</strong> Submit high-quality advertisement images (800x400px recommended)</li>
                <li><strong>Companies:</strong> If your ad is rejected, review admin notes and resubmit with corrections</li>
                <li><strong>Visitors:</strong> Be specific and clear in your inquiry messages to get better, more helpful responses</li>
                <li><strong>Visitors:</strong> Include your contact information (email, phone) so businesses can reach you easily</li>
                <li><strong>Visitors:</strong> Use the search bar and category filter together to find exactly what you need</li>
                <li><strong>Admins:</strong> Regularly review "Manage Users" to monitor company accounts</li>
                <li><strong>Admins:</strong> Use "View Admin" to keep track of all administrator accounts</li>
                <li><strong>Admins:</strong> Monitor inquiries periodically to remove spam or inappropriate content</li>
                <li><strong>Admins:</strong> When adding new admins, use strong passwords for security</li>
                <li><strong>Admins:</strong> Review advertisement submissions promptly to ensure timely approval</li>
                <li><strong>Admins:</strong> Provide clear rejection notes so companies can improve their ads</li>
                <li><strong>All Users:</strong> Navigate using the role-specific menu for quick access to features</li>
                <li><strong>All Users:</strong> The platform shows different options based on your role (visitor/company/admin)</li>
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
