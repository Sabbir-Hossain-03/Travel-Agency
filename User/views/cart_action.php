<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_POST['action'] ?? '';

if ($action === 'add') {
    $service_type = $_POST['service_type'] ?? '';
    $service_id = $_POST['service_id'] ?? '';
    $quantity = (int)($_POST['quantity'] ?? 1);
    $price = (float)($_POST['price'] ?? 0);
    $name = $_POST['name'] ?? 'Unknown Service';
    $image = $_POST['image'] ?? '';

    if ($service_type && $service_id) {
        if (isset($_POST['direct_book'])) {
            // Direct booking bypasses permanent cart storage
            ?>
            <form id="direct_form" action="confirmOrder.php" method="post">
                <input type="hidden" name="service_type" value="<?= htmlspecialchars($service_type) ?>">
                <input type="hidden" name="service_id" value="<?= htmlspecialchars($service_id) ?>">
                <input type="hidden" name="quantity" value="<?= $quantity ?>">
            </form>
            <script>document.getElementById('direct_form').submit();</script>
            <?php
            exit();
        }

        $cart_id = $service_type . '_' . $service_id;
        
        if (isset($_SESSION['cart'][$cart_id])) {
            $_SESSION['cart'][$cart_id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$cart_id] = [
                'service_type' => $service_type,
                'service_id' => $service_id,
                'name' => $name,
                'price' => $price,
                'quantity' => $quantity,
                'image' => $image,
                'added_at' => date('Y-m-d H:i:s')
            ];
        }
        
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
}

if ($action === 'remove') {
    $cart_id = $_POST['cart_id'] ?? '';
    if (isset($_SESSION['cart'][$cart_id])) {
        unset($_SESSION['cart'][$cart_id]);
    }
    header("Location: cart.php");
    exit();
}

if ($action === 'update') {
    $cart_id = $_POST['cart_id'] ?? '';
    $quantity = (int)($_POST['quantity'] ?? 1);
    if (isset($_SESSION['cart'][$cart_id])) {
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$cart_id]);
        } else {
            $_SESSION['cart'][$cart_id]['quantity'] = $quantity;
        }
    }
    header("Location: cart.php");
    exit();
}

header("Location: user_dashboard.php");
exit();
?>
