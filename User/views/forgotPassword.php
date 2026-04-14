<?php
include '../controller/forgotPasswordFormDataHandler.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avestra Travel Agency</title>
    <link rel="stylesheet" href="../styleSheets/forgotPassword.css">
    <link rel="icon" href="../images/logo.png" type="image/png">
</head>

<body>
    <div class="forgot-container">
        <h2>Forgot Password</h2>
<?php
header("Location: ../../Admin/views/forgotPassword.php");
exit();
?>
                    title="Please enter your registered email address"
                    value="<?php echo htmlspecialchars($email); ?>">
                <label for="email">Enter your email address</label>
                <?php if (!empty($email_error)): ?>
                    <span class="error-message"><?php echo $email_error; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <input type="password" id="new-password" name="new-password" placeholder=" " autocomplete="new-password"
                    title="Password must be at least 6 characters long">
                <label for="new-password">New Password</label>
                <?php if (!empty($newPassword_error)): ?>
                    <span class="error-message"><?php echo $newPassword_error; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <input type="password" id="confirm-password" name="confirm-password" placeholder=" " autocomplete="new-password"
                    title="Re-enter your new password. It must match the password above">
                <label for="confirm-password">Confirm New Password</label>
                <?php if (!empty($confirmPassword_error)): ?>
                    <span class="error-message"><?php echo $confirmPassword_error; ?></span>
                <?php endif; ?>
            </div>

            <button type="submit">Reset Password</button>
        </form>
        <div class="forgot-footer">
            Remembered your password? <a href="loginPage.php">Login</a>
        </div>
    </div>
     <script src="../js/theme.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            applyStoredTheme();
        });
    </script>
</html>