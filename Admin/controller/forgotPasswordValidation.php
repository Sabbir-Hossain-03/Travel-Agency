<?php
session_start();
include '../database/dbconnection.php';

$email = $newPassword = $confirmPassword = "";
$email_error = $newPassword_error = $confirmPassword_error = "";
$general_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["email"])) || empty(trim($_POST["new-password"])) || empty(trim($_POST["confirm-password"]))) {
        $general_error = "Please fill in all required fields.";
    } else {
        if (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
            $email_error = "Please enter a valid email address.";
        } else {
            $email = trim($_POST["email"]);
        }
        if (strlen(trim($_POST["new-password"])) < 6) {
            $newPassword_error = "Password must be at least 6 characters long.";
        } else {
            $newPassword = trim($_POST["new-password"]);
        }
        $confirmPassword = trim($_POST["confirm-password"]);
        if ($newPassword != $confirmPassword) {
            $confirmPassword_error = "Passwords do not match.";
        }
    }
    $_SESSION['forgot_form_data'] = [
        'email' => $email
    ];
    $_SESSION['forgot_form_errors'] = [
        'email_error' => $email_error,
        'newPassword_error' => $newPassword_error,
        'confirmPassword_error' => $confirmPassword_error,
        'general_error' => $general_error
    ];
    if (empty($email_error) && empty($newPassword_error) && empty($confirmPassword_error) && empty($general_error)) {
        // Check if email exists in customer or admin table
        $is_admin = false;
        $check_email = $conn->prepare("SELECT email FROM customer WHERE email = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $res = $check_email->get_result();
        
        if ($res->num_rows === 0) {
            $check_email->close();
            $check_email = $conn->prepare("SELECT email FROM admin WHERE email = ?");
            $check_email->bind_param("s", $email);
            $check_email->execute();
            $res = $check_email->get_result();
            if ($res->num_rows > 0) {
                $is_admin = true;
            }
        }

        if ($res->num_rows > 0) {
            
            // --- OTP IMPLEMENTATION ---
            require_once __DIR__ . '/../utils/OTPUtility.php';
            require_once __DIR__ . '/../utils/MailUtility.php';
            
            $otp = \Admin\Utils\OTPUtility::generateOTP();
            \Admin\Utils\OTPUtility::storeOTP($otp);
            
            $_SESSION['otp_action'] = 'forgot';
            $_SESSION['otp_forgot_data'] = [
                'email' => $email,
                'new_password' => password_hash($newPassword, PASSWORD_DEFAULT),
                'table' => $is_admin ? 'admin' : 'customer'
            ];
            
            $mailSent = \Admin\Utils\MailUtility::sendOTPMail($email, $otp);

            if ($mailSent) {
                $_SESSION['otp_success'] = "Verification code sent to $email. Please check your inbox.";
                header("Location: ../views/verifyOTP.php");
                exit();
            } else {
                $_SESSION['forgot_error_message'] = "Failed to send verification email. Please check your email address and try again.";
                header("Location: ../views/forgotPassword.php");
                exit();
            }
            // --- END OTP IMPLEMENTATION ---

        } else {
            $_SESSION['forgot_error_message'] = "Email address not found. Please check and try again.";
        }
        $check_email->close();
    }

    header("Location: ../views/forgotPassword.php");
    exit();
}
?>
