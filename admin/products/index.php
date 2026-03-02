<?php
require_once "../includes/auth.php";
require_once "../../config/database.php";

/* 
   Join products with:
   - Category
   - Count features
   - Count applications
   - Count gallery images
*/

$query = "
SELECT 
    p.*,
    c.name AS category_name,
    (SELECT COUNT(*) FROM product_features pf WHERE pf.product_id = p.id) AS feature_count,
    (SELECT COUNT(*) FROM product_applications pa WHERE pa.product_id = p.id) AS application_count,
    (SELECT COUNT(*) FROM product_images pi WHERE pi.product_id = p.id) AS gallery_count
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
ORDER BY p.id DESC
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Products</title>
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Manage Products</h3>
        <a href="add.php" class="btn btn-danger">+ Add Product</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">

            <table class="table table-bordered table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Main Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Gallery</th>
                        <th>Features</th>
                        <th>Applications</th>
                        <th>Status</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id']; ?></td>

                            <td>
                                <?php if(!empty($row['main_image'])): ?>
                                    <img src="../../uploads/products/<?= $row['main_image']; ?>" 
                                         width="60" 
                                         class="rounded border">
                                <?php else: ?>
                                    <span class="text-muted">No Image</span>
                                <?php endif; ?>
                            </td>

                            <td><?= htmlspecialchars($row['name']); ?></td>

                            <td><?= htmlspecialchars($row['category_name']); ?></td>

                            <td>
                                <span class="badge bg-secondary">
                                    <?= $row['gallery_count']; ?>
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-info text-dark">
                                    <?= $row['feature_count']; ?>
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-warning text-dark">
                                    <?= $row['application_count']; ?>
                                </span>
                            </td>

                            <td>
                                <?php if($row['status']): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inactive</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <a href="edit.php?id=<?= $row['id']; ?>" 
                                   class="btn btn-sm btn-warning mb-1 w-100">
                                   Edit
                                </a>

                                <a href="delete.php?id=<?= $row['id']; ?>" 
                                   class="btn btn-sm btn-danger w-100"
                                   onclick="return confirm('Delete this product?')">
                                   Delete
                                </a>
                            </td>

                        </tr>
                    <?php endwhile; ?>
                </tbody>

            </table>

        </div>
    </div>

</div>

</body>
</html>