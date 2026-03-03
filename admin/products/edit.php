<?php
require_once "../includes/auth.php";
require_once "../../config/database.php";

function generateSlug($string)
{
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
}

function categoryDropdown($parent_id = NULL, $level = 0, $selected_id = NULL)
{
    global $conn;

    if ($parent_id === NULL) {
        $stmt = $conn->prepare("SELECT * FROM categories WHERE parent_id IS NULL ORDER BY name ASC");
    } else {
        $stmt = $conn->prepare("SELECT * FROM categories WHERE parent_id = ? ORDER BY name ASC");
        $stmt->bind_param("i", $parent_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {

        $selected = ($row['id'] == $selected_id) ? "selected" : "";

        echo "<option value='{$row['id']}' $selected>";
        echo str_repeat("— ", $level) . htmlspecialchars($row['name']);
        echo "</option>";

        categoryDropdown($row['id'], $level + 1, $selected_id);
    }
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    die("Product not found");
}

/* FETCH EXISTING DATA */
$features = $conn->query("SELECT * FROM product_features WHERE product_id = $id");
$applications = $conn->query("SELECT * FROM product_applications WHERE product_id = $id");
$gallery = $conn->query("SELECT * FROM product_images WHERE product_id = $id");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name']);
    $category_id = intval($_POST['category_id']);
    $short_description = trim($_POST['short_description']);
    $description = trim($_POST['description']);
    $slug = generateSlug($name);

    $imageName = $product['main_image'];

    /* MAIN IMAGE REPLACE */
    if (!empty($_FILES['main_image']['name'])) {

        $uploadDir = "../../uploads/products/";
        $tmp = $_FILES['main_image']['tmp_name'];
        $ext = pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION);
        $newImage = time() . "_" . rand(1000, 9999) . "." . $ext;

        move_uploaded_file($tmp, $uploadDir . $newImage);

        if ($imageName && file_exists($uploadDir . $imageName)) {
            unlink($uploadDir . $imageName);
        }

        $imageName = $newImage;
    }

    /* UPDATE PRODUCT */
    $stmt = $conn->prepare("UPDATE products 
        SET category_id=?, name=?, slug=?, short_description=?, description=?, main_image=? 
        WHERE id=?");

    $stmt->bind_param(
        "isssssi",
        $category_id,
        $name,
        $slug,
        $short_description,
        $description,
        $imageName,
        $id
    );

    $stmt->execute();

    /* DELETE OLD FEATURES */
    $conn->query("DELETE FROM product_features WHERE product_id = $id");

    if (!empty($_POST['features'])) {
        foreach ($_POST['features'] as $feature) {
            if (!empty(trim($feature))) {
                $stmtF = $conn->prepare(
                    "INSERT INTO product_features (product_id, feature_text, created_at) VALUES (?, ?, NOW())"
                );
                $stmtF->bind_param("is", $id, $feature);
                $stmtF->execute();
            }
        }
    }

    /* DELETE OLD APPLICATIONS */
    $conn->query("DELETE FROM product_applications WHERE product_id = $id");

    if (!empty($_POST['applications'])) {
        foreach ($_POST['applications'] as $app) {
            if (!empty(trim($app))) {
                $stmtA = $conn->prepare(
                    "INSERT INTO product_applications (product_id, application_text, created_at) VALUES (?, ?, NOW())"
                );
                $stmtA->bind_param("is", $id, $app);
                $stmtA->execute();
            }
        }
    }

    /* ADD NEW GALLERY IMAGES */
    if (!empty($_FILES['gallery_images']['name'][0])) {

        $uploadDir = "../../uploads/products/";

        foreach ($_FILES['gallery_images']['tmp_name'] as $key => $tmpName) {

            if (!empty($tmpName)) {

                $ext = pathinfo($_FILES['gallery_images']['name'][$key], PATHINFO_EXTENSION);
                $galleryName = time() . "_" . rand(1000, 9999) . "." . $ext;

                move_uploaded_file($tmpName, $uploadDir . $galleryName);

                $stmtImg = $conn->prepare(
                    "INSERT INTO product_images (product_id, image, created_at) VALUES (?, ?, NOW())"
                );
                $stmtImg->bind_param("is", $id, $galleryName);
                $stmtImg->execute();
            }
        }
    }

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product | RH Enterprise Admin</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --admin-dark: #1a252f;
            --admin-accent: #dc3545;
            --admin-bg: #f4f6f9;
            --admin-darker: #11181f;
        }

        body {
            background-color: var(--admin-bg);
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            overflow-x: hidden;
        }

        /* Sidebar Placeholder Styles (Matches included sidebar) */
        .sidebar {
            background-color: var(--admin-dark);
            min-height: 100vh;
            width: 260px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
        }

        .sidebar-brand {
            background-color: var(--admin-darker);
            padding: 1.5rem 1rem;
            color: white;
            font-weight: 800;
            font-size: 1.25rem;
            text-align: center;
            border-bottom: 3px solid var(--admin-accent);
            margin-bottom: 1.5rem;
        }

        .sidebar-brand span {
            color: var(--admin-accent);
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.7);
            padding: 0.8rem 1.5rem;
            border-left: 4px solid transparent;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .nav-link:hover,
        .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.05);
            border-left-color: var(--admin-accent);
        }

        .nav-link svg {
            opacity: 0.7;
        }

        .nav-link:hover svg,
        .nav-link.active svg {
            opacity: 1;
        }


        /* Main Content Layout */
        .main-content {
            margin-left: 260px;
            padding: 2rem;
            width: calc(100% - 260px);
        }

        /* Card & Layout Styling */
        .admin-card {
            background: #ffffff;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .admin-card-body {
            padding: 2.5rem;
        }

        .accent-line {
            height: 4px;
            width: 50px;
            background-color: var(--admin-accent);
            border-radius: 2px;
            margin-top: 0.5rem;
        }

        /* Form Controls */
        .form-label {
            font-weight: 600;
            color: var(--admin-dark);
            margin-bottom: 0.5rem;
        }

        .form-control,
        .form-select {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border: 1px solid #ced4da;
            transition: all 0.2s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--admin-accent);
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
        }

        /* Dynamic Field Styling */
        .dynamic-field-btn {
            border: 2px dashed #ced4da;
            background: transparent;
            color: #6c757d;
            font-weight: 600;
            border-radius: 8px;
            padding: 0.75rem;
            width: 100%;
            transition: all 0.2s;
        }

        .dynamic-field-btn:hover {
            border-color: var(--admin-accent);
            color: var(--admin-accent);
            background: rgba(220, 53, 69, 0.05);
        }

        /* Image Previews */
        .current-image-preview {
            border: 1px solid #ced4da;
            padding: 0.5rem;
            border-radius: 8px;
            background-color: #f8f9fa;
            display: inline-block;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                min-height: auto;
            }

            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 1rem;
            }

            .admin-card-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>

    <!-- Dynamic Sidebar Included Here -->
    <?php include "../sidebar.php"; ?>

    <!-- MAIN CONTENT WRAPPER -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-xl-9 col-lg-10">

                    <!-- Header & Navigation -->
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                        <div>
                            <h3 class="fw-bold mb-0 text-dark">Edit Product</h3>
                            <div class="accent-line"></div>
                        </div>
                        <a href="index.php" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm rounded-pill px-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z" />
                            </svg>
                            Back to Products
                        </a>
                    </div>

                    <!-- Form Card -->
                    <div class="card admin-card">
                        <div class="admin-card-body">
                            <form method="POST" enctype="multipart/form-data">

                                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">Basic Information</h5>

                                <div class="row mb-4">
                                    <div class="col-md-7 mb-3 mb-md-0">
                                        <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control bg-light" value="<?= htmlspecialchars($product['name']); ?>" required>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label">Category <span class="text-danger">*</span></label>
                                        <select name="category_id" class="form-select bg-light" required>
                                            <option value="">-- Select Category --</option>
                                            <?php categoryDropdown(NULL, 0, $product['category_id']); ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Short Description</label>
                                    <textarea name="short_description" class="form-control bg-light" rows="2"><?= htmlspecialchars($product['short_description']); ?></textarea>
                                </div>

                                <div class="mb-5">
                                    <label class="form-label">Full Description</label>
                                    <textarea name="description" class="form-control bg-light" rows="5"><?= htmlspecialchars($product['description']); ?></textarea>
                                </div>

                                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">Media & Assets</h5>

                                <div class="row mb-4">
                                    <div class="col-md-4 mb-3 mb-md-0">
                                        <label class="form-label d-block">Current Main Image</label>
                                        <div class="current-image-preview text-center w-100">
                                            <?php if ($product['main_image']): ?>
                                                <img src="../../uploads/products/<?= $product['main_image']; ?>" class="img-fluid rounded" style="max-height: 120px; object-fit: contain;" alt="Current Image">
                                            <?php else: ?>
                                                <div class="py-4 text-muted small fw-semibold">No Image Uploaded</div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Replace Main Image</label>
                                        <input type="file" name="main_image" class="form-control bg-light" accept=".jpg,.jpeg,.png,.webp">
                                        <div class="form-text mt-2">Uploading a new image will replace the current one.</div>
                                    </div>
                                </div>

                                <div class="mb-5">
                                    <label class="form-label d-block">Product Gallery</label>
                                    <?php if ($gallery->num_rows > 0): ?>
                                        <div class="d-flex flex-wrap gap-2 mb-3 p-3 bg-light border rounded">
                                            <?php while ($img = $gallery->fetch_assoc()): ?>
                                                <img src="../../uploads/products/<?= $img['image']; ?>" class="rounded shadow-sm" style="width: 70px; height: 70px; object-fit: cover; border: 1px solid #dee2e6;" alt="Gallery Image">
                                            <?php endwhile; ?>
                                        </div>
                                    <?php endif; ?>
                                    <label class="form-label small text-muted">Add New Images to Gallery</label>
                                    <input type="file" name="gallery_images[]" multiple class="form-control bg-light" accept=".jpg,.jpeg,.png,.webp">
                                    <div class="form-text">Select multiple files to append them to the existing gallery.</div>
                                </div>

                                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">Technical Details</h5>

                                <div class="row mb-5">
                                    <div class="col-md-6 mb-4 mb-md-0">
                                        <label class="form-label">Product Features</label>
                                        <div id="feature-wrapper">
                                            <?php if ($features->num_rows > 0): ?>
                                                <?php while ($feat = $features->fetch_assoc()): ?>
                                                    <input type="text" name="features[]" class="form-control bg-light mb-2" value="<?= htmlspecialchars($feat['feature_text']); ?>">
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <input type="text" name="features[]" class="form-control bg-light mb-2" placeholder="e.g. High torque output">
                                            <?php endif; ?>
                                        </div>
                                        <button type="button" class="dynamic-field-btn mt-2" onclick="addFeature()">
                                            + Add Another Feature
                                        </button>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Product Applications</label>
                                        <div id="application-wrapper">
                                            <?php if ($applications->num_rows > 0): ?>
                                                <?php while ($app = $applications->fetch_assoc()): ?>
                                                    <input type="text" name="applications[]" class="form-control bg-light mb-2" value="<?= htmlspecialchars($app['application_text']); ?>">
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <input type="text" name="applications[]" class="form-control bg-light mb-2" placeholder="e.g. CNC Machinery">
                                            <?php endif; ?>
                                        </div>
                                        <button type="button" class="dynamic-field-btn mt-2" onclick="addApplication()">
                                            + Add Another Application
                                        </button>
                                    </div>
                                </div>

                                <hr class="border-secondary opacity-25 mb-4">

                                <div class="d-flex justify-content-end gap-3">
                                    <a href="index.php" class="btn btn-light px-4 fw-semibold border shadow-sm">Cancel</a>
                                    <button type="submit" class="btn btn-danger px-5 fw-semibold shadow-sm d-inline-flex align-items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z" />
                                        </svg>
                                        Update Product
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        // Dynamic inputs with styling matching the theme
        function addFeature() {
            let wrapper = document.getElementById('feature-wrapper');
            let input = document.createElement('input');
            input.type = 'text';
            input.name = 'features[]';
            input.className = 'form-control bg-light mb-2';
            input.placeholder = 'Enter new feature...';
            wrapper.appendChild(input);
        }

        function addApplication() {
            let wrapper = document.getElementById('application-wrapper');
            let input = document.createElement('input');
            input.type = 'text';
            input.name = 'applications[]';
            input.className = 'form-control bg-light mb-2';
            input.placeholder = 'Enter new application...';
            wrapper.appendChild(input);
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>