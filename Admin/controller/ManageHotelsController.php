<?php
session_start();

include ('../database/dbconnection.php');
include ('../validation/HotelValidation.php');

$action = $_POST['action'] ?? '';

function redirectBack() {
    header('Location: ../views/ManageHotels.php');
    exit;
}

// Upload folder (accessible from User/images/hotels/)
$uploadDir = __DIR__ . '/../../User/images/hotels/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// ── Handle image file upload ─────────────────────────────────────────────────
function handleImageUpload($uploadDir) {
    if (!isset($_FILES['hotel_image']) || $_FILES['hotel_image']['error'] !== UPLOAD_ERR_OK) {
        return null; // no upload
    }
    $file     = $_FILES['hotel_image'];
    $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed  = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $allowed)) {
        return false; // bad type
    }
    $filename = uniqid('hotel_', true) . '.' . $ext;
    $dest     = $uploadDir . $filename;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return false;
    }
    return $filename; // just the filename
}

// ── ADD or EDIT ──────────────────────────────────────────────────────────────
if ($action === 'add' || $action === 'edit') {

    $name          = trim($_POST['name']          ?? '');
    $location      = trim($_POST['location']      ?? '');
    $rating        = trim($_POST['rating']        ?? '');
    $rooms         = trim($_POST['rooms']         ?? '');
    $status        = trim($_POST['status']        ?? 'Inactive');
    $price         = ($_POST['price_per_night'] ?? '') !== '' ? floatval($_POST['price_per_night']) : null;
    $includes_text = trim($_POST['includes_text'] ?? '');
    $id            = preg_replace('/[^A-Za-z0-9]/', '', trim($_POST['id'] ?? ''));

    // Validate
    $errors = validateHotelForm($name, $location, $rating, $rooms, $status);
    if (!empty($errors)) {
        $_SESSION['hotel_error'] = implode(' ', $errors);
        redirectBack();
    }
    if (empty($id)) {
        $_SESSION['hotel_error'] = "Hotel ID is required (e.g., H101).";
        redirectBack();
    }

    // Check if ID already exists when adding
    if ($action === 'add') {
        $stmt_check = $conn->prepare("SELECT id FROM hotels WHERE id = ?");
        $stmt_check->bind_param("s", $id);
        $stmt_check->execute();
        $stmt_check->store_result();
        
        if ($stmt_check->num_rows > 0) {
            $stmt_check->close();
            $_SESSION['hotel_error'] = "Hotel ID '{$id}' already exists. Please use a unique ID.";
            redirectBack();
        }
        $stmt_check->close();
    }

    // Handle image
    $newFilename = handleImageUpload($uploadDir);
    if ($newFilename === false) {
        $_SESSION['hotel_error'] = "Invalid image file. Allowed: jpg, jpeg, png, gif, webp.";
        redirectBack();
    }

    // ── ADD ──────────────────────────────────────────────────────────────────
    if ($action === 'add') {
        if ($newFilename === null) {
            $_SESSION['hotel_error'] = "Please upload a hotel image.";
            redirectBack();
        }
        $stmt = $conn->prepare(
            "INSERT INTO hotels (id, name, location, rating, rooms, status, price_per_night, includes_text, image)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("ssssssdss",
            $id, $name, $location, $rating, $rooms, $status, $price, $includes_text, $newFilename
        );
        if ($stmt->execute()) {
            $_SESSION['hotel_success'] = "Hotel added successfully.";
        } else {
            // Clean up uploaded file on failure
            @unlink($uploadDir . $newFilename);
            $_SESSION['hotel_error'] = "Failed to add hotel: " . $stmt->error;
        }
        redirectBack();
    }

    // ── EDIT ─────────────────────────────────────────────────────────────────
    if ($newFilename !== null) {
        // New image uploaded — update image column and delete old file
        // Get old filename first
        $old = $conn->prepare("SELECT image FROM hotels WHERE id=? LIMIT 1");
        $old->bind_param("s", $id);
        $old->execute();
        $old->bind_result($oldFile);
        $old->fetch();
        $old->close();

        $stmt = $conn->prepare(
            "UPDATE hotels SET name=?, location=?, rating=?, rooms=?, status=?, price_per_night=?, includes_text=?, image=? WHERE id=?"
        );
        $stmt->bind_param("ssssssdss",
            $name, $location, $rating, $rooms, $status, $price, $includes_text, $newFilename, $id
        );
        if ($stmt->execute()) {
            // Remove old image file if different
            if ($oldFile && $oldFile !== $newFilename) {
                @unlink($uploadDir . $oldFile);
            }
            $_SESSION['hotel_success'] = "Hotel updated successfully.";
        } else {
            @unlink($uploadDir . $newFilename);
            $_SESSION['hotel_error'] = "Failed to update hotel: " . $stmt->error;
        }
    } else {
        // No new image — keep existing
        $stmt = $conn->prepare(
            "UPDATE hotels SET name=?, location=?, rating=?, rooms=?, status=?, price_per_night=?, includes_text=? WHERE id=?"
        );
        $stmt->bind_param("sssssdss",
            $name, $location, $rating, $rooms, $status, $price, $includes_text, $id
        );
        if ($stmt->execute()) {
            $_SESSION['hotel_success'] = "Hotel updated successfully.";
        } else {
            $_SESSION['hotel_error'] = "Failed to update hotel: " . $stmt->error;
        }
    }
    redirectBack();
}

// ── TOGGLE STATUS ─────────────────────────────────────────────────────────────
if ($action === 'toggle') {
    $id = preg_replace('/[^A-Za-z0-9]/', '', trim($_POST['id'] ?? ''));
    if (empty($id)) {
        $_SESSION['hotel_error'] = "Invalid hotel ID.";
        redirectBack();
    }

    $currentStatus = $_POST['current_status'] ?? 'Inactive';
    $newStatus = (strcasecmp($currentStatus, 'Active') === 0) ? 'Inactive' : 'Active';

    $stmt = $conn->prepare("UPDATE hotels SET status=? WHERE id=?");
    $stmt->bind_param("ss", $newStatus, $id);

    if ($stmt->execute()) {
        $_SESSION['hotel_success'] = "Hotel status updated to {$newStatus}.";
    } else {
        $_SESSION['hotel_error'] = "Failed to update status.";
    }
    redirectBack();
}

// ── DELETE ────────────────────────────────────────────────────────────────────
if ($action === 'delete') {
    $id     = preg_replace('/[^A-Za-z0-9]/', '', trim($_POST['id'] ?? ''));
    $status = $_POST['status'] ?? '';

    if (empty($id)) {
        $_SESSION['hotel_error'] = "Invalid hotel ID.";
        redirectBack();
    }
    if (strcasecmp($status, 'Inactive') !== 0) {
        $_SESSION['hotel_error'] = "Only inactive hotels can be deleted.";
        redirectBack();
    }

    // Get image filename before deleting
    $old = $conn->prepare("SELECT image FROM hotels WHERE id=? LIMIT 1");
    $old->bind_param("s", $id);
    $old->execute();
    $old->bind_result($oldFile);
    $old->fetch();
    $old->close();

    $stmt = $conn->prepare("DELETE FROM hotels WHERE id=?");
    $stmt->bind_param("s", $id);

    if ($stmt->execute()) {
        if ($oldFile) @unlink($uploadDir . $oldFile);
        $_SESSION['hotel_success'] = "Hotel deleted successfully.";
    } else {
        $_SESSION['hotel_error'] = "Failed to delete hotel.";
    }
    redirectBack();
}

$_SESSION['hotel_error'] = "Invalid action.";
redirectBack();
