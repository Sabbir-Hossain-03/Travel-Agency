<?php
// Define data array first
$hotels = array();

include_once(__DIR__ . '/dbconnection.php');

// --- Pagination & Search ---
$perPage     = 6;
$currentPage = max(1, (int)(isset($_GET['page']) ? $_GET['page'] : 1));
$search      = trim(isset($_GET['search']) ? $_GET['search'] : '');
$offset      = ($currentPage - 1) * $perPage;

if ($conn && !$conn->connect_error) {
    // --- Count ---
    if ($search !== '') {
        $stmtCount = $conn->prepare(
            "SELECT COUNT(*) FROM hotels WHERE id LIKE ? OR name LIKE ? OR location LIKE ?"
        );
        $like = '%' . $search . '%';
        $stmtCount->bind_param('sss', $like, $like, $like);
    } else {
        $stmtCount = $conn->prepare("SELECT COUNT(*) FROM hotels");
    }
    $stmtCount->execute();
    $stmtCount->bind_result($totalHotels);
    $stmtCount->fetch();
    $stmtCount->close();
    $totalPages = max(1, (int)ceil($totalHotels / $perPage));
    if ($currentPage > $totalPages) $currentPage = $totalPages;

    // --- Fetch paged hotels ---
    if ($search !== '') {
        $stmt = $conn->prepare(
            "SELECT id, name, location, rating, rooms, status, price_per_night, includes_text, image
             FROM hotels
             WHERE id LIKE ? OR name LIKE ? OR location LIKE ?
             ORDER BY id DESC
             LIMIT ? OFFSET ?"
        );
        $like = '%' . $search . '%';
        $stmt->bind_param('sssii', $like, $like, $like, $perPage, $offset);
    } else {
        $stmt = $conn->prepare(
            "SELECT id, name, location, rating, rooms, status, price_per_night, includes_text, image
             FROM hotels
             ORDER BY id DESC
             LIMIT ? OFFSET ?"
        );
        $stmt->bind_param('ii', $perPage, $offset);
    }
    $stmt->execute();
    $result = safe_get_result($stmt);
    while ($row = $result->fetch_assoc()) {
        $hotels[] = $row;
    }
    $stmt->close();
}
?>
