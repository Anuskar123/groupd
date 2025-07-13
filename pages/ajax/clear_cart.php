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

$database = new Database();

try {
    // Clear all cart items for the user
    $database->query("DELETE FROM cart WHERE user_id = :user_id");
    $database->bind(':user_id', $_SESSION['user_id']);
    
    if($database->execute()) {
        echo json_encode(['success' => true, 'message' => 'Cart cleared successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to clear cart']);
    }
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>
