<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../database/dbconnection.php';

if (!$conn || $conn->connect_error) {
    $_SESSION['login_error_message'] = "Database connection error. Please try again later.";
    header("Location: ../views/loginPage.php");
    exit();
}

$email = $password = "";
$email_error = $password_error = "";
$general_error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if any field is empty
    if (empty(trim($_POST["email"])) || empty(trim($_POST["password"]))) {
        $general_error = 'Please fill in all required fields.';
        $_SESSION['login_form_errors'] = [
            'general_error' => $general_error
        ];
        header("Location: ../views/loginPage.php");
        exit();
    } else {
        // Validate email format
        if (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
            $email_error = "Please enter a valid email address.";
        } else {
            $email = trim($_POST["email"]);
        }

        // Validate password
        if (strlen(trim($_POST["password"])) < 6) {
            $password_error = "Password must be at least 6 characters.";
        } else {
            $password = trim($_POST["password"]);
        }

        // Store form data and errors in session
        $_SESSION['login_form_data'] = [
            'email' => $email
        ];

        $_SESSION['login_form_errors'] = [
            'email_error' => $email_error,
            'password_error' => $password_error,
            'general_error' => $general_error
        ];

        // If no errors, process login
        if (empty($email_error) && empty($password_error) && empty($general_error)) {
            // First check if user is admin
            $check_admin = $conn->prepare("SELECT username, email, password, role, status, phoneNumber, date FROM admin WHERE email = ?");
            $check_admin->bind_param("s", $email);
            $check_admin->execute();
            $admin_result = safe_get_result($check_admin);
            
            if ($admin_result && $admin_result->num_rows > 0) {
                // User found in admin table
                $user = $admin_result->fetch_assoc();
                
                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Check if user is Active (not Blocked or Inactive)
                    if ($user['status'] !== 'Active') {
                        $_SESSION['login_error_message'] = "Your account is " . strtolower($user['status']) . ". Please contact administrator.";
                        header("Location: ../views/loginPage.php");
                        exit();
                    }
                    
                    // Set session variables (admin_ prefix for MyProfile compatibility)
                    $_SESSION['admin_name'] = $user['username'];
                    $_SESSION['admin_email'] = $user['email'];
                    $_SESSION['admin_role'] = $user['role'];
                    $_SESSION['admin_status'] = $user['status'];
                    $_SESSION['admin_phone'] = $user['phoneNumber'];
                    $_SESSION['admin_date'] = $user['date'];
                    
                    // Also set non-prefixed versions for backward compatibility
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];
                    
                    // Remember me: store only email in cookie (never password)
                    if (isset($_POST['remember-me'])) {
                        $cookie_time = time() + (30 * 24 * 60 * 60); // 30 days
                        // PHP < 7.3 legacy signature: name, value, expire, path, domain, secure, httponly
                        setcookie('remember_email', $email, $cookie_time, '/', '', false, true);
                    } else {
                        // Clear cookie if not selected
                        setcookie('remember_email', '', time() - 3600, '/');
                    }
                    
                    // Clear form data
                    unset($_SESSION['login_form_data']);
                    unset($_SESSION['login_form_errors']);
                    
                    // Redirect to admin dashboard
                    header("Location: ../views/Admin.php");
                    exit();
                } else {
                    $_SESSION['login_error_message'] = "Invalid email or password.";
                    header("Location: ../views/loginPage.php");
                    exit();
                }
            } else {
                // Not found in admin table, check customer table
                $check_user = $conn->prepare("SELECT username, email, password, role, status, phoneNumber, date FROM customer WHERE email = ?");
                $check_user->bind_param("s", $email);
                $check_user->execute();
                $result = safe_get_result($check_user);
                
                if ($result && $result->num_rows > 0) {
                    $user = $result->fetch_assoc();
                    
                    // Verify password
                    if (password_verify($password, $user['password'])) {
                        // Check if user is Active (not Blocked or Inactive)
                        if ($user['status'] !== 'Active') {
                            $_SESSION['login_error_message'] = "Your account is " . strtolower($user['status']) . ". Please contact administrator.";
                            header("Location: ../views/loginPage.php");
                            exit();
                        }
                        
                        // Set session variables for customer
                        // First, clear any admin session data
                        unset($_SESSION['admin_email']);
                        unset($_SESSION['admin_username']);
                        
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['email'] = $user['email'];
                        $_SESSION['role'] = $user['role'];
                        $_SESSION['phone'] = $user['phoneNumber'];
                        
                        // Remember me: store only email in cookie (never password)
                        if (isset($_POST['remember-me'])) {
                            $cookie_time = time() + (30 * 24 * 60 * 60); // 30 days
                            // PHP < 7.3 legacy signature
                            setcookie('remember_email', $email, $cookie_time, '/', '', false, true);
                        } else {
                            // Clear cookie if not selected
                            setcookie('remember_email', '', time() - 3600, '/');
                        }
                        
                        // Clear form data
                        unset($_SESSION['login_form_data']);
                        unset($_SESSION['login_form_errors']);
                        
                        // Redirect to user dashboard
                        header("Location: ../../User/views/user_dashboard.php");
                        exit();
                    } else {
                        $_SESSION['login_error_message'] = "Invalid email or password.";
                        header("Location: ../views/loginPage.php");
                        exit();
                    }
                } else {
                    $_SESSION['login_error_message'] = "Invalid email or password.";
                    header("Location: ../views/loginPage.php");
                    exit();
                }
                $check_user->close();
            }
            $check_admin->close();
        } else {
            // Redirect back to login page with validation errors
            header("Location: ../views/loginPage.php");
            exit();
        }
    }
}
?>