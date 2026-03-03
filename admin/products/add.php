<?php
require_once "../includes/auth.php";
require_once "../../config/database.php";

/* -------------------------
   SLUG FUNCTION
------------------------- */
function generateSlug($string)
{
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
}

/* -------------------------
   CATEGORY DROPDOWN
------------------------- */
function categoryDropdown($parent_id = NULL, $level = 0)
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
        echo "<option value='{$row['id']}'>";
        echo str_repeat("— ", $level) . htmlspecialchars($row['name']);
        echo "</option>";

        categoryDropdown($row['id'], $level + 1);
    }
}

/* -------------------------
   FORM SUBMIT
------------------------- */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name']);
    $category_id = intval($_POST['category_id']);
    $short_description = trim($_POST['short_description']);
    $description = trim($_POST['description']);
    $slug = generateSlug($name);

    $imageName = NULL;

    /* MAIN IMAGE UPLOAD */
    if (!empty($_FILES['main_image']['name'])) {

        $uploadDir = "../../uploads/products/";
        $tmp = $_FILES['main_image']['tmp_name'];
        $ext = pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION);
        $imageName = time() . "_" . rand(1000, 9999) . "." . $ext;

        move_uploaded_file($tmp, $uploadDir . $imageName);
    }

    /* INSERT PRODUCT */
    $stmt = $conn->prepare("INSERT INTO products 
        (category_id, name, slug, short_description, description, main_image, status, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, 1, NOW())");

    $stmt->bind_param(
        "isssss",
        $category_id,
        $name,
        $slug,
        $short_description,
        $description,
        $imageName
    );

    $stmt->execute();
    $product_id = $stmt->insert_id;

    /* -------------------------
       INSERT FEATURES
    ------------------------- */
    if (!empty($_POST['features'])) {
        foreach ($_POST['features'] as $feature) {

            $feature = trim($feature);
            if (!empty($feature)) {

                $stmtFeat = $conn->prepare(
                    "INSERT INTO product_features (product_id, feature_text, created_at) 
                     VALUES (?, ?, NOW())"
                );

                $stmtFeat->bind_param("is", $product_id, $feature);
                $stmtFeat->execute();
            }
        }
    }

    /* -------------------------
       INSERT APPLICATIONS
    ------------------------- */
    if (!empty($_POST['applications'])) {
        foreach ($_POST['applications'] as $app) {

            $app = trim($app);
            if (!empty($app)) {

                $stmtApp = $conn->prepare(
                    "INSERT INTO product_applications (product_id, application_text, created_at) 
                     VALUES (?, ?, NOW())"
                );

                $stmtApp->bind_param("is", $product_id, $app);
                $stmtApp->execute();
            }
        }
    }

    /* -------------------------
       INSERT GALLERY IMAGES
    ------------------------- */
    if (!empty($_FILES['gallery_images']['name'][0])) {

        $uploadDir = "../../uploads/products/";

        foreach ($_FILES['gallery_images']['tmp_name'] as $key => $tmpName) {

            if (!empty($tmpName)) {

                $ext = pathinfo($_FILES['gallery_images']['name'][$key], PATHINFO_EXTENSION);
                $galleryName = time() . "_" . rand(1000, 9999) . "." . $ext;

                move_uploaded_file($tmpName, $uploadDir . $galleryName);

                $stmtImg = $conn->prepare(
                    "INSERT INTO product_images (product_id, image, created_at) 
                     VALUES (?, ?, NOW())"
                );

                $stmtImg->bind_param("is", $product_id, $galleryName);
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
    <title>Add Product | RH Enterprise Admin</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --admin-dark: #1a252f;
            --admin-accent: #dc3545;
            --admin-bg: #f4f6f9;
        }

        body {
            background-color: var(--admin-bg);
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
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
    </style>
</head>

<body>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-xl-9 col-lg-10">

                <!-- Header & Navigation -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="fw-bold mb-0 text-dark">Add New Product</h3>
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
                                    <input type="text" name="name" class="form-control bg-light" placeholder="e.g. Heavy Duty Servo Motor" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">Category <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-select bg-light" required>
                                        <option value="">-- Select Category --</option>
                                        <?php categoryDropdown(); ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Short Description</label>
                                <textarea name="short_description" class="form-control bg-light" rows="2" placeholder="Brief summary of the product..."></textarea>
                            </div>

                            <div class="mb-5">
                                <label class="form-label">Full Description</label>
                                <textarea name="description" class="form-control bg-light" rows="5" placeholder="Detailed product specifications, build quality, and operational benefits..."></textarea>
                            </div>

                            <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">Media & Assets</h5>

                            <div class="row mb-5">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="form-label">Main Product Image</label>
                                    <input type="file" name="main_image" class="form-control bg-light" accept=".jpg,.jpeg,.png,.webp">
                                    <div class="form-text">This will be the primary image shown in catalogs.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Gallery Images (Multiple)</label>
                                    <input type="file" name="gallery_images[]" class="form-control bg-light" accept=".jpg,.jpeg,.png,.webp" multiple>
                                    <div class="form-text">Select multiple files for the product image slider.</div>
                                </div>
                            </div>

                            <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">Technical Details</h5>

                            <div class="row mb-5">
                                <div class="col-md-6 mb-4 mb-md-0">
                                    <label class="form-label">Product Features</label>
                                    <div id="feature-wrapper">
                                        <input type="text" name="features[]" class="form-control bg-light mb-2" placeholder="e.g. High torque output">
                                    </div>
                                    <button type="button" class="dynamic-field-btn mt-2" onclick="addFeature()">
                                        + Add Another Feature
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Product Applications</label>
                                    <div id="application-wrapper">
                                        <input type="text" name="applications[]" class="form-control bg-light mb-2" placeholder="e.g. CNC Machinery">
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
                                        <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576 6.636 10.07Zm6.787-8.201L1.591 6.602l4.339 2.76 7.494-7.493Z" />
                                    </svg>
                                    Save Product
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        // Improved dynamic input injection matching the theme
        function addFeature() {
            let wrapper = document.getElementById('feature-wrapper');
            let input = document.createElement('input');
            input.type = 'text';
            input.name = 'features[]';
            input.className = 'form-control bg-light mb-2';
            input.placeholder = 'Enter feature...';
            wrapper.appendChild(input);
        }

        function addApplication() {
            let wrapper = document.getElementById('application-wrapper');
            let input = document.createElement('input');
            input.type = 'text';
            input.name = 'applications[]';
            input.className = 'form-control bg-light mb-2';
            input.placeholder = 'Enter application...';
            wrapper.appendChild(input);
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>