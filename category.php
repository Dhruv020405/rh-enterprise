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
    header("Location: product.php?slug=" . $singleProduct['slug']);
    exit();
}

include "includes/header.php";
include "includes/navbar.php";
?>

<div class="container mt-5">

    <h2 class="mb-4"><?= htmlspecialchars($category['name']); ?></h2>

    <div class="row">

        <?php if ($subcategories->num_rows > 0): ?>

            <!-- SHOW SUBCATEGORIES -->

            <?php while($row = $subcategories->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100">

                        <?php if($row['image']): ?>
                            <img src="uploads/categories/<?= $row['image']; ?>"
                                 class="card-img-top"
                                 style="height:200px; object-fit:cover;">
                        <?php endif; ?>

                        <div class="card-body text-center">
                            <h5><?= htmlspecialchars($row['name']); ?></h5>

                            <a href="category.php?slug=<?= $row['slug']; ?>"
                               class="btn btn-danger btn-sm mt-2">
                               View Category
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>

        <?php elseif ($products->num_rows > 1): ?>

            <!-- SHOW PRODUCT LIST -->

            <?php while($product = $products->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100">

                        <?php if($product['main_image']): ?>
                            <img src="uploads/products/<?= $product['main_image']; ?>"
                                 class="card-img-top"
                                 style="height:200px; object-fit:cover;">
                        <?php endif; ?>

                        <div class="card-body text-center">
                            <h5><?= htmlspecialchars($product['name']); ?></h5>

                            <a href="product.php?slug=<?= $product['slug']; ?>"
                               class="btn btn-danger btn-sm mt-2">
                               View Product
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>

        <?php else: ?>

            <!-- NOTHING FOUND -->

            <div class="col-12">
                <div class="alert alert-warning text-center">
                    No subcategories or products found.
                </div>
            </div>

        <?php endif; ?>

    </div>

</div>

<?php include "includes/footer.php"; ?>