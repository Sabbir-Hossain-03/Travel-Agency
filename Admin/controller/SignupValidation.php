<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../database/dbconnection.php';

if (!$conn || $conn->connect_error) {
    $_SESSION['form_errors'] = ['general_error' => "Database connection error. Please try again later."];
    header("Location: ../views/Signup.php");
    exit();
}

$username = $email = $phoneNumber = $role = $password = $confirmPassword = "";
$username_error = $email_error = $phoneNumber_error =
    $role_error = $password_error = $confirmPassword_error = "";
$general_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["username"])) || empty(trim($_POST["email"])) || 
        empty(trim($_POST["phoneNumber"])) || empty(trim($_POST["role"])) || 
        empty(trim($_POST["password"])) || empty(trim($_POST["confirm-password"]))) {
        $general_error = "Please fill up all requirements.";
    } else {
        if (!preg_match("/^[a-zA-Z\s]+$/", trim($_POST["username"]))) {
            $username_error = " Fullname must contain only letters and spaces.";
        } else {
            $username = trim($_POST["username"]);
        }
        if (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
            $email_error = "Please enter a valid email address.";
        } else {
            $email = trim($_POST["email"]);
        }
        if (!preg_match("/^[0-9]{11}$/", trim($_POST["phoneNumber"]))) {
            $phoneNumber_error = "Phone number must be exactly 11 digits.";
        } else {
            $phoneNumber = trim($_POST["phoneNumber"]);
        }
        $role = trim($_POST["role"]);
        if (strlen(trim($_POST["password"])) < 6) {
            $password_error = "Password must have at least 6 characters.";
        } else {
            $password = trim($_POST["password"]);
        }
        $confirmPassword = trim($_POST["confirm-password"]);
        if ($password != $confirmPassword) {
            $confirmPassword_error = "Passwords do not match.";
        }
    }
    $_SESSION['form_data'] = [
        'username' => $username,
        'email' => $email,
        'phoneNumber' => $phoneNumber,
        'role' => $role,
        'password' => $password,
        'confirmPassword' => $confirmPassword
    ];

    $_SESSION['form_errors'] = [
        'username_error' => $username_error,
        'email_error' => $email_error,
        'phoneNumber_error' => $phoneNumber_error,
        'role_error' => $role_error,
        'password_error' => $password_error,
        'confirmPassword_error' => $confirmPassword_error,
        'general_error' => $general_error
    ];

   
    if (empty($username_error) && empty($email_error) && empty($phoneNumber_error) && empty($role_error) && empty($password_error) && empty($confirmPassword_error) && empty($general_error)) {
        
        // --- OTP IMPLEMENTATION ---
        require_once __DIR__ . '/../utils/OTPUtility.php';
        require_once __DIR__ . '/../utils/MailUtility.php';
        
        $otp = \Admin\Utils\OTPUtility::generateOTP();
        \Admin\Utils\OTPUtility::storeOTP($otp);
        
        // Store session data specifically for OTP verification
        $_SESSION['otp_action'] = 'signup';
        $_SESSION['otp_signup_data'] = [
            'username' => $username,
            'email' => $email,
            'phoneNumber' => $phoneNumber,
            'role' => $role,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'date' => date('Y-m-d H:i:s')
        ];
        
        // Send OTP
        $mailSent = \Admin\Utils\MailUtility::sendOTPMail($email, $otp);

        if ($mailSent) {
            $_SESSION['otp_success'] = "Verification code sent to $email. Please check your inbox.";
            header("Location: ../views/verifyOTP.php");
            exit();
        } else {
            $_SESSION['signup_error_message'] = "Failed to send verification email. Please try again.";
            header("Location: ../views/Signup.php");
            exit();
        }
        // --- END OTP IMPLEMENTATION ---
    }

    // Redirect back to signup form with errors/data in session
    header("Location: ../views/Signup.php");
    exit();
}
?>
