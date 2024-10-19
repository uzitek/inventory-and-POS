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

// Handle form submission for creating a new order
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_order'])) {
    $customer_id = intval($_POST['customer_id']);
    $items = $_POST['items'];
    $total_amount = calculate_total_amount($items);

    $order_id = create_order($customer_id, $items, $total_amount);
    if ($order_id) {
        $success_message = "Order created successfully. Order ID: " . $order_id;
    } else {
        $error_message = "Error creating order. Please try again.";
    }
}

// Fetch orders
$result = $conn->query("SELECT o.*, c.name as customer_name FROM orders o JOIN customers c ON o.customer_id = c.id ORDER BY o.created_at DESC");
$orders = $result->fetch_all(MYSQLI_ASSOC);

// Fetch customers for the dropdown
$customers_result = $conn->query("SELECT id, name FROM customers ORDER BY name");
$customers = $customers_result->fetch_all(MYSQLI_ASSOC);

// Fetch products for the dropdown
$products_result = $conn->query("SELECT id, name, price FROM products ORDER BY name");
$products = $products_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - <?php echo COMPANY_NAME; ?> Inventory & POS</title>
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
                    <h1 class="h2">Orders</h1>
                    <button class="btn btn-primary" data-toggle="modal" data-target="#createOrderModal">Create New Order</button>
                </div>
                
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success" role="alert"><?php echo $success_message; ?></div>
                <?php endif; ?>
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger" role="alert"><?php echo $error_message; ?></div>
                <?php endif; ?>
                
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Total Amount</th>
                                <th>Payment Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td><?php echo $order['payment_status']; ?></td>
                                <td><?php echo $order['created_at']; ?></td>
                                <td>
                                    <a href="view_order.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary">View</a>
                                    <a href="edit_order.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    <a href="generate_invoice.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-info" target="_blank">Invoice</a>
                                    <a href="delete_order.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this order?')">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Create Order Modal -->
    <div class="modal fade" id="createOrderModal" tabindex="-1" role="dialog" aria-labelledby="createOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createOrderModalLabel">Create New Order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="customer_id">Customer</label>
                            <select class="form-control" id="customer_id" name="customer_id" required>
                                <?php foreach ($customers as $customer): ?>
                                    <option value="<?php echo $customer['id']; ?>"><?php echo htmlspecialchars($customer['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div id="order_items">
                            <div class="form-row mb-2">
                                <div class="col">
                                    <select class="form-control product-select" name="items[0][product_id]" required>
                                        <option value="">Select Product</option>
                                        <?php foreach ($products as $product): ?>
                                            <option value="<?php echo $product['id']; ?>" data-price="<?php echo $product['price']; ?>"><?php echo htmlspecialchars($product['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col">
                                    <input type="number" class="form-control quantity-input" name="items[0][quantity]" placeholder="Quantity" required>
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control price-input" name="items[0][price]" placeholder="Price" readonly>
                                </div>
                                <div class="col">
                                    <button type="button" class="btn btn-danger remove-item">Remove</button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary mt-2" id="add_item">Add Item</button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="create_order" class="btn btn-primary">Create Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            let itemCount = 1;

            $('#add_item').click(function() {
                const newItem = $('#order_items .form-row:first').clone();
                newItem.find('select').attr('name', `items[${itemCount}][product_id]`).val('');
                newItem.find('.quantity-input').attr('name', `items[${itemCount}][quantity]`).val('');
                newItem.find('.price-input').attr('name', `items[${itemCount}][price]`).val('');
                $('#order_items').append(newItem);
                itemCount++;
            });

            $(document).on('click', '.remove-item', function() {
                if ($('#order_items .form-row').length > 1) {
                    $(this).closest('.form-row').remove();
                }
            });

            $(document).on('change', '.product-select', function() {
                const price = $(this).find(':selected').data('price');
                $(this).closest('.form-row').find('.price-input').val(price);
            });
        });
    </script>
</body>
</html>