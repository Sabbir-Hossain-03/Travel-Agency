<?php
include_once(__DIR__ . '/dbconnection.php');

$items_per_page = 10;
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$page = max(1, $page);
$offset = ($page - 1) * $items_per_page;

$count_sql = "SELECT COUNT(*) as total FROM contact_messages";
$count_result = $conn->query($count_sql);
$count_row = $count_result->fetch_assoc();
$total_messages = isset($count_row['total']) ? $count_row['total'] : 0;
$total_pages = ($total_messages > 0) ? ceil($total_messages / $items_per_page) : 1;

$sql = "SELECT id, name, email, message, status, date FROM contact_messages ORDER BY date DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $items_per_page, $offset);
$stmt->execute();
$result = safe_get_result($stmt);

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}
$stmt->close();

if ($total_messages > 0) {
    $showing_from = ($offset) + 1;
    $showing_to = $offset + count($messages);
} else {
    $showing_from = 0;
    $showing_to = 0;
}
