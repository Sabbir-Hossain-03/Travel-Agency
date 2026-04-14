<?php
session_start();
include('../database/dbconnection.php');

 $search = $search ?? '';
    $role_filter = $role_filter ?? '';
    $status_filter = $status_filter ?? '';

    // Display success/error messages
    $success_message = $_SESSION['success_message'] ?? '';
    $error_message = $_SESSION['error_message'] ?? '';
    unset($_SESSION['success_message'], $_SESSION['error_message']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch($action) {
        case 'delete':
            $email = $_POST['email'] ?? '';
            if (!empty($email)) {
                // Try to delete from customer table first
                $stmt = $conn->prepare("DELETE FROM customer WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $affected = $stmt->affected_rows;
                $stmt->close();
                
                // If not found in customer, try admin table
                if ($affected === 0) {
                    $stmt = $conn->prepare("DELETE FROM admin WHERE email = ?");
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $affected = $stmt->affected_rows;
                    $stmt->close();
                }
                
                if ($affected > 0) {
                    $_SESSION['success_message'] = "User deleted successfully!";
                } else {
                    $_SESSION['error_message'] = "User not found.";
                }
            }
            break;
            
        case 'block':
            $email = $_POST['email'] ?? '';
            if (!empty($email)) {
                // Try to block in customer table first
                $stmt = $conn->prepare("UPDATE customer SET status = 'Blocked' WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $affected = $stmt->affected_rows;
                $stmt->close();
                
                // If not found in customer, try admin table
                if ($affected === 0) {
                    $stmt = $conn->prepare("UPDATE admin SET status = 'Blocked' WHERE email = ?");
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $affected = $stmt->affected_rows;
                    $stmt->close();
                }
                
                if ($affected > 0) {
                    $_SESSION['success_message'] = "User blocked successfully!";
                } else {
                    $_SESSION['error_message'] = "User not found.";
                }
            }
            break;
            
        case 'unblock':
            $email = $_POST['email'] ?? '';
            if (!empty($email)) {
                // Try to unblock in customer table first
                $stmt = $conn->prepare("UPDATE customer SET status = 'Active' WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $affected = $stmt->affected_rows;
                $stmt->close();
                
                // If not found in customer, try admin table
                if ($affected === 0) {
                    $stmt = $conn->prepare("UPDATE admin SET status = 'Active' WHERE email = ?");
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $affected = $stmt->affected_rows;
                    $stmt->close();
                }
                
                if ($affected > 0) {
                    $_SESSION['success_message'] = "User unblocked successfully!";
                } else {
                    $_SESSION['error_message'] = "User not found.";
                }
            }
            break;
            
        case 'add':
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $role = trim($_POST['role'] ?? 'Admin');
            $status = trim($_POST['status'] ?? 'Active');
            
            // Only allow Admin or Manager roles for admin panel users
            if (!in_array($role, ['Admin'])) {
                $_SESSION['error_message'] = "Invalid role. Only Admin can be added.";
                break;
            }
            
            if (!empty($username) && !empty($email)) {
                // Check if email exists
                $check = $conn->prepare("SELECT email FROM customer WHERE email = ?");
                $check->bind_param("s", $email);
                $check->execute();
                if ($check->get_result()->num_rows > 0) {
                    $_SESSION['error_message'] = "Email already exists!";
                } else {
                    $hashed_password = password_hash('Password123!', PASSWORD_DEFAULT);
                    $date = date('Y-m-d H:i:s');
                    $stmt = $conn->prepare("INSERT INTO customer (username, email, phoneNumber, role, password, status, date) VALUES (?, ?, '', ?, ?, ?, ?)");
                    $stmt->bind_param("ssssss", $username, $email, $role, $hashed_password, $status, $date);
                    if ($stmt->execute()) {
                        $_SESSION['success_message'] = "User added successfully!";
                    } else {
                        $_SESSION['error_message'] = "Error adding user.";
                    }
                    $stmt->close();
                }
                $check->close();
            } else {
                $_SESSION['error_message'] = "Name and email are required!";
            }
            break;
            
        case 'edit':
            $old_email = $_POST['old_email'] ?? '';
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $status = trim($_POST['status'] ?? 'Active');
            
            if (!empty($old_email) && !empty($username) && !empty($email)) {
                // Try to update customer table first
                $stmt = $conn->prepare("UPDATE customer SET username = ?, email = ?, status = ? WHERE email = ?");
                $stmt->bind_param("ssss", $username, $email, $status, $old_email);
                $stmt->execute();
                $affected = $stmt->affected_rows;
                $stmt->close();
                
                // If not found in customer, try admin table
                if ($affected === 0) {
                    $stmt = $conn->prepare("UPDATE admin SET username = ?, email = ?, status = ? WHERE email = ?");
                    $stmt->bind_param("ssss", $username, $email, $status, $old_email);
                    $stmt->execute();
                    $affected = $stmt->affected_rows;
                    $stmt->close();
                }
                
                if ($affected > 0) {
                    $_SESSION['success_message'] = "User updated successfully!";
                } else {
                    $_SESSION['error_message'] = "User not found.";
                }
            } else {
                $_SESSION['error_message'] = "All fields are required!";
            }
            break;
            
        case 'approve_admin':
            $request_id = $_POST['request_id'] ?? '';
            if (!empty($request_id)) {
                // First verify the admin exists and is pending
                $check = $conn->prepare("SELECT username, email FROM admin WHERE id = ? AND status = 'Pending'");
                $check->bind_param("i", $request_id);
                $check->execute();
                $result = $check->get_result();
                
                if ($result->num_rows > 0) {
                    $admin_data = $result->fetch_assoc();
                    $check->close();
                    
                    // Update admin status from Pending to Active
                    $stmt = $conn->prepare("UPDATE admin SET status = 'Active' WHERE id = ?");
                    $stmt->bind_param("i", $request_id);
                    
                    if ($stmt->execute()) {
                        $_SESSION['success_message'] = "Admin request approved! User {$admin_data['username']} has been activated.";
                    } else {
                        $_SESSION['error_message'] = "Error updating admin status: " . $conn->error;
                    }
                    $stmt->close();
                } else {
                    $_SESSION['error_message'] = "Request ID {$request_id} not found or already processed.";
                    $check->close();
                }
            } else {
                $_SESSION['error_message'] = "No request ID provided.";
            }
            break;
            
        case 'reject_admin':
            $request_id = $_POST['request_id'] ?? '';
            $reason = trim($_POST['reason'] ?? 'No reason provided');
            
            if (!empty($request_id)) {
                // Delete the pending admin request
                $stmt = $conn->prepare("DELETE FROM admin WHERE id = ? AND status = 'Pending'");
                $stmt->bind_param("i", $request_id);
                
                if ($stmt->execute() && $stmt->affected_rows > 0) {
                    $_SESSION['success_message'] = "Admin request rejected and removed.";
                } else {
                    $_SESSION['error_message'] = "Request not found or already processed.";
                }
                $stmt->close();
            }
            break;
    }
    
    $conn->close();
    header("Location: ../views/ManageUsers.php");
    exit();
}
?>
