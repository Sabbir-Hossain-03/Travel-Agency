<?php
include 'session_check.php';
include 'dark_mode.php'; // Include user theme helper
include '../database/dbconnection.php';

$booking_id = (int)($_GET['booking_id'] ?? 0);
$email = $_SESSION['email'];

$stmt = $conn->prepare("SELECT * FROM bookings WHERE id=? AND user_email=?");
$stmt->bind_param("is", $booking_id, $email);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    $_SESSION['payment_error'] = "Invalid or inaccessible booking.";
    header("Location: bookingHistory.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment | Avestra</title>
    <link rel="stylesheet" href="../styleSheets/user.css">
    <link rel="stylesheet" href="../styleSheets/payment.css">
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

<div class="payment-container">
    <h2 class="payment-title">💳 Payment</h2>

    <div class="payment-summary">
        <p><b>Service:</b> <?= htmlspecialchars($booking['service_name']) ?></p>
        <p><b>Total:</b> <?= htmlspecialchars($booking['total_price']) ?> ৳</p>
        <p><b>Status:</b> <?= htmlspecialchars($booking['payment_status']) ?></p>
    </div>

    <form action="processPayment.php" method="post" class="payment-form" id="paymentForm">
        <input type="hidden" name="booking_id" value="<?= $booking_id ?>">

        <label><input type="radio" name="payment_method" value="bkash" required> bKash</label><br>
        <label><input type="radio" name="payment_method" value="nagad"> Nagad</label><br>
        <label><input type="radio" name="payment_method" value="card"> Card</label><br><br>

        <button type="submit">Pay Now</button>
    </form>
</div>

<!-- Processing Overlay -->
<div id="processingOverlay" class="processing-overlay">
    <div class="processing-card">
        <div class="processing-icon">💳</div>
        <h3 class="processing-title">Waiting for your payment...</h3>
        <p class="processing-subtitle">Payment verification in progress. Please do not refresh.</p>
        
        <div class="processing-progress-wrapper">
             <div class="progress-container">
                <div id="progressBar" class="progress-bar"></div>
            </div>
            <div class="countdown-display">
                <span id="countdown">10</span> seconds remaining
            </div>
        </div>

        <button type="button" id="cancelPayment" class="cancel-payment-btn">Cancel Payment</button>
    </div>
</div>

<script>
let paymentInterval;
let redirectTimeout;

document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const overlay = document.getElementById('processingOverlay');
    const progressBar = document.getElementById('progressBar');
    const countdownEl = document.getElementById('countdown');
    
    // Show overlay
    overlay.classList.add('active');
    
    // Reset and start progress bar animation (decrease from 100% to 0%)
    progressBar.style.transition = 'none';
    progressBar.style.width = '100%';
    
    // Force reflow
    progressBar.offsetHeight;
    
    progressBar.style.transition = 'width 10s linear';
    progressBar.style.width = '0%';
    
    let secondsLeft = 10;
    countdownEl.textContent = secondsLeft;
    
    paymentInterval = setInterval(() => {
        secondsLeft--;
        countdownEl.textContent = secondsLeft;
        if (secondsLeft <= 0) {
            clearInterval(paymentInterval);
        }
    }, 1000);

    // Initial AJAX submission
    const formData = new FormData(form);
    fetch('processPayment.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        // We wait for the full 10 seconds before redirecting
        redirectTimeout = setTimeout(() => {
            window.location.href = 'bookingHistory.php';
        }, 10000);
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
        hideOverlay();
    });
});

function hideOverlay() {
    const overlay = document.getElementById('processingOverlay');
    overlay.classList.remove('active');
    clearInterval(paymentInterval);
    clearTimeout(redirectTimeout);
}

document.getElementById('cancelPayment').addEventListener('click', function() {
    if(confirm('Are you sure you want to cancel the payment process?')) {
        hideOverlay();
    }
});
</script>

</body>
<?php include 'footer.php'; ?>
</html>