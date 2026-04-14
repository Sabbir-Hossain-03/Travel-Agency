<?php
// Detect if running on live server or localhost
$_isLiveServer = (
    isset($_SERVER['HTTP_HOST']) &&
    strpos($_SERVER['HTTP_HOST'], 'localhost') === false &&
    strpos($_SERVER['HTTP_HOST'], '127.0.0.1') === false
);

define('LOCAL_DEBUG_MODE', false); // Set true to show OTP on-screen (localhost testing only)

// SMTP is enabled on BOTH localhost and live server (InfinityFree allows port 587)
define('SMTP_ENABLED', true);
define('SMTP_HOST',      'smtp.gmail.com');
define('SMTP_PORT',      587);
define('SMTP_SSL',       false);  // false = STARTTLS on port 587

define('SMTP_USER',      'mursalinleon2295@gmail.com'); // Gmail that SENDS the OTP
define('SMTP_PASS',      'sbagsrstobaujhnw');          // Gmail App Password
define('SMTP_FROM',      'mursalinleon2295@gmail.com');
define('SMTP_FROM_NAME', 'Avestra Travel Agency');
?>
