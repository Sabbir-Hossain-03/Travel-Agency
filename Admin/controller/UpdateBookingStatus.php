<?php
session_start();
include('../database/dbconnection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['admin_email'])) {
    $booking_id = (int)$_POST['booking_id'];
    $new_status = $_POST['status']; // 'confirmed' or 'rejected'

    if (in_array($new_status, ['confirmed', 'rejected'])) {
        $stmt = $conn->prepare("UPDATE bookings SET booking_status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $booking_id);
        
        if ($stmt->execute()) {
            $formatted_id = "TX" . (100 + $booking_id);
            $_SESSION['booking_msg'] = "Booking $formatted_id successfully " . htmlspecialchars($new_status) . ".";
        } else {
            $_SESSION['booking_msg'] = "Error updating booking status.";
        }
    }
}

header("Location: ../views/ManageBookings.php");
exit();
?>
