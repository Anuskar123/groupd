<?php
session_start();
require_once '../../config/config.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

if($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$cart_id = isset($_POST['cart_id']) ? (int)$_POST['cart_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
$user_id = $_SESSION['user_id'];

if($cart_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid cart item']);
    exit();
}

$database = new Database();

// Verify cart item belongs to user and get product info
$database->query("SELECT c.cart_id, c.product_id, p.price, p.stock_quantity 
                  FROM cart c 
                  JOIN products p ON c.product_id = p.product_id 
                  WHERE c.cart_id = :cart_id AND c.user_id = :user_id");
$database->bind(':cart_id', $cart_id);
$database->bind(':user_id', $user_id);
$cart_item = $database->single();

if(!$cart_item) {
    echo json_encode(['success' => false, 'message' => 'Cart item not found']);
    exit();
}

if($quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid quantity']);
    exit();
}

// Check stock availability
if($quantity > $cart_item['stock_quantity']) {
    echo json_encode(['success' => false, 'message' => 'Insufficient stock. Only ' . $cart_item['stock_quantity'] . ' items available']);
    exit();
}

try {
    // Update cart quantity
    $database->query("UPDATE cart SET quantity = :quantity WHERE cart_id = :cart_id");
    $database->bind(':quantity', $quantity);
    $database->bind(':cart_id', $cart_id);
    
    if($database->execute()) {
        $item_total = $cart_item['price'] * $quantity;
        echo json_encode([
            'success' => true, 
            'message' => 'Cart updated successfully',
            'item_total' => $item_total
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update cart']);
    }
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>
