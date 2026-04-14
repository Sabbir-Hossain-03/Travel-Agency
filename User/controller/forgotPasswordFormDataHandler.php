<?php
session_start();
// Retrieve form data and errors from session if available

$form_data = $_SESSION['forgot_form_data'] ?? [];
$form_errors = $_SESSION['forgot_form_errors'] ?? [];

$email = $form_data['email'] ?? '';

$email_error = $form_errors['email_error'] ?? '';
$newPassword_error = $form_errors['newPassword_error'] ?? '';
$confirmPassword_error = $form_errors['confirmPassword_error'] ?? '';
$general_error = $form_errors['general_error'] ?? '';

// Clear session after retrieving data
unset($_SESSION['forgot_form_data']);
unset($_SESSION['forgot_form_errors']);
?>