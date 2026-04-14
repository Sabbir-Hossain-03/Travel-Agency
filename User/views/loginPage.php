
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avestra Travel Agency</title>
    <link rel="stylesheet" href="../styleSheets/loginPage.css">
    <link rel="icon" href="../images/logo.png" type="image/png">
<?php
header("Location: ../../Admin/views/loginPage.php");
exit();
?>
</head>

<body>
    <div class="login-container">
        <div class="logo-container">
            <a href="homePage.php">
                <img src="../images/logo.png" alt="Avestra Travel Agency Logo" width="100" height="132">
            </a>
        </div>
        <h2>Login to Your Account</h2>
        <?php include '../controller/LoginMessageDisplay.php'; ?>
        <?php
        // Clear session after displaying messages
        unset($_SESSION['login_form_data']);
        unset($_SESSION['login_form_errors']);
        ?>
        
        <form class="login-form" action="../controller/loginValidation.php" method="post">
            <div class="form-group">
                <input type="email" id="email" name="email" placeholder=" " 
                    autocomplete="email"
                    title="Please enter your registered email address"
                    value="<?php echo htmlspecialchars($email); ?>">
                <label for="email">Email</label>
                <?php if (!empty($email_error)): ?>
                    <span class="error-message"><?php echo $email_error; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <input type="password" id="password" name="password" placeholder=" "
                    autocomplete="current-password"
                    title="Password must be at least 6 characters long"
                    value="<?php echo htmlspecialchars($saved_password); ?>">
                <label for="password">Password</label>
                <?php if (!empty($password_error)): ?>
                    <span class="error-message"><?php echo $password_error; ?></span>
                <?php endif; ?>
            </div>
            <div class="login-form-row">
                <div class="form-group">
                    <input type="checkbox" id="remember-me" name="remember-me" <?php echo $remember_checked ? 'checked' : ''; ?>>
                    <label for="remember-me" class="checkbox-label">Remember</label>
                </div>
                <div class="forgot-link">
                    <a href="forgotPassword.php">Forgot password?</a>
                </div>
            </div>

            <button type="submit">Login</button>
        </form>
        <div class="login-footer">
            Don't have an account? <a href="Signup.php">Sign up</a>
        </div>
    </div>

    <script src="../js/theme.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            applyStoredTheme();
        });
    </script>
</body>
<?php include 'footer.php'; ?>

</html>