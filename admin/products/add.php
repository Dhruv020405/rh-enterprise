<?php
require_once "../includes/auth.php";
require_once "../../config/database.php";

/* -------------------------
   SLUG FUNCTION
------------------------- */
function generateSlug($string) {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
}

/* -------------------------
   CATEGORY DROPDOWN
------------------------- */
function categoryDropdown($parent_id = NULL, $level = 0) {
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
        $imageName = time() . "_" . rand(1000,9999) . "." . $ext;

        move_uploaded_file($tmp, $uploadDir . $imageName);
    }

    /* INSERT PRODUCT */
    $stmt = $conn->prepare("INSERT INTO products 
        (category_id, name, slug, short_description, description, main_image, status, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, 1, NOW())");

    $stmt->bind_param("isssss", 
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
                $galleryName = time() . "_" . rand(1000,9999) . "." . $ext;

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
<html>
<head>
    <title>Add Product</title>
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow p-4">
        <h4 class="mb-4">Add Product</h4>

        <form method="POST" enctype="multipart/form-data">

            <div class="mb-3">
                <label>Product Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Category</label>
                <select name="category_id" class="form-control" required>
                    <option value="">-- Select Category --</option>
                    <?php categoryDropdown(); ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Short Description</label>
                <textarea name="short_description" class="form-control"></textarea>
            </div>

            <div class="mb-3">
                <label>Full Description</label>
                <textarea name="description" class="form-control"></textarea>
            </div>

            <div class="mb-3">
                <label>Main Image</label>
                <input type="file" name="main_image" class="form-control">
            </div>

            <div class="mb-3">
                <label>Gallery Images</label>
                <input type="file" name="gallery_images[]" class="form-control" multiple>
            </div>

            <div class="mb-3">
                <label>Product Features</label>
                <div id="feature-wrapper">
                    <input type="text" name="features[]" class="form-control mb-2">
                </div>
                <button type="button" class="btn btn-sm btn-secondary" onclick="addFeature()">+ Add More</button>
            </div>

            <div class="mb-3">
                <label>Product Applications</label>
                <div id="application-wrapper">
                    <input type="text" name="applications[]" class="form-control mb-2">
                </div>
                <button type="button" class="btn btn-sm btn-secondary" onclick="addApplication()">+ Add More</button>
            </div>

            <button class="btn btn-danger">Add Product</button>

        </form>
    </div>
</div>

<script>
function addFeature() {
    let wrapper = document.getElementById('feature-wrapper');
    let input = document.createElement('input');
    input.type = 'text';
    input.name = 'features[]';
    input.className = 'form-control mb-2';
    wrapper.appendChild(input);
}

function addApplication() {
    let wrapper = document.getElementById('application-wrapper');
    let input = document.createElement('input');
    input.type = 'text';
    input.name = 'applications[]';
    input.className = 'form-control mb-2';
    wrapper.appendChild(input);
}
</script>

</body>
</html>