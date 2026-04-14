<?php
session_start();
include('../database/dbconnection.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    if (!empty($name) && !empty($email) && !empty($message)) {
     
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
            exit();
        }
        
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Your message has been sent successfully. We will get back to you soon!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error sending message. Please try again.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    }
    exit();
}
?>
