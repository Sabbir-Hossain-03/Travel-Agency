<?php
include 'session_check.php';
include 'dark_mode.php';
include '../database/dbconnection.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cart = $_SESSION['cart'] ?? [];
$total_cart_price = 0;
foreach ($cart as $item) {
    $total_cart_price += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - Avestra Travel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styleSheets/user.css">
    <link rel="stylesheet" href="../styleSheets/footer.css">
    <link rel="stylesheet" href="../styleSheets/user-dark-mode.css?v=<?php echo time(); ?>">
    <link rel="icon" href="../images/logo.png" type="image/png">
    <style>
        .cart-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
            min-height: 60vh;
        }
        .cart-header {
            margin-bottom: 30px;
            text-align: center;
        }
        .cart-table-wrap {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .cart-item {
            display: grid;
            grid-template-columns: 100px 1fr 150px 150px 100px;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #f1f5f9;
        }
        .cart-item:last-child {
            border-bottom: none;
        }
        .item-img {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            object-fit: cover;
        }
        .item-info h4 {
            margin: 0 0 5px 0;
            color: #1e293b;
            font-size: 1.1rem;
        }
        .item-info p {
            margin: 0;
            color: #64748b;
            font-size: 0.9rem;
            text-transform: capitalize;
        }
        .item-price, .item-subtotal {
            font-weight: 700;
            color: #334155;
        }
        .item-qty input {
            width: 60px;
            padding: 8px;
            border: 1px solid #cbd5e0;
            border-radius: 8px;
            text-align: center;
        }
        .remove-btn {
            background: #fee2e2;
            color: #ef4444;
            border: none;
            padding: 10px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .remove-btn:hover {
            background: #ef4444;
            color: white;
        }
        .cart-summary {
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
        }
        .summary-card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            width: 100%;
            max-width: 400px;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }
        .total-row {
            border-top: 2px dashed #e2e8f0;
            padding-top: 15px;
            font-weight: 800;
            font-size: 1.4rem;
            color: #1e293b;
        }
        .checkout-btn {
            width: 100%;
            background: var(--primary);
            color: white;
            padding: 18px;
            border-radius: 15px;
            border: none;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.3s;
        }
        .checkout-btn:hover {
            background: #4338ca;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
        }
        .empty-cart {
            text-align: center;
            padding: 60px 20px;
        }
        .empty-icon {
            font-size: 5rem;
            margin-bottom: 20px;
        }
        
        /* Dark Mode Adjustments */
        body.dark-mode .cart-table-wrap, 
        body.dark-mode .summary-card {
            background: #1e293b;
            border-color: rgba(255,255,255,0.05);
        }
        body.dark-mode .item-info h4,
        body.dark-mode .item-price,
        body.dark-mode .item-subtotal,
        body.dark-mode .total-row {
            color: #f8fafc;
        }
        body.dark-mode .cart-item {
            border-bottom-color: rgba(255,255,255,0.05);
        }
    </style>
</head>
<body class="<?= $session_theme_set ? ($is_dark ? 'dark-mode' : 'light-mode') : '' ?>">
    <?php include 'nav.php'; ?>

    <div class="cart-container">
        <div class="cart-header">
            <h1 style="font-weight: 800; font-size: 2.5rem;">🛒 Your Travel Cart</h1>
            <p style="color: #64748b;">Review and finalize your dream journey</p>
        </div>

        <?php if (empty($cart)): ?>
            <div class="empty-cart card-table-wrap">
                <div class="empty-icon">🏜️</div>
                <h2 style="font-weight: 700;">Your cart is empty</h2>
                <p style="color: #64748b; margin-bottom: 20px;">Start adding amazing destinations and stays!</p>
                <a href="user_dashboard.php" style="background: var(--primary); color: white; padding: 12px 25px; border-radius: 12px; text-decoration: none; font-weight: 600;">Go to Dashboard</a>
            </div>
        <?php else: ?>
            <div class="cart-table-wrap">
                <?php foreach ($cart as $id => $item): ?>
                    <div class="cart-item">
                        <img src="<?= strpos($item['image'], 'hotels/') === 0 ? '../images/'.$item['image'] : (strpos($item['image'], 'ticket') === 0 ? '../../Admin/images/'.$item['image'] : '../images/'.$item['image']) ?>" alt="<?= $item['name'] ?>" class="item-img" onerror="this.src='../images/tour1.jpg'">
                        <div class="item-info">
                            <h4><?= htmlspecialchars($item['name']) ?></h4>
                            <p><?= htmlspecialchars($item['service_type']) ?></p>
                        </div>
                        <div class="item-qty">
                            <form action="cart_action.php" method="post">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="cart_id" value="<?= $id ?>">
                                <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" onchange="this.form.submit()">
                            </form>
                        </div>
                        <div class="item-subtotal">
                            <?= number_format($item['price'] * $item['quantity'], 0) ?> ৳
                        </div>
                        <div class="item-remove">
                            <form action="cart_action.php" method="post">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="cart_id" value="<?= $id ?>">
                                <button type="submit" class="remove-btn">🗑️</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary">
                <div class="summary-card">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span><?= number_format($total_cart_price, 0) ?> ৳</span>
                    </div>
                    <div class="summary-row">
                        <span>Platform Fee</span>
                        <span>0 ৳</span>
                    </div>
                    <div class="summary-row total-row">
                        <span>Total</span>
                        <span><?= number_format($total_cart_price, 0) ?> ৳</span>
                    </div>
                    
                    <form action="process_cart_checkout.php" method="post">
                        <button type="submit" class="checkout-btn">Proceed to Checkout →</button>
                    </form>
                    <p style="text-align: center; font-size: 0.85rem; color: #94a3b8; margin-top: 15px;">Secure payment handled by SSLCommerz</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
