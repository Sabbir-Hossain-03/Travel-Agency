<?php
include('dark_mode.php');
include('../database/dbconnection.php');
include('../database/ReportsData.php');

// Security check: Only admins can view this page
if (!isset($_SESSION['admin_email'])) {
    header("Location: loginPage.php");
    exit();
}

// Fetch basic statistics
$stats = getReportStats($conn);

// Monthly Revenue Data
$monthlyRevenue = getMonthlyRevenue($conn, 6);

// Service Breakdown
$serviceRevenue = getServiceRevenue($conn);

// Most Popular Services
$popularServices = getMostPopularServices($conn, 5);

// Recent Transactions
$recentBookings = getRecentTransactions($conn, 5);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports &amp; Analytics Dashboard</title>
    <link rel="stylesheet" href="../styleSheets/Reports.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../styleSheets/Admin.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../styleSheets/dark-mode.css?v=<?php echo time(); ?>">
    <script>
        localStorage.setItem('theme', '<?= $current_theme ?>');
        document.documentElement.setAttribute('data-theme', '<?= $current_theme ?>');
    </script>
    <link rel="icon" href="../images/logo.png" type="image/png">
</head>
<body class="<?= $is_dark ? 'dark-mode' : '' ?>">
    <div class="admin-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div style="padding: 24px 32px;">
                <div style="text-align: center; margin-bottom: 16px;">
                    <img src="../images/logo.png" alt="Avestra Logo" style="width: 60px; height: auto;">
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
                    <li><a href="Payments.php">Payments</a></li>
                    <li><a href="Reports.php" class="active">Reports</a></li>
                    <li><a href="Settings.php">Settings</a></li>
                    <li><a href="MyProfile.php">My Profile</a></li>
                    <li><a href="homePage.php">Logout</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <header class="admin-header">
                <h1>Reports & Analytics</h1>
                <p class="subtitle">Overview of your travel agency's performance</p>
            </header>
            
            <section class="admin-section">
                <!-- Summary KPI Cards -->
                <div class="report-stats">
                    <div class="stat-card">
                        <div class="stat-icon rev-icon">💰</div>
                        <div class="stat-info">
                            <span class="stat-number"><?= number_format($stats['total_revenue'], 0) ?> ৳</span>
                            <span class="stat-label">Total Revenue</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon bk-icon">🎫</div>
                        <div class="stat-info">
                            <span class="stat-number"><?= number_format($stats['total_bookings']) ?></span>
                            <span class="stat-label">Total Bookings</span>
                        </div>
                    </div>
                    <?php if ($stats['pending_bookings'] > 0 || $stats['pending_payments'] > 0): ?>
                    <div class="stat-card" style="border-left: 4px solid #f59e0b;">
                        <div class="stat-icon" style="background: #fff7ed; color: #f59e0b;">⚠️</div>
                        <div class="stat-info">
                            <span class="stat-number"><?= $stats['pending_bookings'] + $stats['pending_payments'] ?></span>
                            <span class="stat-label">Pending Actions</span>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="stat-card">
                        <div class="stat-icon us-icon">👥</div>
                        <div class="stat-info">
                            <span class="stat-number"><?= number_format($stats['total_users']) ?></span>
                            <span class="stat-label">Active Users</span>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="stat-card">
                        <div class="stat-icon tk-icon">🛣️</div>
                        <div class="stat-info">
                            <span class="stat-number"><?= number_format($stats['total_tickets']) ?></span>
                            <span class="stat-label">Live Routes</span>
                        </div>
                    </div>
                </div>

                <div class="report-grid">
                    <!-- Monthly Revenue Table -->
                    <div class="report-card">
                        <h3>Monthly Revenue Trends</h3>
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th style="text-align: right;">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($monthlyRevenue)): ?>
                                    <tr><td colspan="2" style="text-align: center; color: #64748b;">No revenue data available.</td></tr>
                                <?php else: ?>
                                    <?php foreach($monthlyRevenue as $m): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($m['month']) ?></strong></td>
                                        <td style="text-align: right; color: #0f172a; font-weight: 600;"><?= number_format($m['revenue'], 0) ?> ৳</td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Service Breakdown -->
                    <div class="report-card">
                        <h3>Revenue by Service</h3>
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th>Service Type</th>
                                    <th style="text-align: right;">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($serviceRevenue)): ?>
                                    <tr><td colspan="2" style="text-align: center; color: #64748b;">No service data available.</td></tr>
                                <?php else: ?>
                                    <?php foreach($serviceRevenue as $s): ?>
                                    <tr>
                                        <td><span class="booking-type" style="padding: 4px 10px; border-radius: 6px; background: #f1f5f9; color: #475569; font-size: 0.8em;"><?= strtoupper($s['service_type']) ?></span></td>
                                        <td style="text-align: right; font-weight: 600;"><?= number_format($s['revenue'], 0) ?> ৳</td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Most Popular Services -->
                    <div class="report-card">
                        <h3>Most Popular Services</h3>
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th>Service Name</th>
                                    <th style="text-align: right;">Bookings</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($popularServices)): ?>
                                    <tr><td colspan="2" style="text-align: center; color: #64748b;">No popular items yet.</td></tr>
                                <?php else: ?>
                                    <?php foreach($popularServices as $p): ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight: 500; color: #1e293b;"><?= htmlspecialchars($p['service_name']) ?></div>
                                            <small style="color: #64748b; font-size: 0.8em;"><?= ucfirst($p['service_type']) ?></small>
                                        </td>
                                        <td style="text-align: right; font-weight: 600;"><?= number_format($p['booking_count']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Recent Transactions -->
                    <div class="report-card">
                        <h3>Recent Transactions</h3>
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Status</th>
                                    <th style="text-align: right;">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($recentBookings)): ?>
                                    <tr><td colspan="3" style="text-align: center; color: #64748b;">No recent transactions.</td></tr>
                                <?php else: ?>
                                    <?php foreach($recentBookings as $b): ?>
                                    <tr>
                                        <td style="color: #3b82f6; font-weight: 500;">TX<?= 100 + (int)$b['booking_id'] ?></td>
                                        <td>
                                            <span class="status-badge <?= strtolower($b['payment_status']) ?>" style="padding: 4px 10px; border-radius: 20px; font-size: 0.75em; font-weight: 700;">
                                                <?= ucfirst($b['payment_status']) ?>
                                            </span>
                                        </td>
                                        <td style="text-align: right; font-weight: 600;"><?= number_format($b['amount'], 0) ?> ৳</td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </section>
        </main>
    </div>

    <!-- Theme Handling Scripts -->
    <script src="../js/theme.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            applyStoredTheme();
        });
    </script>
</body>
</html>
