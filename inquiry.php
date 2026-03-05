<?php
require_once "config/database.php";

$product_id = $_GET['product_id'] ?? null;
$product_name = '';

/* ===============================
   FETCH PRODUCT DETAILS
=============================== */
if ($product_id) {

    $stmt = $conn->prepare("SELECT id, name FROM products WHERE id = ? AND status=1");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();

    if ($product) {
        $product_name = $product['name'];
    } else {
        $product_id = null;
    }
}

$success = false;
$error = '';

/* ===============================
   FORM SUBMIT
=============================== */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    /* Honeypot Check */
    if (!empty($_POST['website'])) {
        die("Spam detected.");
    }

    $product_id = !empty($_POST['product_id']) ? intval($_POST['product_id']) : NULL;
    $product_name = trim($_POST['product_name']);

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $company = trim($_POST['company']);
    $message = trim($_POST['message']);

    /* ===============================
       STRICT BACKEND VALIDATION
    =============================== */
    if (empty($name) || empty($email) || empty($message)) {
        $error = "Please fill all required fields.";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $error = "Name should only contain letters and spaces. No numbers allowed.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (!empty($phone) && !preg_match("/^\+?[0-9\s\-]{8,15}$/", $phone)) {
        $error = "Please enter a valid phone number (8-15 digits).";
    } elseif (strlen($message) < 10) {
        $error = "Your message is too short. Please provide more details.";
    } else {
        
        /* Rate Limiting */
        $ip = $_SERVER['REMOTE_ADDR'];

        $stmt = $conn->prepare("
            SELECT COUNT(*) as total 
            FROM inquiries 
            WHERE ip_address = ? 
            AND created_at > (NOW() - INTERVAL 1 MINUTE)
        ");
        $stmt->bind_param("s", $ip);
        $stmt->execute();
        $count = $stmt->get_result()->fetch_assoc()['total'];

        if ($count > 5) {
            $error = "Too many requests. Please try again later.";
        } else {
            /* Insert Inquiry */
            $stmt = $conn->prepare("
                INSERT INTO inquiries 
                (product_id, product_name, name, email, phone, company, message, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $user_agent = $_SERVER['HTTP_USER_AGENT'];

            $stmt->bind_param(
                "issssssss",
                $product_id,
                $product_name,
                $name,
                $email,
                $phone,
                $company,
                $message,
                $ip,
                $user_agent
            );

            if ($stmt->execute()) {
                $success = true;
            } else {
                $error = "Something went wrong. Please try again later.";
            }
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
    .inquiry-card {
        background-color: #ffffff;
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        padding: 3rem 2.5rem;
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

    /* Readonly product field */
    .product-readonly {
        background-color: rgba(220, 53, 69, 0.05) !important;
        border-color: rgba(220, 53, 69, 0.2);
        color: var(--industrial-accent);
        font-weight: 600;
        cursor: not-allowed;
    }

    /* Contact Info Sidebar */
    .contact-sidebar {
        background-color: var(--industrial-dark);
        color: #ffffff;
        border-radius: 12px;
        padding: 2.5rem;
        height: 100%;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .contact-item {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        margin-bottom: 2rem;
    }

    .contact-icon {
        width: 40px;
        height: 40px;
        background-color: rgba(255,255,255,0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--industrial-accent);
        flex-shrink: 0;
    }

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
                <li class="breadcrumb-item"><a href="products.php" class="text-white-50 text-decoration-none">Products</a></li>
                <li class="breadcrumb-item active text-white fw-semibold" aria-current="page">Inquiry</li>
            </ol>
        </nav>
        <h1 class="display-5 fw-bold mb-0">Request a Quote</h1>
        <div class="accent-line"></div>
        <p class="mt-4 text-white-50 fs-5 max-w-75">
            Fill out the form below and our technical sales team will get back to you with pricing and specifications.
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
                <h5 class="fw-bold mb-1">Inquiry Submitted Successfully!</h5>
                <p class="mb-0 text-dark opacity-75">Thank you for reaching out. Our team will review your request and contact you shortly.</p>
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
        
        <!-- LEFT: FORM SECTION -->
        <div class="col-lg-8">
            <div class="inquiry-card">
                <h4 class="fw-bold text-dark mb-4 border-bottom pb-3">Inquiry Details</h4>

                <form method="POST">

                    <!-- Honeypot -->
                    <div style="display:none;">
                        <input type="text" name="website" tabindex="-1" autocomplete="off">
                    </div>

                    <?php if ($product_id): ?>
                        <input type="hidden" name="product_id" value="<?= $product_id; ?>">
                        <input type="hidden" name="product_name" value="<?= htmlspecialchars($product_name); ?>">

                        <div class="mb-4">
                            <label class="form-label text-muted small text-uppercase tracking-wide mb-1">Selected Product</label>
                            <input type="text" class="form-control product-readonly fs-5"
                                value="<?= htmlspecialchars($product_name); ?>" readonly>
                        </div>
                    <?php endif; ?>

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
                        <label class="form-label">Additional Message / Requirements <span class="text-danger">*</span></label>
                        <textarea name="message" class="form-control" rows="5" placeholder="Please specify your technical requirements, quantity needed, or any specific questions..." required></textarea>
                    </div>

                    <button class="btn btn-danger btn-lg px-5 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm">
                        Submit Inquiry
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576 6.636 10.07Zm6.787-8.201L1.591 6.602l4.339 2.76 7.494-7.493Z"/></svg>
                    </button>

                </form>
            </div>
        </div>

        <!-- RIGHT: CONTACT INFO SIDEBAR -->
        <div class="col-lg-4">
            <div class="contact-sidebar">
                <h4 class="fw-bold mb-4">Direct Contact</h4>
                <div class="accent-line bg-danger mb-5" style="height: 3px; width: 40px;"></div>

                <div class="contact-item">
                    <div class="contact-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/></svg>
                    </div>
                    <div>
                        <h6 class="fw-bold text-white mb-1">Office Address</h6>
                        <p class="text-white-50 small mb-0 lh-base">
                            45, Devashray Arcade & Ind. Estate, Nr. Radhey Residency, SP Ring road, Nr. Vinzol Circle, Ramol, Ahmedabad, Gujarat, India.
                        </p>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M3 2a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V2zm6 11a1 1 0 1 0-2 0 1 1 0 0 0 2 0z"/></svg>
                    </div>
                    <div>
                        <h6 class="fw-bold text-white mb-1">Call Us</h6>
                        <a href="tel:+919408218427" class="text-white-50 text-decoration-none small d-block mb-1 hover-white">+91 94082 18427</a>
                        <a href="tel:+917359450751" class="text-white-50 text-decoration-none small d-block hover-white">+91 73594 50751</a>
                    </div>
                </div>

                <div class="contact-item mb-0">
                    <div class="contact-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414.05 3.555ZM0 4.697v7.104l5.803-3.558L0 4.697ZM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586l-1.239-.757Zm3.436-.586L16 11.801V4.697l-5.803 3.546Z"/></svg>
                    </div>
                    <div>
                        <h6 class="fw-bold text-white mb-1">Email Us</h6>
                        <a href="mailto:info@rhentp.in" class="text-white-50 text-decoration-none small d-block mb-1 hover-white">info@rhentp.in</a>
                        <a href="mailto:purvang.rhenterprise@gmail.com" class="text-white-50 text-decoration-none small d-block hover-white">purvang.rhenterprise@gmail.com</a>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<?php include "includes/footer.php"; ?>