<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avestra Travel Agency : Loading</title>
    <link rel="stylesheet" href="../styleSheets/loader.css?v=<?php echo time(); ?>">
    <link rel="icon" href="../images/logo.png" type="image/png">
</head>
<body>
    <div class="loader" aria-label="Loading">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
    </div>

    <script src="../js/theme.js"></script>
    <script>
        const theme = applyStoredTheme();
        if (theme === 'light') {
            document.body.style.background = '#EEEEEE';
        }

        setTimeout(function() {
            window.location.href = 'homePage.php';
        }, 1000);
    </script>
</body>
</html>