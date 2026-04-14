<?php
include('dark_mode.php');
include('../database/HotelsData.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manage Hotels - Admin Panel</title>

    <link rel="stylesheet" href="../styleSheets/ManageHotels.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="../styleSheets/ManageHotelsExtra.css?v=<?php echo time(); ?>" />

    <link rel="icon" href="../images/logo.png" type="image/png" />
    <link rel="stylesheet" href="../node_modules/@fortawesome/fontawesome-free/css/all.min.css" />
    <link rel="stylesheet" href="../styleSheets/dark-mode.css?v=<?php echo time(); ?>" />
    <script>
        localStorage.setItem('theme', '<?= $current_theme ?>');
        document.documentElement.setAttribute('data-theme', '<?= $current_theme ?>');
    </script>

    <script src="../js/ManageHotels.js" defer></script>
</head>

<body class="<?= $is_dark ? 'dark-mode' : '' ?>">

    <!-- Custom confirm modal (UI only) -->
    <div id="confirmModal" class="confirm-overlay" aria-hidden="true">
        <div class="confirm-box">
            <div id="confirmModalMessage" class="confirm-message"></div>
            <div class="confirm-actions">
                <button type="button" class="confirm-yes" id="confirmYesBtn">Yes</button>
                <button type="button" class="confirm-no" id="confirmNoBtn">No</button>
            </div>
        </div>
    </div>

    <div class="admin-container">

        <!-- Sidebar -->
        <aside class="sidebar">
            <div style="padding: 24px 32px;">
                <div style="text-align:center; margin-bottom: 16px;">
                    <img src="../images/logo.png" alt="Avestra Logo" style="width:60px; height:auto;">
                </div>
                <h2 class="sidebar-title">Admin Panel</h2>
            </div>

            <nav>
                <ul class="sidebar-menu">
                    <li><a href="Admin.php">Dashboard</a></li>
                    <li><a href="ManageUsers.php">Manage Users</a></li>
                    <li><a href="ManageTickets.php">Tickets</a></li>
                    <li><a href="ManageHotels.php" class="active">Hotels</a></li>
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

        <!-- Main content -->
        <main class="main-content">

            <!-- Alerts -->
            <?php if (isset($_SESSION['hotel_success'])): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($_SESSION['hotel_success']) ?>
                </div>
                <?php unset($_SESSION['hotel_success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['hotel_error'])): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($_SESSION['hotel_error']) ?>
                </div>
                <?php unset($_SESSION['hotel_error']); ?>
            <?php endif; ?>

            <header class="admin-header">
                <h1><i class="fa-solid fa-hotel"></i> Manage Hotels</h1>

            </header>

            <section class="admin-section">
                <div class="admin-card">

                    <div class="hotel-actions">
                        <form method="GET" action="" style="display:flex;gap:8px;flex:1;">
                            <input id="hotelSearch" class="hotel-search" type="text" name="search"
                                placeholder="Search hotels by ID, name or location..."
                                value="<?= htmlspecialchars($search) ?>" />
                            <button class="search-btn" type="submit">
                                <i class="fa-solid fa-magnifying-glass"></i> Search
                            </button>
                            <?php if ($search !== ''): ?>
                                <a href="ManageHotels.php" class="clear-btn"><i class="fa-solid fa-xmark"></i> Clear</a>
                            <?php endif; ?>
                        </form>
                        <a class="add-hotel-btn" href="#hotelModal" id="openAddHotel">
                            <i class="fa-solid fa-plus"></i> Add Hotel
                        </a>
                    </div>

                    <div class="hotel-table-container">
                        <div class="hotel-cards-container" id="hotelGrid">

                            <?php
                            if (!empty($hotels)) {
                                foreach ($hotels as $hotel) {
                                    if (empty($hotel['id'])) continue;
                                    $isActive    = strcasecmp($hotel['status'] ?? '', 'Active') === 0;
                                    $statusClass = $isActive ? 'active' : 'inactive';
                                    $rating      = max(0, min(5, (int)($hotel['rating'] ?? 0)));
                                    $imgFile     = trim($hotel['image'] ?? '');
                                    $imgSrc      = ($imgFile !== '' && file_exists(__DIR__ . '/../../User/images/hotels/' . $imgFile))
                                                   ? '../../User/images/hotels/' . htmlspecialchars($imgFile)
                                                   : '../images/logo.png';
                                    ?>
                                    <div class="hotel-card"
                                        data-name="<?= htmlspecialchars($hotel['name']) ?>"
                                        data-location="<?= htmlspecialchars($hotel['location']) ?>"
                                        data-status="<?= htmlspecialchars($hotel['status']) ?>">

                                        <!-- Image -->
                                        <div class="hotel-card-img-wrap">
                                            <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($hotel['name']) ?>" class="hotel-card-img" loading="lazy">
                                            <span class="hotel-card-id-badge">ID: <?= htmlspecialchars($hotel['id']) ?></span>
                                        </div>

                                        <div class="hotel-card-header">
                                            <h3><i class="fa-solid fa-hotel"></i> <?= htmlspecialchars($hotel['name']) ?></h3>
                                            <span class="status <?= $statusClass ?>"><?= htmlspecialchars($hotel['status']) ?></span>
                                        </div>

                                        <div class="hotel-card-body">
                                            <div class="hotel-info">
                                                <p><i class="fa-solid fa-location-dot"></i> <strong>Location:</strong> <?= htmlspecialchars($hotel['location']) ?></p>
                                                <p><i class="fa-solid fa-star"></i> <strong>Rating:</strong>
                                                    <span class="rating-stars">
                                                        <?php for ($i = 1; $i <= 5; $i++) echo ($i <= $rating) ? '★' : '☆'; ?>
                                                    </span>
                                                </p>
                                                <p><i class="fa-solid fa-bed"></i> <strong>Rooms:</strong> <?= htmlspecialchars($hotel['rooms']) ?></p>
                                                <?php if (!empty($hotel['price_per_night'])): ?>
                                                <p><i class="fa-solid fa-bangladeshi-taka-sign"></i> <strong>Price:</strong> <?= number_format((float)$hotel['price_per_night'], 0) ?> ৳/night</p>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="hotel-card-footer hotel-actions">
                                            <a class="edit-btn" href="#hotelModal"
                                                data-id="<?= htmlspecialchars($hotel['id']) ?>"
                                                data-name="<?= htmlspecialchars($hotel['name']) ?>"
                                                data-location="<?= htmlspecialchars($hotel['location']) ?>"
                                                data-rating="<?= (int)$hotel['rating'] ?>"
                                                data-rooms="<?= htmlspecialchars($hotel['rooms']) ?>"
                                                data-status="<?= htmlspecialchars($hotel['status']) ?>"
                                                data-price="<?= htmlspecialchars($hotel['price_per_night'] ?? '') ?>"
                                                data-includes="<?= htmlspecialchars($hotel['includes_text'] ?? '') ?>">
                                                <i class="fa-regular fa-pen-to-square"></i> Edit
                                            </a>
                                            <form action="../controller/ManageHotelsController.php" method="POST" class="inline-form toggleForm">
                                                <input type="hidden" name="action" value="toggle">
                                                <input type="hidden" name="id" value="<?= htmlspecialchars($hotel['id']) ?>">
                                                <input type="hidden" name="current_status" value="<?= htmlspecialchars($hotel['status']) ?>">
                                                <button type="submit" class="toggle-btn" data-confirm="Change hotel status?">
                                                    <i class="fa-solid fa-arrows-rotate"></i>
                                                    <?= $isActive ? 'Make Inactive' : 'Make Active' ?>
                                                </button>
                                            </form>
                                            <?php if (!$isActive): ?>
                                                <form action="../controller/ManageHotelsController.php" method="POST" class="inline-form deleteForm">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?= htmlspecialchars($hotel['id']) ?>">
                                                    <input type="hidden" name="status" value="<?= htmlspecialchars($hotel['status']) ?>">
                                                    <button type="submit" class="delete-btn" data-confirm="Delete this hotel? This cannot be undone.">
                                                        <i class="fa-solid fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <a class="delete-btn disabled-link" title="Only inactive hotels can be deleted">
                                                    <i class="fa-solid fa-trash"></i> Delete
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php
                                }
                            } else {
                                echo '<div class="no-hotels-message">No hotels found.</div>';
                            }
                            ?>

                        </div><!-- /hotelGrid -->
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <nav class="hotel-pagination">
                        <?php $baseUrl = 'ManageHotels.php?' . ($search !== '' ? 'search=' . urlencode($search) . '&' : ''); ?>
                        <?php if ($currentPage > 1): ?>
                            <a class="page-btn page-nav" href="<?= $baseUrl ?>page=<?= $currentPage - 1 ?>">‹ Prev</a>
                        <?php else: ?>
                            <span class="page-btn page-nav disabled">‹ Prev</span>
                        <?php endif; ?>

                        <?php
                        $start = max(1, $currentPage - 2);
                        $end   = min($totalPages, $currentPage + 2);
                        if ($start > 1): ?>
                            <a class="page-btn" href="<?= $baseUrl ?>page=1">1</a>
                            <?php if ($start > 2): ?><span class="page-dots">…</span><?php endif; ?>
                        <?php endif;
                        for ($p = $start; $p <= $end; $p++): ?>
                            <?php if ($p === $currentPage): ?>
                                <span class="page-btn active"><?= $p ?></span>
                            <?php else: ?>
                                <a class="page-btn" href="<?= $baseUrl ?>page=<?= $p ?>"><?= $p ?></a>
                            <?php endif; ?>
                        <?php endfor;
                        if ($end < $totalPages): ?>
                            <?php if ($end < $totalPages - 1): ?><span class="page-dots">…</span><?php endif; ?>
                            <a class="page-btn" href="<?= $baseUrl ?>page=<?= $totalPages ?>"><?= $totalPages ?></a>
                        <?php endif; ?>

                        <?php if ($currentPage < $totalPages): ?>
                            <a class="page-btn page-nav" href="<?= $baseUrl ?>page=<?= $currentPage + 1 ?>">Next ›</a>
                        <?php else: ?>
                            <span class="page-btn page-nav disabled">Next ›</span>
                        <?php endif; ?>
                    </nav>
                    <?php endif; ?>

                </div>
            </section>

        </main>

    </div>


    <?php
    if (isset($_SESSION['hotel_image_debug'])) {
        echo '<div style="background:#fffbe6;color:#333;padding:10px;border:1px solid #e6c200;margin:10px 0;">';
        echo '<strong>Image Upload Debug Info:</strong><br><pre>';
        foreach ($_SESSION['hotel_image_debug'] as $dbg) {
            echo htmlspecialchars($dbg) . "\n";
        }
        echo '</pre></div>';
        unset($_SESSION['hotel_image_debug']);
    }
    ?>
    <div id="hotelModal" class="modal-overlay">
        <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
            <div class="modal-header">
                <h3 id="modalTitle"><i class="fa-solid fa-circle-plus"></i> Add / Edit Hotel</h3>
                <a class="modal-close" href="#">✕</a>
            </div>

            <div class="modal-body">
                <form class="hotel-form" id="hotelForm" action="../controller/ManageHotelsController.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" id="formAction" value="add">

                    <div class="form-group full" id="hotelIdGroup">
                        <label><i class="fa-solid fa-hashtag"></i> Hotel ID</label>
                        <input type="text" name="id" id="hotelId" placeholder="e.g., H101" pattern="[A-Za-z][A-Za-z0-9]*" required />
                        <small id="hotelIdNote" style="color:#888;font-size:0.8rem;">Set a unique ID for this hotel (e.g., H101). This ID links the image on the user page.</small>
                    </div>
       
                    <div class="form-group full">
                        <label><i class="fa-solid fa-image"></i> Hotel Image</label>
                        <input type="file" name="hotel_image" id="hotelImage" accept="image/*" required />
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label><i class="fa-solid fa-hotel"></i> Hotel Name</label>
                            <input type="text" name="name" id="hotelName" placeholder="e.g., Radision Hotel" required />
                        </div>

                        <div class="form-group">
                            <label><i class="fa-solid fa-location-dot"></i> Location</label>
                            <input type="text" name="location" id="hotelLocation" placeholder="e.g., Dhaha "
                                required />
                        </div>

                 
                        <div class="form-group full">
                            <label><i class="fa-solid fa-star"></i> Rating</label>
                            <div class="star-rating" id="starRating">
                                <input type="radio" name="rating" id="star5" value="5">
                                <label for="star5">★</label>

                                <input type="radio" name="rating" id="star4" value="4">
                                <label for="star4">★</label>

                                <input type="radio" name="rating" id="star3" value="3">
                                <label for="star3">★</label>

                                <input type="radio" name="rating" id="star2" value="2">
                                <label for="star2">★</label>

                                <input type="radio" name="rating" id="star1" value="1" required>
                                <label for="star1">★</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><i class="fa-solid fa-bed"></i> Rooms</label>
                            <input type="number" name="rooms" id="hotelRooms" placeholder="e.g., 150" required />
                        </div>

                        <div class="form-group">
                            <label><i class="fa-solid fa-bangladeshi-taka-sign"></i> Price Per Night (৳)</label>
                            <input type="number" name="price_per_night" id="hotelPrice" placeholder="e.g., 3500" min="0" step="0.01" />
                        </div>

                        <div class="form-group full">
                            <label><i class="fa-solid fa-list-check"></i> Includes (Amenities)</label>
                            <input type="text" name="includes_text" id="hotelIncludes" placeholder="e.g., Free WiFi, Breakfast, Pool" />
                        </div>

                        <div class="form-group full">
                            <label><i class="fa-solid fa-circle-info"></i> Status</label>
                            <select name="status" id="hotelStatus" required>
                                <option value="Active">✓ Active</option>
                                <option value="Inactive">✗ Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button class="save-btn" type="submit">
                            <i class="fa-solid fa-check"></i> Save Hotel
                        </button>
                        <a class="cancel-btn" href="#" id="cancelBtn">
                            <i class="fa-solid fa-xmark"></i> Cancel
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>

</body>

</html>