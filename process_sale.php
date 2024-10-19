<?php
session_start();
require_once 'config.php';
require_once 'functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Get the JSON data from the request body
$json_data = file_get_contents('php://input');
$cart_items = json_decode($json_data, true);

if (!$cart_items) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit();
}

// Calculate total amount
$total_amount = 0;
foreach ($cart_items as $item) {
    $total_amount += $item['price'] * $item['quantity'];
}

// Add sale to database
$sale_id = add_sale($_SESSION['user_id'], $cart_items, $total_amount);

if ($sale_id) {
    $receipt_number = 'R' . str_pad($sale_id, 6, '0', STR_PAD_LEFT);
    echo json_encode([
        'success' => true,
        'message' => 'Sale processed successfully',
        'sale_id' => $sale_id,
        'receipt_number' => $receipt_number
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error processing sale']);
}
?>