<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../database/dbconnection.php';
require_once __DIR__ . '/../utils/OTPUtility.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_otp = isset($_POST['otp_code']) ? $_POST['otp_code'] : '';

    // Brute-force protection: max 5 attempts
    if (!isset($_SESSION['otp_attempts'])) {
        $_SESSION['otp_attempts'] = 0;
    }
    if ($_SESSION['otp_attempts'] >= 5) {
        \Admin\Utils\OTPUtility::clearOTP();
        unset($_SESSION['otp_attempts']);
        $_SESSION['otp_error'] = "Too many incorrect attempts. Please request a new OTP.";
        header("Location: ../views/verifyOTP.php");
        exit();
    }

    if (strlen($user_otp) !== 6) {
        $_SESSION['otp_error'] = "Please enter a valid 6-digit code.";
        header("Location: ../views/verifyOTP.php");
        exit();
    }

    $verification = \Admin\Utils\OTPUtility::verifyOTP($user_otp);

    if ($verification['success']) {
        $action = isset($_SESSION['otp_action']) ? $_SESSION['otp_action'] : '';

        if ($action === 'signup') {
            $data = $_SESSION['otp_signup_data'];
            
            if (strtolower($data['role']) === 'admin') {
                $status = 'Pending';
                $insert = $conn->prepare("INSERT INTO admin (username, email, phoneNumber, role, password, date, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $insert->bind_param("sssssss", $data['username'], $data['email'], $data['phoneNumber'], $data['role'], $data['password'], $data['date'], $status);
                
                if ($insert->execute()) {
                    $_SESSION['signup_success_message'] = "Admin request submitted successfully! Your request will be reviewed.";
                } else {
                    $_SESSION['otp_error'] = "Error finalizing account. Please try again.";
                    header("Location: ../views/verifyOTP.php");
                    exit();
                }
            } else {
                $status = 'Active';
                $insert = $conn->prepare("INSERT INTO customer (username, email, phoneNumber, role, password, date, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $insert->bind_param("sssssss", $data['username'], $data['email'], $data['phoneNumber'], $data['role'], $data['password'], $data['date'], $status);
                
                if ($insert->execute()) {
                    $_SESSION['signup_success_message'] = "Account created successfully! You can now login.";
                } else {
                    $_SESSION['otp_error'] = "Error finalizing account. Please try again.";
                    header("Location: ../views/verifyOTP.php");
                    exit();
                }
            }
            
            // Clean up
            \Admin\Utils\OTPUtility::clearOTP();
            unset($_SESSION['otp_signup_data']);
            unset($_SESSION['otp_action']);
            unset($_SESSION['otp_attempts']);
            header("Location: ../views/loginPage.php");
            exit();

        } elseif ($action === 'forgot') {
            $data = $_SESSION['otp_forgot_data'];
            $table = isset($data['table']) ? $data['table'] : 'customer'; // Default to customer
            
            $update = $conn->prepare("UPDATE $table SET password = ? WHERE email = ?");
            $update->bind_param("ss", $data['new_password'], $data['email']);
            
            if ($update->execute()) {
                $_SESSION['forgot_success_message'] = "Password reset successfully! You can now login.";
            } else {
                $_SESSION['otp_error'] = "Error resetting password. Please try again.";
                header("Location: ../views/verifyOTP.php");
                exit();
            }

            // Clean up
            \Admin\Utils\OTPUtility::clearOTP();
            unset($_SESSION['otp_forgot_data']);
            unset($_SESSION['otp_action']);
            unset($_SESSION['otp_attempts']);
            header("Location: ../views/loginPage.php");
            exit();
        }
    } else {
        $_SESSION['otp_attempts']++;
        $_SESSION['otp_error'] = $verification['message'];
        header("Location: ../views/verifyOTP.php");
        exit();
    }
} else {
    header("Location: ../views/Signup.php");
    exit();
}
?>
