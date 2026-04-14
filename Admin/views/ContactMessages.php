<?php
include('dark_mode.php');
include('../database/dbconnection.php');
include('../database/ContactMessagesData.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages - Avestra Travel Agency</title>
    <link rel="stylesheet" href="../styleSheets/Admin.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../styleSheets/ContactMessages.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../styleSheets/dark-mode.css?v=<?php echo time(); ?>">
    <script>
        localStorage.setItem('theme', '<?= $current_theme ?>');
        document.documentElement.setAttribute('data-theme', '<?= $current_theme ?>');
    </script>
    <link rel="icon" href="../images/logo.png" type="image/png">
</head>
<body class="<?= $is_dark ? 'dark-mode' : '' ?>">
    <div class="admin-container">
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
                <h1>📩 All Contact Messages</h1>
            </header>

            <?php if (isset($_SESSION['delete_success'])): ?>
                <div class="alert-success">
                    <?php echo htmlspecialchars($_SESSION['delete_success']); unset($_SESSION['delete_success']); ?>
                </div>
            <?php endif; ?>

            <section class="admin-section">
                <div class="admin-card">
                    <div class="pagination-info">
                        <p>Showing <?php echo $showing_from; ?>–<?php echo $showing_to; ?> of <?php echo $total_messages; ?> messages</p>
                    </div>

                    <?php if (!empty($messages)): ?>
                        <div>
                            <?php foreach ($messages as $msg): ?>
                                <div class="message-card">
                                    <div class="message-header">
                                        <div class="message-user-info">
                                            <div class="message-avatar">
                                                <?php echo strtoupper(substr($msg['name'], 0, 1)); ?>
                                            </div>
                                            <div class="message-user-details">
                                                <h3><?php echo htmlspecialchars($msg['name']); ?></h3>
                                                <p>✉️ <?php echo htmlspecialchars($msg['email']); ?></p>
                                            </div>
                                        </div>
                                        <div class="message-actions">
                                            <span class="message-date-badge">
                                                🕐 <?php echo date('M d, Y h:i A', strtotime($msg['date'])); ?>
                                            </span>
                                            <div>
                                                <button onclick="viewMessage(<?php echo $msg['id']; ?>, '<?php echo htmlspecialchars(addslashes($msg['name'])); ?>', '<?php echo htmlspecialchars(addslashes($msg['message'])); ?>')" class="btn-view">👁️ View</button>
                                                <button onclick="deleteMessage(<?php echo $msg['id']; ?>)" class="btn-delete">🗑️ Delete</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="message-content">
                                        <p>💬 <?php echo nl2br(htmlspecialchars(substr($msg['message'], 0, 150))); ?><?php echo strlen($msg['message']) > 150 ? '...' : ''; ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Pagination -->
                        <div class="pagination-controls">
                            <div>
                                <?php if ($page > 1): ?>
                                    <form method="POST" action="ContactMessages.php" style="display:inline;">
                                        <input type="hidden" name="page" value="<?php echo $page - 1; ?>">
                                        <button type="submit" class="pagination-btn">← Previous</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                            <div class="page-info">
                                Page <?php echo $page; ?> of <?php echo $total_pages; ?>
                            </div>
                            <div>
                                <?php if ($page < $total_pages): ?>
                                    <form method="POST" action="ContactMessages.php" style="display:inline;">
                                        <input type="hidden" name="page" value="<?php echo $page + 1; ?>">
                                        <button type="submit" class="pagination-btn">Next →</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">📭</div>
                            <h3>No messages yet</h3>
                            <p>Contact messages will appear here</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>

    <!-- View Message Modal -->
    <div id="viewModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalName"></h2>
                <button onclick="closeModal()" class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <p id="modalMessage"></p>
            </div>
        </div>
    </div>

    <script src="../js/theme.js"></script>
    <script src="../js/ContactMessages.js"></script>
</body>
</html>
