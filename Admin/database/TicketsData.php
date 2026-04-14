<?php
include_once(__DIR__ . '/dbconnection.php');

function getBusTickets($q = '', $status = '') {
    global $conn;
    $tickets = [];
    $sql = "SELECT * FROM tickets";
    $where = [];
    if ($q !== '') {
        $where[] = "(ticket_code LIKE '%" . $conn->real_escape_string($q) . "%' OR route LIKE '%" . $conn->real_escape_string($q) . "%')";
    }
    if ($status !== '') {
        $where[] = "status = '" . $conn->real_escape_string($status) . "'";
    }
    if (!empty($where)) {
        $sql .= " WHERE " . implode(' AND ', $where);
    }
    $sql .= " ORDER BY id DESC";
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $tickets[] = $row;
        }
    }
    return $tickets;
}

function insert_ticket($conn, $ticket_code, $route, $bus_class, $seat_count, $status) {
    $ticket_type = 'Bus';
    // Check for duplicate ticket_code
    $check = $conn->prepare("SELECT COUNT(*) FROM tickets WHERE ticket_code = ?");
    $check->bind_param("s", $ticket_code);
    $check->execute();

    $check->fetch();
    $check->close();
    $stmt = $conn->prepare("INSERT INTO tickets (ticket_code, ticket_type, route, bus_class, seat_count, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $ticket_code, $ticket_type, $route, $bus_class, $seat_count, $status);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

function update_ticket($conn, $id, $ticket_code, $route, $bus_class, $seat_count, $status) {
  
    $check = $conn->prepare("SELECT COUNT(*) FROM tickets WHERE ticket_code = ? AND id != ?");
    $check->bind_param("si", $ticket_code, $id);
    $check->execute();
    $check->fetch();
    $check->close();
    $stmt = $conn->prepare("UPDATE tickets SET ticket_code=?, route=?, bus_class=?, seat_count=?, status=? WHERE id=?");
    $stmt->bind_param("sssssi", $ticket_code, $route, $bus_class, $seat_count, $status, $id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}


