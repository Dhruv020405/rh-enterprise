<?php
require_once "config/database.php";

$success = false;
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $company = trim($_POST['company']);
    $message = trim($_POST['message']);

    $ip = $_SERVER['REMOTE_ADDR'];
    $agent = $_SERVER['HTTP_USER_AGENT'];

    /* ===============================
       STRICT BACKEND VALIDATION
    =============================== */
    if (!$name || !$email || !$message) {
        $error = "Please fill all required fields.";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $error = "Name should only contain letters and spaces. No numbers allowed.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (!empty($phone) && !preg_match("/^\+?[0-9\s\-]{8,15}$/", $phone)) {
        $error = "Please enter a valid phone number (8-15 digits).";
    } else {
        
        $stmt = $conn->prepare("
            INSERT INTO inquiries
            (product_id, product_name, name, email, phone, company, message, ip_address, user_agent)
            VALUES (NULL, NULL, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "sssssss",
            $name,
            $email,
            $phone,
            $company,
            $message,
            $ip,
            $agent
        );

        if ($stmt->execute()) {
            $success = true;
        } else {
            $error = "Something went wrong. Please try again later.";
        }
    }
}
?>

<?php include "includes/header.php"; ?>
<?php include "includes/navbar.php"; ?>

<style>
    /* Industrial Theme Styles */
    :root {
        --industrial-dark: #1a252f;
        --industrial-accent: #dc3545;
        --industrial-light: #f8f9fa;
        --industrial-border: #e9ecef;
    }

    body {
        background-color: var(--industrial-light);
    }

    /* Hero Section */
    .page-hero {
        background: linear-gradient(135deg, var(--industrial-dark) 0%, #2c3e50 100%);
        color: #ffffff;
        padding: 4rem 0;
        margin-bottom: 3rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .accent-line {
        height: 4px;
        width: 60px;
        background-color: var(--industrial-accent);
        border-radius: 2px;
        margin-top: 12px;
    }

    /* Form Card Styling */
    .contact-card {
        background-color: #ffffff;
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        padding: 3rem 2.5rem;
        height: 100%;
    }

    /* Form Controls */
    .form-label {
        font-weight: 600;
        color: var(--industrial-dark);
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    .form-control {
        padding: 0.75rem 1rem;
        border-radius: 8px;
        border: 1px solid #ced4da;
        background-color: #f8f9fa;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        background-color: #ffffff;
        border-color: var(--industrial-accent);
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.15);
    }

    /* Contact Info Panel */
    .info-panel {
        background-color: #ffffff;
        border-radius: 12px;
        padding: 2.5rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        margin-bottom: 2rem;
    }

    .contact-item {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        margin-bottom: 1.5rem;
    }

    .contact-icon {
        width: 45px;
        height: 45px;
        background-color: rgba(220, 53, 69, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--industrial-accent);
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .contact-item:hover .contact-icon {
        background-color: var(--industrial-accent);
        color: #ffffff;
        transform: scale(1.05);
    }

    /* Map Box */
    .map-box {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        height: 300px;
    }

    /* Alerts */
    .success-alert {
        background-color: #d1e7dd;
        border: 1px solid #badbcc;
        color: #0f5132;
        border-radius: 8px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
</style>

<!-- HERO SECTION -->
<div class="page-hero">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php" class="text-white-50 text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active text-white fw-semibold" aria-current="page">Contact Us</li>
            </ol>
        </nav>
        <h1 class="display-5 fw-bold mb-0">Get In Touch</h1>
        <div class="accent-line"></div>
        <p class="mt-4 text-white-50 fs-5 max-w-75">
            Have questions about our industrial automation products or need a custom quote? Our team is here to help.
        </p>
    </div>
</div>

<div class="container pb-5 mb-5">

    <?php if ($success): ?>
        <div class="success-alert mb-5 fade show">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
            </svg>
            <div>
                <h5 class="fw-bold mb-1">Message Sent Successfully!</h5>
                <p class="mb-0 text-dark opacity-75">Thank you for contacting us. Our technical sales team will review your inquiry and reach out to you shortly.</p>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger fw-semibold shadow-sm mb-5">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="me-2" viewBox="0 0 16 16">
                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
            </svg>
            <?= htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div class="row g-5">
        
        <!-- LEFT: CONTACT FORM -->
        <div class="col-lg-7">
            <div class="contact-card">
                <h4 class="fw-bold text-dark mb-4 border-bottom pb-3">Send us a Message</h4>

                <form method="POST">
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="John Doe" required 
                                   pattern="[a-zA-Z\s]+" title="Name should only contain letters and spaces."
                                   oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="john@company.com" required>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone" class="form-control" placeholder="+91 98765 43210"
                                   pattern="^\+?[0-9\s\-]{8,15}$" title="Please enter a valid phone number (8-15 digits)."
                                   oninput="this.value = this.value.replace(/[^0-9+\s\-]/g, '')">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Company Name</label>
                            <input type="text" name="company" class="form-control" placeholder="Your Organization Ltd.">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">How can we help you? <span class="text-danger">*</span></label>
                        <textarea name="message" class="form-control" rows="5" placeholder="Please describe your requirements, project details, or questions..." required></textarea>
                    </div>

                    <button class="btn btn-danger btn-lg px-5 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm">
                        Send Message
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576 6.636 10.07Zm6.787-8.201L1.591 6.602l4.339 2.76 7.494-7.493Z"/></svg>
                    </button>
                </form>
            </div>
        </div>

        <!-- RIGHT: CONTACT INFO & MAP -->
        <div class="col-lg-5">
            
            <div class="info-panel">
                <h4 class="fw-bold mb-4" style="color: var(--industrial-dark);">Contact Information</h4>
                
                <!-- Address -->
                <div class="contact-item">
                    <div class="contact-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16"><path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/></svg>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Head Office</h6>
                        <p class="text-muted small mb-0 lh-base">
                            45, Devashray Arcade & Ind. Estate,<br>
                            Nr. Radhey Residency, SP Ring road,<br>
                            Nr. Vinzol Circle, Ramol,<br>
                            Ahmedabad, Gujarat, India.
                        </p>
                    </div>
                </div>

                <!-- Phone -->
                <div class="contact-item">
                    <div class="contact-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16"><path d="M3 2a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V2zm6 11a1 1 0 1 0-2 0 1 1 0 0 0 2 0z"/></svg>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Call Us</h6>
                        <a href="tel:+919408218427" class="text-muted text-decoration-none small d-block mb-1 fw-medium hover-danger">+91 94082 18427</a>
                        <a href="tel:+917359450751" class="text-muted text-decoration-none small d-block fw-medium hover-danger">+91 73594 50751</a>
                    </div>
                </div>

                <!-- Email -->
                <div class="contact-item mb-0">
                    <div class="contact-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16"><path d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414.05 3.555ZM0 4.697v7.104l5.803-3.558L0 4.697ZM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586l-1.239-.757Zm3.436-.586L16 11.801V4.697l-5.803 3.546Z"/></svg>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Email Us</h6>
                        <a href="mailto:info@rhentp.in" class="text-muted text-decoration-none small d-block mb-1 fw-medium hover-danger">info@rhentp.in</a>
                        <a href="mailto:purvang.rhenterprise@gmail.com" class="text-muted text-decoration-none small d-block fw-medium hover-danger">purvang.rhenterprise@gmail.com</a>
                    </div>
                </div>
            </div>

            <!-- Google Map Embed -->
            <div class="map-box">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14690.662205739327!2d72.6465406!3d22.9810141!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x395e86561e1146cd%3A0xc33eab3667104a37!2sRamol%2C%20Ahmedabad%2C%20Gujarat!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin" 
                    width="100%" 
                    height="100%" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>

        </div>

    </div>
</div>

<style>
    .hover-danger:hover { color: var(--industrial-accent) !important; }
</style>

<?php include "includes/footer.php"; ?>