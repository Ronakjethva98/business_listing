<?php
include "auth.php";
include "db.php";

/* BLOCK GUESTS AND REDIRECT BASED ON ACTION */
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

/* VALIDATE POST REQUEST */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: manage_advertisements.php");
    exit();
}

$ad_id = mysqli_real_escape_string($conn, $_POST['ad_id']);
$action = mysqli_real_escape_string($conn, $_POST['action']);
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

if ($action == 'approve') {
    if ($user_role !== 'admin') { header("Location: dashboard.php"); exit(); }
    
    /* APPROVE ADVERTISEMENT */
    $sql = "UPDATE advertisements 
            SET status='approved', 
                approved_by='$user_id', 
                approved_at=NOW() 
            WHERE id='$ad_id'";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: manage_advertisements.php?success=approved");
    } else {
        header("Location: manage_advertisements.php?error=1");
    }
    
} elseif ($action == 'reject') {
    if ($user_role !== 'admin') { header("Location: dashboard.php"); exit(); }
    
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
    // Security Check: Company can only delete their own ads
    $check_sql = "SELECT image_path, company_id FROM advertisements WHERE id='$ad_id'";
    $result = mysqli_query($conn, $check_sql);
    
    if ($row = mysqli_fetch_assoc($result)) {
        if ($user_role !== 'admin' && $row['company_id'] != $user_id) {
            header("Location: my_advertisements.php?error=unauthorized");
            exit();
        }

        $image_path = $row['image_path'];
        
        // Delete from database
        $sql = "DELETE FROM advertisements WHERE id='$ad_id'";
        
        if (mysqli_query($conn, $sql)) {
            // Delete the image file
            if (file_exists($image_path)) {
                unlink($image_path);
            }
            
            // Redirect based on role
            $redirect = ($user_role === 'admin') ? "manage_advertisements.php" : "my_advertisements.php";
            header("Location: $redirect?success=deleted");
        } else {
            $redirect = ($user_role === 'admin') ? "manage_advertisements.php" : "my_advertisements.php";
            header("Location: $redirect?error=1");
        }
    } else {
        $redirect = ($user_role === 'admin') ? "manage_advertisements.php" : "my_advertisements.php";
        header("Location: $redirect?error=1");
    }
}

exit();
?>
