<?php
// Dark mode helper - include this at the top of each Admin view after session_start()
// Reads the dark_mode preference from session and emits the class on <body>
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$session_theme_set = isset($_SESSION['settings']['dark_mode']) || isset($_SESSION['user_theme']);
$is_dark = $session_theme_set && (
    (isset($_SESSION['settings']['dark_mode']) && $_SESSION['settings']['dark_mode'] === 'dark') || 
    (isset($_SESSION['user_theme']) && $_SESSION['user_theme'] === 'dark')
);

$current_theme = $is_dark ? 'dark' : 'light';
?>
