<?php
$pageTitle = 'Products';
require_once 'includes/header.php';

$category = $_GET['cat'] ?? '';
$sort = $_GET['sort'] ?? '';
$price = $_GET['price'] ?? '';
$page = 1;
$perPage = 12;

// Build query
$where = "WHERE 1=1";
$params = [];
$types = "";

if ($category) {
    $where .= " AND c.slug = ?";
    $params[] = $category;
    $types .= "s";
}

if ($price) {
    switch ($price) {
        case 'under-25':
            $where .= " AND p.price_per_day < 25";
            break;
        case '25-50':
            $where .= " AND p.price_per_day BETWEEN 25 AND 50";
            break;
        case '50-100':
            $where .= " AND p.price_per_day BETWEEN 50 AND 100";
            break;
        case 'over-100':
            $where .= " AND p.price_per_day > 100";
            break;
    }
}

$orderBy = "ORDER BY p.created_at DESC";
switch ($sort) {
    case 'price-low': $orderBy = "ORDER BY p.price_per_day ASC"; break;
    case 'price-high': $orderBy = "ORDER BY p.price_per_day DESC"; break;
    case 'rating': $orderBy = "ORDER BY p.rating DESC"; break;
    case 'popular': $orderBy = "ORDER BY p.total_rentals DESC"; break;
}

$countQuery = "SELECT COUNT(*) as total FROM products p JOIN categories c ON p.category_id = c.id $where";
$query = "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id $where $orderBy LIMIT $perPage";

if (!empty($params)) {
    $countStmt = $conn->prepare($countQuery);
    $countStmt->bind_param($types, ...$params);
    $countStmt->execute();
    $totalProducts = $countStmt->get_result()->fetch_assoc()['total'];

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $products = $stmt->get_result();
} else {
    $totalProducts = $conn->query($countQuery)->fetch_assoc()['total'];
    $products = $conn->query($query);
}

// Categories for filter
$allCats = $conn->query("SELECT * FROM categories ORDER BY name");

// User wishlist
$wishlistIds = [];
if (isLoggedIn()) {
    $wStmt = $conn->prepare("SELECT product_id FROM wishlist WHERE user_id = ?");
    $wStmt->bind_param("i", $_SESSION['user_id']);
    $wStmt->execute();
    $wRes = $wStmt->get_result();
    while ($w = $wRes->fetch_assoc()) {
        $wishlistIds[] = $w['product_id'];
    }
}
?>

<!-- Products Hero -->
<section class="products-hero">
    <h1 data-aos="fade-up">Our Products</h1>
    <p data-aos="fade-up" data-aos-delay="100">Browse our premium collection of rental items</p>
</section>

<section class="section-padding" style="padding-top: 0;">
    <div class="container">
        <!-- Filters -->
        <div class="filters-bar" data-aos="fade-up">
            <div class="row g-3 align-items-center">
                <div class="col-md-3">
                    <select class="filter-select w-100" id="categoryFilter">
                        <option value="">All Categories</option>
                        <?php while ($c = $allCats->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($c['slug']); ?>" <?php echo $category === $c['slug'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="filter-select w-100" id="priceFilter">
                        <option value="">Any Price</option>
                        <option value="under-25" <?php echo $price === 'under-25' ? 'selected' : ''; ?>>Under $25/day</option>
                        <option value="25-50" <?php echo $price === '25-50' ? 'selected' : ''; ?>>$25 - $50/day</option>
                        <option value="50-100" <?php echo $price === '50-100' ? 'selected' : ''; ?>>$50 - $100/day</option>
                        <option value="over-100" <?php echo $price === 'over-100' ? 'selected' : ''; ?>>Over $100/day</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="filter-select w-100" id="sortFilter">
                        <option value="">Sort By: Latest</option>
                        <option value="price-low" <?php echo $sort === 'price-low' ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price-high" <?php echo $sort === 'price-high' ? 'selected' : ''; ?>>Price: High to Low</option>
                        <option value="rating" <?php echo $sort === 'rating' ? 'selected' : ''; ?>>Highest Rated</option>
                        <option value="popular" <?php echo $sort === 'popular' ? 'selected' : ''; ?>>Most Popular</option>
                    </select>
                </div>
                <div class="col-md-3 text-end">
                    <span class="text-muted"><?php echo $totalProducts; ?> products found</span>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="row g-4 products-grid" id="productsGrid">
            <?php if ($products->num_rows > 0): ?>
                <?php while ($product = $products->fetch_assoc()): ?>
                <div class="col-lg-3 col-md-6" data-aos="fade-up">
                    <div class="product-card hover-lift">
                        <div class="product-card-image">
                            <img src="https://picsum.photos/seed/<?php echo htmlspecialchars($product['slug']); ?>/400/300" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <?php if ($product['featured']): ?>
                                <span class="product-card-badge">Featured</span>
                            <?php endif; ?>
                            <button class="product-card-wishlist wishlist-toggle <?php echo in_array($product['id'], $wishlistIds) ? 'active' : ''; ?>" data-product-id="<?php echo $product['id']; ?>">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>
                        <div class="product-card-body">
                            <div class="product-card-category"><?php echo htmlspecialchars($product['category_name']); ?></div>
                            <h3 class="product-card-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <div class="product-card-rating">
                                <div class="stars">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star<?php echo $i <= round($product['rating']) ? '' : ' text-muted'; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <span class="rating-text">(<?php echo $product['total_reviews']; ?>)</span>
                            </div>
                            <div class="product-card-footer">
                                <div class="product-price">
                                    $<?php echo number_format($product['price_per_day'], 2); ?>
                                    <span>/day</span>
                                </div>
                                <a href="product-details.php?slug=<?php echo htmlspecialchars($product['slug']); ?>" class="btn-rent">Rent Now</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="empty-state">
                        <i class="fas fa-search"></i>
                        <h3>No products found</h3>
                        <p>Try adjusting your filters or browse all categories.</p>
                        <a href="products.php" class="btn-primary-gradient mt-3">View All Products</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Loading indicator -->
        <div id="loadingMore" class="text-center py-4" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
