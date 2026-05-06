<?php
$pageTitle = 'Dashboard';
require_once 'includes/header.php';
requireLogin();

$userId = $_SESSION['user_id'];

// Get user stats
$bookingCount = $conn->query("SELECT COUNT(*) as cnt FROM bookings WHERE user_id = $userId")->fetch_assoc()['cnt'];
$activeRentals = $conn->query("SELECT COUNT(*) as cnt FROM bookings WHERE user_id = $userId AND status = 'active'")->fetch_assoc()['cnt'];
$wishlistCount = $conn->query("SELECT COUNT(*) as cnt FROM wishlist WHERE user_id = $userId")->fetch_assoc()['cnt'];
$totalSpent = $conn->query("SELECT COALESCE(SUM(total_price), 0) as total FROM bookings WHERE user_id = $userId AND payment_status = 'paid'")->fetch_assoc()['total'];

// Get recent bookings
$bookingsStmt = $conn->prepare("SELECT b.*, p.name as product_name, p.slug FROM bookings b JOIN products p ON b.product_id = p.id WHERE b.user_id = ? ORDER BY b.created_at DESC LIMIT 10");
$bookingsStmt->bind_param("i", $userId);
$bookingsStmt->execute();
$bookings = $bookingsStmt->get_result();

// Get recently viewed
$rvStmt = $conn->prepare("SELECT DISTINCT p.*, c.name as category_name FROM recently_viewed rv JOIN products p ON rv.product_id = p.id JOIN categories c ON p.category_id = c.id WHERE rv.user_id = ? ORDER BY rv.viewed_at DESC LIMIT 4");
$rvStmt->bind_param("i", $userId);
$rvStmt->execute();
$recentlyViewed = $rvStmt->get_result();
?>

<section class="dashboard-section">
    <div class="container">
        <div class="mb-5" data-aos="fade-up">
            <h2 class="section-title mb-2">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
            <p class="text-muted">Here's an overview of your rental activity</p>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-5">
            <div class="col-lg-3 col-md-6" data-aos="fade-up">
                <div class="dashboard-card">
                    <div class="dashboard-stat-icon" style="background: var(--gradient-1);">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="dashboard-stat-value"><?php echo $bookingCount; ?></div>
                    <div class="dashboard-stat-label">Total Bookings</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="dashboard-card">
                    <div class="dashboard-stat-icon" style="background: linear-gradient(135deg, #4ade80, #22c55e);">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <div class="dashboard-stat-value"><?php echo $activeRentals; ?></div>
                    <div class="dashboard-stat-label">Active Rentals</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="dashboard-card">
                    <div class="dashboard-stat-icon" style="background: linear-gradient(135deg, #f5576c, #f093fb);">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="dashboard-stat-value"><?php echo $wishlistCount; ?></div>
                    <div class="dashboard-stat-label">Wishlist Items</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="dashboard-card">
                    <div class="dashboard-stat-icon" style="background: linear-gradient(135deg, #fbbf24, #f59e0b);">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="dashboard-stat-value">$<?php echo number_format($totalSpent, 0); ?></div>
                    <div class="dashboard-stat-label">Total Spent</div>
                </div>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="dashboard-card mb-5" data-aos="fade-up">
            <h4 class="mb-4">Recent Bookings</h4>
            <?php if ($bookings->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="booking-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Dates</th>
                            <th>Days</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($b = $bookings->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <a href="product-details.php?slug=<?php echo $b['slug']; ?>" style="color: var(--accent); font-weight: 600;">
                                    <?php echo htmlspecialchars($b['product_name']); ?>
                                </a>
                            </td>
                            <td><?php echo date('M d', strtotime($b['start_date'])); ?> - <?php echo date('M d, Y', strtotime($b['end_date'])); ?></td>
                            <td><?php echo $b['total_days']; ?></td>
                            <td><strong>$<?php echo number_format($b['total_price'], 2); ?></strong></td>
                            <td><span class="status-badge <?php echo $b['status']; ?>"><?php echo ucfirst($b['status']); ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-calendar"></i>
                <h3>No bookings yet</h3>
                <p>Start renting premium products today!</p>
                <a href="products.php" class="btn-primary-gradient mt-3">Browse Products</a>
            </div>
            <?php endif; ?>
        </div>

        <!-- Recently Viewed -->
        <?php if ($recentlyViewed->num_rows > 0): ?>
        <div data-aos="fade-up">
            <h4 class="mb-4">Recently Viewed</h4>
            <div class="row g-4">
                <?php while ($rv = $recentlyViewed->fetch_assoc()): ?>
                <div class="col-lg-3 col-md-6">
                    <div class="product-card hover-lift">
                        <div class="product-card-image">
                            <img src="https://picsum.photos/seed/<?php echo $rv['slug']; ?>/400/300" alt="<?php echo htmlspecialchars($rv['name']); ?>">
                        </div>
                        <div class="product-card-body">
                            <div class="product-card-category"><?php echo htmlspecialchars($rv['category_name']); ?></div>
                            <h3 class="product-card-title"><?php echo htmlspecialchars($rv['name']); ?></h3>
                            <div class="product-card-footer">
                                <div class="product-price">$<?php echo number_format($rv['price_per_day'], 2); ?> <span>/day</span></div>
                                <a href="product-details.php?slug=<?php echo $rv['slug']; ?>" class="btn-rent">View</a>
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
