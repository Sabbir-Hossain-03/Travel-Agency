<?php
include('dark_mode.php');
include('../database/dbconnection.php');
include('../database/BookingsData.php');

$admin_email = $_SESSION['admin_email'] ?? '';
if (!$admin_email) {
    header("Location: loginPage.php");
    exit();
}

// Pagination
$limit = 20;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$search = isset($_POST['search']) ? trim($_POST['search']) : (isset($_GET['search']) ? trim($_GET['search']) : '');

if ($search !== '') {
    $resultData = searchBookings($conn, $search, $limit, $offset);
} else {
    $resultData = getAllBookings($conn, $limit, $offset);
}

$bookings = $resultData['bookings'];
$totalRecords = $resultData['total'];
$totalPages = ceil($totalRecords / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - Avestra Admin</title>
    <link rel="stylesheet" href="../styleSheets/Payment.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../styleSheets/Admin.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../styleSheets/dark-mode.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../node_modules/@fortawesome/fontawesome-free/css/all.min.css"/>
    <script>
        localStorage.setItem('theme', '<?= $current_theme ?>');
        document.documentElement.setAttribute('data-theme', '<?= $current_theme ?>');
    </script>
    <link rel="icon" href="../images/logo.png" type="image/png">
    <style>
        .status-badge.confirmed { background: #dcfce7; color: #166534; }
        .status-badge.pending { background: #fef9c3; color: #854d0e; }
        .status-badge.rejected { background: #fee2e2; color: #991b1b; }
        .booking-type { font-weight: 600; text-transform: uppercase; font-size: 0.8rem; color: #64748b; }
        .pagination { display: flex; justify-content: center; gap: 8px; margin-top: 30px; }
        .pagination a, .pagination span { padding: 8px 16px; border-radius: 8px; font-weight: 600; text-decoration: none; }
        .pagination a { background: white; color: #475569; border: 1px solid #e2e8f0; }
        .pagination .active { background: #2563eb; color: white; }
    </style>
</head>
<body class="<?= $is_dark ? 'dark-mode' : '' ?>">
<div class="admin-container">
    <aside class="sidebar">
        <div style="padding: 24px 32px;">
            <div style="text-align: center; margin-bottom: 16px;">
                <img src="../images/logo.png" alt="Avestra Logo" style="width: 60px;">
            </div>
            <h2 class="sidebar-title">Admin Panel</h2>
        </div>
        <nav>
            <ul class="sidebar-menu">
                <li><a href="Admin.php">Dashboard</a></li>
                <li><a href="ManageUsers.php">Manage Users</a></li>
                <li><a href="ManageTickets.php">Tickets</a></li>
                <li><a href="ManageHotels.php">Hotels</a></li>
                <li><a href="ManageTours.php">Tours</a></li>
                <li><a href="ManageBookings.php" class="active">Manage Bookings</a></li>
                <li><a href="Payments.php">Payments</a></li>
                <li><a href="Reports.php">Reports</a></li>
                <li><a href="Settings.php">Settings</a></li>
                <li><a href="MyProfile.php">My Profile</a></li>
                <li><a href="homePage.php">Logout</a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header class="admin-header">
            <h1>Manage Bookings</h1>
            <p style="color: #64748b;">Review and approve/reject customer travel requests.</p>
        </header>

        <section class="admin-section">
            <?php if (isset($_SESSION['booking_msg'])): ?>
                <div style="padding:15px; background:#d4edda; color:#155724; border-radius:8px; margin-bottom:20px;">
                    <?= htmlspecialchars($_SESSION['booking_msg']) ?>
                </div>
                <?php unset($_SESSION['booking_msg']); ?>
            <?php endif; ?>

            <div class="admin-card">
                <form method="post" class="payment-search-bar" style="margin-bottom: 20px; display: flex; gap: 10px;">
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search Email or ID..." style="flex: 1; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                    <button type="submit" style="background:#2563eb; color:white; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-weight:600;"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
                </form>

                <div class="table-wrapper">
                    <table class="payment-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Service</th>
                                <th>Date</th>
                                <th>Qty</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $b): ?>
                                <tr>
                                    <td>TX<?= 100 + (int)$b['id'] ?></td>
                                    <td><?= htmlspecialchars($b['user_email']) ?></td>
                                    <td>
                                        <div class="booking-type"><?= htmlspecialchars($b['service_type']) ?></div>
                                        <?= htmlspecialchars($b['service_name']) ?>
                                    </td>
                                    <td><?= htmlspecialchars($b['travel_date']) ?></td>
                                    <td><?= $b['quantity'] ?></td>
                                    <td><b><?= number_format($b['total_price'], 0) ?> ৳</b></td>
                                    <td>
                                        <span class="status-badge <?= strtolower($b['booking_status']) ?>">
                                            <?= ucfirst($b['booking_status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($b['booking_status'] === 'pending'): ?>
                                            <form action="../controller/UpdateBookingStatus.php" method="POST" style="display: flex; gap: 5px;">
                                                <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                                <button type="submit" name="status" value="confirmed" title="Confirm" style="background:#22c55e; color:white; border:none; padding:5px 10px; border-radius:5px; cursor:pointer;"><i class="fas fa-check"></i></button>
                                                <button type="submit" name="status" value="rejected" title="Reject" style="background:#ef4444; color:white; border:none; padding:5px 10px; border-radius:5px; cursor:pointer;"><i class="fas fa-times"></i></button>
                                            </form>
                                        <?php else: ?>
                                            <span style="font-size: 0.8rem; color: #94a3b8;">Processed</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>
</div>
</body>
</html>
