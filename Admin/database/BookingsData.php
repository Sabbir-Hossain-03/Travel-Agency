<?php

function getAllBookings($conn, $limit = 20, $offset = 0) {
    $bookings = [];
    $total = 0;
    
    $countResult = $conn->query("SELECT COUNT(*) as total FROM bookings");
    if ($countResult) {
        $row = $countResult->fetch_assoc();
        $total = $row['total'];
    }
    
    $limit = (int)$limit;
    $offset = (int)$offset;
    $result = $conn->query("SELECT * FROM bookings ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $bookings[] = $row;
        }
    }
    return ['bookings' => $bookings, 'total' => $total];
}

function searchBookings($conn, $search, $limit = 20, $offset = 0) {
    $bookings = [];
    $total = 0;
    $search = trim($search);

    $id_search = "";
    if (stripos($search, 'TX') === 0 && is_numeric(substr($search, 2))) {
        $numeric_id = (int)substr($search, 2) - 100;
        $id_search = " OR id = $numeric_id";
    } elseif (is_numeric($search)) {
        $id_search = " OR id = " . (int)$search;
    }

    $like = "%" . $conn->real_escape_string($search) . "%";
    
    $countSql = "SELECT COUNT(*) as total FROM bookings WHERE (user_email LIKE '$like' OR service_name LIKE '$like' OR booking_status LIKE '$like' $id_search)";
    $countResult = $conn->query($countSql);
    if ($countResult) {
        $row = $countResult->fetch_assoc();
        $total = $row['total'];
    }
    
    $limit = (int)$limit;
    $offset = (int)$offset;
    $sql = "SELECT * FROM bookings WHERE (user_email LIKE '$like' OR service_name LIKE '$like' OR booking_status LIKE '$like' $id_search) ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $bookings[] = $row;
        }
    }
    return ['bookings' => $bookings, 'total' => $total];
}
?>
