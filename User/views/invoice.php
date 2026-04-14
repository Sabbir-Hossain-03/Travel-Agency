<?php
include 'session_check.php';
include 'dark_mode.php'; // Include user theme helper
include '../database/dbconnection.php';

$booking_id = (int)($_GET['id'] ?? 0);
if ($booking_id <= 0) {
    header("Location: bookingHistory.php");
    exit();
}

$email = $_SESSION['email'];

/* Fetch booking (only this user's booking) */
$stmt = $conn->prepare("
    SELECT id, user_email, service_type, service_name, travel_date, quantity, total_price,
           booking_status, payment_status, payment_method, created_at
    FROM bookings
    WHERE id = ? AND user_email = ?
    LIMIT 1
");
$stmt->bind_param("is", $booking_id, $email);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    $_SESSION['booking_error'] = "Invoice not found or you don't have permission to view it.";
    header("Location: bookingHistory.php");
    exit();
}

/* Fetch payment info (if paid/exists) */
$pstmt = $conn->prepare("
    SELECT transaction_id, payment_status, payment_method, payment_date
    FROM payments
    WHERE booking_id = ? AND user_email = ?
    ORDER BY payment_date DESC
    LIMIT 1
");
$pstmt->bind_param("is", $booking_id, $email);
$pstmt->execute();
$payment = $pstmt->get_result()->fetch_assoc();

/* Safe fallback values */
$booking_status = $booking['booking_status'] ?? 'pending';
$payment_status = $booking['payment_status'] ?? 'unpaid';
$payment_method = $booking['payment_method'] ?? '—';

$txn = $payment['transaction_id'] ?? '—';
$paid_status = $payment['payment_status'] ?? $payment_status;
$paid_method = $payment['payment_method'] ?? $payment_method;
$paid_date = $payment['payment_date'] ?? '—';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #TX<?= 100 + (int)$booking['id'] ?> | Avestra</title>
    <link rel="stylesheet" href="../styleSheets/user.css">
    <link rel="stylesheet" href="../styleSheets/invoice.css">
    <link rel="stylesheet" href="../styleSheets/user-dark-mode.css">
    <link rel="stylesheet" href="../styleSheets/footer.css">
    <link rel="icon" href="../images/logo.png" type="image/png">
    <script>
        // Intelligent theme application
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const sessionThemeSet = <?= $session_theme_set ? 'true' : 'false' ?>;
            const currentTheme = '<?= $current_theme ?>';
            
            if (sessionThemeSet) {
                localStorage.setItem('theme', currentTheme);
                document.documentElement.setAttribute('data-theme', currentTheme);
            } else if (savedTheme) {
                document.documentElement.setAttribute('data-theme', savedTheme);
            }
        })();
    </script>
</head>
<body class="<?= $session_theme_set ? ($is_dark ? 'dark-mode' : 'light-mode') : '' ?>">
    <script>
        if (!<?= $session_theme_set ? 'true' : 'false' ?>) {
            const theme = localStorage.getItem('theme') || 'light';
            document.body.classList.add(theme + '-mode');
        }
    </script>

<?php include 'nav.php'; ?>

<div class="invoice-container">
    <div class="invoice-header">
        <h2>🧾 Invoice</h2>
        <div class="invoice-id">Invoice ID: #TX<?= 100 + (int)$booking['id'] ?></div>
        <div class="invoice-date">Booked On: <?= htmlspecialchars(date("d M Y, h:i A", strtotime($booking['created_at']))) ?></div>
    </div>

    <div class="invoice-section invoice-details">
        <h3>📌 Booking Details</h3>
        <p><b>Service Type:</b> <?= htmlspecialchars(ucfirst($booking['service_type'])) ?></p>
        <p><b>Service Name:</b> <?= htmlspecialchars($booking['service_name']) ?></p>
        <p><b>Travel Date:</b> <?= htmlspecialchars($booking['travel_date']) ?></p>
        <p><b>Quantity:</b> <?= (int)$booking['quantity'] ?></p>
        <p><b>Total Price:</b> <span style="color:#43a047;font-weight:600;"><?= (float)$booking['total_price'] ?> ৳</span></p>
    </div>

    <div class="invoice-section invoice-status">
        <div>
            <p><b>Booking Status:</b> 
                <span class="status-<?= strtolower($booking_status) ?>">
                    <?php if (strtolower($booking_status) === 'confirmed'): ?>
                        <i class="fas fa-check-circle"></i>
                    <?php elseif (strtolower($booking_status) === 'pending'): ?>
                        <i class="fas fa-clock"></i>
                    <?php else: ?>
                        <i class="fas fa-times-circle"></i>
                    <?php endif; ?>
                    <?= htmlspecialchars(ucfirst($booking_status)) ?>
                </span>
            </p>
        </div>
        <div>
            <p><b>Payment Status:</b> 
                <span class="status-<?= strtolower($payment_status) ?>">
                    <?php if (strtolower($payment_status) === 'paid' || strtolower($payment_status) === 'success'): ?>
                        <i class="fas fa-check-double"></i> Confirmed
                    <?php elseif (strtolower($payment_status) === 'pending'): ?>
                        <i class="fas fa-hourglass-half"></i> Pending
                    <?php else: ?>
                        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars(ucfirst($payment_status)) ?>
                    <?php endif; ?>
                </span>
            </p>
        </div>
    </div>

    <div class="invoice-section">
        <h3>💳 Payment Info</h3>
        <p><b>Payment Method:</b> <?= htmlspecialchars($paid_method) ?></p>
        <p><b>Payment Status:</b> <?= htmlspecialchars(ucfirst($paid_status)) ?></p>
        <p><b>Transaction ID:</b> <?= htmlspecialchars($txn) ?></p>
        <p><b>Payment Date:</b> <?= htmlspecialchars($paid_date) ?></p>
    </div>

    <a href="bookingHistory.php" class="invoice-btn">⬅ Back to Booking History</a>
</div>

</body>
<?php include 'footer.php'; ?>
</html>