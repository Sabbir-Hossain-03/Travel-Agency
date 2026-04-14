<?php
session_start();
$_SESSION = [];
session_destroy();
header("Location: /Avestra-Travel-Agency/Admin/views/homePage.php");
exit();
?>