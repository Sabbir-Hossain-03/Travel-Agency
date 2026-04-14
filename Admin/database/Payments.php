<?php

function getAllPayments($conn, $limit = 20, $offset = 0) {
    $payments = [];
    $total = 0;
    
    // Get total count
    $countResult = $conn->query("SELECT COUNT(*) as total FROM payments");
    if ($countResult) {
        $row = $countResult->fetch_assoc();
        $total = $row['total'];
    }
    
    $limit = (int)$limit;
    $offset = (int)$offset;
    $result = $conn->query("SELECT * FROM payments ORDER BY id DESC LIMIT $limit OFFSET $offset");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $payments[] = $row;
        }
    }
    return ['payments' => $payments, 'total' => $total];
}

function searchPayments($conn, $search, $limit = 20, $offset = 0) {
    $payments = [];
    $total = 0;
    $search = trim($search);
    if (strpos(strtoupper($search), 'TX') === 0) {
        $search = substr($search, 2);
        if (is_numeric($search)) {
            $search = (int)$search - 100;
        }
    } elseif (strpos($search, '#') === 0) {
        $search = substr($search, 1);
    }
    $like = "%" . $conn->real_escape_string($search) . "%";
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM payments WHERE CAST(booking_id AS CHAR) LIKE '$like' OR LOWER(user_email) LIKE LOWER('$like')";
    $countResult = $conn->query($countSql);
    if ($countResult) {
        $row = $countResult->fetch_assoc();
        $total = $row['total'];
    }
    
    $limit = (int)$limit;
    $offset = (int)$offset;
    $sql = "SELECT * FROM payments WHERE CAST(booking_id AS CHAR) LIKE '$like' OR LOWER(user_email) LIKE LOWER('$like') ORDER BY id DESC LIMIT $limit OFFSET $offset";
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $payments[] = $row;
        }
    }
    return ['payments' => $payments, 'total' => $total];
}

