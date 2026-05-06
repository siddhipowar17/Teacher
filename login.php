<?php
require_once 'includes/auth.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } elseif (loginUser($email, $password)) {
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | LuxeRent</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/animations.css" rel="stylesheet">
</head>
<body>

<section class="auth-section">
    <video class="auth-video-bg" autoplay muted loop playsinline>
        <source src="https://cdn.coverr.co/videos/coverr-typing-on-a-laptop-4488/1080p.mp4" type="video/mp4">
    </video>
    <div class="auth-overlay"></div>

    <div class="auth-card blur-in">
        <div class="auth-logo">
            <a href="index.php">
                <span class="brand-luxe">Luxe</span><span class="brand-rent">Rent</span>
            </a>
        </div>

        <h2 class="auth-title">Welcome Back</h2>
        <p class="auth-subtitle">Sign in to your premium account</p>

        <?php if ($error): ?>
            <div class="auth-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-floating-glass">
                <i class="fas fa-envelope form-icon"></i>
                <input type="email" name="email" placeholder="Email address" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
            </div>

            <div class="form-floating-glass">
                <i class="fas fa-lock form-icon"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <label style="color: rgba(255,255,255,0.5); font-size: 0.85rem; cursor: pointer;">
                    <input type="checkbox" style="margin-right: 6px;"> Remember me
                </label>
                <a href="#" style="color: var(--accent); font-size: 0.85rem;">Forgot password?</a>
            </div>

            <button type="submit" class="btn-auth magnetic-btn">
                Sign In <i class="fas fa-arrow-right ms-2"></i>
            </button>
        </form>

        <div class="auth-divider">or continue with</div>

        <div class="d-flex gap-3">
            <button class="btn-auth" style="background: rgba(255,255,255,0.06); flex: 1;" disabled>
                <i class="fab fa-google me-2"></i> Google
            </button>
            <button class="btn-auth" style="background: rgba(255,255,255,0.06); flex: 1;" disabled>
                <i class="fab fa-github me-2"></i> GitHub
            </button>
        </div>

        <div class="auth-footer">
            Don't have an account? <a href="register.php">Create one</a>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script>
    gsap.from('.auth-card', { y: 50, opacity: 0, duration: 1, ease: 'power3.out', delay: 0.3 });
    gsap.from('.form-floating-glass', { y: 20, opacity: 0, duration: 0.6, stagger: 0.1, ease: 'power3.out', delay: 0.6 });
</script>
</body>
</html>
