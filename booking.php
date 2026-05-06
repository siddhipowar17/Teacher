<?php
$pageTitle = 'Booking';
require_once 'includes/header.php';
requireLogin();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = intval($_POST['product_id'] ?? 0);
    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? '';
    $totalPrice = floatval($_POST['total_price'] ?? 0);
    $totalDays = intval($_POST['total_days'] ?? 0);

    if ($productId && $startDate && $endDate && $totalPrice > 0 && $totalDays > 0) {
        // Get product details
        $pStmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND availability = 'available'");
        $pStmt->bind_param("i", $productId);
        $pStmt->execute();
        $product = $pStmt->get_result()->fetch_assoc();

        if ($product) {
            $deposit = $product['deposit'];
            $bStmt = $conn->prepare("INSERT INTO bookings (user_id, product_id, start_date, end_date, total_days, total_price, deposit_amount) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $bStmt->bind_param("iissidd", $_SESSION['user_id'], $productId, $startDate, $endDate, $totalDays, $totalPrice, $deposit);

            if ($bStmt->execute()) {
                // Add notification
                $nStmt = $conn->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, 'Booking Confirmed', ?, 'success')");
                $msg = "Your booking for " . $product['name'] . " has been placed successfully!";
                $nStmt->bind_param("is", $_SESSION['user_id'], $msg);
                $nStmt->execute();

                $success = 'Booking placed successfully! You can track it from your dashboard.';
            } else {
                $error = 'Failed to create booking. Please try again.';
            }
        } else {
            $error = 'Product not available.';
        }
    } else {
        $error = 'Invalid booking details. Please go back and try again.';
    }
}
?>

<section class="booking-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5" data-aos="fade-up">
                    <h1 class="section-title"><?php echo $success ? 'Booking Confirmed!' : 'Confirm Booking'; ?></h1>
                </div>

                <?php if ($success): ?>
                <div class="dashboard-card text-center" data-aos="fade-up">
                    <div style="font-size: 4rem; color: #22c55e; margin-bottom: 20px;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3 class="mb-3"><?php echo $success; ?></h3>
                    <div class="d-flex gap-3 justify-content-center mt-4">
                        <a href="dashboard.php" class="btn-primary-gradient">
                            <i class="fas fa-th-large me-2"></i> Go to Dashboard
                        </a>
                        <a href="products.php" class="btn-load-more">
                            Continue Browsing
                        </a>
                    </div>
                </div>
                <?php elseif ($error): ?>
                <div class="dashboard-card text-center" data-aos="fade-up">
                    <div style="font-size: 4rem; color: #f5576c; margin-bottom: 20px;">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <h3 class="mb-3"><?php echo $error; ?></h3>
                    <a href="products.php" class="btn-primary-gradient mt-3">
                        Browse Products
                    </a>
                </div>
                <?php else: ?>
                <div class="dashboard-card text-center" data-aos="fade-up">
                    <p>Please select a product and booking dates from the product page.</p>
                    <a href="products.php" class="btn-primary-gradient mt-3">Browse Products</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
