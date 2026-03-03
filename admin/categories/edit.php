<?php
require_once "../includes/auth.php";
require_once "../../config/database.php";

function generateSlug($string)
{
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
}

/* -------- Recursive Dropdown -------- */
function categoryDropdown($parent_id = NULL, $level = 0, $selected_id = NULL, $current_id = NULL)
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

        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            die("Only JPG, PNG, WEBP allowed.");
        }

        if ($fileSize > 2 * 1024 * 1024) {
            die("Image must be under 2MB.");
        }

        $newImage = time() . "_" . rand(1000, 9999) . "." . $ext;

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

    $stmt->bind_param(
        "isssii",
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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category | RH Enterprise Admin</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --admin-dark: #1a252f;
            --admin-darker: #11181f;
            --admin-accent: #dc3545;
            --admin-bg: #f4f6f9;
        }

        body {
            background-color: var(--admin-bg);
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            overflow-x: hidden;
        }

        /* Sidebar Base Styling Required for this page layout */
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

        /* Image Preview Box */
        .current-image-preview {
            border: 1px solid #ced4da;
            padding: 0.5rem;
            border-radius: 8px;
            background-color: #f8f9fa;
            display: inline-block;
        }

        /* Custom Checkbox toggle switch style */
        .form-switch .form-check-input {
            width: 3em;
            height: 1.5em;
            cursor: pointer;
        }

        .form-switch .form-check-input:checked {
            background-color: var(--admin-accent);
            border-color: var(--admin-accent);
        }

        .form-switch .form-check-label {
            padding-top: 0.25rem;
            font-weight: 500;
            cursor: pointer;
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
                <div class="col-lg-10 col-xl-8">

                    <!-- Header & Navigation -->
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                        <div>
                            <h3 class="fw-bold mb-0 text-dark">Edit Category</h3>
                            <div class="accent-line"></div>
                        </div>
                        <a href="index.php" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm rounded-pill px-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z" />
                            </svg>
                            Back to Categories
                        </a>
                    </div>

                    <!-- Form Card -->
                    <div class="card admin-card">
                        <div class="admin-card-body">
                            <form method="POST" enctype="multipart/form-data">

                                <div class="mb-4">
                                    <label class="form-label" for="categoryName">Category Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="categoryName" class="form-control bg-light" value="<?= htmlspecialchars($category['name']); ?>" required>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label" for="parentCategory">Parent Category</label>
                                    <select name="parent_id" id="parentCategory" class="form-select bg-light">
                                        <option value="">-- Main Category (No Parent) --</option>
                                        <?php categoryDropdown(NULL, 0, $category['parent_id'], $id); ?>
                                    </select>
                                    <div class="form-text">Changing this will move the category to a new hierarchy location.</div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-4 mb-3 mb-md-0">
                                        <label class="form-label d-block">Current Image</label>
                                        <div class="current-image-preview text-center w-100">
                                            <?php if ($category['image']): ?>
                                                <img src="../../uploads/categories/<?= $category['image']; ?>" class="img-fluid rounded" style="max-height: 120px; object-fit: contain;" alt="Current Image">
                                            <?php else: ?>
                                                <div class="py-4 text-muted small fw-semibold">No Image Uploaded</div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label" for="replaceImage">Replace Image (Optional)</label>
                                        <input type="file" name="image" id="replaceImage" class="form-control bg-light" accept=".jpg,.jpeg,.png,.webp">
                                        <div class="form-text mt-2">Recommended formats: JPG, PNG, WEBP. Max size: 2MB.<br>Uploading a new image will replace the current one.</div>
                                    </div>
                                </div>

                                <div class="mb-5">
                                    <label class="form-label d-block">Visibility Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="statusSwitch" name="status" <?= $category['status'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label ms-2" for="statusSwitch">Category is Active & Visible</label>
                                    </div>
                                </div>

                                <hr class="border-secondary opacity-25 mb-4">

                                <div class="d-flex justify-content-end gap-3">
                                    <a href="index.php" class="btn btn-light px-4 fw-semibold border shadow-sm">Cancel</a>
                                    <button type="submit" class="btn btn-danger px-4 fw-semibold shadow-sm d-inline-flex align-items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z" />
                                        </svg>
                                        Update Category
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>