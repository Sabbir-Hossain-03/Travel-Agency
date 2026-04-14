<?php

function getReportStats($conn) {
    $stats = [
        'total_users' => 0,
        'total_revenue' => 0,
        'total_bookings' => 0,
        'total_tickets' => 0,
        'pending_bookings' => 0,
        'pending_payments' => 0
    ];

    // 1. Total active users (Customers and Admins)
    $customerQuery = $conn->query("SELECT COUNT(*) as count FROM customer WHERE status='Active'");
    if ($customerQuery) $stats['total_users'] += $customerQuery->fetch_assoc()['count'];

    $adminQuery = $conn->query("SELECT COUNT(*) as count FROM admin WHERE status='Active'");
    if ($adminQuery) $stats['total_users'] += $adminQuery->fetch_assoc()['count'];

    // 2. Total revenue & bookings from payments table
    // Handling both 'success' and 'paid' status
    $revenueQuery = $conn->query("SELECT SUM(amount) as total_revenue, COUNT(*) as total_bookings FROM payments WHERE payment_status IN ('success', 'paid')");
    if ($revenueQuery) {
        $row = $revenueQuery->fetch_assoc();
        $stats['total_revenue'] = isset($row['total_revenue']) ? $row['total_revenue'] : 0;
        $stats['total_bookings'] = isset($row['total_bookings']) ? $row['total_bookings'] : 0;
    }

    // 3. Total active ticket routes
    $ticketsQuery = $conn->query("SELECT COUNT(*) as count FROM tickets WHERE status='active'");
    if ($ticketsQuery) $stats['total_tickets'] = $ticketsQuery->fetch_assoc()['count'];

    // 4. Actionable Alerts (Pending items)
    $pendingBookingQuery = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE booking_status='pending'");
    if ($pendingBookingQuery) $stats['pending_bookings'] = $pendingBookingQuery->fetch_assoc()['count'];

    $pendingPaymentQuery = $conn->query("SELECT COUNT(*) as count FROM payments WHERE payment_status='pending'");
    if ($pendingPaymentQuery) $stats['pending_payments'] = $pendingPaymentQuery->fetch_assoc()['count'];

    return $stats;
}

function getMonthlyRevenue($conn, $limit = 6) {
    $monthlyRevenue = [];
    $limit = (int)$limit;
    $monthlyQuery = $conn->query("
        SELECT DATE_FORMAT(payment_date, '%M %Y') as month, SUM(amount) as revenue 
        FROM payments 
        WHERE payment_status IN ('success', 'paid') 
        GROUP BY DATE_FORMAT(payment_date, '%M %Y') 
        ORDER BY MIN(payment_date) DESC
        LIMIT $limit
    ");
    if ($monthlyQuery) {
        while($row = $monthlyQuery->fetch_assoc()) {
            $monthlyRevenue[] = $row;
        }
    }
    return $monthlyRevenue;
}

function getServiceRevenue($conn) {
    $serviceRevenue = [];
    $sql = "SELECT b.service_type, SUM(p.amount) as revenue 
            FROM payments p 
            JOIN bookings b ON p.booking_id = b.id 
            WHERE p.payment_status IN ('success', 'paid') 
            GROUP BY b.service_type";
    $res = $conn->query($sql);
    if ($res) {
        while($row = $res->fetch_assoc()) {
            $serviceRevenue[] = $row;
        }
    }
    return $serviceRevenue;
}

function getRecentTransactions($conn, $limit = 5) {
    $recentBookings = [];
    $limit = (int)$limit;
    $recentBookingQuery = $conn->query("SELECT booking_id, user_email, amount, payment_date, payment_status FROM payments ORDER BY payment_date DESC LIMIT $limit");
    if ($recentBookingQuery) {
        while($row = $recentBookingQuery->fetch_assoc()) {
            $recentBookings[] = $row;
        }
    }
    return $recentBookings;
}

function getMostPopularServices($conn, $limit = 5) {
    $popular = [];
    $limit = (int)$limit;
    $sql = "SELECT service_name, service_type, COUNT(*) as booking_count 
            FROM bookings 
            WHERE booking_status = 'confirmed' 
            GROUP BY service_name, service_type 
            ORDER BY booking_count DESC 
            LIMIT $limit";
    $res = $conn->query($sql);
    if ($res) {
        while($row = $res->fetch_assoc()) {
            $popular[] = $row;
        }
    }
    return $popular;
}

?>
