<?php
session_start();
require_once 'config.php';
require_once 'functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user = get_user_by_id($_SESSION['user_id']);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['adjust_inventory'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $action = $_POST['action'];
    $reason = trim($_POST['reason']);

    if ($product_id > 0 && $quantity > 0 && !empty($reason)) {
        $conn->begin_transaction();

        try {
            // Update product quantity
            $update_query = $action == 'add' ? 
                "UPDATE products SET quantity = quantity + ? WHERE id = ?" :
                "UPDATE products SET quantity = quantity - ? WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("ii", $quantity, $product_id);
            $stmt->execute();

            // Log the adjustment
            $stmt = $conn->prepare("INSERT INTO inventory_log (product_id, user_id, action, quantity, reason) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iisis", $product_id, $_SESSION['user_id'], $action, $quantity, $reason);
            $stmt->execute();

            $conn->commit();
            $success_message = "Inventory adjusted successfully.";
        } catch (Exception $e) {
            $conn->rollback();
            $error_message = "Error adjusting inventory: " . $e->getMessage();
        }
    }
}

// Fetch products
$result = $conn->query("SELECT * FROM products ORDER BY name");
$products = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Adjustment - <?php echo COMPANY_NAME; ?> Inventory & POS</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <img src="<?php echo COMPANY_LOGO; ?>" alt="<?php echo COMPANY_NAME; ?> Logo" class="company-logo">
        <h1><?php echo COMPANY_NAME; ?> Inventory & POS</h1>
        <p>Welcome, <?php echo htmlspecialchars($user['username']); ?> | <a href="logout.php">Logout</a></p>
    </header>
    
    <nav>
        <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="inventory.php">Inventory</a></li>
            <li><a href="pos.php">Point of Sale</a></li>
            <?php if ($user['role'] == ROLE_ADMIN): ?>
            <li><a href="categories.php">Categories</a></li>
            <li><a href="brands.php">Brands</a></li>
            <li><a href="users.php">Users</a></li>
            <li><a href="reports.php">Reports</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    
    <main>
        <section id="inventory-adjustment">
            <h2>Inventory Adjustment</h2>
            <?php if (isset($success_message)): ?>
                <p class="success"><?php echo $success_message; ?></p>
            <?php endif; ?>
            <?php if (isset($error_message)): ?>
                <p class="error"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <form method="post" action="">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <select name="product_id" required>
                    <option value="">Select Product</option>
                    <?php foreach ($products as $product): ?>
                    <option value="<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?> (Current: <?php echo $product['quantity']; ?>)</option>
                    <?php endforeach; ?>
                </select>
                <input type="number" name="quantity" min="1" required placeholder="Quantity">
                <select name="action" required>
                    <option value="add">Add</option>
                    <option value="remove">Remove</option>
                </select>
                <textarea name="reason" required placeholder="Reason for adjustment"></textarea>
                <button type="submit" name="adjust_inventory">Adjust Inventory</button>
            </form>
        </section>
    </main>
    
    <footer>
        <p>&copy; <?php echo date('Y'); ?> <?php echo COMPANY_NAME; ?> | Designed by <?php echo DESIGNER; ?></p>
    </footer>
</body>
</html>