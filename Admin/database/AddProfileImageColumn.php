<?php
// Add profile_image column to admin table
include_once(__DIR__ . '/dbconnection.php');

// Check if column already exists
$check_sql = "SHOW COLUMNS FROM admin LIKE 'profile_image'";
$result = $conn->query($check_sql);

if ($result->num_rows == 0) {
    // Column doesn't exist, add it
    $alter_sql = "ALTER TABLE admin ADD COLUMN profile_image VARCHAR(255) NULL AFTER password";
    
    if ($conn->query($alter_sql)) {
        echo "✅ Successfully added profile_image column to admin table.<br>";
    } else {
        echo "❌ Error adding column: " . $conn->error . "<br>";
    }
} else {
    echo "ℹ️ Column profile_image already exists in admin table.<br>";
}

$conn->close();
?>
