<?php
$page_title = 'Contact';
require_once __DIR__ . '/includes/header.php';

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        try {
            // Create table if it doesn't exist
            $pdo->exec("CREATE TABLE IF NOT EXISTS contact_messages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                subject VARCHAR(500) NOT NULL,
                message TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                status ENUM('new', 'read', 'replied') DEFAULT 'new',
                INDEX idx_status (status),
                INDEX idx_created (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            
            // Save the message to the database
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $subject, $message]);
            
            $success_message = 'Your message has been sent successfully! We will reply as soon as possible.';
            $name = $email = $subject = $message = '';
        } catch (Exception $e) {
            $error_message = 'An error occurred while sending. Please try again or contact us via WhatsApp.';
        }
    }
}
?>

<style>
.contact-hero {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    padding: 3.5rem 0 3rem;
    position: relative;
    overflow: hidden;
}

.contact-hero h1 {
    font-size: 2.5rem;
    font-weight: 700;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
    margin-bottom: 0.75rem;
}

.contact-hero p {
    font-size: 1.1rem;
    opacity: 0.95;
    margin: 0;
}

.hero-curve {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 30px;
    background: white;
    border-radius: 50% 50% 0 0 / 100% 100% 0 0;
}

.contact-container {
    padding: 2.5rem 15px;
    max-width: 1200px;
    margin: 0 auto;
}

.contact-card {
    background: white;
    border: none;
    border-radius: 1rem;
    box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    height: 100%;
    overflow: hidden;
}

.contact-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 1rem 2rem rgba(0,0,0,0.15);
}

.contact-card-body {
    padding: 2rem;
}

.contact-icon-wrapper {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.25rem;
}

.contact-icon {
    font-size: 1.75rem;
}

.contact-card h3 {
    font-size: 1.35rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 0.75rem;
}

.contact-card p {
    font-size: 0.95rem;
    line-height: 1.6;
    color: #718096;
    margin-bottom: 1.25rem;
}

.contact-info-box {
    background-color: #f7fafc;
    padding: 1rem;
    border-radius: 0.75rem;
    margin-bottom: 1.25rem;
}

.contact-info-box .email-link {
    color: #667eea;
    font-weight: 600;
    text-decoration: none;
    font-size: 0.95rem;
    word-break: break-all;
}

.contact-info-box .email-link:hover {
    color: #764ba2;
}

.contact-btn {
    padding: 0.75rem 1.75rem;
    border-radius: 2rem;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    font-size: 0.95rem;
    border: none;
    cursor: pointer;
}

.btn-email {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.btn-email:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
    color: white;
}

.btn-whatsapp {
    background: #25D366;
    color: white;
    box-shadow: 0 4px 15px rgba(37, 211, 102, 0.4);
    animation: pulse-wa 2s infinite;
}

.btn-whatsapp:hover {
    background: #128C7E;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(37, 211, 102, 0.5);
    color: white;
}

@keyframes pulse-wa {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.03); }
}

.wa-badge {
    background: linear-gradient(135deg, #25D36615 0%, #128C7E15 100%);
    border-left: 3px solid #25D366;
    padding: 0.65rem 1rem;
    border-radius: 0.5rem;
    margin-bottom: 1.25rem;
}

.form-section {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.1);
    padding: 2.5rem;
    margin-bottom: 2rem;
}

.form-section h2 {
    font-size: 1.75rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 0.5rem;
    text-align: center;
}

.form-section .subtitle {
    color: #718096;
    text-align: center;
    margin-bottom: 2rem;
    font-size: 0.95rem;
}

.form-icon-header {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
}

.form-label {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    display: block;
}

/* Modern Input Styles */
.input-with-icon {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #a0aec0;
    font-size: 0.95rem;
    z-index: 2;
    transition: color 0.3s ease;
}

.input-icon-textarea {
    position: absolute;
    left: 1rem;
    top: 1rem;
    color: #a0aec0;
    font-size: 0.95rem;
    z-index: 2;
    transition: color 0.3s ease;
}

.form-control-modern {
    border: 2px solid #e2e8f0;
    border-radius: 0.75rem;
    padding: 0.875rem 1rem 0.875rem 3rem;
    font-size: 0.95rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    width: 100%;
    background: #fafafa;
}

.form-control-modern:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    outline: none;
    background: white;
    transform: translateY(-1px);
}

.form-control-modern:focus + .input-icon,
.form-control-modern:focus ~ .input-icon-textarea {
    color: #667eea;
}

.form-control-modern::placeholder {
    color: #cbd5e0;
}

textarea.form-control-modern {
    resize: vertical;
    min-height: 140px;
}

.char-counter {
    text-align: right;
    font-size: 0.8rem;
    color: #718096;
    margin-top: 0.5rem;
    font-weight: 500;
}

.char-counter i {
    color: #a0aec0;
}

.form-control {
    border: 2px solid #e2e8f0;
    border-radius: 0.5rem;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    width: 100%;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    outline: none;
}

.form-control.is-valid {
    border-color: #38ef7d;
}

.form-control.is-invalid {
    border-color: #f56565;
}

.info-alert {
    background: linear-gradient(135deg, #667eea08 0%, #764ba208 100%);
    border: 1px solid #667eea30;
    border-left: 4px solid #667eea;
    padding: 1rem 1.25rem;
    border-radius: 0.75rem;
    font-size: 0.875rem;
    color: #2d3748;
    display: flex;
    align-items: start;
    gap: 0.5rem;
}

.info-alert i {
    color: #667eea;
    font-size: 1.1rem;
    margin-top: 0.125rem;
}

.btn-submit {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 0.875rem 2.5rem;
    border-radius: 2rem;
    font-weight: 600;
    border: none;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    position: relative;
    overflow: hidden;
}

.btn-submit::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s;
}

.btn-submit:hover::before {
    left: 100%;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(102, 126, 234, 0.5);
}

.btn-submit:active {
    transform: translateY(0);
}

.btn-loading {
    display: none;
}

.btn-reset {
    background: white;
    color: #718096;
    padding: 0.875rem 2.5rem;
    border-radius: 2rem;
    font-weight: 600;
    border: 2px solid #e2e8f0;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-reset:hover {
    border-color: #cbd5e0;
    background: #f7fafc;
}

.services-section {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 0.25rem 1rem rgba(0,0,0,0.08);
    padding: 2rem;
    margin-bottom: 2rem;
}

.services-section h2 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 0.5rem;
    text-align: center;
}

.services-section .subtitle {
    color: #718096;
    text-align: center;
    margin-bottom: 2rem;
    font-size: 0.9rem;
}

.service-item {
    background: #f7fafc;
    padding: 1.25rem;
    border-radius: 0.75rem;
    height: 100%;
    transition: all 0.3s ease;
}

.service-item:hover {
    background: #edf2f7;
    transform: translateY(-3px);
}

.service-icon {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.75rem;
}

.service-item h6 {
    font-size: 0.95rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 0.5rem;
}

.service-item p {
    font-size: 0.85rem;
    color: #718096;
    margin: 0;
    line-height: 1.5;
}

.cta-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 1rem;
    padding: 2.5rem;
    text-align: center;
    color: white;
}

.cta-section h3 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
}

.cta-section p {
    font-size: 1rem;
    opacity: 0.95;
    margin-bottom: 1.5rem;
}

.btn-cta {
    background: white;
    color: #667eea;
    padding: 0.875rem 2.5rem;
    border-radius: 2rem;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.btn-cta:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    color: #667eea;
}

.alert-custom {
    border: none;
    border-radius: 0.75rem;
    padding: 1rem 1.25rem;
    box-shadow: 0 0.25rem 1rem rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}

.alert-success-custom {
    background: linear-gradient(135deg, #38ef7d15 0%, #11998e15 100%);
    border-left: 4px solid #38ef7d;
    color: #047857;
}

.alert-danger-custom {
    background: linear-gradient(135deg, #f5576c15 0%, #f093fb15 100%);
    border-left: 4px solid #f5576c;
    color: #dc2626;
}

@media (max-width: 768px) {
    .contact-hero h1 { font-size: 1.75rem; }
    .contact-hero p { font-size: 0.95rem; }
    .contact-card-body { padding: 1.5rem; }
    .form-section { padding: 1.5rem; }
    .services-section { padding: 1.5rem; }
    .cta-section { padding: 1.75rem; }
    .btn-submit, .btn-reset { width: 100%; margin-top: 0.5rem; }
}

@media (max-width: 576px) {
    .contact-hero h1 { font-size: 1.5rem; }
    .contact-icon-wrapper { width: 60px; height: 60px; }
    .contact-icon { font-size: 1.5rem; }
    .contact-card h3 { font-size: 1.15rem; }
}
</style>

<!-- Hero Section -->
<div class="contact-hero">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center text-white px-3">
                <h1>Contact Us</h1>
                <p>We're here to answer any questions and assist with your needs</p>
            </div>
        </div>
    </div>
    <div class="hero-curve"></div>
</div>

<div class="contact-container">
    <!-- Alert Messages -->
    <?php if ($success_message): ?>
    <div class="row">
        <div class="col-12">
            <div class="alert-custom alert-success-custom">
                <i class="fas fa-check-circle" style="font-size: 1.25rem;"></i>
                <div style="flex: 1;"><?php echo $success_message; ?></div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
    <div class="row">
        <div class="col-12">
            <div class="alert-custom alert-danger-custom">
                <i class="fas fa-exclamation-circle" style="font-size: 1.25rem;"></i>
                <div style="flex: 1;"><?php echo $error_message; ?></div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Contact Cards -->
    <div class="row g-4 mb-4">
        <!-- Email Card -->
        <div class="col-md-6">
            <div class="contact-card">
                <div class="contact-card-body text-center">
                    <div class="contact-icon-wrapper mx-auto" style="background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);">
                        <i class="fas fa-envelope contact-icon" style="color: #667eea;"></i>
                    </div>
                    <h3>Business Email</h3>
                    <p>Send us an email for inquiries, quotes, or technical questions.</p>
                    
                    <div class="contact-info-box">
                        <div class="small text-muted mb-1">Write to us at</div>
                        <a href="mailto:<?php echo $settings['site_email'] ?? 'contact@gpower.com'; ?>" 
                           class="email-link">
                            <?php echo $settings['site_email'] ?? 'contact@gpower.com'; ?>
                        </a>
                    </div>
                    
                                <a href="mailto:<?php echo $settings['site_email'] ?? 'contact@gpower.com'; ?>" 
                                    class="contact-btn btn-email">
                                <i class="fas fa-envelope me-2"></i>Send an Email
                    </a>
                </div>
            </div>
        </div>

        <!-- WhatsApp Card -->
        <div class="col-md-6">
            <div class="contact-card">
                <div class="contact-card-body text-center">
                    <div class="contact-icon-wrapper mx-auto" style="background: linear-gradient(135deg, #25D36615 0%, #128C7E15 100%);">
                        <i class="fab fa-whatsapp contact-icon" style="color: #25D366;"></i>
                    </div>
                    <h3>WhatsApp Direct</h3>
                    <p>For a fast, instant response, contact us directly via WhatsApp.</p>
                    
                    <div class="wa-badge">
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <i class="fas fa-bolt text-success"></i>
                            <small class="fw-semibold text-success">Fast response guaranteed!</small>
                        </div>
                    </div>
                    
                        <a href="https://wa.me/<?php echo $settings['whatsapp_number'] ?? ''; ?>?text=Hello%20Gpower%2C%20I%20would%20like%20to%20get%20information" 
                       target="_blank"
                       class="contact-btn btn-whatsapp">
                        <i class="fab fa-whatsapp me-2"></i>Discuter Maintenant
                    </a>
                    
                    <div class="mt-3">
                        <small class="text-muted"><i class="fas fa-clock me-1"></i>Available 7 days/week</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Form -->
    <div class="row">
        <div class="col-12">
            <div class="form-section">
                <div class="text-center">
                    <div class="form-icon-header mx-auto">
                        <i class="fas fa-paper-plane" style="font-size: 1.5rem; color: #667eea;"></i>
                    </div>
                    <h2>Send Us a Message</h2>
                    <p class="subtitle">Fill the form below and we will reply promptly</p>
                </div>
                
                <form method="POST" action="" class="needs-validation" novalidate id="contactForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="fas fa-user me-1 text-primary"></i>Full Name
                            </label>
                            <div class="input-with-icon">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" 
                                       name="name" 
                                       class="form-control form-control-modern" 
                                            placeholder="e.g. John Doe"
                                       value="<?php echo htmlspecialchars($name ?? ''); ?>"
                                       required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="fas fa-envelope me-1 text-primary"></i>Email Address
                            </label>
                            <div class="input-with-icon">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email" 
                                       name="email" 
                                       class="form-control form-control-modern" 
                                       placeholder="your.email@example.com"
                                       value="<?php echo htmlspecialchars($email ?? ''); ?>"
                                       required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <label class="form-label">
                            <i class="fas fa-tag me-1 text-primary"></i>Subject
                        </label>
                        <div class="input-with-icon">
                            <i class="fas fa-tag input-icon"></i>
                            <input type="text" 
                                   name="subject" 
                                   class="form-control form-control-modern" 
                                   placeholder="e.g. Quote request for generator"
                                   value="<?php echo htmlspecialchars($subject ?? ''); ?>"
                                   required>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <label class="form-label">
                            <i class="fas fa-comment-dots me-1 text-primary"></i>Your Message
                        </label>
                        <div class="input-with-icon">
                            <i class="fas fa-comment-dots input-icon-textarea"></i>
                            <textarea name="message" 
                                      class="form-control form-control-modern" 
                                      rows="6" 
                                      placeholder="Describe your request in detail... The more information you provide, the better we can help!"
                                      required><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                        </div>
                        <div class="char-counter" id="charCounter">
                            <i class="fas fa-keyboard me-1"></i>
                            <span id="charCount">0</span> characters
                        </div>
                    </div>
                    
                        <div class="info-alert mt-3">
                            <i class="fas fa-lightbulb me-2"></i>
                            <div>
                                <strong>Pro Tip:</strong> For an instant response, contact us via WhatsApp. Emails are processed within 24-48 hours.
                            </div>
                        </div>
                    
                    <div class="d-flex flex-column flex-md-row gap-3 justify-content-end mt-4">
                        <button type="reset" class="btn-reset">
                            <i class="fas fa-redo me-2"></i>Reset
                        </button>
                        <button type="submit" name="send_message" class="btn-submit" id="submitBtn">
                            <span class="btn-content">
                                <i class="fas fa-paper-plane me-2"></i>Send Message
                            </span>
                            <span class="btn-loading" style="display: none;">
                                <i class="fas fa-spinner fa-spin me-2"></i>Sending...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Services Section -->
    <div class="row">
        <div class="col-12">
            <div class="services-section">
                <h2>How Can We Help You?</h2>
                <p class="subtitle">Our team is ready to address your needs</p>
                
                <div class="row g-3">
                    <div class="col-sm-6 col-lg-3">
                        <div class="service-item text-center">
                            <div class="service-icon mx-auto" style="background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);">
                                <i class="fas fa-question-circle" style="color: #667eea;"></i>
                            </div>
                            <h6>Product Questions</h6>
                            <p>Technical information and availability</p>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="service-item text-center">
                            <div class="service-icon mx-auto" style="background: linear-gradient(135deg, #11998e15 0%, #38ef7d15 100%);">
                                <i class="fas fa-file-invoice-dollar" style="color: #11998e;"></i>
                            </div>
                            <h6>Quote Request</h6>
                            <p>Fast personalized quote</p>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="service-item text-center">
                            <div class="service-icon mx-auto" style="background: linear-gradient(135deg, #f093fb15 0%, #f5576c15 100%);">
                                <i class="fas fa-handshake" style="color: #f5576c;"></i>
                            </div>
                            <h6>Partnerships</h6>
                            <p>Collaborations & resellers</p>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="service-item text-center">
                            <div class="service-icon mx-auto" style="background: linear-gradient(135deg, #FFA50015 0%, #FF450015 100%);">
                                <i class="fas fa-headset" style="color: #FF6B00;"></i>
                            </div>
                            <h6>Technical Support</h6>
                            <p>Expert assistance</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="row">
        <div class="col-12">
            <div class="cta-section">
                <i class="fas fa-comments mb-3" style="font-size: 2.5rem; opacity: 0.9;"></i>
                <h3>Need a Quick Answer?</h3>
                <p>WhatsApp is the fastest way to reach us. Get a reply in minutes!</p>
                <a href="https://wa.me/<?php echo $settings['whatsapp_number'] ?? ''; ?>?text=Bonjour%20Gpower" 
                   target="_blank"
                   class="btn-cta">
                    <i class="fab fa-whatsapp text-success" style="font-size: 1.25rem;"></i>
                    <span>Start a Conversation</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Character Counter
document.addEventListener('DOMContentLoaded', function() {
    const messageInput = document.querySelector('textarea[name="message"]');
    const charCount = document.getElementById('charCount');
    const charCounter = document.getElementById('charCounter');
    
    if (messageInput && charCount) {
        // Initialize count
        charCount.textContent = messageInput.value.length;
        
        messageInput.addEventListener('input', function() {
            const count = this.value.length;
            charCount.textContent = count;
            
            // Color coding based on length
            if (count < 20) {
                charCounter.style.color = '#f56565';
            } else if (count < 50) {
                charCounter.style.color = '#ed8936';
            } else {
                charCounter.style.color = '#48bb78';
            }
        });
    }
});

// Form Animation and Loading State
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');
    const submitBtn = document.getElementById('submitBtn');
    
    if (contactForm && submitBtn) {
        contactForm.addEventListener('submit', function(e) {
            if (contactForm.checkValidity()) {
                const btnContent = submitBtn.querySelector('.btn-content');
                const btnLoading = submitBtn.querySelector('.btn-loading');
                
                btnContent.style.display = 'none';
                btnLoading.style.display = 'inline-block';
                submitBtn.disabled = true;
            }
        });
    }
    
    // Input animation on focus
    const inputs = document.querySelectorAll('.form-control-modern');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'scale(1.01)';
            this.parentElement.style.transition = 'transform 0.2s ease';
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'scale(1)';
        });
    });
});

// Form Validation
(function() {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();

// Auto-hide alerts with animation
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert-custom');
    alerts.forEach(alert => {
        // Slide in animation
        alert.style.animation = 'slideInDown 0.5s ease';
        
        setTimeout(() => {
            alert.style.animation = 'slideOutUp 0.5s ease';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
});

// Add animation keyframes
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInDown {
        from {
            transform: translateY(-100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutUp {
        from {
            transform: translateY(0);
            opacity: 1;
        }
        to {
            transform: translateY(-100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
