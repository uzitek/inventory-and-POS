<?php
session_start();
require_once 'config.php';
require_once 'functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if order_id is provided
if (!isset($_GET['id'])) {
    die("No order ID provided");
}

$order_id = intval($_GET['id']);

// Fetch order details
$order_query = $conn->prepare("SELECT o.*, c.name as customer_name, c.email as customer_email, c.phone as customer_phone, c.address as customer_address FROM orders o JOIN customers c ON o.customer_id = c.id WHERE o.id = ?");
$order_query->bind_param("i", $order_id);
$order_query->execute();
$order = $order_query->get_result()->fetch_assoc();

if (!$order) {
    die("Order not found");
}

// Fetch order items
$items_query = $conn->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$items_query->bind_param("i", $order_id);
$items_query->execute();
$items = $items_query->get_result()->fetch_all(MYSQLI_ASSOC);

$invoice_number = 'INV-' . str_pad($order_id, 6, '0', STR_PAD_LEFT);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?php echo $invoice_number; ?> - <?php echo COMPANY_NAME; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .invoice {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 20px;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .invoice-header img {
            max-width: 200px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        .total {
            font-weight: bold;
            text-align: right;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 0.8em;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="invoice">
        <div class="invoice-header">
            <img src="<?php echo COMPANY_LOGO; ?>" alt="<?php echo COMPANY_NAME; ?> Logo">
            <h1><?php echo COMPANY_NAME; ?></h1>
            <p>Invoice #<?php echo $invoice_number; ?></p>
        </div>
        <div class="row">
            <div class="col-md-6">
                <h3>Bill To:</h3>
                <p><?php echo htmlspecialchars($order['customer_name']); ?></p>
                <p><?php echo htmlspecialchars($order['customer_email']); ?></p>
                <p><?php echo htmlspecialchars($order['customer_phone']); ?></p>
                <p><?php echo htmlspecialchars($order['customer_address']); ?></p>
            </div>
            <div class="col-md-6 text-right">
                <p><strong>Order Date:</strong> <?php echo date('Y-m-d H:i', strtotime($order['created_at'])); ?></p>
                <p><strong>Payment Status:</strong> <?php echo $order['payment_status']; ?></p>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                    <td>$<?php echo number_format($item['quantity'] * $item['price'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="total">Total: $<?php echo number_format($order['total_amount'], 2); ?></p>
        <div class="footer">
            <p><?php echo COMPANY_NAME; ?></p>
            <p><?php echo COMPANY_ADDRESS; ?></p>
            <p>Email: <?php echo COMPANY_EMAIL; ?> | Phone: <?php echo COMPANY_PHONE; ?></p>
            <p>Created by <?php echo DESIGNER; ?> | Email: <?php echo DESIGNER_EMAIL; ?> | Phone: <?php echo DESIGNER_PHONE; ?></p>
        </div>
    </div>
</body>
</html>