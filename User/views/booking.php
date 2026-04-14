<?php
include 'session_check.php';
include '../database/dbconnection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: user_dashboard.php");
    exit();
}

$email = $_SESSION['email'];

$service_type = trim($_POST['service_type'] ?? '');
$service_name = trim($_POST['service_name'] ?? '');
$travel_date  = trim($_POST['travel_date'] ?? '');
$quantity     = (int)($_POST['quantity'] ?? 1);
$unit_price   = (float)($_POST['unit_price'] ?? 0);

if ($service_type === '' || $service_name === '' || $travel_date === '' || $quantity <= 0 || $unit_price <= 0) {
    $_SESSION['booking_error'] = "Invalid booking data. Please try again.";
    header("Location: user_dashboard.php");
    exit();
}

$total_price = $unit_price * $quantity;

$stmt = $conn->prepare("
    INSERT INTO bookings
    (user_email, service_type, service_name, travel_date, quantity, total_price, booking_status, payment_status, payment_method, created_at)
    VALUES (?, ?, ?, ?, ?, ?, 'pending', 'unpaid', '', NOW())
");

$stmt->bind_param("ssssid", $email, $service_type, $service_name, $travel_date, $quantity, $total_price);

if ($stmt->execute()) {
    header("Location: bookingHistory.php");
    exit();
} else {
    error_log("Booking insert failed: " . $stmt->error);
    $_SESSION['booking_error'] = "Booking could not be completed. Please try again.";
    header("Location: user_dashboard.php");
    exit();
}
?>