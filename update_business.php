<?php
include "auth.php";
include "db.php";

/* Ensure form was submitted */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id          = $_POST['id'];
    $name        = mysqli_real_escape_string($conn, $_POST['name']);
    $category    = mysqli_real_escape_string($conn, $_POST['category']);
    $address     = mysqli_real_escape_string($conn, $_POST['address']);
    $phone       = mysqli_real_escape_string($conn, $_POST['phone']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $old_image   = $_POST['old_image'];
    
    // Handle image upload
    $image_path = $old_image; // Keep old image by default
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        
        // Create uploads directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '_' . time() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Validate image
        $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif', 'webp');
        $max_file_size = 5 * 1024 * 1024; // 5MB
        
        if (in_array($file_extension, $allowed_extensions) && $_FILES['image']['size'] <= $max_file_size) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                // Delete old image if it exists and is different
                if (!empty($old_image) && file_exists($old_image) && $old_image != $target_file) {
                    unlink($old_image);
                }
                $image_path = $target_file;
            }
        }
    }

    mysqli_query($conn,
        "UPDATE businesses SET
            name='$name',
            category='$category',
            address='$address',
            phone='$phone',
            description='$description',
            image='$image_path'
         WHERE id='$id'"
    );
}

/* Redirect back to view page */
header("Location: dashboard.php");
exit();
