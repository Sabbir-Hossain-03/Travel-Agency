<?php
session_start();
require_once __DIR__ . '/../utils/OTPUtility.php';
require_once __DIR__ . '/../utils/MailUtility.php';

if (isset($_SESSION['otp_action'])) {
    $email = "";
    if ($_SESSION['otp_action'] === 'signup' && isset($_SESSION['otp_signup_data']['email'])) {
        $email = $_SESSION['otp_signup_data']['email'];
    } elseif ($_SESSION['otp_action'] === 'forgot' && isset($_SESSION['otp_forgot_data']['email'])) {
        $email = $_SESSION['otp_forgot_data']['email'];
    }

    if (!empty($email)) {
        $otp = \Admin\Utils\OTPUtility::generateOTP();
        \Admin\Utils\OTPUtility::storeOTP($otp);
        unset($_SESSION['otp_attempts']); // Reset attempt counter for new OTP
        
        $mailSent = \Admin\Utils\MailUtility::sendOTPMail($email, $otp);
        
        if ($mailSent || (defined('LOCAL_DEBUG_MODE') && LOCAL_DEBUG_MODE)) {
            $_SESSION['otp_success'] = "A new verification code has been sent to your email.";
        } else {
            $_SESSION['otp_error'] = "Error sending verification code. Please try again.";
        }
    } else {
        $_SESSION['otp_error'] = "Session expired. Please start the process again.";
    }
}

header("Location: ../views/verifyOTP.php");
exit();
?>
