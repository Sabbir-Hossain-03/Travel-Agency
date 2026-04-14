<?php
// Retrieve form data and errors from session if available
// Note: session_start() is called in SignupValidation.php, so not needed here

$form_data = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : array();
$form_errors = isset($_SESSION['form_errors']) ? $_SESSION['form_errors'] : array();

$username = isset($form_data['username']) ? $form_data['username'] : '';
$email = isset($form_data['email']) ? $form_data['email'] : '';
$phoneNumber = isset($form_data['phoneNumber']) ? $form_data['phoneNumber'] : '';
$role = isset($form_data['role']) ? $form_data['role'] : '';
$password = isset($form_data['password']) ? $form_data['password'] : '';
$confirmPassword = isset($form_data['confirmPassword']) ? $form_data['confirmPassword'] : '';

$username_error = isset($form_errors['username_error']) ? $form_errors['username_error'] : '';
$email_error = isset($form_errors['email_error']) ? $form_errors['email_error'] : '';
$phoneNumber_error = isset($form_errors['phoneNumber_error']) ? $form_errors['phoneNumber_error'] : '';
$role_error = isset($form_errors['role_error']) ? $form_errors['role_error'] : '';
$password_error = isset($form_errors['password_error']) ? $form_errors['password_error'] : '';
$confirmPassword_error = isset($form_errors['confirmPassword_error']) ? $form_errors['confirmPassword_error'] : '';
$general_error = isset($form_errors['general_error']) ? $form_errors['general_error'] : '';

// Clear session after retrieving data
unset($_SESSION['form_data']);
unset($_SESSION['form_errors']);
?>
