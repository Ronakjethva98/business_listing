<?php
session_start();
session_destroy();
header("Location: visitor.php");
exit();
