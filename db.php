<?php
$conn = mysqli_connect("localhost", "root", "", "business_db");
if (!$conn) {
    die("Database connection failed");
}

/* HELPER: GET UNREAD INQUIRIES */
function getUnreadInquiryCount($conn, $user_id, $role) {
    if ($role === 'admin') {
        $sql = "SELECT COUNT(*) as count FROM inquiries WHERE is_read = 0";
    } elseif ($role === 'company') {
        $sql = "SELECT COUNT(*) as count FROM inquiries i 
                JOIN businesses b ON i.business_name = b.name 
                WHERE b.user_id = '$user_id' AND i.is_read = 0";
    } else {
        return 0;
    }
    $res = mysqli_query($conn, $sql);
    if (!$res) return 0;
    $data = mysqli_fetch_assoc($res);
    return $data['count'] ?? 0;
}
?>
