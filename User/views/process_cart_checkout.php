<?php
include 'session_check.php';
include '../database/dbconnection.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header("Location: user_dashboard.php");
    exit();
}

$email = $_SESSION['email'];
$booking_ids = [];

foreach ($cart as $item) {
    $service_type = $item['service_type'];
    $service_id = $item['service_id'];
    $service_name = $item['name'];
    $quantity = $item['quantity'];
    $total_price = $item['price'] * $quantity;
    $travel_date = date("Y-m-d"); // Default or could be collected in cart

    $ins = $conn->prepare("
        INSERT INTO bookings
        (user_email, service_type, service_name, travel_date, quantity, total_price,
         booking_status, payment_status, payment_method, created_at)
        VALUES (?, ?, ?, ?, ?, ?, 'pending', 'unpaid', NULL, NOW())
    ");
    $ins->bind_param("ssssid", $email, $service_type, $service_name, $travel_date, $quantity, $total_price);
    
    if ($ins->execute()) {
        $booking_ids[] = $conn->insert_id;
    }
}

// Clear cart after checkout
$_SESSION['cart'] = [];

// Redirect to a summary payment page
$_SESSION['pending_payment_ids'] = $booking_ids;
header("Location: cart_payment.php");
exit();
?>
