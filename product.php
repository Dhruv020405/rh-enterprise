<?php
require_once "config/database.php";

$slug = $_GET['slug'] ?? '';

if (!$slug) {
    die("Product not found.");
}

/* Fetch Product */
$stmt = $conn->prepare("SELECT * FROM products WHERE slug=? AND status=1");
$stmt->bind_param("s", $slug);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    die("Invalid product.");
}

$product_id = $product['id'];

/* Fetch Gallery Images */
$stmtImg = $conn->prepare("SELECT * FROM product_images WHERE product_id=?");
$stmtImg->bind_param("i", $product_id);
$stmtImg->execute();
$gallery = $stmtImg->get_result();

/* Fetch Features */
$stmtFeat = $conn->prepare("SELECT * FROM product_features WHERE product_id=?");
$stmtFeat->bind_param("i", $product_id);
$stmtFeat->execute();
$features = $stmtFeat->get_result();

/* Fetch Applications */
$stmtApp = $conn->prepare("SELECT * FROM product_applications WHERE product_id=?");
$stmtApp->bind_param("i", $product_id);
$stmtApp->execute();
$applications = $stmtApp->get_result();

include "includes/header.php";
include "includes/navbar.php";
?>

<div class="container mt-5">

    <div class="row">

        <!-- LEFT SIDE: MAIN IMAGE -->
        <div class="col-md-6">

            <?php if($product['main_image']): ?>
                <img src="uploads/products/<?= $product['main_image']; ?>"
                     class="img-fluid rounded shadow-sm mb-3">
            <?php endif; ?>

            <!-- GALLERY -->
            <?php if($gallery->num_rows > 0): ?>
                <div class="d-flex gap-2 flex-wrap">
                    <?php while($img = $gallery->fetch_assoc()): ?>
                        <img src="uploads/products/<?= $img['image']; ?>"
                             width="80"
                             class="rounded border">
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>

        </div>

        <!-- RIGHT SIDE: DETAILS -->
        <div class="col-md-6">

            <h2><?= htmlspecialchars($product['name']); ?></h2>

            <p class="text-muted mt-3">
                <?= nl2br(htmlspecialchars($product['description'])); ?>
            </p>

            <a href="contact.php?product=<?= urlencode($product['name']); ?>"
               class="btn btn-danger mt-3">
               Request Inquiry
            </a>

        </div>

    </div>

    <hr class="my-5">

    <!-- FEATURES -->
    <?php if($features->num_rows > 0): ?>
        <h4>Product Features</h4>
        <ul class="list-group mb-4">
            <?php while($feat = $features->fetch_assoc()): ?>
                <li class="list-group-item">
                    <?= htmlspecialchars($feat['feature_text']); ?>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php endif; ?>

    <!-- APPLICATIONS -->
    <?php if($applications->num_rows > 0): ?>
        <h4>Applications</h4>
        <ul class="list-group">
            <?php while($app = $applications->fetch_assoc()): ?>
                <li class="list-group-item">
                    <?= htmlspecialchars($app['application_text']); ?>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php endif; ?>

</div>

<?php include "includes/footer.php"; ?>