<?php
require_once __DIR__ . '/../db.php';

$page = max(1, intval($_GET['page'] ?? 1));
$category = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? '';
$perPage = 12;
$offset = ($page - 1) * $perPage;

$where = "WHERE 1=1";
$params = [];
$types = "";

if ($category) {
    $where .= " AND c.slug = ?";
    $params[] = $category;
    $types .= "s";
}

$orderBy = "ORDER BY p.created_at DESC";
switch ($sort) {
    case 'price-low': $orderBy = "ORDER BY p.price_per_day ASC"; break;
    case 'price-high': $orderBy = "ORDER BY p.price_per_day DESC"; break;
    case 'rating': $orderBy = "ORDER BY p.rating DESC"; break;
    case 'popular': $orderBy = "ORDER BY p.total_rentals DESC"; break;
}

$query = "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id $where $orderBy LIMIT $perPage OFFSET $offset";

if (!empty($params)) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $products = $stmt->get_result();
} else {
    $products = $conn->query($query);
}

// Check wishlist
$wishlistIds = [];
if (session_status() === PHP_SESSION_NONE) session_start();
if (isset($_SESSION['user_id'])) {
    $wStmt = $conn->prepare("SELECT product_id FROM wishlist WHERE user_id = ?");
    $wStmt->bind_param("i", $_SESSION['user_id']);
    $wStmt->execute();
    $wRes = $wStmt->get_result();
    while ($w = $wRes->fetch_assoc()) {
        $wishlistIds[] = $w['product_id'];
    }
}

$html = '';
while ($product = $products->fetch_assoc()) {
    $inWishlist = in_array($product['id'], $wishlistIds) ? 'active' : '';
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        $stars .= '<i class="fas fa-star' . ($i <= round($product['rating']) ? '' : ' text-muted') . '"></i>';
    }

    $html .= '<div class="col-lg-3 col-md-6" data-aos="fade-up">';
    $html .= '<div class="product-card hover-lift">';
    $html .= '<div class="product-card-image">';
    $html .= '<img src="https://picsum.photos/seed/' . htmlspecialchars($product['slug']) . '/400/300" alt="' . htmlspecialchars($product['name']) . '">';
    if ($product['featured']) {
        $html .= '<span class="product-card-badge">Featured</span>';
    }
    $html .= '<button class="product-card-wishlist wishlist-toggle ' . $inWishlist . '" data-product-id="' . $product['id'] . '"><i class="fas fa-heart"></i></button>';
    $html .= '</div>';
    $html .= '<div class="product-card-body">';
    $html .= '<div class="product-card-category">' . htmlspecialchars($product['category_name']) . '</div>';
    $html .= '<h3 class="product-card-title">' . htmlspecialchars($product['name']) . '</h3>';
    $html .= '<div class="product-card-rating"><div class="stars">' . $stars . '</div>';
    $html .= '<span class="rating-text">(' . $product['total_reviews'] . ')</span></div>';
    $html .= '<div class="product-card-footer">';
    $html .= '<div class="product-price">$' . number_format($product['price_per_day'], 2) . ' <span>/day</span></div>';
    $html .= '<a href="product-details.php?slug=' . htmlspecialchars($product['slug']) . '" class="btn-rent">Rent Now</a>';
    $html .= '</div></div></div></div>';
}

header('Content-Type: application/json');
echo json_encode(['html' => $html]);
