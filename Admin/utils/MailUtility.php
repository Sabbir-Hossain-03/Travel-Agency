<?php
namespace Admin\Utils;

require_once __DIR__ . '/SMTPConfig.php';
require_once __DIR__ . '/SMTPSender.php';

class MailUtility {
    /**
     * Send a simple HTML email.
     * - On localhost:   Uses Gmail SMTP via SMTPSender (SMTP_ENABLED = true)
     * - On InfinityFree / live: Uses PHP mail() (SMTP_ENABLED = false)
     */
    public static function sendHTMLMail($to, $subject, $body) {
        // Skip sending if in debug mode on localhost
        if (defined('LOCAL_DEBUG_MODE') && LOCAL_DEBUG_MODE) {
            error_log("DEBUG MODE: Skipping email to $to. Subject: $subject");
            return true;
        }

        // Basic email domain validation (similar to ReacherHQ but lightweight)
        $domain = substr(strrchr($to, "@"), 1);
        if (!empty($domain) && function_exists('checkdnsrr')) {
            if (!checkdnsrr($domain, "MX") && !checkdnsrr($domain, "A")) {
                error_log("Email sending blocked: Domain $domain does not exist.");
                return false;
            }
        }

        // If SMTP is enabled, use our custom authenticated sender
        if (defined('SMTP_ENABLED') && SMTP_ENABLED) {
            return SMTPSender::send($to, $subject, $body);
        }

        // Fallback: PHP built-in mail() — works on InfinityFree and shared hosts
        $from_email = defined('SMTP_FROM') ? SMTP_FROM : 'noreply@avestra-travel.com';
        $from_name  = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'Avestra Travel Agency';

        // For InfinityFree, the From address should ideally be @yourdomain.infinityfreeapp.com
        // but using your gmail address still works — emails may go to spam folder on Gmail recipients
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: $from_name <$from_email>\r\n";
        $headers .= "Reply-To: $from_email\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

        return mail($to, $subject, $body, $headers);
    }

    /**
     * Send OTP verification email
     */
    public static function sendOTPMail($to, $otp) {
        $subject = "Your Verification Code - Avestra Travel Agency";
        $body = "
        <html>
        <head>
            <style>
                body { margin: 0; padding: 0; background: #f4f6f9; font-family: Arial, sans-serif; }
                .wrapper { max-width: 480px; margin: 40px auto; background: #ffffff; border-radius: 10px; padding: 32px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
                h2 { color: #1e40af; margin-top: 0; }
                .otp-box { background: #eff6ff; border: 2px dashed #2563eb; border-radius: 8px; text-align: center; padding: 20px; margin: 24px 0; }
                .otp-code { font-size: 36px; font-weight: bold; color: #2563eb; letter-spacing: 8px; }
                .note { font-size: 13px; color: #6b7280; }
                .footer { border-top: 1px solid #e5e7eb; margin-top: 24px; padding-top: 16px; font-size: 12px; color: #9ca3af; text-align: center; }
            </style>
        </head>
        <body>
            <div class='wrapper'>
                <h2>🔐 Verification Code</h2>
                <p>Hello,</p>
                <p>Please use the following code to verify your action on <strong>Avestra Travel Agency</strong>:</p>
                <div class='otp-box'>
                    <div class='otp-code'>$otp</div>
                </div>
                <p class='note'>⏱ This code will expire in <strong>5 minutes</strong>. Do not share it with anyone.</p>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " Avestra Travel Agency. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        return self::sendHTMLMail($to, $subject, $body);
    }
}
?>
