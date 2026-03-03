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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Category | RH Enterprise Admin</title>
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
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .admin-card-header {
            background-color: #ffffff;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 1.5rem 2rem;
        }

        .admin-card-body {
            padding: 2rem;
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

        .form-control, .form-select {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border: 1px solid #ced4da;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--admin-accent);
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
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
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            
            <!-- Header & Navigation -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-0 text-dark">Add New Category</h3>
                    <div class="accent-line"></div>
                </div>
                <a href="index.php" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm rounded-pill px-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/></svg>
                    Back to Categories
                </a>
            </div>

            <!-- Form Card -->
            <div class="card admin-card">
                <div class="admin-card-body">
                    <form method="POST" enctype="multipart/form-data">

                        <div class="mb-4">
                            <label class="form-label" for="categoryName">Category Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="categoryName" class="form-control bg-light" placeholder="e.g. Servo Motors" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="parentCategory">Parent Category</label>
                            <select name="parent_id" id="parentCategory" class="form-select bg-light">
                                <option value="">-- Main Category (No Parent) --</option>
                                <?php categoryDropdown(); ?>
                            </select>
                            <div class="form-text">Select a parent if this is a subcategory.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="categoryImage">Category Image</label>
                            <input type="file" name="image" id="categoryImage" class="form-control bg-light" accept=".jpg,.jpeg,.png,.webp">
                            <div class="form-text">Recommended formats: JPG, PNG, WEBP.</div>
                        </div>

                        <div class="mb-5">
                            <label class="form-label d-block">Visibility Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="statusSwitch" name="status" checked>
                                <label class="form-check-label ms-2" for="statusSwitch">Category is Active & Visible</label>
                            </div>
                        </div>

                        <hr class="border-secondary opacity-25 mb-4">

                        <div class="d-flex justify-content-end gap-3">
                            <a href="index.php" class="btn btn-light px-4 fw-semibold border shadow-sm">Cancel</a>
                            <button type="submit" class="btn btn-danger px-4 fw-semibold shadow-sm d-inline-flex align-items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576 6.636 10.07Zm6.787-8.201L1.591 6.602l4.339 2.76 7.494-7.493Z"/></svg>
                                Save Category
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>