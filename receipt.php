<?php
session_start();
require_once 'config.php';
require_once 'functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if sale_id is provided
if (!isset($_GET['id'])) {
    die("No sale ID provided");
}

$sale_id = intval($_GET['id']);

// Fetch sale details
$sale_query = $conn->prepare("SELECT s.*, u.username FROM sales s JOIN users u ON s.user_id = u.id WHERE s.id = ?");
$sale_query->bind_param("i", $sale_id);
$sale_query->execute();
$sale = $sale_query->get_result()->fetch_assoc();

if (!$sale) {
    die("Sale not found");
}

// Fetch sale items
$items_query = $conn->prepare("SELECT si.*, p.name FROM sale_items si JOIN products p ON si.product_id = p.id WHERE si.sale_id = ?");
$items_query->bind_param("i", $sale_id);
$items_query->execute();
$items = $items_query->get_result()->fetch_all(MYSQLI_ASSOC);

$receipt_number = 'R' . str_pad($sale_id, 6, '0', STR_PAD_LEFT);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #<?php echo $receipt_number; ?> - <?php echo COMPANY_NAME; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .receipt {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 20px;
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .receipt-header img {
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
    </style>
</head>
<body>
    <div class="receipt">
        <div class="receipt-header">
            <img src="<?php echo COMPANY_LOGO; ?>" alt="<?php echo COMPANY_NAME; ?> Logo">
            <h1><?php echo COMPANY_NAME; ?></h1>
            <p>Receipt #<?php echo $receipt_number; ?></p>
        </div>
        <p><strong>Date:</strong> <?php echo date('Y-m-d H:i:s', strtotime($sale['created_at'])); ?></p>
        <p><strong>Cashier:</strong> <?php echo htmlspecialchars($sale['username']); ?></p>
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
        <p class="total">Total: $<?php echo number_format($sale['total_amount'], 2); ?></p>
        <p>Thank you for your purchase!</p>
    </div>
</body>
</html>