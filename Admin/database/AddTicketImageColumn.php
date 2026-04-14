<?php
// Add image column to tickets table
include_once(__DIR__ . '/dbconnection.php');

// Check if column already exists
$check_sql = "SHOW COLUMNS FROM tickets LIKE 'image'";
$result = $conn->query($check_sql);

if ($result->num_rows == 0) {
    // Column doesn't exist, add it
    $alter_sql = "ALTER TABLE tickets ADD COLUMN image VARCHAR(255) NULL AFTER seat_count";
    
    if ($conn->query($alter_sql)) {
        echo "✅ Successfully added image column to tickets table.<br>";
    } else {
        echo "❌ Error adding column: " . $conn->error . "<br>";
    }
} else {
    echo "ℹ️ Column image already exists in tickets table.<br>";
}

$conn->close();
?>
