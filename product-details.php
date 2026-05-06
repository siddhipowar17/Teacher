<?php
require_once 'includes/header.php';

$slug = $_GET['slug'] ?? '';
if (empty($slug)) {
    header('Location: products.php');
    exit;
}

$stmt = $conn->prepare("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.slug = ?");
$stmt->bind_param("s", $slug);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    header('Location: products.php');
    exit;
}

$pageTitle = $product['name'];

// Track recently viewed
if (isLoggedIn()) {
    $rvStmt = $conn->prepare("INSERT INTO recently_viewed (user_id, product_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE viewed_at = NOW()");
    $rvStmt->bind_param("ii", $_SESSION['user_id'], $product['id']);
    $rvStmt->execute();
}

// Check wishlist
$inWishlist = false;
if (isLoggedIn()) {
    $wCheck = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
    $wCheck->bind_param("ii", $_SESSION['user_id'], $product['id']);
    $wCheck->execute();
    $inWishlist = $wCheck->get_result()->num_rows > 0;
}

// Get reviews
$revStmt = $conn->prepare("SELECT r.*, u.full_name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = ? ORDER BY r.created_at DESC LIMIT 10");
$revStmt->bind_param("i", $product['id']);
$revStmt->execute();
$reviews = $revStmt->get_result();

// Get similar products
$simStmt = $conn->prepare("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.category_id = ? AND p.id != ? LIMIT 4");
$simStmt->bind_param("ii", $product['category_id'], $product['id']);
$simStmt->execute();
$similar = $simStmt->get_result();
?>

<section class="product-detail-section">
    <div class="container">
        <div class="row">
            <!-- Product Gallery -->
            <div class="col-lg-6" data-aos="fade-right">
                <div class="product-gallery">
                    <img class="product-main-image" src="https://picsum.photos/seed/<?php echo htmlspecialchars($product['slug']); ?>/800/600" alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
                <div class="product-thumbnails mt-3">
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                    <div class="product-thumb <?php echo $i === 1 ? 'active' : ''; ?>">
                        <img src="https://picsum.photos/seed/<?php echo htmlspecialchars($product['slug']) . '-' . $i; ?>/200/200" alt="Thumbnail <?php echo $i; ?>">
                    </div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-lg-6" data-aos="fade-left">
                <div class="product-info">
                    <span class="product-category-tag"><?php echo htmlspecialchars($product['category_name']); ?></span>
                    <h1><?php echo htmlspecialchars($product['name']); ?></h1>

                    <div class="product-rating-detail">
                        <div class="stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star<?php echo $i <= round($product['rating']) ? '' : ' text-muted'; ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <span><?php echo $product['rating']; ?> (<?php echo $product['total_reviews']; ?> reviews)</span>
                        <span class="text-muted">|</span>
                        <span><?php echo $product['total_rentals']; ?> rentals</span>
                    </div>

                    <div class="product-price-detail">
                        $<?php echo number_format($product['price_per_day'], 2); ?> <span>/day</span>
                    </div>

                    <?php if ($product['deposit'] > 0): ?>
                        <p class="text-muted mb-3">Security deposit: $<?php echo number_format($product['deposit'], 2); ?></p>
                    <?php endif; ?>

                    <span class="availability-badge <?php echo $product['availability']; ?>">
                        <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
                        <?php echo ucfirst($product['availability']); ?>
                    </span>

                    <p class="mt-3 mb-4" style="line-height: 1.8;"><?php echo htmlspecialchars($product['description']); ?></p>

                    <!-- Booking Calculator -->
                    <?php if ($product['availability'] === 'available'): ?>
                    <form action="booking.php" method="POST" class="booking-form">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <input type="hidden" id="pricePerDay" value="<?php echo $product['price_per_day']; ?>">
                        <input type="hidden" name="total_price" id="totalPriceInput" value="">
                        <input type="hidden" name="total_days" id="totalDaysInput" value="">

                        <div class="row g-3">
                            <div class="col-6">
                                <label>Start Date</label>
                                <input type="date" name="start_date" id="startDate" min="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-6">
                                <label>End Date</label>
                                <input type="date" name="end_date" id="endDate" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                            </div>
                        </div>

                        <div class="total-price-display">
                            <div>
                                <span class="label">Total (<span id="totalDays">0</span> days)</span>
                            </div>
                            <div class="amount" id="totalPrice">$0.00</div>
                        </div>

                        <button type="submit" class="btn-book-now magnetic-btn">
                            <i class="fas fa-calendar-check me-2"></i> Book Now
                        </button>
                    </form>
                    <?php else: ?>
                        <div class="alert alert-warning mt-3">This product is currently unavailable for rent.</div>
                    <?php endif; ?>

                    <button class="btn-wishlist-detail wishlist-toggle <?php echo $inWishlist ? 'active' : ''; ?>" data-product-id="<?php echo $product['id']; ?>">
                        <i class="fas fa-heart me-2"></i>
                        <?php echo $inWishlist ? 'In Wishlist' : 'Add to Wishlist'; ?>
                    </button>
                </div>
            </div>
        </div>

        <!-- Reviews -->
        <div class="mt-5 pt-5">
            <h3 class="mb-4" data-aos="fade-up">Customer Reviews</h3>
            <div class="row g-4">
                <?php if ($reviews->num_rows > 0): ?>
                    <?php while ($rev = $reviews->fetch_assoc()): ?>
                    <div class="col-md-6" data-aos="fade-up">
                        <div class="review-card">
                            <div class="review-stars">
                                <?php for ($i = 0; $i < $rev['rating']; $i++): ?>
                                    <i class="fas fa-star"></i>
                                <?php endfor; ?>
                            </div>
                            <p class="review-text">"<?php echo htmlspecialchars($rev['comment']); ?>"</p>
                            <div class="review-author">
                                <div class="review-avatar"><?php echo strtoupper($rev['full_name'][0]); ?></div>
                                <div class="review-author-info">
                                    <h6><?php echo htmlspecialchars($rev['full_name']); ?></h6>
                                    <span><?php echo date('M d, Y', strtotime($rev['created_at'])); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                        <p class="text-muted">No reviews yet. Be the first to review this product!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Similar Products -->
        <?php if ($similar->num_rows > 0): ?>
        <div class="mt-5 pt-5">
            <h3 class="mb-4" data-aos="fade-up">Similar Products</h3>
            <div class="row g-4">
                <?php while ($sim = $similar->fetch_assoc()): ?>
                <div class="col-lg-3 col-md-6" data-aos="fade-up">
                    <div class="product-card hover-lift">
                        <div class="product-card-image">
                            <img src="https://picsum.photos/seed/<?php echo htmlspecialchars($sim['slug']); ?>/400/300" alt="<?php echo htmlspecialchars($sim['name']); ?>">
                        </div>
                        <div class="product-card-body">
                            <div class="product-card-category"><?php echo htmlspecialchars($sim['category_name']); ?></div>
                            <h3 class="product-card-title"><?php echo htmlspecialchars($sim['name']); ?></h3>
                            <div class="product-card-footer">
                                <div class="product-price">$<?php echo number_format($sim['price_per_day'], 2); ?> <span>/day</span></div>
                                <a href="product-details.php?slug=<?php echo htmlspecialchars($sim['slug']); ?>" class="btn-rent">View</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
