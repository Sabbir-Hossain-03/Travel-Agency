<?php
// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Retrieve form data and errors from session if available
$form_data   = $_SESSION['form_data'] ?? [];
$form_errors = $_SESSION['form_errors'] ?? [];

// Form values
$username         = $form_data['username'] ?? '';
$email            = $form_data['email'] ?? '';
$phoneNumber      = $form_data['phoneNumber'] ?? '';
$role             = $form_data['role'] ?? '';
$password         = $form_data['password'] ?? '';
$confirmPassword  = $form_data['confirmPassword'] ?? '';

// Error messages
$username_error        = $form_errors['username_error'] ?? '';
$email_error           = $form_errors['email_error'] ?? '';
$phoneNumber_error     = $form_errors['phoneNumber_error'] ?? '';
$role_error            = $form_errors['role_error'] ?? '';
$password_error        = $form_errors['password_error'] ?? '';
$confirmPassword_error = $form_errors['confirmPassword_error'] ?? '';
$general_error         = $form_errors['general_error'] ?? '';

// Clear session after retrieving data (optional)
unset($_SESSION['form_data']);
unset($_SESSION['form_errors']);
?>