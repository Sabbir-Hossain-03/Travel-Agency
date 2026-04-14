<?php
$messages = [];

// Collect messages and their types for login context
if (!empty($general_error)) {
    $messages[] = ['type' => 'general-error-message', 'text' => $general_error];
}

if (!empty($_SESSION['login_error_message'])) {
    $messages[] = ['type' => 'general-error-message', 'text' => $_SESSION['login_error_message']];
    unset($_SESSION['login_error_message']);
}


// Display all messages
foreach ($messages as $msg) {
    echo '<div class="' . htmlspecialchars($msg['type']) . '">' . htmlspecialchars($msg['text']) . '</div>';
}
?>