<?php
namespace Admin\Utils;

class OTPUtility {
    /**
     * Generate a 6-digit random OTP
     */
    public static function generateOTP() {
        return sprintf("%06d", random_int(100000, 999999));
    }

    /**
     * Store OTP in session with timestamp
     */
    public static function storeOTP($otp) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['otp_code'] = $otp;
        $_SESSION['otp_expiry'] = time() + (5 * 60); // 5 minutes validity
    }

    /**
     * Verify OTP
     */
    public static function verifyOTP($user_otp) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['otp_code']) || !isset($_SESSION['otp_expiry'])) {
            return ['success' => false, 'message' => 'No OTP found. Please request a new one.'];
        }

        if (time() > $_SESSION['otp_expiry']) {
            self::clearOTP();
            return ['success' => false, 'message' => 'OTP has expired. Please request a new one.'];
        }

        if ($user_otp === $_SESSION['otp_code']) {
            return ['success' => true];
        }

        return ['success' => false, 'message' => 'Invalid OTP code. Please try again.'];
    }

    /**
     * Clear OTP from session
     */
    public static function clearOTP() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION['otp_code']);
        unset($_SESSION['otp_expiry']);
    }
}
?>
