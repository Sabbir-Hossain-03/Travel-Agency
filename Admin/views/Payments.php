<?php
include('dark_mode.php');
include('../database/dbconnection.php');
include('../database/Payments.php');
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
$err = isset($_GET['err']) ? $_GET['err'] : '';
if (!isset($msg)) $msg = '';
if (!isset($err)) $err = '';

// Pagination variables
$limit = 20;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$search = isset($_POST['search']) ? trim($_POST['search']) : (isset($_GET['search']) ? trim($_GET['search']) : '');

if ($search !== '') {
    $resultData = searchPayments($conn, $search, $limit, $offset);
} else {
    $resultData = getAllPayments($conn, $limit, $offset);
}

$payments = $resultData['payments'];
$totalRecords = $resultData['total'];
$totalPages = ceil($totalRecords / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments - Avestra Travel Agency</title>
    <link rel="stylesheet" href="../styleSheets/Payment.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../styleSheets/Admin.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../styleSheets/dark-mode.css?v=<?php echo time(); ?>">
    <script>
        localStorage.setItem('theme', '<?= $current_theme ?>');
        document.documentElement.setAttribute('data-theme', '<?= $current_theme ?>');
    </script>
    <link rel="icon" href="../images/logo.png" type="image/png">
    <link rel="stylesheet" href="../node_modules/@fortawesome/fontawesome-free/css/all.min.css"/>
    <style>
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 30px;
        }
        .pagination a, .pagination span {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.95em;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }
        .pagination a {
            background: #ffffff;
            color: #475569;
            border: 1px solid #e2e8f0;
        }
        .pagination a:hover {
            background: #f8fafc;
            color: #0d9488;
            border-color: #0d9488;
        }
        .pagination .active {
            background: #0d9488;
            color: #ffffff;
            border: 1px solid #0d9488;
        }
        .pagination .disabled {
            background: #f1f5f9;
            color: #94a3b8;
            border: 1px solid #e2e8f0;
            cursor: not-allowed;
        }
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
                    <li><a href="ManageBookings.php">Manage Bookings</a></li>
                    <li><a href="Payments.php" class="active">Payments</a></li>
                    <li><a href="Reports.php">Reports</a></li>
                    <li><a href="Settings.php">Settings</a></li>
                    <li><a href="MyProfile.php">My Profile</a></li>
                    <li><a href="homePage.php">Logout</a></li>
                </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header class="admin-header">
            <h1>Payments</h1>
        </header>

        <section class="admin-section">

            <?php if (isset($_SESSION['payment_msg'])): ?>
                <div class="alert-<?= $_SESSION['payment_msg_type'] === 'success' ? 'success' : 'error' ?>" style="padding:16px 20px; border-radius:8px; margin-bottom:24px; font-weight:600; display:block; opacity:1; background: <?= $_SESSION['payment_msg_type'] === 'success' ? '#d4edda' : '#f8d7da' ?>; color: <?= $_SESSION['payment_msg_type'] === 'success' ? '#155724' : '#721c24' ?>;">
                    <i class="fa-solid <?= $_SESSION['payment_msg_type'] === 'success' ? 'fa-check-circle' : 'fa-circle-xmark' ?>" style="margin-right:8px; font-size:1.2em;"></i>
                    <?= htmlspecialchars($_SESSION['payment_msg']) ?>
                </div>
                <?php 
                unset($_SESSION['payment_msg']); 
                unset($_SESSION['payment_msg_type']); 
                ?>
            <?php endif; ?>

            <div class="admin-card">
                <h3>Customer Payment History</h3>
                <form method="post" action="Payments.php" class="payment-search-bar" style="margin-bottom: 18px; display: flex; gap: 12px; align-items: center;">
                    <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Search Email or ID..." class="search-input" style="padding: 10px 16px; border-radius: 7px; border: 1px solid #dbe6f3; font-size: 1em; min-width: 220px;">
                    <button type="submit" class="search-btn" style="background: #0ecb81; color: #fff; border: none; border-radius: 7px; padding: 10px 28px; font-weight: 600; font-size: 1em; cursor: pointer;"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
                    <?php if ($search): ?>
                        <a href="Payments.php" class="reset-btn" style="background: #f87171; color: #fff; border: none; border-radius: 7px; padding: 10px 18px; font-weight: 600; font-size: 1em; text-decoration: none; margin-left: 4px;">Reset</a>
                    <?php endif; ?>
                </form>

                <div class="table-wrapper">
                    <table class="payment-table">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>User Email</th>
                                <th>Amount</th>
                                <th>Payment Method</th>
                                <th>Transaction ID</th>
                                <th>Status</th>
                                <th>Payment Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($payments)): ?>
                            <tr>
                                <td colspan="8" style="text-align:center;">No payments found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($payments as $p): ?>
                                <tr>
                                    <td class="payment-id">TX<?= 100 + (int)$p['booking_id'] ?></td>
                                    <td><?= htmlspecialchars($p['user_email']) ?></td>
                                    <td><b>$<?= number_format($p['amount'], 2) ?></b></td>
                                    <td><?= htmlspecialchars($p['payment_method']) ?></td>
                                    <td class="payment-id"><?= htmlspecialchars($p['transaction_id']) ?></td>
                                    <td>
                                        <span class="status-badge <?= strtolower(htmlspecialchars($p['payment_status'])) ?>">
                                            <?php
                                                $display_status = strtolower($p['payment_status']);
                                                if ($display_status === 'paid' || $display_status === 'success') {
                                                    echo 'Success';
                                                } else {
                                                    echo htmlspecialchars(ucfirst($display_status));
                                                }
                                            ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($p['payment_date']) ?></td>
                                    <td style="display: flex; gap: 8px;">
                                        <?php if (strtolower($p['payment_status']) === 'pending'): ?>
                                            <form method="POST" action="../controller/UpdatePaymentStatus.php" style="display:inline;">
                                                <input type="hidden" name="payment_id" value="<?= htmlspecialchars($p['id']) ?>">
                                                <input type="hidden" name="new_status" value="success">
                                                <button type="submit" class="action-btn accept-btn" style="background:#0ecb81; color:#fff; border:none; padding:6px 12px; border-radius:6px; cursor:pointer; font-weight:600;"><i class="fa-solid fa-check"></i></button>
                                            </form>
                                            <form method="POST" action="../controller/UpdatePaymentStatus.php" style="display:inline;">
                                                <input type="hidden" name="payment_id" value="<?= htmlspecialchars($p['id']) ?>">
                                                <input type="hidden" name="new_status" value="rejected">
                                                <button type="submit" class="action-btn reject-btn" style="background:#f87171; color:#fff; border:none; padding:6px 12px; border-radius:6px; cursor:pointer; font-weight:600;"><i class="fa-solid fa-xmark"></i></button>
                                            </form>
                                        <?php elseif (strtolower($p['payment_status']) === 'success' || strtolower($p['payment_status']) === 'paid'): ?>
                                            <button disabled class="action-btn accept-btn" style="background:#0ecb81; color:#fff; border:none; padding:6px 12px; border-radius:6px; cursor:not-allowed; opacity: 0.5; font-weight:600;"><i class="fa-solid fa-check"></i></button>
                                        <?php else: ?>
                                            <button disabled class="action-btn reject-btn" style="background:#f87171; color:#fff; border:none; padding:6px 12px; border-radius:6px; cursor:not-allowed; opacity: 0.5; font-weight:600;"><i class="fa-solid fa-xmark"></i></button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php 
                    $searchParam = $search ? '&search=' . urlencode($search) : ''; 
                    
                    // Previous Button
                    if ($page > 1): ?>
                        <a href="Payments.php?page=<?= $page - 1 ?><?= $searchParam ?>">&laquo; Previous</a>
                    <?php else: ?>
                        <span class="disabled">&laquo; Previous</span>
                    <?php endif; ?>

                    <?php 
                    // Page Numbers
                    for ($i = 1; $i <= $totalPages; $i++): 
                        if ($i == $page): ?>
                            <span class="active"><?= $i ?></span>
                        <?php else: ?>
                            <a href="Payments.php?page=<?= $i ?><?= $searchParam ?>"><?= $i ?></a>
                        <?php endif;
                    endfor; 
                    ?>

                    <?php 
                    // Next Button
                    if ($page < $totalPages): ?>
                        <a href="Payments.php?page=<?= $page + 1 ?><?= $searchParam ?>">Next &raquo;</a>
                    <?php else: ?>
                        <span class="disabled">Next &raquo;</span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

            </div>
        </section>
    </main>
</div>
</body>
</html>
