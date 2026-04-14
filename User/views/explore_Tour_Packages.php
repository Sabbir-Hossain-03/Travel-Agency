<?php
include 'session_check.php';
include 'dark_mode.php';
include '../database/dbconnection.php';

// Search term
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchParam = '%' . $searchQuery . '%';

// Pagination variables
$limit = 6;
$page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total available active tours
if (!empty($searchQuery)) {
    $count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM tours WHERE status='active' AND name LIKE ?");
    $count_stmt->bind_param("s", $searchParam);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
} else {
    $count_query = "SELECT COUNT(*) as total FROM tours WHERE status='active'";
    $count_result = $conn->query($count_query);
}
$total_tours = $count_result->fetch_assoc()['total'];
$total_pages = $total_tours > 0 ? ceil($total_tours / $limit) : 1;

// Fetch paginated active tours
if (!empty($searchQuery)) {
    $stmt = $conn->prepare("SELECT id, name, duration, includes_text, price, image
                            FROM tours
                            WHERE status='active' AND name LIKE ?
                            ORDER BY id DESC
                            LIMIT ? OFFSET ?");
    $stmt->bind_param("sii", $searchParam, $limit, $offset);
} else {
    $stmt = $conn->prepare("SELECT id, name, duration, includes_text, price, image
                            FROM tours
                            WHERE status='active'
                            ORDER BY id DESC
                            LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $limit, $offset);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tour Packages</title>
    <link rel="stylesheet" href="../styleSheets/user.css">
    <link rel="stylesheet" href="../styleSheets/explore_Tour_Packages.css">
    <link rel="stylesheet" href="../styleSheets/footer.css">
    <link rel="icon" href="../images/logo.png" type="image/png">
    <link rel="stylesheet" href="../node_modules/@fortawesome/fontawesome-free/css/all.min.css"/>
    <link rel="stylesheet" href="../styleSheets/user-dark-mode.css?v=<?php echo time(); ?>">

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

<!-- Page Header -->
<div class="tp-hero">
    <video autoplay loop muted playsinline class="tp-hero-video">
        <source src="../images/0_Woman_Forest_1920x1080.mp4" type="video/mp4">
    </video>
    <div class="tp-hero-overlay"></div>
    <div class="tp-hero-inner" style="display: flex; flex-direction: column; align-items: center; padding: 0 20px;">
        <h1 class="tp-hero-title">🌍 Explore Tour Packages</h1>
        <p class="tp-hero-sub">Discover curated experiences and unforgettable adventures</p>
        
        <!-- Search Bar Overlay -->
        <div style="margin-top: 30px; width: 100%; max-width: 700px;">
            <form action="explore_Tour_Packages.php" method="GET" style="display: flex; background: rgba(255, 255, 255, 0.95); border-radius: 40px; padding: 6px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); backdrop-filter: blur(10px);">
                <div style="position: relative; flex-grow: 1; display: flex; align-items: center;">
                    <i class="fas fa-search" style="position: absolute; left: 20px; color: #718096; font-size: 1.1rem;"></i>
                    <input type="text" name="search" placeholder="🔍 Search for destinations, tours or packages..." value="<?= htmlspecialchars($searchQuery) ?>" style="width: 100%; padding: 12px 16px 12px 50px; border: none; background: transparent; font-size: 1rem; outline: none; color: #2d3748; font-weight: 500;">
                </div>
                <?php if (!empty($searchQuery)): ?>
                    <a href="explore_Tour_Packages.php" style="padding: 0 16px; display: flex; align-items: center; color: #a0aec0; text-decoration: none; transition: color 0.2s; font-size: 1.1rem;" onmouseover="this.style.color='#e53e3e'" onmouseout="this.style.color='#a0aec0'" title="Clear Search">
                        <i class="fas fa-times"></i>
                    </a>
                <?php endif; ?>
                <button type="submit" style="padding: 10px 30px; background: linear-gradient(135deg, #3182ce 0%, #2b6cb0 100%); color: white; border: none; border-radius: 30px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; box-shadow: 0 4px 15px rgba(49,130,206,0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(49,130,206,0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(49,130,206,0.3)';">Search</button>
            </form>
        </div>
    </div>
</div>

<!-- Results Info -->
<div style="max-width: 1180px; margin: 24px auto 16px; padding: 0 20px; display: flex; justify-content: space-between; align-items: center;">
    <div style="background: white; padding: 8px 16px; border-radius: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: inline-flex; align-items: center; gap: 8px; color: #4a5568; font-weight: 500; font-size: 0.95rem;">
        <i class="fas fa-box-open" style="color: #3182ce;"></i>
        <span><?= $total_tours ?> package<?= $total_tours !== 1 ? 's' : '' ?> available <?= !empty($searchQuery) ? 'for "<strong>'.htmlspecialchars($searchQuery).'</strong>"' : '' ?></span>
    </div>
    <div style="background: white; padding: 8px 16px; border-radius: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); color: #718096; font-size: 0.9rem; font-weight: 500;">
        Page <?= $page ?> of <?= $total_pages ?>
    </div>
</div>

<!-- Tour Grid -->
<div class="tp-grid">
    <?php if ($result->num_rows == 0): ?>
        <div class="tp-empty">
            <div class="tp-empty-icon">🌍</div>
            <h3>No Packages Found</h3>
            <p>No tour packages available right now. Please check back later.</p>
        </div>
    <?php else: ?>
        <?php $delay = 0.1; ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="tp-card" style="animation-delay: <?= $delay ?>s;">
                <!-- Image & ID Badge -->
                <div class="tp-card-img-wrap">
                    <?php if (!empty($row['image'])): ?>
                        <img src="../images/<?= htmlspecialchars($row['image']) ?>" alt="Tour Image" class="tp-card-img">
                    <?php else: ?>
                        <img src="../images/tour1.jpg" alt="Default Tour Image" class="tp-card-img">
                    <?php endif; ?>
                    <div class="tp-card-price-badge">
                        ID: <?= htmlspecialchars($row['id'] ?? 'N/A') ?>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="tp-card-body">
                    <h3 class="tp-card-name"><?= htmlspecialchars($row['name']) ?></h3>
                    
                    <div class="tp-card-info">
                        <span class="tp-icon"><i class="fas fa-clock"></i></span>
                        <?= htmlspecialchars($row['duration']) ?>
                    </div>
                    
                    <div class="tp-card-info" style="margin-bottom: 12px; font-weight: 600; color: #1565c0;">
                        <span class="tp-icon" style="color: #4a5568;"><i class="fas fa-bangladeshi-taka-sign"></i></span>
                        <?= (float)$row['price'] ?> ৳
                    </div>

                    <?php if (!empty($row['includes_text'])): ?>
                        <p class="tp-card-includes">
                            <span class="tp-inc-label">Includes:</span>
                            <?= htmlspecialchars($row['includes_text']) ?>
                        </p>
                    <?php endif; ?>

                    <form action="cart_action.php" method="post" class="tp-cart-form">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="service_type" value="tour">
                        <input type="hidden" name="service_id" value="<?= htmlspecialchars($row['id']) ?>">
                        <input type="hidden" name="name" value="<?= htmlspecialchars($row['name']) ?>">
                        <input type="hidden" name="price" value="<?= (float)$row['price'] ?>">
                        <input type="hidden" name="image" value="<?= htmlspecialchars($row['image']) ?>">

                        <div class="qt-selector" style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px; justify-content: center;">
                            <label for="qt_<?= $row['id'] ?>" style="font-weight: 600; color: #4a5568;">People:</label>
                            <input type="number" id="qt_<?= $row['id'] ?>" name="quantity" value="1" min="1" max="20" style="width: 60px; padding: 6px; border-radius: 6px; border: 1px solid #cbd5e0; text-align: center;">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <button type="submit" class="cart-btn">
                                🛒 +Cart
                            </button>
                            <button type="submit" name="direct_book" value="1" class="tp-book-btn">
                                Book Now
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <?php $delay += 0.1; ?>
        <?php endwhile; ?>
    <?php endif; ?>
</div>

<!-- Pagination Controls -->
<?php if ($total_pages > 1): ?>
<?php $search_query_string = !empty($searchQuery) ? '&search=' . urlencode($searchQuery) : ''; ?>
<nav class="tp-pagination" aria-label="Tour pagination">
    <?php if ($page > 1): ?>
        <a class="tp-page-btn tp-page-nav" href="explore_Tour_Packages.php?page=<?= $page - 1 ?><?= $search_query_string ?>">‹ Prev</a>
    <?php else: ?>
        <span class="tp-page-btn tp-page-nav disabled">‹ Prev</span>
    <?php endif; ?>

    <?php
    $start = max(1, $page - 2);
    $end   = min($total_pages, $page + 2);
    if ($start > 1): ?>
        <a class="tp-page-btn" href="explore_Tour_Packages.php?page=1<?= $search_query_string ?>">1</a>
        <?php if ($start > 2): ?><span class="tp-page-dots">…</span><?php endif; ?>
    <?php endif;

    for ($p = $start; $p <= $end; $p++): ?>
        <?php if ($p === $page): ?>
            <span class="tp-page-btn active"><?= $p ?></span>
        <?php else: ?>
            <a class="tp-page-btn" href="explore_Tour_Packages.php?page=<?= $p ?><?= $search_query_string ?>"><?= $p ?></a>
        <?php endif; ?>
    <?php endfor;

    if ($end < $total_pages): ?>
        <?php if ($end < $total_pages - 1): ?><span class="tp-page-dots">…</span><?php endif; ?>
        <a class="tp-page-btn" href="explore_Tour_Packages.php?page=<?= $total_pages ?><?= $search_query_string ?>"><?= $total_pages ?></a>
    <?php endif; ?>

    <?php if ($page < $total_pages): ?>
        <a class="tp-page-btn tp-page-nav" href="explore_Tour_Packages.php?page=<?= $page + 1 ?><?= $search_query_string ?>">Next ›</a>
    <?php else: ?>
        <span class="tp-page-btn tp-page-nav disabled">Next ›</span>
    <?php endif; ?>
</nav>
<?php endif; ?>

</body>
<?php include 'footer.php'; ?>
</html>