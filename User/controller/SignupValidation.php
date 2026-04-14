<?php
session_start();
include '../database/dbconnection.php';

if (!$conn || $conn->connect_error) {
    $_SESSION['signup_error_message'] = "Database connection error. Please try again later.";
    header("Location: ../../Admin/views/Signup.php");
    exit();
}

$username = $email = $phoneNumber = $role = $password = "";
$username_error = $email_error = $phoneNumber_error =
$role_error = $password_error = $confirmPassword_error = "";
$general_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (
        empty(trim($_POST["username"])) ||
        empty(trim($_POST["email"])) ||
        empty(trim($_POST["phoneNumber"])) ||
        empty(trim($_POST["role"])) ||
        empty(trim($_POST["password"])) ||
        empty(trim($_POST["confirm-password"]))
    ) {
        $general_error = "Please fill up all requirements.";
    } else {

        if (!preg_match("/^[a-zA-Z\s]+$/", $_POST["username"])) {
            $username_error = "Full name must contain only letters and spaces.";
        } else {
            $username = trim($_POST["username"]);
        }

        if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            $email_error = "Invalid email address.";
        } else {
            $email = trim($_POST["email"]);
        }

        if (!preg_match("/^[0-9]{11}$/", $_POST["phoneNumber"])) {
            $phoneNumber_error = "Phone number must be exactly 11 digits.";
        } else {
            $phoneNumber = trim($_POST["phoneNumber"]);
        }

        $role = trim($_POST["role"]);

        if (strlen($_POST["password"]) < 6) {
            $password_error = "Password must be at least 6 characters.";
        } else {
            $password = $_POST["password"];
        }

        if ($_POST["password"] !== $_POST["confirm-password"]) {
            $confirmPassword_error = "Passwords do not match.";
        }
    }

    if (
        empty($username_error) && empty($email_error) &&
        empty($phoneNumber_error) && empty($password_error) &&
        empty($confirmPassword_error) && empty($general_error)
    ) {

        $check = $conn->prepare("SELECT email FROM customer WHERE email=?");
        if (!$check) {
            // Fallback to signup table if customer table query fails
            $check = $conn->prepare("SELECT email FROM signup WHERE email=?");
        }
        $check->bind_param("s", $email);
        $check->execute();
        $res = safe_get_result($check);

        if ($res && $res->num_rows > 0) {
            $_SESSION['signup_error_message'] = "Email already exists.";
            header("Location: ../views/Signup.php");
            exit();
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $insert = $conn->prepare(
            "INSERT INTO signup(username,email,phoneNumber,role,password)
             VALUES (?,?,?,?,?)"
        );
        $insert->bind_param("sssss", $username, $email, $phoneNumber, $role, $hashed);

        if ($insert->execute()) {
            // --- OTP IMPLEMENTATION ---
            require_once __DIR__ . '/../../Admin/utils/OTPUtility.php';
            require_once __DIR__ . '/../../Admin/utils/MailUtility.php';
            
            $otp = \Admin\Utils\OTPUtility::generateOTP();
            \Admin\Utils\OTPUtility::storeOTP($otp);
            
            // Store session data specifically for OTP verification
            $_SESSION['otp_action'] = 'signup';
            $_SESSION['otp_signup_data'] = [
                'username' => $username,
                'email' => $email,
                'phoneNumber' => $phoneNumber,
                'role' => $role,
                'password' => $hashed,
                'date' => date('Y-m-d H:i:s')
            ];
            
            // Send OTP
            $mailSent = \Admin\Utils\MailUtility::sendOTPMail($email, $otp);

            if ($mailSent) {
                $_SESSION['otp_success'] = "Verification code sent to $email. Please check your inbox.";
                header("Location: ../../Admin/views/verifyOTP.php");
                exit();
            } else {
                $_SESSION['signup_error_message'] = "Failed to send verification email. Please try again.";
                header("Location: ../../Admin/views/Signup.php");
                exit();
            }
            // --- END OTP IMPLEMENTATION ---
        }
    }

    header("Location: ../../Admin/views/Signup.php");
    exit();
}
?>