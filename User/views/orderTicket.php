<?php
include 'session_check.php';
include '../database/dbconnection.php'; // Connect to avestra-Travel-Agency database

$sql = "SELECT * FROM tickets WHERE status = 'active'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmed</title>
    <link rel="stylesheet" href="../styleSheets/tickets.css">
    <link rel="stylesheet" href="../styleSheets/footer.css">
</head>
<body>

<div class="tickets-container">
    <h2 class="tickets-title">Ticket Order Confirmed</h2>
    <p>You have successfully ordered: <strong><?php echo $item; ?></strong></p>
    <a href="homePage.php" class="btn">Go to Home</a>

    <h2 class="tickets-title" style="margin-top:32px;">Active Tickets</h2>
    <?php
    if ($result && $result->num_rows > 0) {
        echo "<table class='tickets-table'>";
        echo "<tr>
                <th>ID</th>
                <th>Ticket Code</th>
                <th>Ticket Type</th>
                <th>Route</th>
                <th>Bus Class</th>
                <th>Seat Count</th>
                <th>Status</th>
              </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['ticket_code']}</td>
                    <td>{$row['ticket_type']}</td>
                    <td>{$row['route']}</td>
                    <td>{$row['bus_class']}</td>
                    <td>{$row['seat_count']}</td>
                    <td>{$row['status']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No active tickets found.</p>";
    }
    ?>
</div>

</body>
<?php include 'footer.php'; ?>
</html>