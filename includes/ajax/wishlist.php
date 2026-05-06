<?php
require_once __DIR__ . '/../auth.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['status' => 'login_required']);
    exit;
}

$productId = intval($_POST['product_id'] ?? 0);
if (!$productId) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid product']);
    exit;
}

$userId = $_SESSION['user_id'];

// Check if already in wishlist
$check = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
$check->bind_param("ii", $userId, $productId);
$check->execute();

if ($check->get_result()->num_rows > 0) {
    // Remove from wishlist
    $del = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
    $del->bind_param("ii", $userId, $productId);
    $del->execute();
    echo json_encode(['status' => 'removed']);
} else {
    // Add to wishlist
    $add = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
    $add->bind_param("ii", $userId, $productId);
    $add->execute();
    echo json_encode(['status' => 'added']);
}
