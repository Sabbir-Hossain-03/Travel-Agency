<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database logic for MyProfile
include('../database/MyProfileData.php');

// Check if admin is logged in
$admin_email = $_SESSION['admin_email'] ?? '';
$is_admin_logged_in = false;

if (empty($admin_email)) {
    $error_message = 'Please log in to view your profile.';
} else {
    // Set variables from session
    $admin_name = $_SESSION['admin_name'] ?? 'Admin User';
    $admin_role = $_SESSION['admin_role'] ?? 'Administrator';
    $admin_phone = $_SESSION['admin_phone'] ?? '';
    $admin_status = $_SESSION['admin_status'] ?? 'Inactive';
    $admin_date = $_SESSION['admin_date'] ?? date('Y-m-d');
    
    // Check if admin is active
    if ($admin_status === 'Active') {
        $is_admin_logged_in = true;
    } else {
        $error_message = 'Your account is not active. Please contact administrator.';
    }
}

// Display success/error messages
$success_message = $_SESSION['success_message'] ?? '';
$error_message = $error_message ?? ($_SESSION['error_message'] ?? '');
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>
