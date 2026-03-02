<?php
require_once "../includes/auth.php";
require_once "../../config/database.php";

function generateSlug($string) {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
}

function categoryDropdown($parent_id = NULL, $level = 0, $selected_id = NULL) {
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
        $newImage = time() . "_" . rand(1000,9999) . "." . $ext;

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
                $galleryName = time() . "_" . rand(1000,9999) . "." . $ext;

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
<html>
<head>
    <title>Edit Product</title>
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
<div class="card shadow p-4">
<h4>Edit Product</h4>

<form method="POST" enctype="multipart/form-data">

<input type="text" name="name" class="form-control mb-3"
value="<?= htmlspecialchars($product['name']); ?>" required>

<select name="category_id" class="form-control mb-3" required>
<?php categoryDropdown(NULL, 0, $product['category_id']); ?>
</select>

<textarea name="short_description" class="form-control mb-3"><?= htmlspecialchars($product['short_description']); ?></textarea>

<textarea name="description" class="form-control mb-3"><?= htmlspecialchars($product['description']); ?></textarea>

<div class="mb-3">
<label>Current Main Image</label><br>
<?php if($product['main_image']): ?>
<img src="../../uploads/products/<?= $product['main_image']; ?>" width="120">
<?php endif; ?>
</div>

<input type="file" name="main_image" class="form-control mb-3">

<h5>Gallery Images</h5>
<div class="mb-3">
<?php while($img = $gallery->fetch_assoc()): ?>
<img src="../../uploads/products/<?= $img['image']; ?>" width="80" class="me-2 mb-2">
<?php endwhile; ?>
</div>

<input type="file" name="gallery_images[]" multiple class="form-control mb-3">

<h5>Features</h5>
<div id="feature-wrapper">
<?php while($feat = $features->fetch_assoc()): ?>
<input type="text" name="features[]" class="form-control mb-2"
value="<?= htmlspecialchars($feat['feature_text']); ?>">
<?php endwhile; ?>
</div>
<button type="button" onclick="addFeature()" class="btn btn-sm btn-secondary mb-3">+ Add Feature</button>

<h5>Applications</h5>
<div id="application-wrapper">
<?php while($app = $applications->fetch_assoc()): ?>
<input type="text" name="applications[]" class="form-control mb-2"
value="<?= htmlspecialchars($app['application_text']); ?>">
<?php endwhile; ?>
</div>
<button type="button" onclick="addApplication()" class="btn btn-sm btn-secondary mb-3">+ Add Application</button>

<br><br>
<button class="btn btn-danger">Update Product</button>

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