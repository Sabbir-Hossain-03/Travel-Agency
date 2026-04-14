<?php
// 1. Define functions first
if (!function_exists('getActiveToursCount')) {
    function getActiveToursCount($tours)
    {
        if (!is_array($tours)) return 0;
        return count(array_filter($tours, function ($tour) {
            return isset($tour['status']) && strcasecmp($tour['status'], 'Active') === 0;
        }));
    }
}

if (!function_exists('getTotalToursCount')) {
    function getTotalToursCount($tours)
    {
        if (!is_array($tours)) return 0;
        return count($tours);
    }
}

if (!function_exists('getTourById')) {
    function getTourById($conn, $id) {
        if (!$conn || $conn->connect_error) return null;
        $sql = "SELECT id, name, destination, duration, price, status FROM tours WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = safe_get_result($stmt);
        return $result->fetch_assoc();
    }
}

function getAllTours($conn) {
    $tours_list = array();
    if (!$conn || $conn->connect_error) return $tours_list;
    
    $sql = "SELECT id, name, destination, duration, price, status FROM tours ORDER BY id DESC";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $tours_list[] = $row;
        }
    }
    
    return $tours_list;
}

// 2. Clear variable and include connection
$tours = array();
include_once(__DIR__ . '/dbconnection.php');

// 3. Fetch data if connection is alive
if ($conn && !$conn->connect_error) {
    $tours = getAllTours($conn);
}
?>
