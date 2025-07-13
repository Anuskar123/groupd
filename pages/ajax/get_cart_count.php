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

// Get cart count for the user
$database->query("SELECT COUNT(*) as count FROM cart WHERE user_id = :user_id");
$database->bind(':user_id', $_SESSION['user_id']);
$result = $database->single();

echo $result['count'] ?? 0;
?>
