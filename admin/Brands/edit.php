<?php
require_once "../includes/auth.php";
require_once "../../config/database.php";

$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM brand_clients WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    die("Brand or Client not found!");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name']);
    $type = trim($_POST['type']);
    $status = isset($_POST['status']) ? 1 : 0;
    $order = intval($_POST['sort_order']);

    $logo = $data['logo'];

    /* ---- Secure Image Upload & Replace ---- */
    if (!empty($_FILES['logo']['name'])) {

        $uploadDir = "../../uploads/brands/";
        $tmp = $_FILES['logo']['tmp_name'];
        $fileName = $_FILES['logo']['name'];

        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            die("Only JPG, PNG, WEBP allowed.");
        }

        $newLogo = time() . "_" . rand(1000, 9999) . "." . $ext;

        move_uploaded_file($tmp, $uploadDir . $newLogo);

        // Delete old logo to save disk space
        if ($logo && file_exists($uploadDir . $logo)) {
            unlink($uploadDir . $logo);
        }

        $logo = $newLogo;
    }

    /* ---- Update Query ---- */
    $stmt = $conn->prepare("
        UPDATE brand_clients 
        SET name=?, logo=?, type=?, status=?, sort_order=? 
        WHERE id=?
    ");

    $stmt->bind_param(
        "sssiii",
        $name,
        $logo,
        $type,
        $status,
        $order,
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
    <title>Edit Brand or Client | RH Enterprise Admin</title>
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

        /* Image Preview Box */
        .current-image-preview {
            border: 1px solid #ced4da;
            padding: 0.5rem;
            border-radius: 8px;
            background-color: #f8f9fa;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 120px;
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
                        <h3 class="fw-bold mb-0 text-dark">Edit Brand / Client</h3>
                        <div class="accent-line"></div>
                    </div>
                    <a href="index.php" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm rounded-pill px-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/></svg>
                        Back to List
                    </a>
                </div>

                <!-- Form Card -->
                <div class="card admin-card">
                    <div class="admin-card-body">
                        <form method="POST" enctype="multipart/form-data">

                            <div class="row mb-4">
                                <div class="col-md-7 mb-3 mb-md-0">
                                    <label class="form-label" for="entityName">Entity Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="entityName" class="form-control bg-light" value="<?= htmlspecialchars($data['name']); ?>" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label" for="entityType">Type <span class="text-danger">*</span></label>
                                    <select name="type" id="entityType" class="form-select bg-light" required>
                                        <option value="partner" <?= $data['type'] == 'partner' ? 'selected' : '' ?>>Partner Brand</option>
                                        <option value="client" <?= $data['type'] == 'client' ? 'selected' : '' ?>>Valued Client</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <label class="form-label d-block">Current Logo</label>
                                    <div class="current-image-preview w-100">
                                        <?php if($data['logo']): ?>
                                            <img src="../../uploads/brands/<?= htmlspecialchars($data['logo']); ?>" class="img-fluid rounded" style="max-height: 90px; object-fit: contain;" alt="Current Logo">
                                        <?php else: ?>
                                            <div class="text-muted small fw-semibold">No Logo Uploaded</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label" for="replaceLogo">Replace Logo (Optional)</label>
                                    <input type="file" name="logo" id="replaceLogo" class="form-control bg-light" accept=".jpg,.jpeg,.png,.webp">
                                    <div class="form-text mt-2">Recommended formats: PNG with transparent background.<br>Uploading a new logo will replace the current one.</div>
                                </div>
                            </div>

                            <div class="mb-5">
                                <label class="form-label" for="sortOrder">Sort Order</label>
                                <input type="number" name="sort_order" id="sortOrder" class="form-control bg-light w-50" value="<?= $data['sort_order']; ?>" min="0">
                                <div class="form-text">Lower numbers appear first.</div>
                            </div>

                            <div class="mb-5">
                                <label class="form-label d-block">Visibility Status</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="statusSwitch" name="status" value="1" <?= $data['status'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label ms-2" for="statusSwitch">Brand/Client is Active & Visible</label>
                                </div>
                            </div>

                            <hr class="border-secondary opacity-25 mb-4">

                            <div class="d-flex justify-content-end gap-3">
                                <a href="index.php" class="btn btn-light px-4 fw-semibold border shadow-sm">Cancel</a>
                                <button type="submit" class="btn btn-danger px-5 fw-semibold shadow-sm d-inline-flex align-items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/></svg>
                                    Update Entry
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