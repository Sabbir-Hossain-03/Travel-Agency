<?php
include 'session_check.php';
include 'dark_mode.php';
include '../database/dbconnection.php';

$email = $_SESSION['email'];

$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count
$count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM bookings WHERE user_email = ?");
$count_stmt->bind_param("s", $email);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_bookings = $count_result->fetch_assoc()['total'];
$total_pages = $total_bookings > 0 ? ceil($total_bookings / $limit) : 1;

// Fetch
$stmt = $conn->prepare("
    SELECT id, service_name, service_type, travel_date, quantity, total_price,
           booking_status, payment_status, created_at
    FROM bookings
    WHERE user_email = ?
    ORDER BY created_at DESC
    LIMIT ? OFFSET ?
");
$stmt->bind_param("sii", $email, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Booking History</title>
    <link rel="stylesheet" href="../styleSheets/user.css">
    <link rel="stylesheet" href="../styleSheets/bookingHistory.css">
    <link rel="stylesheet" href="../styleSheets/footer.css">
    <link rel="icon" href="../images/logo.png" type="image/png">
    <link rel="stylesheet" href="../node_modules/@fortawesome/fontawesome-free/css/all.min.css"/>
    <link rel="stylesheet" href="../styleSheets/user-dark-mode.css?v=<?php echo time(); ?>">
    <style>
        body {
            background-color: #f7fafc;
        }
        .bh-hero {
            background: linear-gradient(135deg, #1A365D 0%, #2B6CB0 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
            margin-bottom: 30px;
        }
        .bh-hero-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .bh-hero-sub {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        .bh-container {
            max-width: 1100px;
            margin: 0 auto 60px;
            padding: 0 20px;
        }
        .bh-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }
        .bh-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        .bh-table th {
            background-color: #f8fafc;
            color: #4a5568;
            font-weight: 600;
            padding: 16px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 2px solid #e2e8f0;
        }
        .bh-table td {
            padding: 16px;
            color: #2d3748;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .bh-table tr:last-child td {
            border-bottom: none;
        }
        .bh-table tr:hover {
            background-color: #f8fafc;
        }
        .bh-status {
            padding: 4px 12px;
            border-radius: 100px;
            font-size: 0.8rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .bh-status i {
            font-size: 0.85rem;
        }
        .bh-status-confirmed, .bh-status-completed, .bh-status-paid, .bh-status-success {
            background-color: #ecfdf5;
            color: #059669;
            border: 1px solid #d1fae5;
        }
        .bh-status-pending {
            background-color: #fffbeb;
            color: #d97706;
            border: 1px solid #fef3c7;
        }
        .bh-status-cancelled, .bh-status-failed, .bh-status-rejected, .bh-status-unpaid {
            background-color: #fef2f2;
            color: #dc2626;
            border: 1px solid #fee2e2;
        }
        .bh-view-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            background-color: #ebf4ff;
            color: #3182ce;
            text-decoration: none;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        .bh-view-btn:hover {
            background-color: #3182ce;
            color: white;
        }
        .bh-pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 30px;
            align-items: center;
        }
        .bh-page-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            padding: 0 12px;
            border-radius: 8px;
            background: white;
            color: #4a5568;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: all 0.2s;
        }
        .bh-page-btn:hover:not(.disabled):not(.active) {
            background: #f7fafc;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .bh-page-btn.active {
            background: #3182ce;
            color: white;
            box-shadow: 0 4px 10px rgba(49,130,206,0.3);
        }
        .bh-page-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            box-shadow: none;
            background: #f7fafc;
        }
        .bh-empty {
            text-align: center;
            padding: 60px 20px;
            color: #718096;
        }
        .bh-empty i {
            font-size: 4rem;
            color: #cbd5e0;
            margin-bottom: 20px;
        }
        .bh-empty h3 {
            font-size: 1.5rem;
            color: #2d3748;
            margin-bottom: 10px;
        }
    </style>
</head>
<body class="<?= $session_theme_set ? ($is_dark ? 'dark-mode' : 'light-mode') : '' ?>">
    <script>
        // Fallback for session-less theme application
        if (!<?= $session_theme_set ? 'true' : 'false' ?>) {
            const theme = localStorage.getItem('theme') || 'light';
            document.body.classList.add(theme + '-mode');
        }
    </script>

<?php include 'nav.php'; ?>

<div class="bh-hero">
    <h1 class="bh-hero-title"><i class="fas fa-history"></i> My Booking History</h1>
    <p class="bh-hero-sub">Review and manage your past and upcoming travels</p>
</div>

<div class="bh-container">
    <?php if ($result->num_rows === 0): ?>
        <div class="bh-card bh-empty">
            <i class="fas fa-folder-open"></i>
            <h3>No Bookings Found</h3>
            <p>You haven't made any bookings yet. Start exploring our packages today!</p>
        </div>
    <?php else: ?>
        
        <!-- Meta info -->
        <div style="margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center; color: #718096; font-size: 0.95rem; font-weight: 500;">
            <span>Total: <strong><?= $total_bookings ?></strong> booking<?= $total_bookings !== 1 ? 's' : '' ?></span>
            <span>Page <?= $page ?> of <?= $total_pages ?></span>
        </div>

        <div class="bh-card">
            <div style="overflow-x: auto;">
                <table class="bh-table">
                    <tr>
                        <th>#</th>
                        <th>Service</th>
                        <th>Type</th>
                        <th>Travel Date</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Booking</th>
                        <th>Payment</th>
                        <th>Booked On</th>
                        <th>Action</th>
                    </tr>
                    <?php 
                    $i = $offset + 1; 
                    while ($row = $result->fetch_assoc()): 
                        // Status styling logic
                        $book_status = strtolower($row['booking_status']);
                        $pay_status = strtolower($row['payment_status']);
                    ?>
                        <tr>
                            <td><span style="color: #a0aec0; font-weight: 600;">TX<?= 100 + (int)$row['id'] ?></span></td>
                            <td style="font-weight: 600;"><?= htmlspecialchars($row['service_name']) ?></td>
                            <td>
                                <?php
                                    $icon = 'fa-suitcase';
                                    if ($row['service_type'] === 'ticket') $icon = 'fa-ticket-alt';
                                    if ($row['service_type'] === 'hotel') $icon = 'fa-bed';
                                    if ($row['service_type'] === 'tour') $icon = 'fa-map-marked-alt';
                                ?>
                                <i class="fas <?= $icon ?>" style="color: #a0aec0; margin-right: 4px;"></i> 
                                <?= ucfirst($row['service_type']) ?>
                            </td>
                            <td style="color: #4a5568;"><?= htmlspecialchars($row['travel_date']) ?></td>
                            <td><?= (int)$row['quantity'] ?></td>
                            <td style="font-weight: 600; color: #2d3748;"><?= number_format((float)$row['total_price'], 0) ?> ৳</td>
                            <td>
                                <span class="bh-status bh-status-<?= $book_status ?>">
                                    <?php if ($book_status === 'confirmed'): ?>
                                        <i class="fas fa-check-circle"></i>
                                    <?php elseif ($book_status === 'pending'): ?>
                                        <i class="fas fa-clock"></i>
                                    <?php elseif ($book_status === 'rejected' || $book_status === 'cancelled'): ?>
                                        <i class="fas fa-times-circle"></i>
                                    <?php endif; ?>
                                    <?= ucfirst($book_status) ?>
                                </span>
                            </td>
                            <td>
                                <span class="bh-status bh-status-<?= $pay_status ?>">
                                    <?php if ($pay_status === 'paid' || $pay_status === 'success'): ?>
                                        <i class="fas fa-check-double"></i> Confirmed
                                    <?php elseif ($pay_status === 'pending'): ?>
                                        <i class="fas fa-hourglass-half"></i> Pending
                                    <?php else: ?>
                                        <i class="fas fa-exclamation-circle"></i> <?= ucfirst($pay_status) ?>
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td style="color: #718096; font-size: 0.9rem;"><?= date("d M Y", strtotime($row['created_at'])) ?></td>
                            <td>
                                <a href="invoice.php?id=<?= (int)$row['id'] ?>" class="bh-view-btn">
                                    <i class="fas fa-file-invoice"></i> View
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <nav class="bh-pagination" aria-label="Pagination">
            <?php if ($page > 1): ?>
                <a class="bh-page-btn" href="bookingHistory.php?page=<?= $page - 1 ?>">‹ Prev</a>
            <?php else: ?>
                <span class="bh-page-btn disabled">‹ Prev</span>
            <?php endif; ?>

            <?php
            $start = max(1, $page - 2);
            $end   = min($total_pages, $page + 2);
            for ($p = $start; $p <= $end; $p++): ?>
                <?php if ($p === $page): ?>
                    <span class="bh-page-btn active"><?= $p ?></span>
                <?php else: ?>
                    <a class="bh-page-btn" href="bookingHistory.php?page=<?= $p ?>"><?= $p ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a class="bh-page-btn" href="bookingHistory.php?page=<?= $page + 1 ?>">Next ›</a>
            <?php else: ?>
                <span class="bh-page-btn disabled">Next ›</span>
            <?php endif; ?>
        </nav>
        <?php endif; ?>

    <?php endif; ?>
</div>

</body>
<?php include 'footer.php'; ?>
</html>