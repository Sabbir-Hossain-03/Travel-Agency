<?php
include 'session_check.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tour Booked</title>
</head>
<body>

<h2>Tour Booking Successful</h2>
<p>You booked: <strong><?php echo $item; ?></strong></p>

<a href="homePage.php">Go to Home</a>

</body>
<?php include 'footer.php'; ?>
</html>