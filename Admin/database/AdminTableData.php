<?php
// Include database connection
include_once(__DIR__ . '/dbconnection.php');


if (!$conn->query($create_admin_table)) {
    // Silently fail if table already exists
}

// Function to get all admin users
function getAdminUsers($conn, $search = '', $status_filter = '') {
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

    if (!empty($status_filter)) {
        $where_conditions[] = "status = ?";
        $params[] = $status_filter;
        $types .= 's';
    }

    $where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";
    
    $sql = "SELECT * FROM admin $where_clause ORDER BY date DESC";
    $stmt = $conn->prepare($sql);
    
    if (!empty($types)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = safe_get_result($stmt);
    $admins = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    return $admins;
}

// Function to check if admin exists by email
function adminExists($conn, $email) {
    $stmt = $conn->prepare("SELECT email FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = safe_get_result($stmt);
    $exists = $result->num_rows > 0;
    $stmt->close();
    return $exists;
}
?>
