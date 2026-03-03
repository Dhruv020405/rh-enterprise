<?php
require_once "config/database.php";

$slug = $_GET['slug'] ?? '';

if (!$slug) {
    die("Category not found.");
}

/* Get Current Category */
$stmt = $conn->prepare("SELECT * FROM categories WHERE slug=? AND status=1");
$stmt->bind_param("s", $slug);
$stmt->execute();
$category = $stmt->get_result()->fetch_assoc();
$breadcrumbPath = getCategoryPath($category['id']);

if (!$category) {
    die("Invalid category.");
}

$category_id = $category['id'];

/* Check for Subcategories */
$stmtSub = $conn->prepare("SELECT * FROM categories WHERE parent_id=? AND status=1 ORDER BY name ASC");
$stmtSub->bind_param("i", $category_id);
$stmtSub->execute();
$subcategories = $stmtSub->get_result();

/* Check for Products */
$stmtProd = $conn->prepare("SELECT * FROM products WHERE category_id=? AND status=1 ORDER BY name ASC");
$stmtProd->bind_param("i", $category_id);
$stmtProd->execute();
$products = $stmtProd->get_result();

/* ==============================
   SMART REDIRECT LOGIC
============================== */

if ($subcategories->num_rows == 0 && $products->num_rows == 1) {
    $singleProduct = $products->fetch_assoc();
    header("Location: product.php?slug=" . urlencode($singleProduct['slug']));
    exit();
}
//path 
function getCategoryPath($category_id) {
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
include "includes/header.php";
include "includes/navbar.php";
?>

<style>
    /* Industrial Theme Styles for Category Page */
    :root {
        --industrial-dark: #1a252f;
        --industrial-accent: #dc3545;
        --industrial-light: #f8f9fa;
    }

    body {
        background-color: var(--industrial-light);
    }

    /* Category Hero Section */
    .category-hero {
        background: linear-gradient(135deg, var(--industrial-dark) 0%, #2c3e50 100%);
        color: #ffffff;
        padding: 3.5rem 0;
        margin-bottom: 3rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .accent-line {
        height: 4px;
        width: 60px;
        background-color: var(--industrial-accent);
        border-radius: 2px;
        margin-top: 0.5rem;
    }

    /* Premium Hover Cards */
    .hover-card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        background-color: #ffffff;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 4px 6px rgba(0,0,0,0.04);
    }

    .hover-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }

    .card-img-wrapper {
        position: relative;
        overflow: hidden;
        background-color: #e9ecef;
        height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .card-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    /* Smooth Image Zoom on Card Hover */
    .hover-card:hover .card-img-wrapper img {
        transform: scale(1.05);
    }

    .card-title-text {
        color: var(--industrial-dark);
        font-weight: 700;
        font-size: 1.25rem;
    }
</style>

<!-- HERO SECTION -->
<div class="category-hero">
    <div class="container">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb mb-0">

        <li class="breadcrumb-item">
            <a href="index.php" class="text-white-50 text-decoration-none">Home</a>
        </li>

        <?php foreach ($breadcrumbPath as $item): ?>
            <li class="breadcrumb-item <?= ($item['id'] == $category['id']) ? 'active text-white' : '' ?>">

                <?php if ($item['id'] == $category['id']): ?>
                    <?= htmlspecialchars($item['name']); ?>
                <?php else: ?>
                    <a href="category.php?slug=<?= $item['slug']; ?>" 
                       class="text-white-50 text-decoration-none">
                        <?= htmlspecialchars($item['name']); ?>
                    </a>
                <?php endif; ?>

            </li>
        <?php endforeach; ?>

    </ol>
</nav>
        
        <h1 class="display-5 fw-bold mb-0"><?= htmlspecialchars($category['name']); ?></h1>
        <div class="accent-line"></div>
        
        <?php if(!empty($category['description'])): ?>
            <p class="mt-3 text-white-50 fs-5 max-w-75">
                <?= htmlspecialchars($category['description']); ?>
            </p>
        <?php endif; ?>
    </div>
</div>

<!-- MAIN CONTENT GRID -->
<div class="container pb-5">

    <div class="row g-4">

        <?php if ($subcategories->num_rows > 0): ?>

            <!-- SHOW SUBCATEGORIES -->
            <div class="col-12 mb-2">
                <h4 class="fw-bold text-secondary">Browse Subcategories</h4>
            </div>

            <?php while($row = $subcategories->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card hover-card h-100">

                        <div class="card-img-wrapper">
                            <?php if($row['image']): ?>
                                <img src="uploads/categories/<?= $row['image']; ?>" alt="<?= htmlspecialchars($row['name']); ?>">
                            <?php else: ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#adb5bd" viewBox="0 0 16 16"><path d="M4 0h5.293A1 1 0 0 1 10 .293L13.707 4a1 1 0 0 1 .293.707V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2zm5.5 1.5v2a1 1 0 0 0 1 1h2l-3-3zM2.5 14c0 .275.225.5.5.5h9c.275 0 .5-.225.5-.5V5.5H8a2 2 0 0 1-2-2V1.5H3a.5.5 0 0 0-.5.5v12z"/></svg>
                            <?php endif; ?>
                        </div>

                        <div class="card-body text-center p-4 d-flex flex-column justify-content-between">
                            <h5 class="card-title-text mb-3"><?= htmlspecialchars($row['name']); ?></h5>
                            <a href="category.php?slug=<?= urlencode($row['slug']); ?>" class="btn btn-outline-danger w-100 fw-semibold">
                                View Category
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="ms-1 mb-1" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>

        <?php elseif ($products->num_rows > 1): ?>

            <!-- SHOW PRODUCT LIST -->
            <div class="col-12 mb-2">
                <h4 class="fw-bold text-secondary">Available Products</h4>
            </div>

            <?php while($product = $products->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card hover-card h-100">

                        <div class="card-img-wrapper">
                            <?php if($product['main_image']): ?>
                                <img src="uploads/products/<?= $product['main_image']; ?>" alt="<?= htmlspecialchars($product['name']); ?>">
                            <?php else: ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#adb5bd" viewBox="0 0 16 16"><path d="M4 0h5.293A1 1 0 0 1 10 .293L13.707 4a1 1 0 0 1 .293.707V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2zm5.5 1.5v2a1 1 0 0 0 1 1h2l-3-3zM2.5 14c0 .275.225.5.5.5h9c.275 0 .5-.225.5-.5V5.5H8a2 2 0 0 1-2-2V1.5H3a.5.5 0 0 0-.5.5v12z"/></svg>
                            <?php endif; ?>
                        </div>

                        <div class="card-body text-center p-4 d-flex flex-column justify-content-between">
                            <h5 class="card-title-text mb-3"><?= htmlspecialchars($product['name']); ?></h5>
                            <a href="product.php?slug=<?= urlencode($product['slug']); ?>" class="btn btn-danger w-100 fw-semibold">
                                View Product
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="ms-1 mb-1" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>

        <?php else: ?>

            <!-- NOTHING FOUND -->
            <div class="col-12 py-5">
                <div class="text-center bg-white p-5 rounded-4 shadow-sm border">
                    <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" fill="#dc3545" class="mb-3 opacity-75" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/></svg>
                    <h3 class="fw-bold text-dark">No Items Found</h3>
                    <p class="text-muted mb-0">There are currently no subcategories or products listed under this category.</p>
                </div>
            </div>

        <?php endif; ?>

    </div>

</div>

<?php include "includes/footer.php"; ?>