<?php
require_once "../includes/auth.php";
require_once "../../config/database.php";

function generateSlug($string) {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
}

/* -------- Recursive Dropdown -------- */
function categoryDropdown($parent_id = NULL, $level = 0, $selected_id = NULL, $current_id = NULL) {
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

        // Prevent self-parent
        if ($row['id'] == $current_id) {
            continue;
        }

        $selected = ($row['id'] == $selected_id) ? "selected" : "";

        echo "<option value='{$row['id']}' $selected>";
        echo str_repeat("— ", $level) . htmlspecialchars($row['name']);
        echo "</option>";

        categoryDropdown($row['id'], $level + 1, $selected_id, $current_id);
    }
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$category = $stmt->get_result()->fetch_assoc();

if (!$category) {
    die("Category not found!");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name']);
    $parent_id = !empty($_POST['parent_id']) ? intval($_POST['parent_id']) : NULL;
    $status = isset($_POST['status']) ? 1 : 0;
    $slug = generateSlug($name);

    /* ---- Duplicate Slug Check ---- */
    $check = $conn->prepare("SELECT id FROM categories WHERE slug = ? AND id != ?");
    $check->bind_param("si", $slug, $id);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $slug = $slug . "-" . time();
    }

    /* ---- Image Upload ---- */
    $imageName = $category['image'];

    if (!empty($_FILES['image']['name'])) {

        $uploadDir = "../../uploads/categories/";
        $tmp = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileSize = $_FILES['image']['size'];

        $allowed = ['jpg','jpeg','png','webp'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            die("Only JPG, PNG, WEBP allowed.");
        }

        if ($fileSize > 2 * 1024 * 1024) {
            die("Image must be under 2MB.");
        }

        $newImage = time() . "_" . rand(1000,9999) . "." . $ext;

        move_uploaded_file($tmp, $uploadDir . $newImage);

        // Delete old image
        if ($imageName && file_exists($uploadDir . $imageName)) {
            unlink($uploadDir . $imageName);
        }

        $imageName = $newImage;
    }

    /* ---- Update Query ---- */
    $stmt = $conn->prepare("
        UPDATE categories 
        SET parent_id=?, name=?, slug=?, image=?, status=? 
        WHERE id=?
    ");

    $stmt->bind_param("isssii",
        $parent_id,
        $name,
        $slug,
        $imageName,
        $status,
        $id
    );

    if (!$stmt->execute()) {
        die("Update Error: " . $stmt->error);
    }

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Category</title>
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow p-4">
        <h4>Edit Category</h4>

        <form method="POST" enctype="multipart/form-data">

            <div class="mb-3">
                <label>Category Name</label>
                <input type="text" name="name" class="form-control"
                       value="<?= htmlspecialchars($category['name']); ?>" required>
            </div>

            <div class="mb-3">
                <label>Parent Category</label>
                <select name="parent_id" class="form-control">
                    <option value="">-- Main Category --</option>
                    <?php categoryDropdown(NULL, 0, $category['parent_id'], $id); ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Current Image</label><br>
                <?php if($category['image']): ?>
                    <img src="../../uploads/categories/<?= $category['image']; ?>" width="100">
                <?php else: ?>
                    No Image
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label>Replace Image</label>
                <input type="file" name="image" class="form-control">
            </div>

            <div class="mb-3">
                <input type="checkbox" name="status"
                    <?= $category['status'] ? 'checked' : ''; ?>>
                Active
            </div>

            <button class="btn btn-danger">Update Category</button>

        </form>
    </div>
</div>

</body>
</html>