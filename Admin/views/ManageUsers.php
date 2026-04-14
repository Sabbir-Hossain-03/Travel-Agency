<?php
include('dark_mode.php');
if (!isset($_SESSION['admin_email'])) {
    header('Location: loginPage.php');
    exit();
}
include('../database/dbconnection.php');
include('../database/ManageUsersData.php');
include('../database/AdminRequestsData.php');
$is_dark = isset($_SESSION['settings']['dark_mode']) && $_SESSION['settings']['dark_mode'] === 'dark';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Avestra Travel Agency</title>
    <link rel="stylesheet" href="../styleSheets/ManageUsers.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../node_modules/@fortawesome/fontawesome-free/css/all.min.css">
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
                    <li><a href="ManageUsers.php" class="active">Manage Users</a></li>
                    <li><a href="ManageTickets.php">Tickets</a></li>
                    <li><a href="ManageHotels.php">Hotels</a></li>
                    <li><a href="ManageTours.php">Tours</a></li>
                    <li><a href="ManageBookings.php">Manage Bookings</a></li>
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
                <h1><i class="fa-solid fa-users"></i> Manage Users</h1>
            </header>

            <section class="admin-section">
                <div class="admin-card">

               
                    <?php // Remove SMS success message display from ManageUsers page
                    if (!empty($success_message) && $success_message !== 'SMS sent successfully!'): ?>
                        <div
                            style="padding:12px; background:#d0f8e8; color:#2e7d32; border-radius:8px; margin-bottom:16px;">
                            <?php echo htmlspecialchars($success_message); ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($error_message)): ?>
                        <div
                            style="padding:12px; background:#ffe0e0; color:#c62828; border-radius:8px; margin-bottom:16px;">
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Admin Requests Section -->
                    <?php if (!empty($pending_requests)): ?>
                        <div style="margin-bottom: 32px; padding: 20px; background: #f9f9f9; border-radius: 8px; border-left: 4px solid #ff9800;">
                            <h3 style="margin-top: 0; color: #ff9800;">Pending Admin Requests (<?php echo count($pending_requests); ?>)</h3>
                            <div class="user-table-container">
                                <table class="user-table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Requested Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pending_requests as $request): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($request['username']); ?></td>
                                                <td><?php echo htmlspecialchars($request['email']); ?></td>
                                                <td><?php echo htmlspecialchars($request['phone_number'] ?? 'N/A'); ?></td>
                                                <td><?php echo date('Y-m-d', strtotime($request['requested_date'])); ?></td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <button class="edit-btn" type="button" onclick="approveAdminRequest(<?php echo $request['id']; ?>, '<?php echo htmlspecialchars($request['username']); ?>')">Approve</button>
                                                        <button class="delete-btn" type="button" onclick="rejectAdminRequest(<?php echo $request['id']; ?>, '<?php echo htmlspecialchars($request['username']); ?>')">Reject</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>


                    <form method="POST" id="filterForm">
                        <div class="user-actions">

                            <!-- Search -->
                            <input type="text" name="search" class="user-search"
                                placeholder="Search by name or email..."
                                value="<?php echo htmlspecialchars($search ?? ''); ?>">

                            <!-- Filters -->
                            <select name="role_filter" class="user-search" style="width:180px;"
                                onchange="document.getElementById('filterForm').submit()">
                                <option value="">All Roles</option>
                                <option value="Admin" <?php echo ($role_filter === 'Admin') ? 'selected' : ''; ?>>Admin
                                </option>
                                <option value="Customer" <?php echo ($role_filter === 'Customer') ? 'selected' : ''; ?>>
                                    Customer</option>
                            </select>

                            <select name="status_filter" class="user-search" style="width:180px;"
                                onchange="document.getElementById('filterForm').submit()">
                                <option value="">All Status</option>
                                <option value="Active" <?php echo ($status_filter === 'Active') ? 'selected' : ''; ?>>
                                    Active</option>
                                <option value="Inactive" <?php echo ($status_filter === 'Inactive') ? 'selected' : ''; ?>>
                                    Inactive</option>
                                <option value="Blocked" <?php echo ($status_filter === 'Blocked') ? 'selected' : ''; ?>>
                                    Blocked</option>
                            </select>

                            <!-- Actions -->
                            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                                <button class="edit-btn" type="submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
                                <button class="add-user-btn" type="button" onclick="openAddModal()"><i class="fa-solid fa-plus"></i> Add User</button>
                            </div>
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="user-table-container">
                        <table class="user-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Registered</th>
                                    <th style="width:260px;">Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (!empty($users)): ?>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td><?php echo htmlspecialchars($user['role'] ?? 'Customer'); ?></td>
                                            <td>
                                                <?php
                                                $status = $user['status'] ?? 'Active';
                                                $statusClass = ($status === 'Active') ? 'active' : 'inactive';
                                                ?>
                                                <span
                                                    class="status <?php echo $statusClass; ?>"><?php echo htmlspecialchars($status); ?></span>
                                            </td>
                                            <td>
                                                <?php
                                                if (!empty($user['created_at']) && $user['created_at'] != '0000-00-00 00:00:00') {
                                                    echo date('Y-m-d', strtotime($user['created_at']));
                                                } else {
                                                    echo 'N/A';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <button class="edit-btn" type="button"
                                                        onclick='openEditModal(<?php echo json_encode($user); ?>)'><i class="fa-regular fa-pen-to-square"></i> Edit</button>
                                                    <?php if ($status === 'Active'): ?>
                                                        <button class="block-btn" type="button"
                                                            onclick="blockUser('<?php echo htmlspecialchars($user['email']); ?>')">Block</button>
                                                    <?php elseif ($status === 'Blocked'): ?>
                                                        <button class="unblock-btn" type="button"
                                                            onclick="unblockUser('<?php echo htmlspecialchars($user['email']); ?>')">Unblock</button>
                                                    <?php else: ?>
                                                        <button class="unblock-btn" type="button"
                                                            onclick="unblockUser('<?php echo htmlspecialchars($user['email']); ?>')">Activate</button>
                                                    <?php endif; ?>
                                                    <button class="delete-btn" type="button"
                                                        onclick="deleteUser('<?php echo htmlspecialchars($user['email']); ?>', '<?php echo htmlspecialchars($user['username']); ?>')"><i class="fa-solid fa-trash"></i> Delete</button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" style="text-align:center; padding:20px;">No users found</td>
                                    </tr>
                                <?php endif; ?>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="pagination-bar">
                        <div class="pagination-info">Showing <?php echo $showing_from; ?>–<?php echo $showing_to; ?> of
                            <?php echo $total_users; ?> users</div>

                        <div class="pagination-controls">
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="search"
                                    value="<?php echo htmlspecialchars($search ?? ''); ?>">
                                <input type="hidden" name="role_filter"
                                    value="<?php echo htmlspecialchars($role_filter ?? ''); ?>">
                                <input type="hidden" name="status_filter"
                                    value="<?php echo htmlspecialchars($status_filter ?? ''); ?>">
                                <input type="hidden" name="page" value="<?php echo max(1, $current_page - 1); ?>">
                                <button class="edit-btn" type="submit" <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>>Prev</button>
                            </form>
                            <span class="pagination-page">Page <?php echo $current_page; ?> of
                                <?php echo $total_pages; ?></span>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="search"
                                    value="<?php echo htmlspecialchars($search ?? ''); ?>">
                                <input type="hidden" name="role_filter"
                                    value="<?php echo htmlspecialchars($role_filter ?? ''); ?>">
                                <input type="hidden" name="status_filter"
                                    value="<?php echo htmlspecialchars($status_filter ?? ''); ?>">
                                <input type="hidden" name="page"
                                    value="<?php echo min($total_pages, $current_page + 1); ?>">
                                <button class="edit-btn" type="submit" <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>>Next</button>
                            </form>
                        </div>
                    </div>

                </div>
            </section>

        </main>
    </div>

    <!-- Add/Edit User Modal -->
    <div id="userModal">
        <div class="admin-card modal-card">
            <h2 class="modal-title" id="modalTitle">Add User</h2>

            <form method="POST" action="../controller/ManageUsersActions.php" id="userForm">
                <input type="hidden" name="action" id="modalAction" value="add">
                <input type="hidden" name="old_email" id="oldEmail" value="">

                <div class="form-group">
                    <label class="form-label">Name</label>
                    <input class="user-search form-input" type="text" name="username" id="modalUsername"
                        required>
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input class="user-search form-input" type="email" name="email" id="modalEmail" required>
                </div>


                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select class="user-search form-input" name="status" id="modalStatus">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                        <option value="Blocked">Blocked</option>
                    </select>
                </div>

                <div class="modal-actions">
                    <button class="edit-btn" type="button" onclick="closeModal()">Cancel</button>
                    <button class="add-user-btn" type="submit">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmModal">
        <div class="admin-card modal-card confirm-card">
            <div class="confirm-icon">!</div>
            <h3 class="modal-title">Please Confirm</h3>
            <p id="confirmMessage" class="confirm-message"></p>
            <div class="confirm-actions">
                <button class="edit-btn" type="button" onclick="closeConfirmModal()">Cancel</button>
                <button class="delete-btn" type="button" onclick="submitPendingAction()">Confirm</button>
            </div>
        </div>
    </div>
    <script src="../js/theme.js"></script>
    <script src="../js/ManageUsers.js"></script>

</body>
</html>
