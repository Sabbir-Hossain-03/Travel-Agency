<?php
include('../database/dbconnection.php');
include('../database/TicketsData.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/ManageTickets.php');
    exit;
}

if (isset($_POST['action']) && $_POST['action'] === 'toggle_status') {
    $id = (int)$_POST['id'];
    $current_status = $_POST['current_status'];
    $new_status = (strtolower($current_status) === 'active') ? 'inactive' : 'active';
    $stmt = $conn->prepare("UPDATE tickets SET status=? WHERE id=?");
    $stmt->bind_param("si", $new_status, $id);
    $result = $stmt->execute();
    $stmt->close();
    if ($result) {
        header("Location: ../views/ManageTickets.php?msg=Ticket status updated successfully");
    } else {
        header("Location: ../views/ManageTickets.php?err=Failed to update ticket status");
    }
    exit;
}

if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = (int)$_POST['id'];
    $stmt = $conn->prepare("DELETE FROM tickets WHERE id=?");
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();
    $stmt->close();
    if ($result) {
        header("Location: ../views/ManageTickets.php?msg=Ticket deleted successfully");
    } else {
        header("Location: ../views/ManageTickets.php?err=Failed to delete ticket");
    }
    exit;
}

if (isset($_POST['action']) && $_POST['action'] === 'add') {
    $ticket_code = trim($_POST['ticket_code']);
    $route = trim($_POST['route']);
    $bus_class = $_POST['bus_class'];
    $seat_count = (int)$_POST['seat_count'];
    $status = $_POST['status'];
    $ticket_type = 'Bus';
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
    $image_filename = NULL;
    if (isset($_FILES['ticket_image']) && $_FILES['ticket_image']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['ticket_image']['name'], PATHINFO_EXTENSION);
        $image_filename = 'ticket_' . time() . '_' . rand(1000,9999) . '.' . $ext;
        $target_path = dirname(__DIR__) . '/images/' . $image_filename;
        if (!move_uploaded_file($_FILES['ticket_image']['tmp_name'], $target_path)) {
            $image_filename = NULL;
        }
    }
    // Check for duplicate ticket_code
    $dupCheck = $conn->prepare("SELECT id FROM tickets WHERE ticket_code = ?");
    $dupCheck->bind_param("s", $ticket_code);
    $dupCheck->execute();
    $dupCheck->store_result();
    if ($dupCheck->num_rows > 0) {
        $dupCheck->close();
        header("Location: ../views/ManageTickets.php?err=Ticket code '" . urlencode($ticket_code) . "' already exists. Please use a different code.");
        exit;
    }
    $dupCheck->close();

    $stmt = $conn->prepare("INSERT INTO tickets (ticket_code, ticket_type, route, bus_class, seat_count, status, price, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssds", $ticket_code, $ticket_type, $route, $bus_class, $seat_count, $status, $price, $image_filename);
    $result = $stmt->execute();
    $stmt->close();
    if ($result) {
        header("Location: ../views/ManageTickets.php?msg=Ticket added successfully");
    } else {
        header("Location: ../views/ManageTickets.php?err=Failed to add ticket");
    }
    exit;
}

if (isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = (int)$_POST['id'];
    $ticket_code = trim($_POST['ticket_code']);
    $route = trim($_POST['route']);
    $bus_class = $_POST['bus_class'];
    $seat_count = (int)$_POST['seat_count'];
    $status = $_POST['status'];
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
    $image_filename = NULL;
    if (isset($_FILES['ticket_image']) && $_FILES['ticket_image']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['ticket_image']['name'], PATHINFO_EXTENSION);
        $image_filename = 'ticket_' . time() . '_' . rand(1000,9999) . '.' . $ext;
        $target_path = dirname(__DIR__) . '/images/' . $image_filename;
        if (!move_uploaded_file($_FILES['ticket_image']['tmp_name'], $target_path)) {
            $image_filename = NULL;
        }
    }
    if ($image_filename) {
        $stmt = $conn->prepare("UPDATE tickets SET ticket_code=?, route=?, bus_class=?, seat_count=?, status=?, price=?, image=? WHERE id=?");
        $stmt->bind_param("sssssssi", $ticket_code, $route, $bus_class, $seat_count, $status, $price, $image_filename, $id);
    } else {
        $stmt = $conn->prepare("UPDATE tickets SET ticket_code=?, route=?, bus_class=?, seat_count=?, status=?, price=? WHERE id=?");
        $stmt->bind_param("ssssssi", $ticket_code, $route, $bus_class, $seat_count, $status, $price, $id);
    }
    $result = $stmt->execute();
    $stmt->close();
    if ($result) {
        header("Location: ../views/ManageTickets.php?msg=Ticket updated successfully");
    } else {
        header("Location: ../views/ManageTickets.php?err=Failed to update ticket");
    }
    exit;
}
