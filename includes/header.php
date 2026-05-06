<?php
require_once __DIR__ . '/auth.php';
$currentUser = getCurrentUser();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// Get unread notifications count
$notifCount = 0;
if (isLoggedIn()) {
    $nStmt = $conn->prepare("SELECT COUNT(*) as cnt FROM notifications WHERE user_id = ? AND is_read = 0");
    $nStmt->bind_param("i", $_SESSION['user_id']);
    $nStmt->execute();
    $notifCount = $nStmt->get_result()->fetch_assoc()['cnt'];
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | ' . SITE_NAME : SITE_NAME . ' - Premium Rental Marketplace'; ?></title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- AOS Animations -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Swiper.js -->
    <link href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/animations.css" rel="stylesheet">
</head>
<body class="<?php echo $currentPage; ?>-page">

<!-- Loading Screen -->
<div id="loading-screen">
    <div class="loader-content">
        <div class="loader-logo">
            <span class="logo-text">Luxe</span><span class="logo-accent">Rent</span>
        </div>
        <div class="loader-bar">
            <div class="loader-progress"></div>
        </div>
    </div>
</div>

<!-- Cursor Glow -->
<div class="cursor-glow" id="cursorGlow"></div>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg fixed-top glass-nav" id="mainNav">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <span class="brand-luxe">Luxe</span><span class="brand-rent">Rent</span>
        </a>

        <button class="navbar-toggler glass-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="toggler-icon"><i class="fas fa-bars"></i></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Live Search -->
            <div class="nav-search-wrapper mx-auto">
                <div class="search-glass">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="liveSearch" class="search-input" placeholder="Search premium rentals..." autocomplete="off">
                    <div class="search-results-dropdown" id="searchResults"></div>
                </div>
            </div>

            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage === 'index' ? 'active' : ''; ?>" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage === 'products' ? 'active' : ''; ?>" href="products.php">Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage === 'contact' ? 'active' : ''; ?>" href="contact.php">Contact</a>
                </li>

                <?php if (isLoggedIn()): ?>
                    <li class="nav-item">
                        <a class="nav-link icon-link" href="wishlist.php" title="Wishlist">
                            <i class="fas fa-heart"></i>
                        </a>
                    </li>
                    <li class="nav-item position-relative">
                        <a class="nav-link icon-link" href="#" id="notifBell" title="Notifications">
                            <i class="fas fa-bell"></i>
                            <?php if ($notifCount > 0): ?>
                                <span class="notif-badge"><?php echo $notifCount; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle user-dropdown" href="#" data-bs-toggle="dropdown">
                            <div class="user-avatar-small">
                                <i class="fas fa-user"></i>
                            </div>
                            <span class="user-name-nav"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        </a>
                        <ul class="dropdown-menu glass-dropdown">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="dashboard.php"><i class="fas fa-th-large me-2"></i>Dashboard</a></li>
                            <?php if (isAdmin()): ?>
                                <li><a class="dropdown-item" href="admin-panel.php"><i class="fas fa-cog me-2"></i>Admin Panel</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link btn-glass-nav" href="login.php">Sign In</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn-glow-nav" href="register.php">Get Started</a>
                    </li>
                <?php endif; ?>

                <!-- Dark Mode Toggle -->
                <li class="nav-item">
                    <button class="dark-mode-toggle" id="darkModeToggle" title="Toggle Dark Mode">
                        <i class="fas fa-moon"></i>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>
