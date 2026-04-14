<?php
include_once(__DIR__ . '/dbconnection.php');
$maintenance_file = __DIR__ . '/maintenance_mode.txt';
if (file_exists($maintenance_file)) {
    $maintenance_mode = trim(file_get_contents($maintenance_file));
} else {
    $maintenance_mode = 'off';
}

$site_theme = isset($_SESSION['settings']['site_theme']) ? $_SESSION['settings']['site_theme'] : 'light';
$message_option = isset($_SESSION['settings']['message_option']) ? $_SESSION['settings']['message_option'] : 'enabled';
$language = isset($_SESSION['settings']['language']) ? $_SESSION['settings']['language'] : 'en';
$timezone = isset($_SESSION['settings']['timezone']) ? $_SESSION['settings']['timezone'] : 'UTC';
$privacy_mode = isset($_SESSION['settings']['privacy_mode']) ? $_SESSION['settings']['privacy_mode'] : 'public';

$current_username = '';
$current_email = '';

if (isset($_SESSION['admin_email'])) {
    $admin_email = $_SESSION['admin_email'];
    $sql = "SELECT username, email FROM admin WHERE email = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $admin_email);
        $stmt->execute();
        $result = safe_get_result($stmt);
        if ($result->num_rows > 0) {
            $admin_data = $result->fetch_assoc();
            $current_username = $admin_data['username'];
            $current_email = $admin_data['email'];
        }
        $stmt->close();
    }
}
