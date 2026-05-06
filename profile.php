<?php
$pageTitle = 'Profile';
require_once 'includes/header.php';
requireLogin();

$user = getCurrentUser();
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';

    if (empty($name)) {
        $error = 'Name is required.';
    } else {
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, phone = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $phone, $_SESSION['user_id']);
        $stmt->execute();
        $_SESSION['user_name'] = $name;

        // Change password if provided
        if (!empty($currentPassword) && !empty($newPassword)) {
            if (password_verify($currentPassword, $user['password'])) {
                if (strlen($newPassword) >= 6) {
                    $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
                    $pStmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $pStmt->bind_param("si", $hashed, $_SESSION['user_id']);
                    $pStmt->execute();
                    $success = 'Profile and password updated successfully!';
                } else {
                    $error = 'New password must be at least 6 characters.';
                }
            } else {
                $error = 'Current password is incorrect.';
            }
        } else {
            $success = 'Profile updated successfully!';
        }

        $user = getCurrentUser();
    }
}
?>

<section class="profile-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="section-header" data-aos="fade-up">
                    <h2 class="section-title">My Profile</h2>
                    <p class="section-subtitle">Manage your account settings</p>
                </div>

                <div class="profile-card" data-aos="fade-up">
                    <div class="profile-avatar-large">
                        <?php echo strtoupper($user['full_name'][0]); ?>
                    </div>
                    <h3 class="mb-1"><?php echo htmlspecialchars($user['full_name']); ?></h3>
                    <p class="text-muted mb-4"><?php echo htmlspecialchars($user['email']); ?></p>

                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST" class="profile-form text-start mt-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Full Name</label>
                            <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Phone</label>
                            <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>

                        <hr class="my-4">
                        <h5 class="mb-3">Change Password</h5>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Current Password</label>
                            <input type="password" name="current_password" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">New Password</label>
                            <input type="password" name="new_password" class="form-control">
                        </div>

                        <button type="submit" class="btn-primary-gradient w-100 mt-3">
                            <i class="fas fa-save me-2"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
