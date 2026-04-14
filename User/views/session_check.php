<?php
session_start();

if (!isset($_SESSION['email'])) {
    // Only store local paths as redirect targets (prevent open redirect)
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    if (!empty($request_uri) && strpos($request_uri, '/') === 0) {
        $_SESSION['redirect_after_login'] = $request_uri;
    }
    header("Location: loginPage.php");
    exit();
}
?>