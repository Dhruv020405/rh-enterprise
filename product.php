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
$breadcrumbPath = getCategoryPath($product['category_id']);

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

function getCategoryPath($category_id)
{
    global $conn;

    $path = [];

    while ($category_id != NULL) {

        $stmt = $conn->prepare("SELECT id, parent_id, name, slug FROM categories WHERE id = ?");
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result) {
            $path[] = $result;
            $category_id = $result['parent_id'];
        } else {
            break;
        }
    }

    return array_reverse($path);
}

/* ==========================
   RELATED PRODUCTS
========================== */

$stmtRelated = $conn->prepare("
    SELECT id, name, slug, main_image 
    FROM products 
    WHERE category_id = ? 
    AND id != ? 
    AND status = 1
    ORDER BY id DESC
    LIMIT 4
");

$stmtRelated->bind_param("ii", $product['category_id'], $product['id']);
$stmtRelated->execute();
$relatedProducts = $stmtRelated->get_result();

include "includes/header.php";
include "includes/navbar.php";
?>

<style>
    /* Industrial Theme Styles for Product Page */
    :root {
        --industrial-dark: #1a252f;
        --industrial-accent: #dc3545;
        --industrial-light: #f8f9fa;
        --industrial-border: #e9ecef;
    }

    body {
        background-color: var(--industrial-light);
    }

    /* =========================================
       AMAZON STYLE GALLERY (PC & Mobile)
    ========================================= */
    .amazon-gallery {
        display: flex;
        flex-direction: column-reverse; /* Mobile view: thumbnails below main image */
        gap: 15px;
    }

    .amazon-gallery-thumbnails {
        display: flex;
        flex-direction: row;
        gap: 10px;
        overflow-x: auto; /* Horizontal scroll for mobile */
        scrollbar-width: thin; /* Firefox scrollbar */
        padding-bottom: 5px;
    }

    /* Webkit custom scrollbar for thumbnails */
    .amazon-gallery-thumbnails::-webkit-scrollbar {
        height: 5px;
        width: 5px;
    }
    .amazon-gallery-thumbnails::-webkit-scrollbar-thumb {
        background-color: #dee2e6;
        border-radius: 10px;
    }

    .amazon-thumbnail {
        width: 55px;
        height: 55px;
        flex-shrink: 0;
        border: 1px solid var(--industrial-border);
        border-radius: 6px;
        cursor: pointer;
        object-fit: cover;
        background-color: #fff;
        padding: 2px;
        transition: all 0.2s ease;
    }

    .amazon-thumbnail:hover, 
    .amazon-thumbnail.active {
        border: 2px solid var(--industrial-accent);
        box-shadow: 0 2px 8px rgba(220, 53, 69, 0.2);
    }

    .amazon-gallery-main {
        flex: 1;
        background-color: #ffffff;
        border: 1px solid var(--industrial-border);
        border-radius: 12px;
        padding: 1rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .amazon-gallery-main img {
        width: 100%;
        max-height: 480px;
        object-fit: contain;
        border-radius: 8px;
    }

    /* Desktop View adjustments */
    @media (min-width: 992px) {
        .amazon-gallery {
            flex-direction: row; /* Desktop view: Thumbnails on Left, Main Image Right */
            align-items: flex-start;
        }
        
        .amazon-gallery-thumbnails {
            flex-direction: column; /* Vertical stack */
            width: 65px;
            max-height: 500px;
            overflow-y: auto;
            overflow-x: hidden;
            padding-bottom: 0;
            padding-right: 5px;
        }
    }

    /* Typography & Accents */
    .product-title {
        color: var(--industrial-dark);
        font-weight: 800;
        font-size: 2.2rem;
        margin-bottom: 0.5rem;
    }

    .accent-line {
        height: 4px;
        width: 60px;
        background-color: var(--industrial-accent);
        border-radius: 2px;
        margin-bottom: 1.5rem;
    }

    /* Feature & Application Lists */
    .custom-list {
        list-style: none;
        padding-left: 0;
    }

    .custom-list li {
        position: relative;
        padding-left: 2.5rem;
        margin-bottom: 1rem;
        font-size: 1.05rem;
        color: #495057;
        background-color: #ffffff;
        padding-top: 0.8rem;
        padding-bottom: 0.8rem;
        padding-right: 1rem;
        border-radius: 8px;
        border: 1px solid var(--industrial-border);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
    }

    .custom-list.features li::before {
        content: '';
        position: absolute;
        left: 1rem;
        top: 1.1rem;
        width: 16px;
        height: 16px;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23dc3545'%3e%3cpath d='M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z'/%3e%3c/svg%3e");
        background-size: cover;
    }

    .custom-list.applications li::before {
        content: '';
        position: absolute;
        left: 1rem;
        top: 1rem;
        width: 18px;
        height: 18px;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%231a252f'%3e%3cpath d='M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z'/%3e%3cpath d='M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z'/%3e%3c/svg%3e");
        background-size: cover;
    }
</style>

<!-- Product Section -->
<div class="container py-5">

    <!-- Breadcrumb (Optional but good for UX) -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="index.php" class="text-decoration-none text-danger">Home</a>
            </li>

            <?php foreach ($breadcrumbPath as $item): ?>
                <li class="breadcrumb-item">
                    <a href="category.php?slug=<?= $item['slug']; ?>" class="text-decoration-none text-danger">
                        <?= htmlspecialchars($item['name']); ?>
                    </a>
                </li>
            <?php endforeach; ?>

            <li class="breadcrumb-item active" aria-current="page">
                <?= htmlspecialchars($product['name']); ?>
            </li>
        </ol>
    </nav>

    <div class="row g-5">

        <!-- LEFT SIDE: MAIN IMAGE & GALLERY -->
        <div class="col-lg-6">
            
            <!-- AMAZON STYLE GALLERY -->
            <div class="amazon-gallery">

                <!-- THUMBNAILS -->
                <div class="amazon-gallery-thumbnails">
                    
                    <?php if ($product['main_image']): ?>
                        <img src="uploads/products/<?= $product['main_image']; ?>"
                            class="amazon-thumbnail active"
                            onmouseover="changeMainImage(this)"
                            onclick="changeMainImage(this)"
                            alt="Main Thumbnail">
                    <?php endif; ?>

                    <!-- Loop through extra gallery images -->
                    <?php if ($gallery->num_rows > 0): ?>
                        <?php while ($img = $gallery->fetch_assoc()): ?>
                            <img src="uploads/products/<?= $img['image']; ?>"
                                class="amazon-thumbnail"
                                onmouseover="changeMainImage(this)"
                                onclick="changeMainImage(this)"
                                alt="Gallery Thumbnail">
                        <?php endwhile; ?>
                    <?php endif; ?>

                </div>

                <!-- MAIN DISPLAY IMAGE -->
                <div class="amazon-gallery-main">
                    <?php if ($product['main_image']): ?>
                        <img src="uploads/products/<?= $product['main_image']; ?>"
                            id="mainProductImg"
                            alt="<?= htmlspecialchars($product['name']); ?>">
                    <?php else: ?>
                        <!-- Placeholder if no image exists -->
                        <div class="text-muted d-flex align-items-center justify-content-center w-100" style="min-height: 350px;">
                            No Image Available
                        </div>
                    <?php endif; ?>
                </div>

            </div>
            <!-- /AMAZON STYLE GALLERY -->

        </div>

        <!-- RIGHT SIDE: DETAILS -->
        <div class="col-lg-6">

            <h1 class="product-title"><?= htmlspecialchars($product['name']); ?></h1>
            <div class="accent-line"></div>

            <div class="mb-4">
                <p class="fs-5 text-secondary" style="line-height: 1.7;">
                    <?= nl2br(htmlspecialchars($product['description'])); ?>
                </p>
            </div>

            <div class="d-flex flex-column flex-sm-row gap-3 mt-4">
                <a href="inquiry.php?product_id=<?= $product['id']; ?>"
                    class="btn btn-danger mt-3 px-4 py-2 fw-semibold">
                    Request Inquiry
                </a>
                <a href="uploads/brochures/placeholder.pdf" target="_blank" class="btn btn-outline-dark mt-3 px-4 py-2 fw-semibold d-inline-flex align-items-center justify-content-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z" />
                        <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z" />
                    </svg>
                    Datasheet
                </a>
            </div>

            <!-- FEATURES & APPLICATIONS TABS -->
            <div class="mt-5">

                <!-- FEATURES -->
                <?php if ($features->num_rows > 0): ?>
                    <h4 class="fw-bold mb-3" style="color: var(--industrial-dark);">Product Features</h4>
                    <ul class="custom-list features mb-4">
                        <?php while ($feat = $features->fetch_assoc()): ?>
                            <li>
                                <?= htmlspecialchars($feat['feature_text']); ?>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php endif; ?>

                <!-- APPLICATIONS -->
                <?php if ($applications->num_rows > 0): ?>
                    <h4 class="fw-bold mb-3 mt-4" style="color: var(--industrial-dark);">Applications</h4>
                    <ul class="custom-list applications">
                        <?php while ($app = $applications->fetch_assoc()): ?>
                            <li>
                                <?= htmlspecialchars($app['application_text']); ?>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php endif; ?>

            </div>

        </div>

    </div>
</div>

<?php if ($relatedProducts->num_rows > 0): ?>

    <div class="container mb-5">
        <hr class="mb-5">
        <h4 class="fw-bold mb-4" style="color: var(--industrial-dark);">Related Products</h4>

        <div class="row g-4">

            <?php while ($rel = $relatedProducts->fetch_assoc()): ?>

                <div class="col-md-6 col-lg-3">
                    <div class="card shadow-sm h-100 border-0" style="transition: transform 0.3s; cursor: pointer;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">

                        <?php if (!empty($rel['main_image'])): ?>
                            <img src="uploads/products/<?= $rel['main_image']; ?>"
                                class="card-img-top p-3 bg-white"
                                style="height:180px; object-fit:contain;">
                        <?php endif; ?>

                        <div class="card-body text-center bg-light rounded-bottom">
                            <h6 class="fw-bold mb-3" style="color: var(--industrial-dark);"><?= htmlspecialchars($rel['name']); ?></h6>
                            <a href="product.php?slug=<?= $rel['slug']; ?>" class="btn btn-outline-danger btn-sm w-100 fw-semibold">
                                View Product
                            </a>
                        </div>

                    </div>
                </div>

            <?php endwhile; ?>

        </div>
    </div>

<?php endif; ?>

<!-- JAVASCRIPT FOR AMAZON GALLERY HOVER EFFECT -->
<script>
    function changeMainImage(element) {
        // Change the main image source
        document.getElementById('mainProductImg').src = element.src;
        
        // Remove active red border from all thumbnails
        let thumbnails = document.querySelectorAll('.amazon-thumbnail');
        thumbnails.forEach(thumb => thumb.classList.remove('active'));
        
        // Add active red border to hovered/clicked thumbnail
        element.classList.add('active');
    }
</script>

<?php include "includes/footer.php"; ?>