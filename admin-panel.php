<?php
require_once 'includes/auth.php';
requireAdmin();

$currentUser = getCurrentUser();
$activeTab = $_GET['tab'] ?? 'dashboard';

// Dashboard Stats
$totalUsers = $conn->query("SELECT COUNT(*) as cnt FROM users")->fetch_assoc()['cnt'];
$totalProducts = $conn->query("SELECT COUNT(*) as cnt FROM products")->fetch_assoc()['cnt'];
$totalBookings = $conn->query("SELECT COUNT(*) as cnt FROM bookings")->fetch_assoc()['cnt'];
$totalRevenue = $conn->query("SELECT COALESCE(SUM(total_price), 0) as total FROM bookings WHERE payment_status = 'paid'")->fetch_assoc()['total'];
$pendingBookings = $conn->query("SELECT COUNT(*) as cnt FROM bookings WHERE status = 'pending'")->fetch_assoc()['cnt'];
$unreadMessages = $conn->query("SELECT COUNT(*) as cnt FROM contact_messages WHERE is_read = 0")->fetch_assoc()['cnt'];

// Monthly revenue data for chart
$monthlyRevenue = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $rev = $conn->query("SELECT COALESCE(SUM(total_price), 0) as total FROM bookings WHERE DATE_FORMAT(created_at, '%Y-%m') = '$month' AND payment_status = 'paid'")->fetch_assoc()['total'];
    $monthlyRevenue[] = ['month' => date('M', strtotime("-$i months")), 'revenue' => floatval($rev)];
}

// Fetch data based on tab
$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 20");
$products = $conn->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC LIMIT 20");
$bookings = $conn->query("SELECT b.*, u.full_name, p.name as product_name FROM bookings b JOIN users u ON b.user_id = u.id JOIN products p ON b.product_id = p.id ORDER BY b.created_at DESC LIMIT 20");
$messages = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 20");

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_booking_status') {
        $bookingId = intval($_POST['booking_id']);
        $status = $_POST['status'];
        $conn->query("UPDATE bookings SET status = '$status' WHERE id = $bookingId");
        header("Location: admin-panel.php?tab=bookings&msg=updated");
        exit;
    }

    if ($action === 'delete_product') {
        $productId = intval($_POST['product_id']);
        $conn->query("DELETE FROM products WHERE id = $productId");
        header("Location: admin-panel.php?tab=products&msg=deleted");
        exit;
    }

    if ($action === 'update_user_role') {
        $userId = intval($_POST['user_id']);
        $role = $_POST['role'];
        $conn->query("UPDATE users SET role = '$role' WHERE id = $userId");
        header("Location: admin-panel.php?tab=users&msg=updated");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | LuxeRent</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/animations.css" rel="stylesheet">
</head>
<body>

<div class="admin-wrapper">
    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="admin-sidebar-brand">
            <span class="brand-luxe" style="color:#fff;">Luxe</span><span class="brand-rent">Rent</span>
            <small class="d-block" style="color:rgba(255,255,255,0.4);font-size:0.75rem;margin-top:4px;">Admin Panel</small>
        </div>

        <ul class="admin-nav">
            <li><a href="?tab=dashboard" class="<?php echo $activeTab === 'dashboard' ? 'active' : ''; ?>"><i class="fas fa-th-large"></i> Dashboard</a></li>
            <li><a href="?tab=products" class="<?php echo $activeTab === 'products' ? 'active' : ''; ?>"><i class="fas fa-box"></i> Products</a></li>
            <li><a href="?tab=bookings" class="<?php echo $activeTab === 'bookings' ? 'active' : ''; ?>"><i class="fas fa-calendar-check"></i> Bookings <?php if ($pendingBookings > 0): ?><span class="badge bg-warning ms-auto"><?php echo $pendingBookings; ?></span><?php endif; ?></a></li>
            <li><a href="?tab=users" class="<?php echo $activeTab === 'users' ? 'active' : ''; ?>"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="?tab=messages" class="<?php echo $activeTab === 'messages' ? 'active' : ''; ?>"><i class="fas fa-envelope"></i> Messages <?php if ($unreadMessages > 0): ?><span class="badge bg-danger ms-auto"><?php echo $unreadMessages; ?></span><?php endif; ?></a></li>
            <li><a href="?tab=analytics" class="<?php echo $activeTab === 'analytics' ? 'active' : ''; ?>"><i class="fas fa-chart-bar"></i> Analytics</a></li>
            <li style="margin-top: 40px;"><a href="index.php"><i class="fas fa-arrow-left"></i> Back to Site</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        <!-- Mobile Toggle -->
        <button class="btn btn-dark d-lg-none mb-3" onclick="document.getElementById('adminSidebar').classList.toggle('active')">
            <i class="fas fa-bars"></i> Menu
        </button>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                Action completed successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($activeTab === 'dashboard'): ?>
        <!-- Dashboard Tab -->
        <div class="admin-header">
            <h2 class="admin-title">Dashboard Overview</h2>
            <span class="text-muted"><?php echo date('F d, Y'); ?></span>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="admin-stat-card">
                    <div class="admin-stat-icon" style="background: var(--gradient-1);">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="admin-stat-info">
                        <h3><?php echo $totalUsers; ?></h3>
                        <p>Total Users</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="admin-stat-card">
                    <div class="admin-stat-icon" style="background: linear-gradient(135deg, #4ade80, #22c55e);">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="admin-stat-info">
                        <h3><?php echo $totalProducts; ?></h3>
                        <p>Products</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="admin-stat-card">
                    <div class="admin-stat-icon" style="background: linear-gradient(135deg, #fbbf24, #f59e0b);">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="admin-stat-info">
                        <h3><?php echo $totalBookings; ?></h3>
                        <p>Total Bookings</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="admin-stat-card">
                    <div class="admin-stat-icon" style="background: linear-gradient(135deg, #f5576c, #f093fb);">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="admin-stat-info">
                        <h3>$<?php echo number_format($totalRevenue, 0); ?></h3>
                        <p>Revenue</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Chart -->
        <div class="admin-card">
            <h5 class="mb-4">Revenue Overview</h5>
            <div class="chart-container">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <?php elseif ($activeTab === 'products'): ?>
        <!-- Products Tab -->
        <div class="admin-header">
            <h2 class="admin-title">Products Management</h2>
        </div>

        <div class="admin-card">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price/Day</th>
                            <th>Rating</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($p = $products->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $p['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($p['name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($p['category_name']); ?></td>
                            <td>$<?php echo number_format($p['price_per_day'], 2); ?></td>
                            <td><i class="fas fa-star text-warning"></i> <?php echo $p['rating']; ?></td>
                            <td><span class="status-badge <?php echo $p['availability']; ?>"><?php echo ucfirst($p['availability']); ?></span></td>
                            <td>
                                <a href="product-details.php?slug=<?php echo $p['slug']; ?>" class="btn btn-sm btn-outline-primary" target="_blank"><i class="fas fa-eye"></i></a>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this product?');">
                                    <input type="hidden" name="action" value="delete_product">
                                    <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php elseif ($activeTab === 'bookings'): ?>
        <!-- Bookings Tab -->
        <div class="admin-header">
            <h2 class="admin-title">Bookings Management</h2>
        </div>

        <div class="admin-card">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Product</th>
                            <th>Dates</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($b = $bookings->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $b['id']; ?></td>
                            <td><?php echo htmlspecialchars($b['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($b['product_name']); ?></td>
                            <td><?php echo date('M d', strtotime($b['start_date'])); ?> - <?php echo date('M d', strtotime($b['end_date'])); ?></td>
                            <td>$<?php echo number_format($b['total_price'], 2); ?></td>
                            <td><span class="status-badge <?php echo $b['status']; ?>"><?php echo ucfirst($b['status']); ?></span></td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="action" value="update_booking_status">
                                    <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                                    <select name="status" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                        <?php foreach (['pending', 'confirmed', 'active', 'completed', 'cancelled'] as $s): ?>
                                            <option value="<?php echo $s; ?>" <?php echo $b['status'] === $s ? 'selected' : ''; ?>><?php echo ucfirst($s); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php elseif ($activeTab === 'users'): ?>
        <!-- Users Tab -->
        <div class="admin-header">
            <h2 class="admin-title">User Management</h2>
        </div>

        <div class="admin-card">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($u = $users->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $u['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($u['full_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($u['email']); ?></td>
                            <td><?php echo htmlspecialchars($u['phone'] ?? '-'); ?></td>
                            <td>
                                <span class="status-badge <?php echo $u['role'] === 'admin' ? 'active' : 'confirmed'; ?>">
                                    <?php echo ucfirst($u['role']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="action" value="update_user_role">
                                    <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                    <select name="role" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                        <option value="user" <?php echo $u['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                                        <option value="admin" <?php echo $u['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php elseif ($activeTab === 'messages'): ?>
        <!-- Messages Tab -->
        <div class="admin-header">
            <h2 class="admin-title">Contact Messages</h2>
        </div>

        <div class="admin-card">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($m = $messages->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($m['name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($m['email']); ?></td>
                            <td><?php echo htmlspecialchars($m['subject'] ?: '-'); ?></td>
                            <td><?php echo htmlspecialchars(substr($m['message'], 0, 80)); ?>...</td>
                            <td><?php echo date('M d, Y', strtotime($m['created_at'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php elseif ($activeTab === 'analytics'): ?>
        <!-- Analytics Tab -->
        <div class="admin-header">
            <h2 class="admin-title">Analytics</h2>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="admin-card">
                    <h5 class="mb-4">Revenue Trend</h5>
                    <div class="chart-container">
                        <canvas id="revenueTrendChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="admin-card">
                    <h5 class="mb-4">Booking Status</h5>
                    <div class="chart-container">
                        <canvas id="bookingStatusChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="admin-card">
                    <h5 class="mb-4">Category Distribution</h5>
                    <div class="chart-container">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="admin-card">
                    <h5 class="mb-4">Top Products</h5>
                    <?php
                    $topProducts = $conn->query("SELECT name, total_rentals, rating FROM products ORDER BY total_rentals DESC LIMIT 5");
                    while ($tp = $topProducts->fetch_assoc()):
                    ?>
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
                        <div>
                            <strong><?php echo htmlspecialchars($tp['name']); ?></strong>
                            <br><small class="text-muted"><?php echo $tp['total_rentals']; ?> rentals</small>
                        </div>
                        <span class="badge bg-primary"><?php echo $tp['rating']; ?> <i class="fas fa-star"></i></span>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </main>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
// Dark mode
const saved = localStorage.getItem('luxerent-dark-mode');
if (saved === 'true') {
    document.documentElement.setAttribute('data-theme', 'dark');
}

// Chart.js - Revenue
const monthlyData = <?php echo json_encode($monthlyRevenue); ?>;

<?php if ($activeTab === 'dashboard'): ?>
const revenueCtx = document.getElementById('revenueChart')?.getContext('2d');
if (revenueCtx) {
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: monthlyData.map(d => d.month),
            datasets: [{
                label: 'Revenue ($)',
                data: monthlyData.map(d => d.revenue),
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointBackgroundColor: '#667eea',
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });
}
<?php endif; ?>

<?php if ($activeTab === 'analytics'): ?>
// Revenue Trend Chart
const trendCtx = document.getElementById('revenueTrendChart')?.getContext('2d');
if (trendCtx) {
    new Chart(trendCtx, {
        type: 'bar',
        data: {
            labels: monthlyData.map(d => d.month),
            datasets: [{
                label: 'Revenue ($)',
                data: monthlyData.map(d => d.revenue),
                backgroundColor: ['#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe', '#00f2fe'],
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });
}

// Booking Status Chart
const statusCtx = document.getElementById('bookingStatusChart')?.getContext('2d');
if (statusCtx) {
    <?php
    $statusData = [];
    $statuses = ['pending', 'confirmed', 'active', 'completed', 'cancelled'];
    foreach ($statuses as $s) {
        $cnt = $conn->query("SELECT COUNT(*) as cnt FROM bookings WHERE status = '$s'")->fetch_assoc()['cnt'];
        $statusData[$s] = $cnt;
    }
    ?>
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode(array_map('ucfirst', array_keys($statusData))); ?>,
            datasets: [{
                data: <?php echo json_encode(array_values($statusData)); ?>,
                backgroundColor: ['#fbbf24', '#3b82f6', '#22c55e', '#667eea', '#f5576c'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });
}

// Category Distribution
const catCtx = document.getElementById('categoryChart')?.getContext('2d');
if (catCtx) {
    <?php
    $catData = $conn->query("SELECT c.name, COUNT(p.id) as cnt FROM categories c LEFT JOIN products p ON c.id = p.category_id GROUP BY c.id ORDER BY cnt DESC LIMIT 6");
    $catLabels = [];
    $catCounts = [];
    while ($cd = $catData->fetch_assoc()) {
        $catLabels[] = $cd['name'];
        $catCounts[] = $cd['cnt'];
    }
    ?>
    new Chart(catCtx, {
        type: 'polarArea',
        data: {
            labels: <?php echo json_encode($catLabels); ?>,
            datasets: [{
                data: <?php echo json_encode($catCounts); ?>,
                backgroundColor: ['rgba(102,126,234,0.7)', 'rgba(118,75,162,0.7)', 'rgba(240,147,251,0.7)', 'rgba(245,87,108,0.7)', 'rgba(79,172,254,0.7)', 'rgba(0,242,254,0.7)'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });
}
<?php endif; ?>
</script>
</body>
</html>
