<?php
include "auth.php";
include "db.php";

/* BLOCK NON-ADMIN USERS */
if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

/* VALIDATE POST REQUEST */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: manage_advertisements.php");
    exit();
}

$ad_id = mysqli_real_escape_string($conn, $_POST['ad_id']);
$action = mysqli_real_escape_string($conn, $_POST['action']);
$admin_id = $_SESSION['user_id'];

if ($action == 'approve') {
    /* APPROVE ADVERTISEMENT */
    $sql = "UPDATE advertisements 
            SET status='approved', 
                approved_by='$admin_id', 
                approved_at=NOW() 
            WHERE id='$ad_id'";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: manage_advertisements.php?success=approved");
    } else {
        header("Location: manage_advertisements.php?error=1");
    }
    
} elseif ($action == 'reject') {
    /* REJECT ADVERTISEMENT */
    $admin_notes = mysqli_real_escape_string($conn, trim($_POST['admin_notes']));
    
    $sql = "UPDATE advertisements 
            SET status='rejected', 
                admin_notes='$admin_notes' 
            WHERE id='$ad_id'";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: manage_advertisements.php?success=rejected");
    } else {
        header("Location: manage_advertisements.php?error=1");
    }
    
} elseif ($action == 'delete') {
    /* DELETE ADVERTISEMENT */
    // First get the image path to delete the file
    $result = mysqli_query($conn, "SELECT image_path FROM advertisements WHERE id='$ad_id'");
    if ($row = mysqli_fetch_assoc($result)) {
        $image_path = $row['image_path'];
        
        // Delete from database
        $sql = "DELETE FROM advertisements WHERE id='$ad_id'";
        
        if (mysqli_query($conn, $sql)) {
            // Delete the image file
            if (file_exists($image_path)) {
                unlink($image_path);
            }
            header("Location: manage_advertisements.php?success=deleted");
        } else {
            header("Location: manage_advertisements.php?error=1");
        }
    } else {
        header("Location: manage_advertisements.php?error=1");
    }
}

exit();
?>
