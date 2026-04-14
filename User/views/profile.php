<?php
include 'session_check.php';
include 'dark_mode.php';
include '../database/dbconnection.php';

// Redirect admins to their specific profile page
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: ../../Admin/views/MyProfile.php");
    exit();
}

$old_email = $_SESSION['email'];

$stmt = $conn->prepare("SELECT username, email, phoneNumber, role, image FROM customer WHERE email=?");
$stmt->bind_param("s", $old_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    // If user not in customer table, it might be an admin that wasn't redirected above 
    // or a session mismatch. Redirect to home as a safety.
    header("Location: user_dashboard.php");
    exit();
}
$user = $result->fetch_assoc();
$current_image = !empty($user['image']) ? $user['image'] : 'logo.png';

$success = "";
$error = "";

if (isset($_SESSION['profile_success'])) {
    $success = $_SESSION['profile_success'];
    unset($_SESSION['profile_success']);
}
if (isset($_SESSION['profile_error'])) {
    $error = $_SESSION['profile_error'];
    unset($_SESSION['profile_error']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $phone    = trim($_POST['phoneNumber']);
    $new_email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    $image_path = $user['image']; // Keep existing by default

    if (!preg_match("/^[a-zA-Z\s]+$/", $username)) {
        $error = "Name can contain only letters and spaces.";
    } elseif (!preg_match("/^[0-9]{11}$/", $phone)) {
        $error = "Phone number must be exactly 11 digits.";
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        $email_available = true;
        if ($new_email !== $old_email) {
            $check = $conn->prepare("SELECT email FROM customer WHERE email=?");
            $check->bind_param("s", $new_email);
            $check->execute();
            if ($check->get_result()->num_rows > 0) {
                $email_available = false;
                $error = "Email is already in use.";
            }
        }

        if ($email_available) {
            // Handle image upload
            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../images/profiles/';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                
                $file_tmp = $_FILES['profile_picture']['tmp_name'];
                $file_name = time() . '_' . basename($_FILES['profile_picture']['name']);
                $target_file = $upload_dir . $file_name;
                
                $file_type = mime_content_type($file_tmp);
                if (in_array($file_type, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
                    if (move_uploaded_file($file_tmp, $target_file)) {
                        $image_path = "profiles/" . $file_name;
                    } else {
                        $error = "Failed to upload image.";
                    }
                } else {
                    $error = "Invalid image format. Only JPG, PNG, GIF, WEBP allowed.";
                }
            }

            if (empty($error)) {
                if (!empty($password)) {
                    if ($password !== $confirm_password) {
                        $error = "Passwords do not match.";
                    } elseif (strlen($password) < 6) {
                        $error = "Password must be at least 6 characters.";
                    } else {
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $update = $conn->prepare("UPDATE customer SET username=?, email=?, phoneNumber=?, image=?, password=? WHERE email=?");
                        $update->bind_param("ssssss", $username, $new_email, $phone, $image_path, $hashed_password, $old_email);
                    }
                } else {
                    $update = $conn->prepare("UPDATE customer SET username=?, email=?, phoneNumber=?, image=? WHERE email=?");
                    $update->bind_param("sssss", $username, $new_email, $phone, $image_path, $old_email);
                }

                // Save theme preference to session immediately
                if (isset($_POST['user_theme'])) {
                    $_SESSION['user_theme'] = $_POST['user_theme'];
                }

                if (empty($error) && isset($update)) {
                    if ($update->execute()) {
                        $_SESSION['username'] = $username;
                        $_SESSION['email'] = $new_email;
                        $_SESSION['profile_success'] = "Profile updated successfully.";
                        
                        header("Location: profile.php");
                        exit();
                    } else {
                        $error = "Update failed.";
                    }
                }
            }
        }
    }
    
    if (!empty($error)) {
        $_SESSION['profile_error'] = $error;
        header("Location: profile.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile | Avestra</title>
    <link rel="stylesheet" href="../styleSheets/user.css">
    <link rel="stylesheet" href="../styleSheets/profile.css">
    <link rel="stylesheet" href="../styleSheets/footer.css">
    <link rel="stylesheet" href="../styleSheets/user-dark-mode.css?v=<?php echo time(); ?>">
    <link rel="icon" href="../images/logo.png" type="image/png">
</head>
<body class="<?= $session_theme_set ? ($is_dark ? 'dark-mode' : 'light-mode') : '' ?>">
    <script>
        // Fallback for session-less theme application
        if (!<?= $session_theme_set ? 'true' : 'false' ?>) {
            const theme = localStorage.getItem('theme') || 'light';
            document.body.classList.add(theme + '-mode');
        }
    </script>

<?php include 'nav.php'; ?>

<div class="profile-container">
    <?php if ($success): ?><p class="message success"><?= $success ?></p><?php endif; ?>
    <?php if ($error): ?><p class="message error"><?= $error ?></p><?php endif; ?>

    <div class="profile-cover">
         <h2 class="profile-title">My Profile</h2>
    </div>

    <form method="post" enctype="multipart/form-data" class="profile-form-wrapper" id="profileForm">
        
        <div class="profile-header-section">
            <div class="profile-image-container">
                <img src="../images/<?= htmlspecialchars($current_image) ?>" alt="Profile Picture" class="profile-avatar" id="avatarPreview" onerror="this.src='../images/logo.png'">
                <div class="profile-image-upload">
                    <label for="profile_picture" class="upload-btn" title="Change Picture">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                    </label>
                    <input type="file" name="profile_picture" id="profile_picture" accept="image/*" style="display: none;" onchange="previewImage(event)">
                </div>
            </div>
            <div class="profile-user-info">
                <h3><?= htmlspecialchars($user['username']) ?></h3>
                <span class="role-badge"><?= ucfirst($user['role']) ?></span>
            </div>
        </div>

        <div class="profile-info-grid">
            <div class="profile-info-row">
                <label>Full Name</label>
                <div class="input-wrapper">
                    <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>
            </div>
            <div class="profile-info-row">
                <label>Email Address</label>
                <div class="input-wrapper">
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
            </div>
            <div class="profile-info-row">
                <label>Phone Number</label>
                <div class="input-wrapper">
                    <input type="text" name="phoneNumber" value="<?= htmlspecialchars($user['phoneNumber']) ?>" required>
                </div>
            </div>
            <div class="profile-info-row">
                <label>Account Type</label>
                <div class="input-wrapper">
                    <input type="text" value="<?= ucfirst($user['role']) ?>" readonly>
                </div>
            </div>
            <div class="profile-info-row">
                <label>Site Theme</label>
                <div class="input-wrapper">
                    <select name="user_theme" id="user_theme_select" style="width: 100%; padding: 14px 16px; border-radius: 12px; border: 1.5px solid #e2e8f0; background: #f8fafc; font-family: inherit; font-weight: 500; appearance: none; cursor: pointer;">
                        <option value="light" <?= !$is_dark ? 'selected' : '' ?>>Light Mode</option>
                        <option value="dark" <?= $is_dark ? 'selected' : '' ?>>Dark Mode</option>
                    </select>
                </div>
            </div>
            <div class="profile-info-row">
                <label>New Password <span class="optional-text">(Optional)</span></label>
                <div class="input-wrapper">
                    <input type="password" name="password" placeholder="Enter new password">
                </div>
            </div>
            <div class="profile-info-row">
                <label>Confirm Password</label>
                <div class="input-wrapper">
                    <input type="password" name="confirm_password" placeholder="Confirm new password">
                </div>
            </div>
        </div>

        <div class="profile-actions">
            <button type="submit" class="save-changes-btn">Save Changes</button>
        </div>
    </form>
</div>

<!-- Custom Confirmation Modal -->
<div id="confirmModal" class="custom-modal">
    <div class="modal-content">
        <h3>Confirm Update</h3>
        <p>Are you sure you want to save your profile changes?</p>
        <div class="modal-actions">
            <button type="button" class="btn-cancel" onclick="closeConfirmModal()">Cancel</button>
            <button type="button" class="btn-confirm" onclick="submitProfileForm()">Yes</button>
        </div>
    </div>
</div>

<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const output = document.getElementById('avatarPreview');
        output.src = reader.result;
    };
    if(event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
    }
}

// Hide messages after 3 seconds
document.addEventListener("DOMContentLoaded", function() {
    const message = document.querySelector('.message');
    if (message) {
        setTimeout(() => {
            message.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            message.style.opacity = '0';
            message.style.transform = 'scale(0.95)';
            setTimeout(() => message.remove(), 500);
        }, 3000);
    }
});

// Custom Modal Logic
const profileForm = document.getElementById('profileForm');
const confirmModal = document.getElementById('confirmModal');

profileForm.addEventListener('submit', function(e) {
    e.preventDefault(); // Stop default submission
    confirmModal.classList.add('active');
});

function closeConfirmModal() {
    confirmModal.classList.remove('active');
}
// Hide modal and submit programmatically
function submitProfileForm() {  
    const themeSelect = document.getElementById('user_theme_select');
    if (themeSelect) {
        localStorage.setItem('theme', themeSelect.value);
    }
    confirmModal.classList.remove('active');
    profileForm.submit();
}
</script>

</body>
<?php include 'footer.php'; ?>
</html>