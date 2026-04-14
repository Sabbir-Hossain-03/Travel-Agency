<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_email'])) {
    header('Location: ../views/loginPage.php');
    exit();
}

include('../database/dbconnection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_id = isset($_POST['payment_id']) ? (int)$_POST['payment_id'] : 0;
    $new_status = isset($_POST['new_status']) ? trim($_POST['new_status']) : '';

    if ($payment_id > 0 && in_array($new_status, ['success', 'rejected'])) {
        
        // Start transaction
        $conn->begin_transaction();

        try {
            // Update the master payments table
            $sql = "UPDATE payments SET payment_status = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $new_status, $payment_id);
            $stmt->execute();
            
            // Wait, we need the booking_id and payment_method to update the correct booking tables
            $fetch_sql = "SELECT booking_id, payment_method FROM payments WHERE id = ?";
            $fetch_stmt = $conn->prepare($fetch_sql);
            $fetch_stmt->bind_param("i", $payment_id);
            $fetch_stmt->execute();
            $result = $fetch_stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $booking_id = $row['booking_id'];
                $payment_method = $row['payment_method'];
                
                if ($new_status === 'success') {
                    $b_status = 'confirmed';
                    $p_status = 'paid';
                } else {
                    $b_status = 'rejected';
                    $p_status = 'rejected';
                }
                
                // Update the global `bookings` table
                $conn->query("UPDATE bookings SET booking_status = '$b_status', payment_status = '$p_status' WHERE id = $booking_id");
            }

            $conn->commit();
            
            $formatted_booking_id = "TX" . (100 + $booking_id);
            $_SESSION['payment_msg'] = "Payment for booking " . $formatted_booking_id . " marked as " . ucfirst($new_status) . ".";
            $_SESSION['payment_msg_type'] = "success";
            
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['payment_msg'] = "Failed to update payment. System error.";
            $_SESSION['payment_msg_type'] = "error";
        }
    } else {
        $_SESSION['payment_msg'] = "Invalid request.";
        $_SESSION['payment_msg_type'] = "error";
    }
}

header('Location: ../views/Payments.php');
exit();
?>
