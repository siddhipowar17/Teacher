<?php
$pageTitle = 'Contact';
require_once 'includes/header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        $error = 'Please fill in all required fields.';
    } else {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $subject, $message);
        if ($stmt->execute()) {
            $success = 'Message sent successfully! We\'ll get back to you soon.';
        } else {
            $error = 'Failed to send message. Please try again.';
        }
    }
}
?>

<section class="products-hero">
    <h1 data-aos="fade-up">Get in Touch</h1>
    <p data-aos="fade-up" data-aos-delay="100">We'd love to hear from you</p>
</section>

<section class="contact-section">
    <div class="container">
        <!-- Contact Info Cards -->
        <div class="row g-4 mb-5">
            <div class="col-md-4" data-aos="fade-up">
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h5>Our Location</h5>
                    <p class="text-muted mb-0">123 Innovation Drive<br>San Francisco, CA 94102</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h5>Email Us</h5>
                    <p class="text-muted mb-0">hello@luxerent.com<br>support@luxerent.com</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <h5>Call Us</h5>
                    <p class="text-muted mb-0">+1 (555) 123-4567<br>Mon-Fri, 9am-6pm PST</p>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="row justify-content-center">
            <div class="col-lg-8" data-aos="fade-up">
                <?php if ($success): ?>
                    <div class="alert alert-success text-center"><?php echo $success; ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger text-center"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="contact-form">
                    <h3 class="mb-4">Send us a message</h3>
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <input type="text" name="name" class="form-control" placeholder="Your Name" value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <input type="email" name="email" class="form-control" placeholder="Your Email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                            </div>
                            <div class="col-12">
                                <input type="text" name="subject" class="form-control" placeholder="Subject" value="<?php echo htmlspecialchars($subject ?? ''); ?>">
                            </div>
                            <div class="col-12">
                                <textarea name="message" class="form-control" rows="6" placeholder="Your Message" required><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn-primary-gradient w-100">
                                    <i class="fas fa-paper-plane me-2"></i> Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
