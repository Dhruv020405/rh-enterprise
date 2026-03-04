<?php
require_once "../includes/auth.php";
require_once "../../config/database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name']);
    $type = trim($_POST['type']);
    $status = intval($_POST['status']);
    $order = intval($_POST['sort_order']);

    $logo = NULL;

    /* ---- Secure Image Upload ---- */
    if (!empty($_FILES['logo']['name'])) {

        $uploadDir = "../../uploads/brands/";
        $tmp = $_FILES['logo']['tmp_name'];
        $fileName = $_FILES['logo']['name'];

        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            die("Only JPG, PNG, WEBP allowed.");
        }

        $logo = time() . "_" . rand(1000, 9999) . "." . $ext;

        move_uploaded_file($tmp, $uploadDir . $logo);
    }

    /* ---- Insert Query ---- */
    $stmt = $conn->prepare("
        INSERT INTO brand_clients 
        (name, logo, type, status, sort_order) 
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "sssii",
        $name,
        $logo,
        $type,
        $status,
        $order
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
    <title>Add Brand or Client | RH Enterprise Admin</title>
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

        /* Main Content Layout */
        .main-content {
            padding: 2rem;
            width: 100%;
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
            .main-content {
                padding: 1rem;
            }
            .admin-card-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>

<!-- MAIN CONTENT WRAPPER -->
<div class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">
                
                <!-- Header & Navigation -->
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                    <div>
                        <h3 class="fw-bold mb-0 text-dark">Add Brand / Client</h3>
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
                                    <input type="text" name="name" id="entityName" class="form-control bg-light" placeholder="e.g. Bonvario" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label" for="entityType">Type <span class="text-danger">*</span></label>
                                    <select name="type" id="entityType" class="form-select bg-light" required>
                                        <option value="partner">Partner Brand</option>
                                        <option value="client">Valued Client</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label" for="entityLogo">Logo Image</label>
                                <input type="file" name="logo" id="entityLogo" class="form-control bg-light" accept=".jpg,.jpeg,.png,.webp">
                                <div class="form-text">Recommended formats: PNG with transparent background.</div>
                            </div>

                            <div class="mb-5">
                                <label class="form-label" for="sortOrder">Sort Order</label>
                                <input type="number" name="sort_order" id="sortOrder" class="form-control bg-light w-50" value="0" min="0">
                                <div class="form-text">Lower numbers appear first. Leave as 0 to use drag-and-drop later.</div>
                            </div>

                            <div class="mb-5">
                                <label class="form-label d-block">Visibility Status</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="statusSwitch" name="status" value="1" checked>
                                    <label class="form-check-label ms-2" for="statusSwitch">Brand/Client is Active & Visible</label>
                                </div>
                            </div>

                            <hr class="border-secondary opacity-25 mb-4">

                            <div class="d-flex justify-content-end gap-3">
                                <a href="index.php" class="btn btn-light px-4 fw-semibold border shadow-sm">Cancel</a>
                                <button type="submit" class="btn btn-danger px-5 fw-semibold shadow-sm d-inline-flex align-items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576 6.636 10.07Zm6.787-8.201L1.591 6.602l4.339 2.76 7.494-7.493Z"/></svg>
                                    Save Entry
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