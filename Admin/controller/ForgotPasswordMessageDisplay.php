<?php
$messages = [];

// Collect messages and their types for forgot password context
if (!empty($general_error)) {
    $messages[] = ['type' => 'general-error-message', 'text' => $general_error];
}
if (!empty($_SESSION['forgot_success_message'])) {
    $messages[] = ['type' => 'success-message', 'text' => $_SESSION['forgot_success_message']];
    unset($_SESSION['forgot_success_message']);
}
if (!empty($_SESSION['forgot_error_message'])) {
    $messages[] = ['type' => 'general-error-message', 'text' => $_SESSION['forgot_error_message']];
    unset($_SESSION['forgot_error_message']);
}

// Display all messages
foreach ($messages as $msg) {
    echo '<div class="' . htmlspecialchars($msg['type']) . '">' . htmlspecialchars($msg['text']) . '</div>';
}
?>
