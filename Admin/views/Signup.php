<?php
include('dark_mode.php'); // Include theme helper
include '../controller/SignupValidation.php';
include '../controller/SignupFormDataHandler.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avestra Travel Agency</title>
    <link rel="stylesheet" href="../styleSheets/Signup.css?v=<?php echo time(); ?>">
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
    <div class="signup-container">
        <h2>Create Your Account</h2>
        <?php include '../controller/SignupMessageDisplay.php'; ?>
        
        <form class="signup-form" action="../controller/SignupValidation" method="post">
            <div class="form-group">
                <input type="text" id="username" name="username" placeholder=" " autocomplete="username"
                    title="Full name must contain only letters and spaces."
                    value="<?php echo htmlspecialchars($username); ?>">
                <label for="username">Full Name</label>
                <?php if (!empty($username_error)): ?>
                    <span class="error-message"><?php echo $username_error; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <input type="email" id="email" name="email" placeholder=" " autocomplete="email"
                    title="Please enter a valid email address (e.g., example@domain.com)"
                    value="<?php echo htmlspecialchars($email); ?>">
                <label for="email">Email</label>
                <?php if (!empty($email_error)): ?>
                    <span class="error-message"><?php echo $email_error; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <input type="text" id="phoneNumber" name="phoneNumber" placeholder=" " autocomplete="phoneNumber"
                    title="Phone number must be exactly 11 digits."
                    value="<?php echo htmlspecialchars($phoneNumber); ?>">
                <label for="phoneNumber">Phone Number</label>
                <?php if (!empty($phoneNumber_error)): ?>
                    <span class="error-message"><?php echo $phoneNumber_error; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group select-group">
                <select id="role" name="role">
                    <option value="" disabled selected>Select account type</option>
                    <option value="customer" <?php if ($role === 'customer')
                        echo 'selected'; ?>>Customer</option>
                    <option value="admin" <?php if ($role === 'admin')
                        echo 'selected'; ?>>Admin</option>
                </select>
                <?php if (!empty($role_error)): ?>
                    <span class="error-message"><?php echo $role_error; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <input type="password" id="password" name="password" placeholder=" "
                    title="Password must be at least 6 characters long."
                    value="<?php echo htmlspecialchars($password); ?>">
                <label for="password">Password</label>
                <?php if (!empty($password_error)): ?>
                    <span class="error-message"><?php echo $password_error; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <input type="password" id="confirm-password" name="confirm-password" placeholder=" "
                    autocomplete="new-password" title="Re-enter your password. It must match the password above."
                    value="<?php echo htmlspecialchars($confirmPassword); ?>">
                <label for="confirm-password">Confirm Password</label>
                <?php if (!empty($confirmPassword_error)): ?>
                    <span class="error-message"><?php echo $confirmPassword_error; ?></span>
                <?php endif; ?>
            </div>

            <button type="submit">Sign Up</button>
        </form>
        <div class="signup-footer">
            Already have an account? <a href="loginPage.php">Sign In</a>
        </div>
    </div>

    <script src="../js/theme.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            applyStoredTheme();

            // Auto-dismiss success/error messages after 7 seconds
            const messages = document.querySelectorAll('.success-message, .general-error-message, .error-message');
            messages.forEach(msg => {
                // For span.error-message (inline errors), we might not want to hide them automatically 
                // but let's hide only the main banners for now if they are divs/blocks
                if (msg.tagName === 'DIV' || msg.classList.contains('success-message') || msg.classList.contains('general-error-message')) {
                    setTimeout(() => {
                        msg.style.opacity = '0';
                        setTimeout(() => { 
                            msg.style.display = 'none'; 
                        }, 600); // Wait for CSS transition
                    }, 7000);
                }
            });
        });
    </script>
</body>

</html>