<?php
include 'dark_mode.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="navbar">
    <a href="user_dashboard.php" class="nav-left">
        <img src="../images/logo.png" alt="Logo" height="40">
        <span class="brand-name"><span class="highlight">Avestra</span> Travel Agency</span>
    </a>

    <div class="nav-right">
        <a href="user_dashboard.php"      class="<?= ($current_page === 'user_dashboard.php')     ? 'active' : '' ?>">Dashboard</a>
        <a href="start_Booking.php"       class="<?= ($current_page === 'start_Booking.php')      ? 'active' : '' ?>">Tickets</a>
        <a href="find_Hotels.php"         class="<?= ($current_page === 'find_Hotels.php')        ? 'active' : '' ?>">Hotels</a>
        <a href="explore_Tour_Packages.php" class="<?= ($current_page === 'explore_Tour_Packages.php') ? 'active' : '' ?>">Tours</a>
        <a href="bookingHistory.php"      class="<?= ($current_page === 'bookingHistory.php')     ? 'active' : '' ?>">Bookings</a>
        <a href="profile.php"             class="<?= ($current_page === 'profile.php')            ? 'active' : '' ?>">Profile</a>
        <a href="cart.php" class="nav-cart <?= ($current_page === 'cart.php') ? 'active' : '' ?>">
            🛒 Cart <span class="cart-count"><?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?></span>
        </a>
        <a href="logout.php" class="logout">Logout</a>
    </div>
</div>

<script src="../js/theme.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        applyStoredTheme();
    });
</script>
