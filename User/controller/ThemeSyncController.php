<?php
session_start();

// Simple AJAX endpoint for User branch theme synchronization
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $theme = $_POST['site_theme'] ?? 'light';
    $_SESSION['user_theme'] = ($theme === 'dark') ? 'dark' : 'light';
    
    echo json_encode(['status' => 'success', 'theme' => $_SESSION['user_theme']]);
    exit();
}
?>
