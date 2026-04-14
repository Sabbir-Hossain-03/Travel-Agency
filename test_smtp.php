<?php
session_start();

// Include SMTP config and utilities
require_once __DIR__ . '/Admin/utils/SMTPConfig.php';
require_once __DIR__ . '/Admin/utils/SMTPSender.php';
require_once __DIR__ . '/Admin/utils/MailUtility.php';

$result = '';
$error  = '';

if (isset($_POST['send'])) {
    $to  = trim($_POST['to_email']);
    $otp = rand(100000, 999999);

    $sent = \Admin\Utils\MailUtility::sendOTPMail($to, $otp);

    if ($sent) {
        $result = "✅ Email sent successfully to <b>$to</b>! OTP was: <b>$otp</b>";
    } else {
        $result = "❌ Email sending FAILED. Check PHP error log for details.";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>SMTP Test - Avestra</title></head>
<body style="font-family:Arial; padding:30px; max-width:500px;">
    <h2>📧 OTP Email Test</h2>
    <p><b>SMTP_ENABLED:</b> <?= defined('SMTP_ENABLED') ? (SMTP_ENABLED ? '✅ true (using Gmail SMTP)' : '❌ false (using PHP mail())') : 'NOT DEFINED'; ?></p>
    <p><b>SMTP_HOST:</b> <?= defined('SMTP_HOST') ? SMTP_HOST : 'NOT DEFINED'; ?></p>
    <p><b>SMTP_PORT:</b> <?= defined('SMTP_PORT') ? SMTP_PORT : 'NOT DEFINED'; ?></p>
    <p><b>SMTP_USER:</b> <?= defined('SMTP_USER') ? SMTP_USER : 'NOT DEFINED'; ?></p>
    <p><b>EHLO HOST:</b> <?= (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost'; ?></p>

    <hr>
    <form method="POST">
        <label><b>Send test OTP to email:</b></label><br><br>
        <input type="email" name="to_email" required placeholder="Enter any email"
               style="padding:8px; width:100%; margin-bottom:10px; box-sizing:border-box;">
        <button type="submit" name="send"
                style="padding:10px 20px; background:#2563eb; color:#fff; border:none; border-radius:5px; cursor:pointer;">
            Send OTP Email
        </button>
    </form>

    <?php if ($result): ?>
        <div style="margin-top:20px; padding:15px; background:#f0fdf4; border:1px solid #16a34a; border-radius:5px;">
            <?= $result ?>
        </div>
    <?php endif; ?>

    <p style="color:red; font-size:12px;">⚠️ Delete this file after testing!</p>
</body>
</html>
