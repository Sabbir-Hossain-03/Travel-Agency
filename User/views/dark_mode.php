<?php
// Dark mode helper for User branch
// Reads the dark_mode preference from session and provides $is_dark variable
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$session_theme_set = isset($_SESSION['user_theme']) || isset($_SESSION['settings']['dark_mode']);
$is_dark = $session_theme_set && (
    (isset($_SESSION['user_theme']) && $_SESSION['user_theme'] === 'dark') ||
    (isset($_SESSION['settings']['dark_mode']) && $_SESSION['settings']['dark_mode'] === 'dark')
);

$current_theme = $is_dark ? 'dark' : 'light';
?>
