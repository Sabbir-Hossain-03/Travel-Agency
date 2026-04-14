<?php
include('dark_mode.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$otp_email = "";
if (isset($_SESSION['otp_action'])) {
    if ($_SESSION['otp_action'] === 'signup' && isset($_SESSION['otp_signup_data']['email'])) {
        $otp_email = $_SESSION['otp_signup_data']['email'];
    } elseif ($_SESSION['otp_action'] === 'forgot' && isset($_SESSION['otp_forgot_data']['email'])) {
        $otp_email = $_SESSION['otp_forgot_data']['email'];
    }
}

if (empty($otp_email)) {
    header("Location: Signup.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - Avestra Travel Agency</title>
    <link rel="stylesheet" href="../styleSheets/Signup.css?v=<?php echo filemtime(__DIR__ . '/../styleSheets/Signup.css'); ?>">
    <link rel="stylesheet" href="../styleSheets/dark-mode.css?v=<?php echo filemtime(__DIR__ . '/../styleSheets/dark-mode.css'); ?>">
    <link rel="icon" href="../images/logo.png" type="image/png">
    <style>
        /* OTP Specific Styles that were inline */
        .otp-input-container {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 20px 0;
        }
        .otp-digit {
            width: 45px;
            height: 55px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            background: #f8fafc;
            color: #1e293b;
            transition: all 0.3s ease;
        }
        .otp-digit:focus {
            border-color: #2563eb;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            outline: none;
        }
        .resend-btn {
            background: none;
            border: none;
            color: #2563eb;
            font-weight: 600;
            cursor: pointer;
            text-decoration: underline;
            padding: 0;
            font-size: 0.95em;
        }
        .resend-btn:hover {
            color: #1d4ed8;
        }
    </style>
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
</head>
<body class="<?= $session_theme_set ? ($is_dark ? 'dark-mode' : 'light-mode') : '' ?>">
    <script>
        if (!<?= $session_theme_set ? 'true' : 'false' ?>) {
            const theme = localStorage.getItem('theme') || 'light';
            document.body.classList.add(theme + '-mode');
        }
    </script>
    <div class="signup-container">
        <div style="text-align: center; margin-bottom: 24px;">
            <img src="../images/logo.png" alt="Logo" style="width: 70px; margin-bottom: 16px;">
            <h2>Verify Your Email</h2>
            <p style="color: #64748b; font-size: 0.95em; margin-top: 8px;">
                We've sent a 6-digit verification code to<br>
                <strong><?= htmlspecialchars($otp_email) ?></strong>
            </p>
            <?php if (defined('LOCAL_DEBUG_MODE') && LOCAL_DEBUG_MODE): ?>
                <div style="margin-top: 15px; padding: 10px; background: #fffbeb; border: 1px dashed #f59e0b; border-radius: 8px; color: #b45309; font-size: 0.9em;">
                    <strong>[DEBUG MODE]</strong> Your OTP is: <span style="font-family: monospace; font-size: 1.2em; font-weight: bold;"><?= $_SESSION['otp_code'] ?? 'None' ?></span>
                </div>
            <?php endif; ?>
        </div>

        <?php if (isset($_SESSION['otp_error'])): ?>
            <div id="otpBanner" class="error-message" style="display: block; text-align: center; margin-bottom: 15px; background: #fee2e2; border: 1px solid #ef4444; padding: 10px; border-radius: 8px; color: #b91c1c; transition: opacity 0.6s ease;">
                <?= $_SESSION['otp_error']; unset($_SESSION['otp_error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['otp_success'])): ?>
            <div id="otpBanner" class="success-message" style="display: block; text-align: center; margin-bottom: 15px; background: #dcfce7; border: 1px solid #22c55e; padding: 10px; border-radius: 8px; color: #166534; transition: opacity 0.6s ease;">
                <?= $_SESSION['otp_success']; unset($_SESSION['otp_success']); ?>
            </div>
        <?php endif; ?>

        <form class="signup-form" action="../controller/verifyOTPHandler.php" method="post" id="otpForm">
            <div class="otp-input-container">
                <input type="text" class="otp-digit" maxlength="1" pattern="\d*" required autofocus>
                <input type="text" class="otp-digit" maxlength="1" pattern="\d*" required>
                <input type="text" class="otp-digit" maxlength="1" pattern="\d*" required>
                <input type="text" class="otp-digit" maxlength="1" pattern="\d*" required>
                <input type="text" class="otp-digit" maxlength="1" pattern="\d*" required>
                <input type="text" class="otp-digit" maxlength="1" pattern="\d*" required>
            </div>
            <input type="hidden" name="otp_code" id="otp_full_code">
            
            <button type="submit" style="margin-top: 10px;">Verify & Proceed</button>
        </form>

        <div class="signup-footer" style="margin-top: 30px;">
            Didn't receive the code? <a href="../controller/resendOTP.php" class="resend-btn">Resend OTP</a>
        </div>
    </div>

    <script>
        const digits = document.querySelectorAll('.otp-digit');
        const hiddenInput = document.getElementById('otp_full_code');
        const form = document.getElementById('otpForm');

        digits.forEach((digit, index) => {
            digit.addEventListener('input', (e) => {
                if (e.target.value.length >= 1) {
                    if (index < digits.length - 1) {
                        digits[index + 1].focus();
                    }
                }
                updateHiddenInput();
            });

            digit.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    digits[index - 1].focus();
                }
            });

            digit.addEventListener('paste', (e) => {
                e.preventDefault();
                const data = e.clipboardData.getData('text').slice(0, digits.length);
                if (/^\d+$/.test(data)) {
                    data.split('').forEach((char, i) => {
                        if (digits[index + i]) {
                            digits[index + i].value = char;
                        }
                    });
                    updateHiddenInput();
                    if (index + data.length < digits.length) {
                        digits[index + data.length].focus();
                    } else {
                        digits[digits.length - 1].focus();
                    }
                }
            });
        });

        function updateHiddenInput() {
            let code = "";
            digits.forEach(d => code += d.value);
            hiddenInput.value = code;
        }

        form.addEventListener('submit', (e) => {
            updateHiddenInput();
            if (hiddenInput.value.length !== 6) {
                e.preventDefault();
                alert('Please enter all 6 digits.');
            }
        });
    </script>
    <script src="../js/theme.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            applyStoredTheme();

            // Auto-dismiss success/error banner after 8 seconds with fade-out
            const banner = document.getElementById('otpBanner');
            if (banner) {
                setTimeout(() => {
                    banner.style.opacity = '0';
                    setTimeout(() => { banner.style.display = 'none'; }, 600);
                }, 8000);
            }
        });
    </script>
</body>
</html>
