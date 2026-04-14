<?php
include 'session_check.php';
include '../database/dbconnection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit();

$pending_ids = $_SESSION['pending_payment_ids'] ?? [];
$email = $_SESSION['email'];
$payment_method = $_POST['payment_method'] ?? 'unknown';

if (!empty($pending_ids)) {
    $placeholders = implode(',', array_fill(0, count($pending_ids), '?'));
    
    // Prepare to update bookings and insert into payments
    $stmt = $conn->prepare("UPDATE bookings SET payment_status='pending', payment_method=? WHERE id IN ($placeholders) AND user_email=?");
    
    // Append email to params
    $params_upd = array_merge([$payment_method], $pending_ids, [$email]);
    $types_upd = 's' . str_repeat('i', count($pending_ids)) . 's';
    
    $stmt->bind_param($types_upd, ...$params_upd);
    $stmt->execute();

    // Insert into payments table for each booking
    $get_bookings = $conn->prepare("SELECT id, total_price FROM bookings WHERE id IN ($placeholders) AND user_email=?");
    $params_get = array_merge($pending_ids, [$email]);
    $types_get = str_repeat('i', count($pending_ids)) . 's';
    $get_bookings->bind_param($types_get, ...$params_get);
    $get_bookings->execute();
    $bookings_result = $get_bookings->get_result();

    $pay_stmt = $conn->prepare("INSERT INTO payments (booking_id, user_email, amount, payment_method, transaction_id, payment_status, payment_date) VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
    
    while ($row = $bookings_result->fetch_assoc()) {
        $b_id = $row['id'];
        $amt = (float)$row['total_price'];
        $txn = strtoupper(uniqid("CART"));
        $pay_stmt->bind_param("isdss", $b_id, $email, $amt, $payment_method, $txn);
        $pay_stmt->execute();
    }
    
    unset($_SESSION['pending_payment_ids']);
}

echo "success";
exit();
?>
