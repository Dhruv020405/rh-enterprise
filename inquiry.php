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

    /* Basic Validation */
    if (strlen($name) < 3) die("Invalid name.");
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) die("Invalid email.");
    if (strlen($message) < 10) die("Message too short.");

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
        die("Too many requests. Try later.");
    }

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

    $stmt->execute();

    $success = true;
}
?>

<?php include "includes/header.php"; ?>
<?php include "includes/navbar.php"; ?>

<div class="container mt-5">

    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            Thank you! Our team will contact you shortly.
        </div>
    <?php endif; ?>

    <div class="card shadow p-4">
        <h4>Product Inquiry</h4>

        <form method="POST">

            <!-- Honeypot -->
            <div style="display:none;">
                <input type="text" name="website">
            </div>

            <?php if ($product_id): ?>
                <input type="hidden" name="product_id" value="<?= $product_id; ?>">
                <input type="hidden" name="product_name" value="<?= htmlspecialchars($product_name); ?>">

                <div class="mb-3">
                    <label>Product</label>
                    <input type="text" class="form-control"
                        value="<?= htmlspecialchars($product_name); ?>" readonly>
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <label>Your Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Phone</label>
                <input type="text" name="phone" class="form-control">
            </div>

            <div class="mb-3">
                <label>Company</label>
                <input type="text" name="company" class="form-control">
            </div>

            <div class="mb-3">
                <label>Message</label>
                <textarea name="message" class="form-control" rows="4" required></textarea>
            </div>

            <button class="btn btn-danger">Submit Inquiry</button>

        </form>
    </div>

</div>

<?php include "includes/footer.php"; ?>