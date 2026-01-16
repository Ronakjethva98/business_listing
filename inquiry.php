<?php
include "db.php";

$business = $_GET['business'] ?? '';
$success  = "";
$error    = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $business = $_POST['business'];
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $phone    = $_POST['phone'];
    $message  = $_POST['message'];

    if ($name && $email && $message) {

        mysqli_query($conn,
            "INSERT INTO inquiries (business_name, name, email, phone, message)
             VALUES ('$business', '$name', '$email', '$phone', '$message')"
        );

        $success = "Your inquiry has been sent successfully.";
    } else {
        $error = "Please fill all required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Inquiry - Business Listing Portal</title>
    <meta name="description" content="Send inquiry to businesses">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body class="no-sidebar">

<!-- TOPBAR -->
<div class="topbar">
    ✉️ Send Inquiry
</div>

<!-- CONTENT -->
<div class="content content-center">

    <form method="POST" class="form">
        <h2>Send Inquiry</h2>

        <input type="hidden" name="business" value="<?php echo htmlspecialchars($business); ?>">

        <input
            type="text"
            value="<?php echo htmlspecialchars($business); ?>"
            disabled
        >

        <input
            type="text"
            name="name"
            placeholder="Your Name"
            required
            autofocus
        >

        <input
            type="email"
            name="email"
            placeholder="Your Email"
            required
        >

        <input
            type="text"
            name="phone"
            placeholder="Phone (optional)"
        >

        <textarea
            name="message"
            placeholder="Your Message"
            required
        ></textarea>

        <button type="submit" class="btn-inquiry">📤 Send Inquiry</button>

        <!-- BACK BUTTON -->
        <a href="visitor.php" style="text-decoration: none; display: block; margin-top: 16px;">
            <button type="button" class="btn-back">
                ← Back to Listings
            </button>
        </a>        
        <?php if ($success != "") { ?>
            <p class="success-message"><?php echo $success; ?></p>
        <?php } ?>

        <?php if ($error != "") { ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php } ?>
    </form>

</div>

</body>
</html>
