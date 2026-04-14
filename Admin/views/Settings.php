<?php
include('dark_mode.php');

// Check if user is logged in
if (!isset($_SESSION['admin_email'])) {
    header('Location: loginPage.php');
    exit();
}

include('../database/dbconnection.php');
include('../database/SettingsData.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Avestra Travel Agency</title>
    <link rel="stylesheet" href="../styleSheets/Settings.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../node_modules/@fortawesome/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../styleSheets/dark-mode.css?v=<?php echo time(); ?>">
    <link rel="icon" href="../images/logo.png" type="image/png">
    <script>
        // Sync PHP session theme to localStorage instantly in head
        localStorage.setItem('theme', '<?= $current_theme ?>');
        document.documentElement.setAttribute('data-theme', '<?= $current_theme ?>');
    </script>
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
                    <li><a href="ManageBookings.php">Manage Bookings</a></li>
                    <li><a href="Payments.php">Payments</a></li>
                    <li><a href="Reports.php">Reports</a></li>
                    <li><a href="Settings.php" class="active">Settings</a></li>
                    <li><a href="MyProfile.php">My Profile</a></li>
                    <li><a href="homePage.php">Logout</a></li>
                </ul>
            </nav>
        </aside>
        <main class="main-content">
            <header class="admin-header">
                <h1><i class="fa-solid fa-gear" style="color: #4fc3f7; margin-right: 12px;"></i>Settings</h1>
            </header>
            <section class="admin-section">
                <?php if (isset($_SESSION['settings_updated'])): ?>
                    <div class="alert-success">
                        <?php echo htmlspecialchars($_SESSION['settings_updated']); unset($_SESSION['settings_updated']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['settings_error'])): ?>
                    <div class="alert-error">
                        <?php echo htmlspecialchars($_SESSION['settings_error']); unset($_SESSION['settings_error']); ?>
                    </div>
                <?php endif; ?>

                <!-- General Settings -->
                <div class="admin-card">
                    <h3><i class="fa-solid fa-sliders" style="color: #4fc3f7;"></i> General Settings</h3>
                    <form class="settings-form" action="../controller/SettingsController.php" method="POST">
                        <div class="settings-row">
                            <label for="site-theme">Site Theme:</label>
                            <select id="site-theme" name="site_theme">
                                <option value="light" <?php echo (!isset($_SESSION['settings']['dark_mode']) || $_SESSION['settings']['dark_mode'] !== 'dark') ? 'selected' : ''; ?>>Light</option>
                                <option value="dark"  <?php echo (isset($_SESSION['settings']['dark_mode']) && $_SESSION['settings']['dark_mode'] === 'dark') ? 'selected' : ''; ?>>Dark</option>
                            </select>
                        </div>
                        <div class="settings-row">
                            <label for="message-option">Message Option:</label>
                            <select id="message-option" name="message_option">
                                <option value="enabled" <?php echo $message_option === 'enabled' ? 'selected' : ''; ?>>Enabled</option>
                                <option value="disabled" <?php echo $message_option === 'disabled' ? 'selected' : ''; ?>>Disabled</option>
                            </select>
                        </div>
                        <div class="settings-row">
                            <label for="language">Language:</label>
                            <select id="language" name="language">
                                <option value="en" <?php echo $language === 'en' ? 'selected' : ''; ?>>English</option>
                            </select>
                        </div>
                        <div class="settings-row">
                            <label for="maintenance">Maintenance Mode:</label>
                            <select id="maintenance" name="maintenance_mode">
                                <option value="off" <?php echo $maintenance_mode === 'off' ? 'selected' : ''; ?>>Off</option>
                                <option value="on" <?php echo $maintenance_mode === 'on' ? 'selected' : ''; ?>>On</option>
                            </select>
                        </div>
                        <div class="settings-row">
                            <button type="submit" class="save-settings-btn">Save Settings</button>
                        </div>
                    </form>
                </div>

                <!-- Profile Settings -->
                <div class="admin-card">
                    <h3><i class="fa-solid fa-user-pen" style="color: #4fc3f7;"></i> Profile Settings</h3>
                    <form class="settings-form" action="../controller/SettingsController.php" method="POST">
                        <input type="hidden" name="form_type" value="profile">
                        <div class="settings-row">
                            <label for="profile-name">Name:</label>
                            <input type="text" id="profile-name" name="username" placeholder="Enter your name" value="<?php echo htmlspecialchars($current_username); ?>" required>
                        </div>
                        <div class="settings-row">
                            <label for="profile-email">Email:</label>
                            <input type="email" id="profile-email" name="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($current_email); ?>" required>
                        </div>
                        <div class="settings-row">
                            <button type="submit" class="save-settings-btn">Update Profile</button>
                        </div>
                    </form>
                </div>

                <!-- Password Settings -->
                <div class="admin-card">
                    <h3><i class="fa-solid fa-lock" style="color: #4fc3f7;"></i> Change Password</h3>
                    <form class="settings-form" action="../controller/SettingsController.php" method="POST">
                        <input type="hidden" name="form_type" value="password">
                        <div class="settings-row">
                            <label for="current-password">Current Password:</label>
                            <input type="password" id="current-password" name="current_password" placeholder="Enter current password" required>
                        </div>
                        <div class="settings-row">
                            <label for="new-password">New Password:</label>
                            <input type="password" id="new-password" name="new_password" placeholder="Enter new password" required>
                        </div>
                        <div class="settings-row">
                            <label for="confirm-password">Confirm Password:</label>
                            <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirm new password" required>
                        </div>
                        <div class="settings-row">
                            <button type="submit" class="save-settings-btn">Change Password</button>
                        </div>
                    </form>
                </div>

            </section>
        </main>
    </div>
    <script src="../js/theme.js"></script>
    <script src="../js/Settings.js"></script>
</body>
</html>
