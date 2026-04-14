<?php
include 'session_check.php';
include '../database/dbconnection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: user_dashboard.php");
    exit();
}

$booking_id = (int)($_POST['booking_id'] ?? 0);
$method = trim($_POST['payment_method'] ?? '');
$email = $_SESSION['email'];

if ($booking_id <= 0 || $method === '') {
    $_SESSION['payment_error'] = "Invalid payment request.";
    header("Location: user_dashboard.php");
    exit();
}

/* Get booking amount */
$stmt = $conn->prepare("SELECT total_price FROM bookings WHERE id=? AND user_email=?");
$stmt->bind_param("is", $booking_id, $email);
$stmt->execute();
$b = $stmt->get_result()->fetch_assoc();

if (!$b) {
    $_SESSION['payment_error'] = "Booking not found.";
    header("Location: bookingHistory.php");
    exit();
}

$amount = (float)$b['total_price'];
$txn = strtoupper(uniqid("TXN"));

/* Insert payment record */
$pay = $conn->prepare("
    INSERT INTO payments
    (booking_id, user_email, amount, payment_method, transaction_id, payment_status, payment_date)
    VALUES (?, ?, ?, ?, ?, 'pending', NOW())
");

$pay->bind_param("isdss", $booking_id, $email, $amount, $method, $txn);

if (!$pay->execute()) {
    error_log("Payment insert failed: " . $pay->error);
    $_SESSION['payment_error'] = "Payment could not be processed. Please try again.";
    header("Location: payment.php?booking_id=" . $booking_id);
    exit();
}

/* Update booking */
$upd = $conn->prepare("
    UPDATE bookings
    SET payment_status='pending',
        booking_status='pending',
        payment_method=?
    WHERE id=? AND user_email=?
");

$upd->bind_param("sis", $method, $booking_id, $email);
$upd->execute();

header("Location: bookingHistory.php");
exit();