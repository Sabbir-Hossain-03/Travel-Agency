<?php
$messages = [];

// Collect messages and their types for signup context
if (!empty($general_error)) {
    $messages[] = ['type' => 'general-error-message', 'text' => $general_error];
}
if (!empty($_SESSION['signup_success_message'])) {
    $messages[] = ['type' => 'success-message', 'text' => $_SESSION['signup_success_message']];
    unset($_SESSION['signup_success_message']);
}
if (!empty($_SESSION['signup_error_message'])) {
    $messages[] = ['type' => 'general-error-message', 'text' => $_SESSION['signup_error_message']];
    unset($_SESSION['signup_error_message']);
}
if (!empty($_SESSION['signup_info_message'])) {
    $messages[] = ['type' => 'info-message', 'text' => $_SESSION['signup_info_message']];
    unset($_SESSION['signup_info_message']);
}
if (!empty($_SESSION['signup_warning_message'])) {
    $messages[] = ['type' => 'warning-message', 'text' => $_SESSION['signup_warning_message']];
    unset($_SESSION['signup_warning_message']);
}

// Display all messages
foreach ($messages as $msg) {
    echo '<div class="' . htmlspecialchars($msg['type']) . '">' . htmlspecialchars($msg['text']) . '</div>';
}
?>
