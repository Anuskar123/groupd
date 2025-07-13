<?php
session_start();
require_once '../../config/config.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to add items to cart']);
    exit();
}

if($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
$user_id = $_SESSION['user_id'];

if($product_id <= 0 || $quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product or quantity']);
    exit();
}

$database = new Database();

// Check if product exists and is active
$database->query("SELECT product_id, product_name, stock_quantity FROM products WHERE product_id = :product_id AND is_active = 1");
$database->bind(':product_id', $product_id);
$product = $database->single();

if(!$product) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit();
}

// Check stock availability
if($product['stock_quantity'] < $quantity) {
    echo json_encode(['success' => false, 'message' => 'Insufficient stock. Only ' . $product['stock_quantity'] . ' items available']);
    exit();
}

try {
    // Check if item already exists in cart
    $database->query("SELECT cart_id, quantity FROM cart WHERE user_id = :user_id AND product_id = :product_id");
    $database->bind(':user_id', $user_id);
    $database->bind(':product_id', $product_id);
    $existing_item = $database->single();

    if($existing_item) {
        // Update existing cart item
        $new_quantity = $existing_item['quantity'] + $quantity;
        
        // Check if new quantity exceeds stock
        if($new_quantity > $product['stock_quantity']) {
            echo json_encode(['success' => false, 'message' => 'Cannot add more. Maximum available: ' . $product['stock_quantity']]);
            exit();
        }
        
        $database->query("UPDATE cart SET quantity = :quantity WHERE cart_id = :cart_id");
        $database->bind(':quantity', $new_quantity);
        $database->bind(':cart_id', $existing_item['cart_id']);
        
        if($database->execute()) {
            echo json_encode(['success' => true, 'message' => 'Cart updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update cart']);
        }
    } else {
        // Add new cart item
        $database->query("INSERT INTO cart (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)");
        $database->bind(':user_id', $user_id);
        $database->bind(':product_id', $product_id);
        $database->bind(':quantity', $quantity);
        
        if($database->execute()) {
            echo json_encode(['success' => true, 'message' => 'Product added to cart successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add product to cart']);
        }
    }
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>
