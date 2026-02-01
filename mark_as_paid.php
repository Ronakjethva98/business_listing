<?php
include "auth.php";
include "db.php";

/* ONLY ADMIN */
if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

/* VALIDATE POST REQUEST */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: manage_advertisements.php");
    exit();
}

$ad_id = intval($_POST['ad_id']);

/* UPDATE PAYMENT STATUS */
$sql = "UPDATE advertisements SET is_paid = 1 WHERE id = '$ad_id'";

if (mysqli_query($conn, $sql)) {
    header("Location: manage_advertisements.php?success=payment_updated");
} else {
    header("Location: manage_advertisements.php?error=payment_update_failed");
}
exit();
?>
