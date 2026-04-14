<?php

$isLocal = in_array($_SERVER['SERVER_NAME'] ?? 'localhost', ['localhost', '127.0.0.1', '::1']);

if ($isLocal) {
    
    $host     = "localhost";
    $user     = "root";
    $password = "";
    $database = "avestra_db"; 
} else {
    $host     = "sql208.infinityfree.com";
    $user     = "if0_41288586";
    $password = "bQmut138SiJr";
    $database = "if0_41288586_avestra_db";
}

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    error_log("DB connection failed: " . $conn->connect_error);
    die("A database error occurred. Please try again later.");
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");