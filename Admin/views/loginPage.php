<?php
session_start();
include('dark_mode.php'); // Include theme helper
include '../controller/loginFormDataHandler.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avestra Travel Agency</title>
    <link rel="stylesheet" href="../styleSheets/loginPage.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../../User/styleSheets/user-dark-mode.css?v=<?php echo time(); ?>">
    <script>
        // Intelligent theme application
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const sessionThemeSet = <?= $session_theme_set ? 'true' : 'false' ?>;
            const currentTheme = '<?= $current_theme ?>';
            
            if (sessionThemeSet) {
                localStorage.setItem('theme', currentTheme);
                document.documentElement.setAttribute('data-theme', currentTheme);
            } else if (savedTheme) {
                document.documentElement.setAttribute('data-theme', savedTheme);
            }
        })();
    </script>
    <link rel="icon" href="../images/logo.png" type="image/png">
</head>

<body class="<?= $session_theme_set ? ($is_dark ? 'dark-mode' : 'light-mode') : '' ?>">
    <script>
        if (!<?= $session_theme_set ? 'true' : 'false' ?>) {
            const theme = localStorage.getItem('theme') || 'light';
            document.body.classList.add(theme + '-mode');
        }
    </script>
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
        
        <form class="login-form" action="../controller/loginValidation" method="post">
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

            // Auto-dismiss success/error messages after 7 seconds
            const messages = document.querySelectorAll('.success-message, .general-error-message, .info-message, .warning-message');
            messages.forEach(msg => {
                setTimeout(() => {
                    msg.style.opacity = '0';
                    setTimeout(() => { 
                        msg.style.display = 'none'; 
                    }, 600); // Wait for CSS transition
                }, 7000);
            });
        });
    </script>
</body>

</html>