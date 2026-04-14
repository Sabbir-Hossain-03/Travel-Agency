<?php
include 'session_check.php';
?>
<!DOCTYPE html>
<html>
<head>
<title>Support</title>
<link rel="stylesheet" href="../styleSheets/user.css">
<link rel="icon" href="../images/logo.png" type="image/png">
</head>
<body>

<?php include 'nav.php'; ?>

<div class="container">
    <br><br>
    <div class="card">
        <h2>Support Ticket</h2>
        <form method="post" action="#">
            <textarea placeholder="Describe your issue" style="width:100%;height:120px;"></textarea>
            <button type="submit">Submit Ticket</button>
        </form>
    </div>
</div>

</body>
<?php include 'footer.php'; ?>
</html>