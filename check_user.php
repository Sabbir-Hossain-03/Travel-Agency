<?php
require_once __DIR__ . '/Admin/database/dbconnection.php';

$email = 'mursalinleon95@gmail.com';

echo "Checking email: " . $email . "\n";

$check_customer = $conn->prepare("SELECT email FROM customer WHERE email = ?");
$check_customer->bind_param("s", $email);
$check_customer->execute();
$res_customer = $check_customer->get_result();
echo "Customer table matches: " . $res_customer->num_rows . "\n";

$check_admin = $conn->prepare("SELECT email FROM admin WHERE email = ?");
$check_admin->bind_param("s", $email);
$check_admin->execute();
$res_admin = $check_admin->get_result();
echo "Admin table matches: " . $res_admin->num_rows . "\n";

if ($res_customer->num_rows === 0 && $res_admin->num_rows === 0) {
    echo "NO MATCH FOUND. Listing all emails context:\n";
    $all = $conn->query("SELECT email, 'customer' as source FROM customer UNION SELECT email, 'admin' as source FROM admin");
    while($row = $all->fetch_assoc()) {
        echo "- " . $row['email'] . " (" . $row['source'] . ")\n";
    }
}
?>
