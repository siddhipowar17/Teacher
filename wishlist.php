<?php
$pageTitle = 'Wishlist';
require_once 'includes/header.php';
requireLogin();

$stmt = $conn->prepare("SELECT p.*, c.name as category_name FROM wishlist w JOIN products p ON w.product_id = p.id JOIN categories c ON p.category_id = c.id WHERE w.user_id = ? ORDER BY w.created_at DESC");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$wishlistItems = $stmt->get_result();
?>

<section class="wishlist-section">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <h2 class="section-title">My Wishlist</h2>
            <p class="section-subtitle">Your saved rental items</p>
        </div>

        <?php if ($wishlistItems->num_rows > 0): ?>
        <div class="row g-4" id="wishlistGrid">
            <?php while ($item = $wishlistItems->fetch_assoc()): ?>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" id="wishlist-item-<?php echo $item['id']; ?>">
                <div class="product-card hover-lift">
                    <div class="product-card-image">
                        <img src="https://picsum.photos/seed/<?php echo $item['slug']; ?>/400/300" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <button class="product-card-wishlist wishlist-toggle active" data-product-id="<?php echo $item['id']; ?>">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                    <div class="product-card-body">
                        <div class="product-card-category"><?php echo htmlspecialchars($item['category_name']); ?></div>
                        <h3 class="product-card-title"><?php echo htmlspecialchars($item['name']); ?></h3>
                        <div class="product-card-footer">
                            <div class="product-price">
                                $<?php echo number_format($item['price_per_day'], 2); ?> <span>/day</span>
                            </div>
                            <a href="product-details.php?slug=<?php echo $item['slug']; ?>" class="btn-rent">Rent Now</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="empty-state" data-aos="fade-up">
            <i class="fas fa-heart"></i>
            <h3>Your wishlist is empty</h3>
            <p>Start adding products you love to your wishlist!</p>
            <a href="products.php" class="btn-primary-gradient mt-3">Browse Products</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
