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

$database = new Database();

// Calculate cart summary
$database->query("SELECT SUM(p.price * c.quantity) as subtotal 
                  FROM cart c 
                  JOIN products p ON c.product_id = p.product_id 
                  WHERE c.user_id = :user_id AND p.is_active = 1");
$database->bind(':user_id', $_SESSION['user_id']);
$result = $database->single();

$subtotal = $result['subtotal'] ?? 0;
$shipping_fee = $subtotal >= 2000 ? 0 : 100; // Free shipping above Rs. 2000
$total = $subtotal + $shipping_fee;

echo json_encode([
    'success' => true,
    'subtotal' => $subtotal,
    'shipping_fee' => $shipping_fee,
    'total' => $total
]);
?>
