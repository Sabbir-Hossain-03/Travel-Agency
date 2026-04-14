<?php
include 'session_check.php';
include 'dark_mode.php';
include '../database/dbconnection.php';

// --- Pagination & Search Setup ---
$perPage     = 6;
$currentPage = max(1, (int)($_GET['page'] ?? 1));
$search      = trim($_GET['search'] ?? '');
$offset      = ($currentPage - 1) * $perPage;

// --- Count total matching hotels ---
if ($search !== '') {
    $stmtCount = $conn->prepare(
        "SELECT COUNT(*) FROM hotels WHERE LOWER(status)='active' AND name LIKE ?"
    );
    $like = '%' . $search . '%';
    $stmtCount->bind_param('s', $like);
} else {
    $stmtCount = $conn->prepare(
        "SELECT COUNT(*) FROM hotels WHERE LOWER(status)='active'"
    );
}
$stmtCount->execute();
$stmtCount->bind_result($totalRows);
$stmtCount->fetch();
$stmtCount->close();
$totalPages = max(1, (int)ceil($totalRows / $perPage));
if ($currentPage > $totalPages) $currentPage = $totalPages;

// --- Fetch paged hotels ---
if ($search !== '') {
    $stmt = $conn->prepare(
        "SELECT id, name, location, rating, includes_text, price_per_night, image
         FROM hotels
         WHERE LOWER(status)='active' AND name LIKE ?
         ORDER BY id DESC
         LIMIT ? OFFSET ?"
    );
    $like = '%' . $search . '%';
    $stmt->bind_param('sii', $like, $perPage, $offset);
} else {
    $stmt = $conn->prepare(
        "SELECT id, name, location, rating, includes_text, price_per_night, image
         FROM hotels
         WHERE LOWER(status)='active'
         ORDER BY id DESC
         LIMIT ? OFFSET ?"
    );
    $stmt->bind_param('ii', $perPage, $offset);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Hotels – Avestra Travel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styleSheets/user.css">
    <link rel="stylesheet" href="../styleSheets/find_Hotels.css">
    <link rel="stylesheet" href="../styleSheets/footer.css">
    <link rel="stylesheet" href="../styleSheets/user-dark-mode.css?v=<?php echo time(); ?>">
    <link rel="icon" href="../images/logo.png" type="image/png">
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
<div class="fh-hero">
    <div class="fh-hero-inner">
        <h1 class="fh-hero-title">🏨 Find Your Perfect Hotel</h1>
        <p class="fh-hero-sub">Browse our handpicked selection of premium hotels</p>

        <!-- Search Bar -->
        <form class="fh-search-form" method="GET" action="">
            <div class="fh-search-wrap">
                <span class="fh-search-icon">🔍</span>
                <input
                    class="fh-search-input"
                    type="text"
                    name="search"
                    placeholder="Search hotels by name…"
                    value="<?= htmlspecialchars($search) ?>"
                    autocomplete="off"
                >
                <button class="fh-search-btn" type="submit">Search</button>
            </div>
        </form>
    </div>
</div>

<!-- Results Info -->
<div class="fh-meta" style="max-width: 1180px; margin: 24px auto 16px; padding: 0 20px; display: flex; justify-content: space-between; align-items: center; background: transparent; border: none; box-shadow: none;">
    <?php if ($search !== ''): ?>
        <div style="background: white; padding: 8px 16px; border-radius: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: inline-flex; align-items: center; gap: 8px; color: #4a5568; font-weight: 500; font-size: 0.95rem;">
            <i class="fas fa-bed" style="color: #3182ce;"></i>
            <span>Results for "<strong><?= htmlspecialchars($search) ?></strong>" — <?= $totalRows ?> hotel<?= $totalRows !== 1 ? 's' : '' ?> found</span>
            <a class="fh-clear-search" href="find_Hotels.php" style="margin-left: 8px; text-decoration: none; color: #e53e3e; font-weight: 600;">✕ Clear</a>
        </div>
    <?php else: ?>
        <div style="background: white; padding: 8px 16px; border-radius: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: inline-flex; align-items: center; gap: 8px; color: #4a5568; font-weight: 500; font-size: 0.95rem;">
            <i class="fas fa-bed" style="color: #3182ce;"></i>
            <span><?= $totalRows ?> hotel<?= $totalRows !== 1 ? 's' : '' ?> available</span>
        </div>
    <?php endif; ?>
    <div style="background: white; padding: 8px 16px; border-radius: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); color: #718096; font-size: 0.9rem; font-weight: 500;">
        Page <?= $currentPage ?> of <?= $totalPages ?>
    </div>
</div>

<!-- Hotel Grid -->
<div class="fh-grid">
    <?php if ($result->num_rows === 0): ?>
        <div class="fh-empty">
            <div class="fh-empty-icon">🏨</div>
            <h3>No Hotels Found</h3>
            <p><?= $search !== '' ? 'Try a different search term.' : 'No hotels available right now.' ?></p>
            <?php if ($search !== ''): ?>
                <a class="fh-back-btn" href="find_Hotels.php">View All Hotels</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="fh-card">
                <!-- Hotel Image -->
                <div class="fh-card-img-wrap">
                    <?php
                    $imgFile = trim($row['image'] ?? '');
                    $imgPath = '../images/hotels/' . $imgFile;
                    $fullPath = __DIR__ . '/../images/hotels/' . $imgFile;
                    if ($imgFile !== '' && file_exists($fullPath)):
                    ?>
                        <img
                            class="fh-card-img"
                            src="<?= htmlspecialchars($imgPath) ?>"
                            alt="<?= htmlspecialchars($row['name']) ?>"
                            loading="lazy"
                        >
                    <?php else: ?>
                        <img
                            class="fh-card-img"
                            src="../images/hotel1.jpg"
                            alt="Default Hotel"
                            loading="lazy"
                        >
                    <?php endif; ?>
                    <!-- Price Badge -->
                    <div class="fh-card-price-badge">
                        <?= $row['price_per_night'] > 0
                            ? number_format((float)$row['price_per_night'], 0) . ' ৳/night'
                            : 'Contact for price' ?>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="fh-card-body">
                    <h3 class="fh-card-name"><?= htmlspecialchars($row['name']) ?></h3>
                    <p class="fh-card-location">
                        <span class="fh-loc-icon">📍</span>
                        <?= htmlspecialchars($row['location']) ?>
                    </p>
                    
                    <div class="fh-card-rating" style="color: #f59e0b; margin-bottom: 12px; font-size: 1.1rem; letter-spacing: 2px;">
                        <?php 
                        $rating = max(0, min(5, (int)($row['rating'] ?? 0)));
                        for ($i = 1; $i <= 5; $i++) echo ($i <= $rating) ? '★' : '<span style="color:#d1d5db;">☆</span>'; 
                        ?>
                    </div>

                    <?php if (!empty($row['includes_text'])): ?>
                        <p class="fh-card-includes">
                            <span class="fh-inc-label">Includes:</span>
                            <?= htmlspecialchars($row['includes_text']) ?>
                        </p>
                    <?php endif; ?>

                    <form action="cart_action.php" method="post" class="fh-cart-form">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="service_type" value="hotel">
                        <input type="hidden" name="service_id" value="<?= htmlspecialchars($row['id']) ?>">
                        <input type="hidden" name="name" value="<?= htmlspecialchars($row['name']) ?>">
                        <input type="hidden" name="price" value="<?= (float)$row['price_per_night'] ?>">
                        <input type="hidden" name="image" value="hotels/<?= htmlspecialchars($row['image']) ?>">
                        
                        <div class="qt-selector" style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px; justify-content: center;">
                            <label for="qt_<?= $row['id'] ?>" style="font-weight: 600; color: #4a5568;">Rooms:</label>
                            <input type="number" id="qt_<?= $row['id'] ?>" name="quantity" value="1" min="1" max="10" style="width: 60px; padding: 6px; border-radius: 6px; border: 1px solid #cbd5e0; text-align: center;">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <button type="submit" class="cart-btn">
                                🛒 +Cart
                            </button>
                            <button type="submit" name="direct_book" value="1" class="fh-book-btn">
                                Book Now
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<nav class="fh-pagination" aria-label="Hotel pagination">
    <?php
    // Build base URL preserving search
    $baseUrl = 'find_Hotels.php?' . ($search !== '' ? 'search=' . urlencode($search) . '&' : '');
    ?>

    <!-- Prev -->
    <?php if ($currentPage > 1): ?>
        <a class="fh-page-btn fh-page-nav" href="<?= $baseUrl ?>page=<?= $currentPage - 1 ?>">‹ Prev</a>
    <?php else: ?>
        <span class="fh-page-btn fh-page-nav disabled">‹ Prev</span>
    <?php endif; ?>

    <!-- Numbered pages -->
    <?php
    $start = max(1, $currentPage - 2);
    $end   = min($totalPages, $currentPage + 2);
    if ($start > 1): ?>
        <a class="fh-page-btn" href="<?= $baseUrl ?>page=1">1</a>
        <?php if ($start > 2): ?><span class="fh-page-dots">…</span><?php endif; ?>
    <?php endif;

    for ($p = $start; $p <= $end; $p++): ?>
        <?php if ($p === $currentPage): ?>
            <span class="fh-page-btn active"><?= $p ?></span>
        <?php else: ?>
            <a class="fh-page-btn" href="<?= $baseUrl ?>page=<?= $p ?>"><?= $p ?></a>
        <?php endif; ?>
    <?php endfor;

    if ($end < $totalPages): ?>
        <?php if ($end < $totalPages - 1): ?><span class="fh-page-dots">…</span><?php endif; ?>
        <a class="fh-page-btn" href="<?= $baseUrl ?>page=<?= $totalPages ?>"><?= $totalPages ?></a>
    <?php endif; ?>

    <!-- Next -->
    <?php if ($currentPage < $totalPages): ?>
        <a class="fh-page-btn fh-page-nav" href="<?= $baseUrl ?>page=<?= $currentPage + 1 ?>">Next ›</a>
    <?php else: ?>
        <span class="fh-page-btn fh-page-nav disabled">Next ›</span>
    <?php endif; ?>
</nav>
<?php endif; ?>

</body>
<?php include 'footer.php'; ?>
</html>