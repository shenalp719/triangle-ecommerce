<?php
/**
 * Contact Page - Triangle Printing Solutions
 */
session_start();
require_once 'db.php';

$page_title = 'Contact Us';
include 'includes/header.php';

$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');
    
    if ($name && $email && $subject && $message) {
        // In production, send this to a database or email service
        $success = 'Thank you! We\'ll get back to you soon.';
    }
}
?>

    <section class="container" style="padding-top: 3rem; padding-bottom: 6rem;">
        <h1 style="text-align: center; margin-bottom: 1rem;">Contact Us</h1>
        <p style="text-align: center; color: var(--text-light); margin-bottom: 3rem; max-width: 600px; margin-left: auto; margin-right: auto;">
            Have questions about our products or services? We're here to help! Get in touch with our support team.
        </p>

        <div class="grid grid-2">
            <!-- Contact Form -->
            <div>
                <div class="card">
                    <div class="card-header">
                        <h4>Send us a Message</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($success): ?>
                            <div class="alert alert-success mb-3"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="form-group">
                                <label for="name">Name *</label>
                                <input type="text" id="name" name="name" required>
                            </div>

                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email" required>
                            </div>

                            <div class="form-group">
                                <label for="subject">Subject *</label>
                                <input type="text" id="subject" name="subject" required>
                            </div>

                            <div class="form-group">
                                <label for="message">Message *</label>
                                <textarea id="message" name="message" required rows="5"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">
                                Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 style="margin-bottom: 1.5rem;">📍 Our Location</h5>
                        <p>123 Print Street<br>Design City, DC 12345<br>United States</p>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-body">
                        <h5 style="margin-bottom: 1.5rem;">📞 Contact Information</h5>
                        <p>
                            <strong>Phone:</strong><br>
                            <a href="tel:+1234567890">+1 (234) 567-890</a><br><br>
                            <strong>Email:</strong><br>
                            <a href="mailto:info@triangleprinting.com">info@triangleprinting.com</a><br><br>
                            <strong>Sales:</strong><br>
                            <a href="mailto:sales@triangleprinting.com">sales@triangleprinting.com</a>
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 style="margin-bottom: 1.5rem;">⏰ Business Hours</h5>
                        <p>
                            <strong>Monday - Friday:</strong> 9:00 AM - 6:00 PM<br>
                            <strong>Saturday:</strong> 10:00 AM - 4:00 PM<br>
                            <strong>Sunday:</strong> Closed
                        </p>
                    </div>
                </div>

                <div class="card" style="margin-top: 1.5rem;">
                    <div class="card-body" style="text-align: center;">
                        <h5 style="margin-bottom: 1rem;">Need Instant Help?</h5>
                        <button class="btn btn-primary btn-sm" onclick="document.getElementById('chatbot-toggle').click()">
                            💬 Chat with Us
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <section style="margin-top: 4rem;">
            <h2 style="text-align: center; margin-bottom: 2rem;">Frequently Asked Questions</h2>
            
            <div style="max-width: 800px; margin: 0 auto;">
                <div class="card" style="margin-bottom: 1rem;">
                    <div class="card-body">
                        <h5>How long does shipping take?</h5>
                        <p>Standard shipping is 5-7 business days, and express shipping is 2-3 business days. Free shipping available on orders over $150.</p>
                    </div>
                </div>

                <div class="card" style="margin-bottom: 1rem;">
                    <div class="card-body">
                        <h5>What image resolution do I need?</h5>
                        <p>We recommend images with at least 300 DPI (dots per inch) for print quality. Our customizer will warn you if your image is too low resolution.</p>
                    </div>
                </div>

                <div class="card" style="margin-bottom: 1rem;">
                    <div class="card-body">
                        <h5>Can I save my designs?</h5>
                        <p>Yes! Create an account and save your designs to access them anytime. You can edit and reorder your saved designs.</p>
                    </div>
                </div>

                <div class="card" style="margin-bottom: 1rem;">
                    <div class="card-body">
                        <h5>What payment methods do you accept?</h5>
                        <p>We accept all major credit cards (Visa, Mastercard, Amex), PayPal, and Apple Pay.</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5>What is your return policy?</h5>
                        <p>We offer a 30-day money-back guarantee if you're not satisfied with your order. Custom designs are non-refundable once printing begins.</p>
                    </div>
                </div>
            </div>
        </section>
    </section>

<?php include 'includes/footer.php'; ?>
