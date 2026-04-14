<?php
include_once(__DIR__ . '/dbconnection.php');

$search = isset($_POST['search']) ? $_POST['search'] : '';
$role_filter = isset($_POST['role_filter']) ? $_POST['role_filter'] : '';
$status_filter = isset($_POST['status_filter']) ? $_POST['status_filter'] : '';

$items_per_page = 5;
$current_page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;

$where_conditions = [];
$params = [];
$types = '';

if (!empty($search)) {
    $where_conditions[] = "(username LIKE ? OR email LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'ss';
}

if (!empty($role_filter)) {
    $where_conditions[] = "role = ?";
    $params[] = $role_filter;
    $types .= 's';
}

if (!empty($status_filter)) {
    $check_column = $conn->query("SHOW COLUMNS FROM customer LIKE 'status'");
    if ($check_column->num_rows > 0) {
        $where_conditions[] = "status = ?";
        $params[] = $status_filter;
        $types .= 's';
    }
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

$admin_where = "status != 'Pending'";
if (!empty($where_conditions)) {
    $admin_where .= " AND " . implode(" AND ", $where_conditions);
}

$customer_count = 0;
$admin_count = 0;

if (!empty($params)) {
    $count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM customer $where_clause");
    $count_stmt->bind_param($types, ...$params);
    $count_stmt->execute();
    $result = safe_get_result($count_stmt);
    $row = $result->fetch_assoc();
    $customer_count = intval(isset($row['total']) ? $row['total'] : 0);
    $count_stmt->close();
} else {
    $result = $conn->query("SELECT COUNT(*) as total FROM customer");
    $row = $result->fetch_assoc();
    $customer_count = intval(isset($row['total']) ? $row['total'] : 0);
}

// Count admins (excluding Pending)
$admin_where_simple = "status != 'Pending'";
if (!empty($where_clause)) {
    $admin_where_simple .= " AND " . ltrim(str_replace("WHERE ", "", $where_clause));
}

if (!empty($params)) {
    $count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM admin WHERE $admin_where_simple");
    $count_stmt->bind_param($types, ...$params);
    $count_stmt->execute();
    $result = safe_get_result($count_stmt);
    $row = $result->fetch_assoc();
    $admin_count = intval(isset($row['total']) ? $row['total'] : 0);
    $count_stmt->close();
} else {
    $result = $conn->query("SELECT COUNT(*) as total FROM admin WHERE $admin_where_simple");
    $row = $result->fetch_assoc();
    $admin_count = intval(isset($row['total']) ? $row['total'] : 0);
}

$total_users = $customer_count + $admin_count;
$total_pages = ($total_users > 0) ? ceil($total_users / $items_per_page) : 0;

// Ensure valid pagination values
$total_pages = ($total_users > 0) ? max(1, (int)ceil($total_users / $items_per_page)) : 1;
$current_page = max(1, min($current_page, $total_pages));
$offset = ($current_page - 1) * $items_per_page;

// Fetch users from both customer and admin tables with pagination using UNION - exclude Pending admins
$sql = "SELECT username, email, role, status, date as created_at FROM customer $where_clause
        UNION ALL
        SELECT username, email, role, status, date as created_at FROM admin WHERE $admin_where
        ORDER BY created_at DESC LIMIT $items_per_page OFFSET $offset";


if (!empty($params)) {
    // For UNION queries, we need to bind params twice (once for each SELECT)
    $all_params = array_merge($params, $params);
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types . $types, ...$all_params);
    $stmt->execute();
    $result = safe_get_result($stmt);
} else {
    $result = $conn->query($sql);
}


// Fetch all users into array
$users = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Calculate pagination display values
if ($total_users > 0) {
    $showing_from = ($offset) + 1;
    $showing_to = $offset + count($users);
} else {
    $showing_from = 0;
    $showing_to = 0;
}

// Debug - uncomment to see values
echo "<!-- DEBUG: offset=$offset, current_page=$current_page, total_users=$total_users, items_per_page=$items_per_page, count(users)=" . count($users) . ", showing_from=$showing_from, showing_to=$showing_to, total_pages=$total_pages -->";

