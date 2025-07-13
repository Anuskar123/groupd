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
$user_id = $_SESSION['user_id'];

if($cart_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid cart item']);
    exit();
}

$database = new Database();

// Verify cart item belongs to user
$database->query("SELECT cart_id FROM cart WHERE cart_id = :cart_id AND user_id = :user_id");
$database->bind(':cart_id', $cart_id);
$database->bind(':user_id', $user_id);
$cart_item = $database->single();

if(!$cart_item) {
    echo json_encode(['success' => false, 'message' => 'Cart item not found']);
    exit();
}

try {
    // Remove cart item
    $database->query("DELETE FROM cart WHERE cart_id = :cart_id");
    $database->bind(':cart_id', $cart_id);
    
    if($database->execute()) {
        echo json_encode(['success' => true, 'message' => 'Item removed from cart']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to remove item']);
    }
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>
