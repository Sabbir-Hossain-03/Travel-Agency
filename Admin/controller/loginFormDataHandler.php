<?php
// Only start session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$form_data = $_SESSION['login_form_data'] ?? [];
$form_errors = $_SESSION['login_form_errors'] ?? [];

// Check for remember me cookies
if (isset($_COOKIE['remember_email']) && isset($_COOKIE['remember_password'])) {
    $email = $_COOKIE['remember_email'];
    $saved_password = $_COOKIE['remember_password'];
    $remember_checked = true;
} else {
    $email = $form_data['email'] ?? '';
    $saved_password = '';
    $remember_checked = false;
}

$email_error = $form_errors['email_error'] ?? '';
$password_error = $form_errors['password_error'] ?? '';
$general_error = $form_errors['general_error'] ?? '';

// Don't unset until after all variables are used
?>
