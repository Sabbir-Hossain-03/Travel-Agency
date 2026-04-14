<?php
session_start();

// Include database logic for managing users
include('../database/ManageUsersData.php');

// Ensure filter variables are defined
$search = $search ?? '';
$role_filter = $role_filter ?? '';
$status_filter = $status_filter ?? '';

// Display success/error messages
$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>
