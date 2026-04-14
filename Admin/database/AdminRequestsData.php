<?php
// Include database connection
include_once(__DIR__ . '/dbconnection.php');

// Ensure admin table has id column (check and add if missing)
$check_id = $conn->query("SHOW COLUMNS FROM admin LIKE 'id'");
if ($check_id->num_rows == 0) {
    // Add id column if it doesn't exist
    $conn->query("ALTER TABLE admin ADD COLUMN id INT AUTO_INCREMENT PRIMARY KEY FIRST");
}

// Pagination setup for pending admin requests (use pending_ prefix to avoid conflicts)
$pending_items_per_page = 10;
$pending_current_page = isset($_POST['pending_page']) ? (int)$_POST['pending_page'] : 1;
$pending_offset = ($pending_current_page - 1) * $pending_items_per_page;

// Get total count of pending admin requests
$count_sql = "SELECT COUNT(*) as total FROM admin WHERE status = 'Pending'";
$count_result = $conn->query($count_sql);
$total_requests = $count_result->fetch_assoc()['total'];
$pending_total_pages = ($total_requests > 0) ? ceil($total_requests / $pending_items_per_page) : 0;

// Get pending admin requests from admin table
$requests_sql = "SELECT id, username, email, phoneNumber as phone_number, date as requested_date FROM admin WHERE status = 'Pending' ORDER BY date DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($requests_sql);
$stmt->bind_param("ii", $pending_items_per_page, $pending_offset);
$stmt->execute();
$requests_result = safe_get_result($stmt);
$pending_requests = $requests_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Set pagination variables
$pending_showing_from = ($total_requests > 0) ? $pending_offset + 1 : 0;
$pending_showing_to = min($pending_offset + $pending_items_per_page, $total_requests);
?>
