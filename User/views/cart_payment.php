<?php
include 'session_check.php';
include 'dark_mode.php';
include '../database/dbconnection.php';

$pending_ids = $_SESSION['pending_payment_ids'] ?? [];
$email = $_SESSION['email'];

if (empty($pending_ids)) {
    header("Location: bookingHistory.php");
    exit();
}

$placeholders = implode(',', array_fill(0, count($pending_ids), '?'));
$types = str_repeat('i', count($pending_ids)) . 's';
$params = array_merge($pending_ids, [$email]);

$stmt = $conn->prepare("SELECT * FROM bookings WHERE id IN ($placeholders) AND user_email=?");
$stmt->bind_param($types, ...$params);
$stmt->execute();
$bookings = $stmt->get_result();

$total_cart_price = 0;
$items = [];
while ($row = $bookings->fetch_assoc()) {
    $items[] = $row;
    $total_cart_price += $row['total_price'];
}

if (empty($items)) {
    header("Location: bookingHistory.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cart Payment | Avestra</title>
    <link rel="stylesheet" href="../styleSheets/user.css">
    <link rel="stylesheet" href="../styleSheets/payment.css">
    <link rel="stylesheet" href="../styleSheets/user-dark-mode.css">
    <link rel="stylesheet" href="../styleSheets/footer.css">
    <link rel="icon" href="../images/logo.png" type="image/png">
    <style>
        .summary-list {
            background: rgba(0,0,0,0.02);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px dashed rgba(0,0,0,0.1);
        }
        .summary-item:last-child {
            border-bottom: none;
        }
        .total-pay {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
            text-align: right;
            margin-top: 10px;
        }
        body.dark-mode .summary-list {
            background: rgba(255,255,255,0.05);
        }
    </style>
</head>
<body class="<?= $session_theme_set ? ($is_dark ? 'dark-mode' : 'light-mode') : '' ?>">
<?php include 'nav.php'; ?>

<div class="payment-container" style="max-width: 600px;">
    <h2 class="payment-title">💳 Complete Your Booking</h2>

    <div class="summary-list">
        <?php foreach ($items as $item): ?>
            <div class="summary-item">
                <span><?= htmlspecialchars($item['service_name']) ?> (x<?= $item['quantity'] ?>)</span>
                <span><?= number_format($item['total_price'], 0) ?> ৳</span>
            </div>
        <?php endforeach; ?>
        <div class="total-pay">
            Total: <?= number_format($total_cart_price, 0) ?> ৳
        </div>
    </div>

    <form action="process_cart_payment.php" method="post" class="payment-form" id="cartPaymentForm">
        <label><input type="radio" name="payment_method" value="bkash" required> bKash</label><br>
        <label><input type="radio" name="payment_method" value="nagad"> Nagad</label><br>
        <label><input type="radio" name="payment_method" value="card"> Card</label><br><br>

        <button type="submit" style="width: 100%; padding: 18px; font-size: 1.2rem;">Pay Now</button>
    </form>
</div>

<!-- Reusing Processing Overlay from payment.php -->
<div id="processingOverlay" class="processing-overlay">
    <div class="processing-card">
        <div class="processing-icon">💳</div>
        <h3 class="processing-title">Processing Cart Payment...</h3>
        <p class="processing-subtitle">Finalizing your adventure. Please do not refresh.</p>
        <div class="progress-container"><div id="progressBar" class="progress-bar"></div></div>
        <div class="countdown-display"><span id="countdown">10</span> seconds remaining</div>
    </div>
</div>

<script>
document.getElementById('cartPaymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const overlay = document.getElementById('processingOverlay');
    const progressBar = document.getElementById('progressBar');
    const countdownEl = document.getElementById('countdown');
    overlay.classList.add('active');
    progressBar.style.transition = 'width 10s linear';
    progressBar.style.width = '0%';
    let secondsLeft = 10;
    const interval = setInterval(() => {
        secondsLeft--;
        countdownEl.textContent = secondsLeft;
        if (secondsLeft <= 0) clearInterval(interval);
    }, 1000);

    const formData = new FormData(this);
    fetch('process_cart_payment.php', { method: 'POST', body: formData })
    .then(() => {
        setTimeout(() => { window.location.href = 'bookingHistory.php'; }, 10000);
    });
});
</script>

</body>
<?php include 'footer.php'; ?>
</html>
