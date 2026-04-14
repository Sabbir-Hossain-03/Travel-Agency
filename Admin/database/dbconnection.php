<?php
include_once(__DIR__ . '/mysqli_poly.php');

// Auto-detect environment: use local DB on localhost/XAMPP, live DB on production
$isLocal = in_array($_SERVER['SERVER_NAME'] ?? 'localhost', ['localhost', '127.0.0.1', '::1']);

if ($isLocal) {
    // Local XAMPP credentials
    $host     = "localhost";
    $user     = "root";
    $password = "";
    $database = "avestra_db"; // Your local MySQL database name
} else {
    $host     = "sql208.infinityfree.com";
    $user     = "if0_41288586";
    $password = "bQmut138SiJr";
    $database = "if0_41288586_avestra_db";
}

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    error_log("DB connection failed: " . $conn->connect_error);
    die("A database error occurred. Please try again later.");
}

$conn->set_charset("utf8mb4");
