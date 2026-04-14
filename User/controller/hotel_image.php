<?php
include '../database/dbconnection.php';

$id = preg_replace('/[^A-Za-z0-9]/', '', $_GET['id'] ?? '');
if (empty($id)) {
    http_response_code(404);
    echo "Missing or invalid hotel id.";
    exit;
}

$stmt = $conn->prepare("SELECT image FROM hotels WHERE id = ? LIMIT 1");
$stmt->bind_param("s", $id);
$stmt->execute();
$r = $stmt->get_result();

$row = $r->fetch_assoc();
$img = $row['image'] ?? null;


if ($img && strlen($img) > 0) {
    // Try to detect MIME type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->buffer($img);
    if (!$mime || strpos($mime, 'image/') !== 0) {
        // fallback to jpeg
        $mime = 'image/jpeg';
    }
    header('Content-Type: ' . $mime);
    header('Content-Length: ' . strlen($img));
    header('Pragma: public');
    header('Cache-Control: max-age=86400');
    echo $img;
    exit;
} else {
    // Debug: No image data found
    header('Content-Type: text/plain');
    echo "No image data found for hotel id $id.";
    exit;
}

// Fallback to default image (unreachable with above exit, but kept for reference)
$default = __DIR__ . '/../images/hotel1.jpg';
if (file_exists($default)) {
    header('Content-Type: image/jpeg');
    readfile($default);
    exit;
}

http_response_code(404);
echo "Image not found and no default image available.";
