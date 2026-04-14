<?php
session_start();
include('../database/dbconnection.php');

$admin_email = $_SESSION['admin_email'] ?? '';

if (empty($admin_email)) {
    $_SESSION['error_message'] = 'Please log in to update your profile.';
    header('Location: ../views/MyProfile.php');
    exit();
}

if (isset($_FILES['profile-image']) && $_FILES['profile-image']['error'] === 0) {
    // Validate using actual file contents, not the client-supplied MIME type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $real_mime = $finfo->file($_FILES['profile-image']['tmp_name']);
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_size = $_FILES['profile-image']['size'];

    $proceed_with_upload = true;

    if (!in_array($real_mime, $allowed_types)) {
        $_SESSION['error_message'] = 'Invalid file type. Only JPG, PNG, and GIF are allowed.';
        $proceed_with_upload = false;
    }
    elseif ($file_size > 5 * 1024 * 1024) {
        $_SESSION['error_message'] = 'File size too large. Maximum 5MB allowed.';
        $proceed_with_upload = false;
    }

    if ($proceed_with_upload) {
        $upload_dir = '../images/profiles/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $file_extension = pathinfo($_FILES['profile-image']['name'], PATHINFO_EXTENSION);
        $new_filename = 'profile_' . md5($admin_email) . '.' . $file_extension;
        $upload_path = $upload_dir . $new_filename;
        
        $sql = "SELECT profile_image FROM admin WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $admin_email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $old_image = $row['profile_image'];
            if (!empty($old_image) && file_exists('../images/profiles/' . $old_image)) {
                unlink('../images/profiles/' . $old_image);
            }
        }
        $stmt->close();

        // Move uploaded file
        if (move_uploaded_file($_FILES['profile-image']['tmp_name'], $upload_path)) {
            // Update database with new image filename
            $sql = "UPDATE admin SET profile_image = ? WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $new_filename, $admin_email);
            
            if ($stmt->execute()) {
                $_SESSION['success_message'] = 'Profile image updated successfully!';
            } else {
                $_SESSION['error_message'] = 'Error updating profile image in database.';
            }
            $stmt->close();
        } else {
            $_SESSION['error_message'] = 'Error uploading file.';
        }
    }
}

// Handle other profile updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full-name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if (!empty($fullName) && !empty($email)) {
        // Prepare dynamic update query using correct column names from admin table
        $sql = "UPDATE admin SET username = ?, email = ?, phoneNumber = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $fullName, $email, $phone, $admin_email);
        
        if ($stmt->execute()) {
            // Update session vars to reflect the new state immediately
            $_SESSION['admin_name'] = $fullName;
            $_SESSION['admin_email'] = $email; // Need to update the session email too if they change it
            $_SESSION['admin_phone'] = $phone;
            $_SESSION['success_message'] = 'Profile updated successfully!';
        } else {
            $_SESSION['error_message'] = 'Error updating profile database details.';
        }
        $stmt->close();
    }
}

header('Location: ../views/MyProfile.php');
exit();
?>
