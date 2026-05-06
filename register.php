<?php
require_once 'includes/auth.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Please fill in all required fields.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        // Check if email exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $error = 'Email already registered.';
        } else {
            $userId = registerUser($name, $email, $password, $phone);
            if ($userId) {
                $success = 'Account created successfully! You can now sign in.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | LuxeRent</title>
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

        <h2 class="auth-title">Get Started</h2>
        <p class="auth-subtitle">Create your premium rental account</p>

        <?php if ($error): ?>
            <div class="auth-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="auth-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-floating-glass">
                <i class="fas fa-user form-icon"></i>
                <input type="text" name="name" placeholder="Full Name" value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
            </div>

            <div class="form-floating-glass">
                <i class="fas fa-envelope form-icon"></i>
                <input type="email" name="email" placeholder="Email address" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
            </div>

            <div class="form-floating-glass">
                <i class="fas fa-phone form-icon"></i>
                <input type="tel" name="phone" placeholder="Phone (optional)" value="<?php echo htmlspecialchars($phone ?? ''); ?>">
            </div>

            <div class="form-floating-glass">
                <i class="fas fa-lock form-icon"></i>
                <input type="password" name="password" placeholder="Password (min 6 chars)" required>
            </div>

            <div class="form-floating-glass">
                <i class="fas fa-shield-alt form-icon"></i>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            </div>

            <button type="submit" class="btn-auth magnetic-btn">
                Create Account <i class="fas fa-arrow-right ms-2"></i>
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
            Already have an account? <a href="login.php">Sign in</a>
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
