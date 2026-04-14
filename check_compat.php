<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>PHP Environment Check</h1>";
echo "PHP Version: " . phpversion() . "<br>";

if (function_exists('mysqli_connect')) {
    echo "MySQLi is enabled.<br>";
} else {
    echo "MySQLi is NOT enabled.<br>";
}

if (method_exists('mysqli_stmt', 'get_result')) {
    echo "mysqli_stmt::get_result() is AVAILABLE.<br>";
} else {
    echo "mysqli_stmt::get_result() is NOT AVAILABLE.<br>";
}

echo "<h2>Directory Check</h2>";
echo "Current directory: " . __DIR__ . "<br>";
echo "Document root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
?>
