<?php
session_start();
require_once 'config.php';
require_once 'functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user information
$user = get_user_by_id($_SESSION['user_id']);

// Fetch products for POS
$result = $conn->query("SELECT p.*, c.name as category_name, b.name as brand_name 
                        FROM products p 
                        JOIN categories c ON p.category_id = c.id 
                        JOIN brands b ON p.brand_id = b.id
                        WHERE p.quantity > 0");
$products = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Point of Sale - <?php echo COMPANY_NAME; ?> Inventory & POS</title>
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
        <section id="pos">
            <h2>Point of Sale</h2>
            <div class="pos-container">
                <div class="product-list">
                    <h3>Products</h3>
                    <input type="text" id="product-search" placeholder="Search products...">
                    <ul id="product-items">
                        <?php foreach ($products as $product): ?>
                        <li data-id="<?php echo $product['id']; ?>" data-name="<?php echo htmlspecialchars($product['name']); ?>" data-price="<?php echo $product['price']; ?>">
                            <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>" width="50">
                            <span><?php echo htmlspecialchars($product['name']); ?></span>
                            <span>$<?php echo number_format($product['price'], 2); ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="cart">
                    <h3>Cart</h3>
                    <ul id="cart-items"></ul>
                    <div id="cart-total">Total: $0.00</div>
                    <button id="checkout-btn">Checkout</button>
                </div>
            </div>
        </section>
    </main>
    
    <footer>
        <p>&copy; <?php echo date('Y'); ?> <?php echo COMPANY_NAME; ?> | Designed by <?php echo DESIGNER; ?></p>
    </footer>

    <script src="pos.js"></script>
</body>
</html>