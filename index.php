<?php
$pageTitle = 'Home';
require_once 'includes/header.php';

// Fetch trending products
$trendingQuery = "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.trending = 1 ORDER BY p.total_rentals DESC LIMIT 8";
$trendingResult = $conn->query($trendingQuery);

// Fetch categories
$catQuery = "SELECT c.*, COUNT(p.id) as product_count FROM categories c LEFT JOIN products p ON c.id = p.category_id GROUP BY c.id ORDER BY c.name";
$catResult = $conn->query($catQuery);

// Fetch stats
$statsQuery = "SELECT * FROM site_stats";
$statsResult = $conn->query($statsQuery);
$stats = [];
while ($s = $statsResult->fetch_assoc()) {
    $stats[$s['stat_key']] = $s['stat_value'];
}

// Fetch reviews
$reviewQuery = "SELECT r.*, u.full_name, p.name as product_name FROM reviews r JOIN users u ON r.user_id = u.id JOIN products p ON r.product_id = p.id ORDER BY r.created_at DESC LIMIT 6";
$reviewResult = $conn->query($reviewQuery);

// User wishlist IDs
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

<!-- Hero Section -->
<section class="hero-section">
    <video class="hero-video-bg" autoplay muted loop playsinline>
        <source src="https://cdn.coverr.co/videos/coverr-aerial-view-of-city-at-night-1573/1080p.mp4" type="video/mp4">
    </video>
    <div class="hero-overlay"></div>

    <div class="hero-content">
        <div class="hero-badge">
            <span class="badge-dot"></span>
            Premium Rental Platform
        </div>
        <h1 class="hero-title">
            Rent Anything.<br>
            <span class="title-gradient">Anytime.</span>
        </h1>
        <p class="hero-subtitle">
            Premium rental experience for the modern lifestyle. From electronics to fashion,
            rent premium products with confidence and style.
        </p>
        <div class="hero-buttons">
            <a href="products.php" class="btn-hero-primary magnetic-btn">
                Explore Products <i class="fas fa-arrow-right"></i>
            </a>
            <a href="#trending" class="btn-hero-secondary">
                <i class="fas fa-play"></i> See What's Trending
            </a>
        </div>
    </div>

    <div class="scroll-indicator">
        <span>Scroll</span>
        <div class="scroll-mouse"></div>
    </div>
</section>

<!-- Trending Rentals -->
<section class="trending-section section-padding" id="trending">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-badge"><i class="fas fa-fire"></i> Trending</span>
            <h2 class="section-title">Trending Rentals</h2>
            <p class="section-subtitle">Most popular products loved by our community</p>
        </div>

        <div class="row g-4 products-grid-animated">
            <?php while ($product = $trendingResult->fetch_assoc()): ?>
            <div class="col-lg-3 col-md-6">
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
        </div>

        <div class="text-center mt-5" data-aos="fade-up">
            <a href="products.php" class="btn-hero-primary">
                View All Products <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- Categories -->
<section class="categories-section section-padding">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-badge"><i class="fas fa-th-large"></i> Categories</span>
            <h2 class="section-title">Browse by Category</h2>
            <p class="section-subtitle">Find exactly what you need from our curated categories</p>
        </div>

        <div class="row g-4">
            <?php while ($cat = $catResult->fetch_assoc()): ?>
            <div class="col-lg-2 col-md-4 col-6" data-aos="fade-up" data-aos-delay="<?php echo $cat['id'] * 50; ?>">
                <a href="products.php?cat=<?php echo htmlspecialchars($cat['slug']); ?>" class="category-card d-block">
                    <div class="category-card-content">
                        <div class="category-icon">
                            <i class="fas <?php echo $cat['icon']; ?>"></i>
                        </div>
                        <h4 class="category-name"><?php echo htmlspecialchars($cat['name']); ?></h4>
                        <span class="category-count"><?php echo $cat['product_count']; ?> items</span>
                    </div>
                </a>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- Premium Showcase -->
<section class="showcase-section section-padding">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="showcase-content">
                    <span class="section-badge" style="border-color: rgba(255,255,255,0.15); background: rgba(255,255,255,0.06); color: #fff;">
                        <i class="fas fa-gem"></i> Premium Experience
                    </span>
                    <h2 class="showcase-title">Why Choose<br>LuxeRent?</h2>
                    <p class="showcase-text">
                        We offer a premium rental experience with verified products,
                        insurance coverage, and doorstep delivery. Rent with confidence.
                    </p>
                    <ul class="showcase-features">
                        <li><i class="fas fa-shield-alt"></i> Full Insurance Coverage</li>
                        <li><i class="fas fa-truck"></i> Free Doorstep Delivery</li>
                        <li><i class="fas fa-check-double"></i> Verified & Sanitized Products</li>
                        <li><i class="fas fa-headset"></i> 24/7 Customer Support</li>
                        <li><i class="fas fa-undo"></i> Easy Returns & Exchanges</li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="showcase-video-wrapper">
                    <img src="https://picsum.photos/seed/showcase/600/450" alt="Premium Showcase">
                    <div class="showcase-video-overlay">
                        <div class="play-btn-showcase floating">
                            <i class="fas fa-play"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Customer Reviews -->
<section class="reviews-section section-padding">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-badge"><i class="fas fa-star"></i> Reviews</span>
            <h2 class="section-title">What Our Customers Say</h2>
            <p class="section-subtitle">Trusted by thousands of happy renters worldwide</p>
        </div>

        <div class="swiper reviews-swiper" data-aos="fade-up">
            <div class="swiper-wrapper">
                <?php
                $testimonials = [
                    ['name' => 'Sarah Johnson', 'role' => 'Photographer', 'text' => 'LuxeRent has been a game-changer for my photography business. I can rent high-end cameras without the huge upfront cost. The quality is always top-notch!', 'rating' => 5],
                    ['name' => 'Michael Chen', 'role' => 'Event Planner', 'text' => 'I use LuxeRent for all my event equipment needs. The projectors and speakers are always in perfect condition. Highly recommended!', 'rating' => 5],
                    ['name' => 'Emily Rodriguez', 'role' => 'Freelancer', 'text' => 'Rented a MacBook Pro for a month-long project. The process was seamless and the delivery was right on time. Will definitely use again!', 'rating' => 4],
                    ['name' => 'David Park', 'role' => 'Gamer', 'text' => 'The PS5 bundle was amazing! Great condition, all accessories included. Perfect for a gaming weekend with friends.', 'rating' => 5],
                    ['name' => 'Priya Sharma', 'role' => 'Interior Designer', 'text' => 'Love renting furniture for staging homes. LuxeRent offers premium pieces at very reasonable daily rates.', 'rating' => 5],
                    ['name' => 'James Wilson', 'role' => 'Motorcyclist', 'text' => 'Rented a Ducati for a weekend trip. Absolute dream bike and the insurance coverage gave me peace of mind.', 'rating' => 5],
                ];
                foreach ($testimonials as $t):
                ?>
                <div class="swiper-slide">
                    <div class="review-card">
                        <div class="review-stars">
                            <?php for ($i = 0; $i < $t['rating']; $i++): ?>
                                <i class="fas fa-star"></i>
                            <?php endfor; ?>
                        </div>
                        <p class="review-text">"<?php echo $t['text']; ?>"</p>
                        <div class="review-author">
                            <div class="review-avatar"><?php echo strtoupper($t['name'][0]); ?></div>
                            <div class="review-author-info">
                                <h6><?php echo $t['name']; ?></h6>
                                <span><?php echo $t['role']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-pagination mt-4"></div>
        </div>
    </div>
</section>

<!-- Statistics -->
<section class="stats-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="stat-item" data-aos="fade-up">
                    <div class="stat-number" data-count="<?php echo $stats['total_users'] ?? 12500; ?>">0</div>
                    <div class="stat-label">Happy Users</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-item" data-aos="fade-up" data-aos-delay="100">
                    <div class="stat-number" data-count="<?php echo $stats['total_products'] ?? 850; ?>">0</div>
                    <div class="stat-label">Premium Products</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-item" data-aos="fade-up" data-aos-delay="200">
                    <div class="stat-number" data-count="<?php echo $stats['total_rentals'] ?? 45000; ?>">0</div>
                    <div class="stat-label">Successful Rentals</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-item" data-aos="fade-up" data-aos-delay="300">
                    <div class="stat-number" data-count="<?php echo $stats['total_reviews'] ?? 8900; ?>">0</div>
                    <div class="stat-label">5-Star Reviews</div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
