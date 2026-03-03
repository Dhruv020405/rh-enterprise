<?php
require_once "config/database.php";

/* ============================
   GET CATEGORY PATH FUNCTION
============================ */
function getCategoryPath($category_id)
{
    global $conn;

    $path = [];

    while ($category_id != NULL) {

        $stmt = $conn->prepare("
            SELECT id, parent_id, name, slug 
            FROM categories 
            WHERE id = ?
        ");
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

/* ============================
   GET SLUG
============================ */
$slug = $_GET['slug'] ?? '';

if (!$slug) {
    die("Category not found.");
}

/* ============================
   FETCH CATEGORY
============================ */
$stmt = $conn->prepare("
    SELECT * FROM categories 
    WHERE slug=? AND status=1
");
$stmt->bind_param("s", $slug);
$stmt->execute();
$category = $stmt->get_result()->fetch_assoc();

if (!$category) {
    die("Invalid category.");
}

$category_id = $category['id'];

/* ============================
   BREADCRUMB PATH
============================ */
$breadcrumbPath = getCategoryPath($category_id);

/* ============================
   FETCH SUBCATEGORIES
============================ */
$stmtSub = $conn->prepare("
    SELECT * FROM categories 
    WHERE parent_id=? AND status=1 
    ORDER BY sort_order ASC, name ASC
");
$stmtSub->bind_param("i", $category_id);
$stmtSub->execute();
$subcategories = $stmtSub->get_result();

/* ============================
   FETCH PRODUCTS
============================ */
$stmtProd = $conn->prepare("
    SELECT * FROM products 
    WHERE category_id=? AND status=1 
    ORDER BY sort_order ASC, name ASC
");
$stmtProd->bind_param("i", $category_id);
$stmtProd->execute();
$products = $stmtProd->get_result();

/* ============================
   SMART REDIRECT
============================ */
if ($subcategories->num_rows == 0 && $products->num_rows == 1) {
    $singleProduct = $products->fetch_assoc();
    header("Location: product.php?slug=" . urlencode($singleProduct['slug']));
    exit();
}

include "includes/header.php";
include "includes/navbar.php";
?>

<style>
    :root {
        --industrial-dark: #1a252f;
        --industrial-accent: #dc3545;
        --industrial-light: #f8f9fa;
    }

    body {
        background-color: var(--industrial-light);
    }

    .category-hero {
        background: linear-gradient(135deg, var(--industrial-dark), #2c3e50);
        color: #fff;
        padding: 3.5rem 0;
        margin-bottom: 3rem;
    }

    .accent-line {
        height: 4px;
        width: 60px;
        background: var(--industrial-accent);
        margin-top: 8px;
    }

    .hover-card {
        border: none;
        border-radius: 12px;
        transition: .3s;
    }

    .hover-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 15px 25px rgba(0, 0, 0, .1);
    }

    .card-img-wrapper {
        height: 220px;
        overflow: hidden;
    }

    .card-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>

<!-- HERO -->
<div class="category-hero">
    <div class="container">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">

                <li class="breadcrumb-item">
                    <a href="index.php" class="text-white-50 text-decoration-none">
                        Home
                    </a>
                </li>

                <?php foreach ($breadcrumbPath as $item): ?>
                    <li class="breadcrumb-item <?= ($item['id'] == $category_id) ? 'active text-white' : '' ?>">

                        <?php if ($item['id'] == $category_id): ?>
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

        <h1 class="fw-bold"><?= htmlspecialchars($category['name']); ?></h1>
        <div class="accent-line"></div>

        <?php if (!empty($category['description'])): ?>
            <p class="mt-3 text-white-50">
                <?= htmlspecialchars($category['description']); ?>
            </p>
        <?php endif; ?>

    </div>
</div>

<!-- CONTENT -->
<div class="container pb-5">
    <div class="row g-4">

        <!-- SUBCATEGORIES -->
        <?php if ($subcategories->num_rows > 0): ?>

            <div class="col-12">
                <h4 class="fw-bold text-secondary">Browse Subcategories</h4>
            </div>

            <?php while ($row = $subcategories->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card hover-card h-100">

                        <div class="card-img-wrapper">
                            <?php if ($row['image']): ?>
                                <img src="uploads/categories/<?= $row['image']; ?>">
                            <?php endif; ?>
                        </div>

                        <div class="card-body text-center">
                            <h5><?= htmlspecialchars($row['name']); ?></h5>
                            <a href="category.php?slug=<?= urlencode($row['slug']); ?>"
                                class="btn btn-outline-danger w-100">
                                View Category
                            </a>
                        </div>

                    </div>
                </div>
            <?php endwhile; ?>

        <?php endif; ?>


        <!-- PRODUCTS -->
        <?php if ($products->num_rows > 0): ?>

            <div class="col-12 mt-5">
                <h4 class="fw-bold text-secondary">Available Products</h4>
            </div>

            <?php while ($product = $products->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card hover-card h-100">

                        <div class="card-img-wrapper">
                            <?php if ($product['main_image']): ?>
                                <img src="uploads/products/<?= $product['main_image']; ?>">
                            <?php endif; ?>
                        </div>

                        <div class="card-body text-center">
                            <h5><?= htmlspecialchars($product['name']); ?></h5>
                            <a href="product.php?slug=<?= urlencode($product['slug']); ?>"
                                class="btn btn-danger w-100">
                                View Product
                            </a>
                        </div>

                    </div>
                </div>
            <?php endwhile; ?>

        <?php endif; ?>


        <!-- NOTHING FOUND -->
        <?php if ($subcategories->num_rows == 0 && $products->num_rows == 0): ?>

            <div class="col-12 text-center py-5">
                <h4>No Items Found</h4>
                <p class="text-muted">
                    There are no subcategories or products under this category.
                </p>
            </div>

        <?php endif; ?>

    </div>
</div>

<?php include "includes/footer.php"; ?>