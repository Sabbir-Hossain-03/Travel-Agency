<?php
include 'session_check.php';
include '../database/dbconnection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: user_dashboard.php");
    exit();
}

$email = $_SESSION['email'];
$service_type = trim($_POST['service_type'] ?? '');

// For hotels, the ID is a string (e.g. H101). For others, it's typically an int.
$service_id_raw = trim($_POST['service_id'] ?? '');

if ($service_type === '' || empty($service_id_raw)) {
    $_SESSION['booking_error'] = "Invalid booking request.";
    header("Location: user_dashboard.php");
    exit();
}

/* Fetch service from DB based on service_type */
if ($service_type === 'ticket') {
    $service_id = (int)$service_id_raw;
    if ($service_id <= 0) {
        $_SESSION['booking_error'] = "Invalid booking request.";
        header("Location: start_Booking.php");
        exit();
    }
    $q = $conn->prepare("SELECT route, price FROM tickets WHERE id=? AND status='active'");
    $q->bind_param("i", $service_id);
    $q->execute();
    $s = $q->get_result()->fetch_assoc();
    if (!$s) {
        $_SESSION['booking_error'] = "Ticket not found or no longer available.";
        header("Location: start_Booking.php");
        exit();
    }

    $service_name = $s['route'];
    $total_price  = (float)$s['price'];
    if ($total_price <= 0) {
        $total_price = rand(500, 5000); // Assign a random price if 0 or missing
    }

} elseif ($service_type === 'hotel') {
    $hotel_id = $service_id_raw; // String ID
    $q = $conn->prepare("SELECT name, price_per_night FROM hotels WHERE id=? AND LOWER(status)='active'");
    $q->bind_param("s", $hotel_id);
    $q->execute();
    $s = $q->get_result()->fetch_assoc();
    if (!$s) {
        $_SESSION['booking_error'] = "Hotel not found or no longer available.";
        header("Location: find_Hotels.php");
        exit();
    }

    $service_name = $s['name'];
    $total_price  = (float)$s['price_per_night'];
    if ($total_price <= 0) {
        $total_price = rand(1000, 10000); // Assign a random price if 0 or missing
    }

} elseif ($service_type === 'tour') {
    $tour_id = trim($service_id_raw); // String ID like TRV-001

    $q = $conn->prepare("SELECT name, price FROM tours WHERE id=? AND status='active'");
    $q->bind_param("s", $tour_id);
    $q->execute();
    $s = $q->get_result()->fetch_assoc();
    if (!$s) {
        $_SESSION['booking_error'] = "Tour package not found or no longer available.";
        header("Location: explore_Tour_Packages.php");
        exit();
    }

    $service_name = $s['name'];
    $total_price  = (float)$s['price'];
    if ($total_price <= 0) {
        $total_price = rand(2000, 20000); // Assign a random price if 0 or missing
    }

} else {
    $_SESSION['booking_error'] = "Invalid service type.";
    header("Location: user_dashboard.php");
    exit();
}

/* Default booking values */
$travel_date = date("Y-m-d"); // user can edit later if you want
$quantity = (int)($_POST['quantity'] ?? 1);
if ($quantity <= 0) $quantity = 1;

$total_price = $total_price * $quantity;

/* Insert booking */
$ins = $conn->prepare("
    INSERT INTO bookings
    (user_email, service_type, service_name, travel_date, quantity, total_price,
     booking_status, payment_status, payment_method, created_at)
    VALUES (?, ?, ?, ?, ?, ?, 'pending', 'unpaid', NULL, NOW())
");

$ins->bind_param("ssssid", $email, $service_type, $service_name, $travel_date, $quantity, $total_price);

if ($ins->execute()) {
    $booking_id = $conn->insert_id;
    header("Location: payment.php?booking_id=" . $booking_id);
    exit();
}

error_log("Booking insert failed: " . $ins->error);
$_SESSION['booking_error'] = "Booking could not be completed. Please try again.";
header("Location: user_dashboard.php");
exit();