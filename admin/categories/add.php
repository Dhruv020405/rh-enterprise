<?php
require_once "../includes/auth.php";
require_once "../../config/database.php";

/* -------------------------
   SLUG GENERATOR
------------------------- */
function generateSlug($string) {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
}

/* -------------------------
   RECURSIVE DROPDOWN
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

    if (empty($name)) {
        die("Category name required.");
    }

    $parent_id = !empty($_POST['parent_id']) ? intval($_POST['parent_id']) : NULL;
    $status = isset($_POST['status']) ? 1 : 0;
    $slug = generateSlug($name);

    /* ---- Check Duplicate Slug ---- */
    $check = $conn->prepare("SELECT id FROM categories WHERE slug = ?");
    $check->bind_param("s", $slug);
    $check->execute();
    $exists = $check->get_result()->num_rows;

    if ($exists > 0) {
        $slug = $slug . "-" . time();
    }

    /* -------------------------
       IMAGE UPLOAD (SECURE)
    ------------------------- */
    $imageName = NULL;

    if (!empty($_FILES['image']['name'])) {

        $uploadDir = "../../uploads/categories/";
        $tmp = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];

        $allowed = ['jpg','jpeg','png','webp'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            die("Only JPG, PNG, WEBP allowed.");
        }


        $imageName = time() . "_" . rand(1000,9999) . "." . $ext;

        move_uploaded_file($tmp, $uploadDir . $imageName);
    }

    /* -------------------------
       INSERT QUERY
    ------------------------- */
    $stmt = $conn->prepare("
        INSERT INTO categories 
        (parent_id, name, slug, image, status, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");

    $stmt->bind_param("isssi", 
        $parent_id,
        $name,
        $slug,
        $imageName,
        $status
    );

    if (!$stmt->execute()) {
        die("Insert Error: " . $stmt->error);
    }

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Category</title>
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow p-4">
        <h4 class="mb-4">Add Category</h4>

        <form method="POST" enctype="multipart/form-data">

            <div class="mb-3">
                <label>Category Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Parent Category</label>
                <select name="parent_id" class="form-control">
                    <option value="">-- Main Category --</option>
                    <?php categoryDropdown(); ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Category Image</label>
                <input type="file" name="image" class="form-control">
            </div>

            <div class="mb-3">
                <label>Status</label><br>
                <input type="checkbox" name="status" checked> Active
            </div>

            <button class="btn btn-danger">Add Category</button>

        </form>
    </div>
</div>

</body>
</html>