<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_email'])) {
    header('Location: ../views/loginPage.php');
    exit();
}

include('../database/dbconnection.php');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/Settings.php');
    exit();
}

$admin_email = $_SESSION['admin_email'];
$form_type = $_POST['form_type'] ?? 'general';

if ($form_type === 'general') {
    // Save general settings to session
    $theme = $_POST['site_theme'] ?? 'light';
    $_SESSION['settings']['site_theme'] = $theme;
    $_SESSION['settings']['dark_mode'] = ($theme === 'dark') ? 'dark' : 'light';
    
    $_SESSION['settings']['message_option'] = $_POST['message_option'] ?? 'enabled';
    $_SESSION['settings']['language'] = $_POST['language'] ?? 'en';
    $_SESSION['settings']['timezone'] = $_POST['timezone'] ?? 'UTC';
    $_SESSION['settings']['privacy_mode'] = $_POST['privacy_mode'] ?? 'public';
    $_SESSION['settings']['maintenance_mode'] = $_POST['maintenance_mode'] ?? 'off';
    
    // Save maintenance mode to file so customers can read it
    $maintenance_file = '../database/maintenance_mode.txt';
    file_put_contents($maintenance_file, $_SESSION['settings']['maintenance_mode']);
    
    $_SESSION['settings_updated'] = '✓ Settings saved successfully!';
}
else if ($form_type === 'profile') {
    // Update profile in admin database
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    
    if (!empty($username) && !empty($email)) {
        try {
            // First, get current profile data
            $sql = "SELECT username, email FROM admin WHERE email = ? LIMIT 1";
            $stmt = $conn->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("s", $admin_email);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $current_data = $result->fetch_assoc();
                    
                    // Check if new values are same as current values
                    if ($username === $current_data['username'] && $email === $current_data['email']) {
                        $_SESSION['settings_error'] = '✗ No changes detected. Please update at least one field.';
                        $stmt->close();
                    } else {
                        $stmt->close();
                        
                        // Update profile
                        $sql = "UPDATE admin SET username = ?, email = ? WHERE email = ?";
                        $stmt = $conn->prepare($sql);
                        
                        if ($stmt) {
                            $stmt->bind_param("sss", $username, $email, $admin_email);
                            
                            if ($stmt->execute()) {
                                $_SESSION['admin_email'] = $email;
                                $_SESSION['settings_updated'] = '✓ Profile updated successfully!';
                            } else {
                                $_SESSION['settings_error'] = '✗ Error updating profile. Please try again.';
                            }
                            $stmt->close();
                        } else {
                            $_SESSION['settings_error'] = '✗ Database error. Please try again.';
                        }
                    }
                } else {
                    $_SESSION['settings_error'] = '✗ Admin not found.';
                    $stmt->close();
                }
            } else {
                $_SESSION['settings_error'] = '✗ Database error. Please try again.';
            }
        } catch (Exception $e) {
            $_SESSION['settings_error'] = '✗ Error: ' . $e->getMessage();
        }
    } else {
        $_SESSION['settings_error'] = '✗ Please fill in all fields.';
    }
}
else if ($form_type === 'password') {
    // Change password in admin database
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $_SESSION['settings_error'] = '✗ Please fill in all password fields.';
    } else if ($new_password !== $confirm_password) {
        $_SESSION['settings_error'] = '✗ New passwords do not match.';
    } else if (strlen($new_password) < 6) {
        $_SESSION['settings_error'] = '✗ Password must be at least 6 characters long.';
    } else {
        try {
            // First verify current password
            $sql = "SELECT password FROM admin WHERE email = ? LIMIT 1";
            $stmt = $conn->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("s", $admin_email);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    
                    // Verify current password (using password_verify for bcrypt)
                    if (password_verify($current_password, $row['password'])) {
                        // Check if new password is same as current password
                        if (password_verify($new_password, $row['password'])) {
                            $_SESSION['settings_error'] = '✗ New password cannot be the same as current password.';
                        } else {
                            // Update password with bcrypt hash
                            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                            $stmt->close();
                            
                            $sql = "UPDATE admin SET password = ? WHERE email = ?";
                            $stmt = $conn->prepare($sql);
                            
                            if ($stmt) {
                                $stmt->bind_param("ss", $new_password_hash, $admin_email);
                                
                                if ($stmt->execute()) {
                                    $_SESSION['settings_updated'] = '✓ Password changed successfully!';
                                } else {
                                    $_SESSION['settings_error'] = '✗ Error changing password. Please try again.';
                                }
                                $stmt->close();
                            } else {
                                $_SESSION['settings_error'] = '✗ Database error. Please try again.';
                            }
                        }
                    } else {
                        $_SESSION['settings_error'] = '✗ Current password is incorrect.';
                        $stmt->close();
                    }
                } else {
                    $_SESSION['settings_error'] = '✗ Admin not found.';
                    $stmt->close();
                }
            } else {
                $_SESSION['settings_error'] = '✗ Database error. Please try again.';
            }
        } catch (Exception $e) {
            $_SESSION['settings_error'] = '✗ Error: ' . $e->getMessage();
        }
    }
}

// Redirect back to settings page
header('Location: ../views/Settings.php');
exit();
?>
