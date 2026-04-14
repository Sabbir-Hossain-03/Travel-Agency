<?php
session_start();
include(__DIR__ . '/../database/dbconnection.php');
require_once(__DIR__ . '/../database/PaymentsData.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: Payments.php");
    exit;
}

$id = (int)($_POST['payment_id'] ?? 0);
$action = $_POST['action'] ?? '';

if ($id <= 0) {
    $_SESSION['err'] = 'Invalid payment ID';
    header("Location: Payments.php");
    exit;
}

if ($action === 'accept') {
    $status = 'success';
} elseif ($action === 'reject') {
    $status = 'failed';
} else {
    $_SESSION['err'] = 'Invalid action';
    header("Location: Payments.php");
    exit;
}

if (payment_update_status($conn, $id, $status)) {
    $_SESSION['msg'] = ($status === 'success')
        ? 'Payment accepted successfully'
        : 'Payment rejected successfully';
} else {
    $_SESSION['err'] = 'Payment already processed or not pending';
}

header("Location: Payments.php");
exit;
