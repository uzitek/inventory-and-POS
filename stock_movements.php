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

// Handle form submission for stock movement
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_movement'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $movement_type = $_POST['movement_type'];
    $reason = trim($_POST['reason']);

    if ($product_id > 0 && $quantity > 0) {
        add_stock_movement($product_id, $quantity, $movement_type, $reason);
    }
}

// Fetch stock movements
$movements = get_stock_movements();

// Fetch products for the dropdown
$products_result = $conn->query("SELECT id, name FROM products ORDER BY name");
$products = $products_result->fetch_all(MYSQLI_ASSOC);

// Fetch low stock alerts
$low_stock_products = get_low_stock_products();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Movements - <?php echo COMPANY_NAME; ?> Inventory & POS</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Stock Movements</h1>
                    <button class="btn btn-primary" data-toggle="modal" data-target="#movementModal">Add Stock Movement</button>
                </div>
                
                <?php if (!empty($low_stock_products)): ?>
                <div class="alert alert-warning" role="alert">
                    <h4 class="alert-heading">Low Stock Alert!</h4>
                    <p>The following products are running low on stock:</p>
                    <ul>
                        <?php foreach ($low_stock_products as $product): ?>
                        <li><?php echo htmlspecialchars($product['name']); ?> - Current quantity: <?php echo $product['quantity']; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Movement Type</th>
                                <th>Reason</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($movements as $movement): ?>
                            <tr>
                                <td><?php echo $movement['id']; ?></td>
                                <td><?php echo htmlspecialchars($movement['product_name']); ?></td>
                                <td><?php echo $movement['quantity']; ?></td>
                                <td><?php echo $movement['movement_type']; ?></td>
                                <td><?php echo htmlspecialchars($movement['reason']); ?></td>
                                <td><?php echo $movement['created_at']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Stock Movement Modal -->
    <div class="modal fade" id="movementModal" tabindex="-1" role="dialog" aria-labelledby="movementModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="movementModalLabel">Add Stock Movement</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="product_id">Product</label>
                            <select class="form-control" id="product_id" name="product_id" required>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="quantity">Quantity</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" required>
                        </div>
                        <div class="form-group">
                            <label for="movement_type">Movement Type</label>
                            <select class="form-control" id="movement_type" name="movement_type" required>
                                <option value="inbound">Inbound</option>
                                <option value="outbound">Outbound</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="reason">Reason</label>
                            <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="submit_movement" class="btn btn-primary">Save Movement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>