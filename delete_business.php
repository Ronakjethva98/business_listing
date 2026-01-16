<?php
include "auth.php";
include "db.php";

mysqli_query($conn, "DELETE FROM businesses WHERE id=$_GET[id]");
header("Location: dashboard.php");
exit();
