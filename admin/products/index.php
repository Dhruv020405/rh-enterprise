<?php
require_once "../includes/auth.php";
require_once "../../config/database.php";

/* =========================
   SEARCH + FILTER INPUT
========================= */
$search = $_GET['search'] ?? '';
$category_filter = $_GET['category_id'] ?? '';
// Sorting is disabled ONLY if a text search is active. 
// If filtering by category, sorting is perfectly fine and encouraged!
$disable_sorting = !empty($search); 

$where = [];
$params = [];
$types = "";

if ($search) {
    $where[] = "p.name LIKE ?";
    $params[] = "%" . $search . "%";
    $types .= "s";
}

if ($category_filter) {
    $where[] = "p.category_id = ?";
    $params[] = $category_filter;
    $types .= "i";
}

/* =========================
   MAIN QUERY
========================= */
$query = "
SELECT 
    p.*,
    c.name AS category_name,
    (SELECT COUNT(*) FROM product_features pf WHERE pf.product_id = p.id) AS feature_count,
    (SELECT COUNT(*) FROM product_applications pa WHERE pa.product_id = p.id) AS application_count,
    (SELECT COUNT(*) FROM product_images pi WHERE pi.product_id = p.id) AS gallery_count
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
";

if (!empty($where)) {
    $query .= " WHERE " . implode(" AND ", $where);
}

// Crucial: Order by Category Name FIRST, then by the sort order within that category.
$query .= " ORDER BY c.name ASC, p.sort_order ASC, p.id DESC";

$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

/* =========================
   GROUP PRODUCTS BY CATEGORY
========================= */
$grouped_products = [];
while ($row = $result->fetch_assoc()) {
    $cat_name = $row['category_name'] ? $row['category_name'] : 'Uncategorized';
    $grouped_products[$cat_name][] = $row;
}

/* =========================
   FETCH CATEGORIES FOR FILTER
========================= */
$catResult = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products | RH Enterprise Admin</title>
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

        /* Sidebar Base Styling */
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

        .sidebar-brand span { color: var(--admin-accent); }

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

        .nav-link:hover, .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.05);
            border-left-color: var(--admin-accent);
        }

        .nav-link svg { opacity: 0.7; }
        .nav-link:hover svg, .nav-link.active svg { opacity: 1; }

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

        .accent-line {
            height: 4px;
            width: 50px;
            background-color: var(--admin-accent);
            border-radius: 2px;
            margin-top: 0.5rem;
        }

        /* Category Banner */
        .category-banner {
            background: linear-gradient(to right, #f8f9fa, #ffffff);
            border-left: 5px solid var(--admin-accent);
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Form Controls for Filters */
        .form-control, .form-select {
            padding: 0.7rem 1rem;
            border-radius: 8px;
            border: 1px solid #ced4da;
            transition: all 0.2s ease;
            background-color: #f8f9fa;
        }
        .form-control:focus, .form-select:focus {
            background-color: #ffffff;
            border-color: var(--admin-accent);
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.15);
        }

        /* Table Styling */
        .table-custom {
            margin-bottom: 0;
            white-space: nowrap;
        }

        .table-custom thead th {
            background-color: #f8f9fa;
            color: var(--admin-dark);
            font-weight: 600;
            border-bottom: 2px solid #e9ecef;
            padding: 1rem;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-custom tbody td {
            padding: 1rem;
            vertical-align: middle;
            color: #495057;
            font-size: 0.95rem;
        }

        .table-custom tbody tr:hover {
            background-color: #f8f9fa !important;
        }

        /* Sub-badges for counts */
        .count-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 28px;
            height: 28px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.85rem;
        }

        /* Drag & Drop Styles */
        .drag-handle { cursor: grab; }
        .drag-handle:active { cursor: grabbing !important; }
        .sortable-ghost { background-color: #f1f3f5 !important; opacity: 0.8; }

        @media (max-width: 768px) {
            .sidebar { width: 100%; height: auto; position: relative; min-height: auto; }
            .main-content { margin-left: 0; width: 100%; padding: 1rem; }
        }
    </style>
</head>

<body>

    <!-- Dynamic Sidebar Included Here -->
    <?php include "../sidebar.php"; ?>

    <!-- MAIN CONTENT WRAPPER -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <!-- Header & Navigation -->
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                        <div>
                            <h3 class="fw-bold mb-0 text-dark">Manage Products</h3>
                            <div class="accent-line"></div>
                        </div>
                        <a href="add.php" class="btn btn-danger d-inline-flex align-items-center gap-2 shadow-sm fw-semibold rounded-pill px-4 py-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                            </svg>
                            Add Product
                        </a>
                    </div>

                    <!-- Search & Filter Card -->
                    <div class="card admin-card mb-4">
                        <div class="card-body p-4">
                            <form method="GET" class="row g-3 align-items-end">
                                
                                <div class="col-md-5 col-lg-4">
                                    <label class="form-label small fw-semibold text-muted mb-1">Search Product Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0 text-muted">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>
                                        </span>
                                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="e.g. Gearbox, Servo..." value="<?= htmlspecialchars($search) ?>">
                                    </div>
                                </div>

                                <div class="col-md-4 col-lg-4">
                                    <label class="form-label small fw-semibold text-muted mb-1">Filter by Category</label>
                                    <select name="category_id" class="form-select">
                                        <option value="">All Categories</option>
                                        <?php while($cat = $catResult->fetch_assoc()): ?>
                                            <option value="<?= $cat['id'] ?>" <?= ($category_filter == $cat['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cat['name']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <div class="col-md-3 col-lg-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-dark px-4 fw-semibold shadow-sm flex-grow-1 flex-md-grow-0">
                                        Filter
                                    </button>
                                    <?php if(!empty($search) || !empty($category_filter)): ?>
                                        <a href="index.php" class="btn btn-light border px-3 fw-semibold shadow-sm text-secondary" title="Clear Filters">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 3a5 5 0 1 1-4.546 2.914.5.5 0 0 0-.908-.417A6 6 0 1 0 8 2v1z"/><path d="M8 4.466V.534a.25.25 0 0 0-.41-.192L5.23 2.308a.25.25 0 0 0 0 .384l2.36 1.966A.25.25 0 0 0 8 4.466z"/></svg>
                                        </a>
                                    <?php endif; ?>
                                </div>

                            </form>
                        </div>
                    </div>

                    <!-- Drag and drop helper text -->
                    <div class="text-muted small mb-3 ms-1 d-flex align-items-center gap-2">
                        <?php if(!$disable_sorting): ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" /><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z" /></svg>
                            Tip: Drag and drop the grip icon to reorder products within their specific category.
                        <?php else: ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/></svg>
                            Note: Drag and drop ordering is temporarily disabled while a text search is active.
                        <?php endif; ?>
                    </div>

                    <!-- Products Output: Grouped by Category -->
                    <?php if (empty($grouped_products)): ?>
                        
                        <!-- Empty State -->
                        <div class="card admin-card">
                            <div class="card-body text-center py-5">
                                <div class="text-muted">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="mb-3 opacity-50" viewBox="0 0 16 16">
                                        <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5 8 5.961 14.154 3.5 8.186 1.113zM15 4.239l-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923l6.5 2.6zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464L7.443.184z" />
                                    </svg>
                                    <p class="mb-0 fw-semibold">No products found.</p>
                                    <small>Try adjusting your search filters or click 'Add Product'.</small>
                                </div>
                            </div>
                        </div>

                    <?php else: ?>

                        <!-- Loop through each Category Group -->
                        <?php foreach ($grouped_products as $cat_name => $prods): ?>
                            <div class="card admin-card mb-4">
                                
                                <!-- Category Banner -->
                                <div class="category-banner">
                                    <h5 class="mb-0 fw-bold d-flex align-items-center gap-2" style="color: var(--admin-dark);">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="var(--admin-accent)" viewBox="0 0 16 16">
                                            <path d="M9.828 3h3.982a2 2 0 0 1 1.992 2.181l-.637 7A2 2 0 0 1 13.174 14H2.825a2 2 0 0 1-1.991-1.819l-.637-7a1.99 1.99 0 0 1 .342-1.31L.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3zm-8.322.12C1.72 3.042 1.95 3 2.19 3h5.396l-.707-.707A1 1 0 0 0 6.172 2H2.5a1 1 0 0 0-1 .981l.006.139z"/>
                                        </svg>
                                        Category: <?= htmlspecialchars($cat_name); ?>
                                    </h5>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-2 rounded-pill">
                                        <?= count($prods); ?> Product(s)
                                    </span>
                                </div>

                                <!-- Category Table -->
                                <div class="card-body p-0 table-responsive">
                                    <table class="table table-custom border-0 mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-center" width="4%"></th> 
                                                <th class="text-center" width="5%">ID</th>
                                                <th width="10%">Image</th>
                                                <th width="37%">Product Name</th>
                                                <th class="text-center" width="8%">Gallery</th>
                                                <th class="text-center" width="8%">Features</th>
                                                <th class="text-center" width="8%">Apps</th>
                                                <th class="text-center" width="9%">Status</th>
                                                <th class="text-center" width="11%">Actions</th>
                                            </tr>
                                        </thead>

                                        <!-- Independent Sortable List for this Category -->
                                        <tbody class="sortable-products">
                                            <?php foreach ($prods as $row): ?>
                                                <tr class="product-item align-middle border-bottom" data-id="<?= $row['id']; ?>">
                                                    
                                                    <!-- Drag Handle Icon -->
                                                    <?php if (!$disable_sorting): ?>
                                                        <td class="text-center text-muted drag-handle" title="Drag to reorder within <?= htmlspecialchars($cat_name); ?>">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M7 2a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM7 5a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM7 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm-3 3a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm-3 3a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/></svg>
                                                        </td>
                                                    <?php else: ?>
                                                        <td class="text-center text-muted">
                                                            <small title="Sorting disabled while searching">-</small>
                                                        </td>
                                                    <?php endif; ?>

                                                    <td class="text-center fw-bold text-muted">#<?= $row['id']; ?></td>

                                                    <!-- Main Image -->
                                                    <td>
                                                        <?php if (!empty($row['main_image'])): ?>
                                                            <img src="../../uploads/products/<?= $row['main_image']; ?>"
                                                                class="rounded shadow-sm border bg-white"
                                                                style="width: 50px; height: 50px; object-fit: contain;">
                                                        <?php else: ?>
                                                            <div class="bg-light text-muted border rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-size: 0.7rem; font-weight: 500;">No Img</div>
                                                        <?php endif; ?>
                                                    </td>

                                                    <!-- Product Name -->
                                                    <td>
                                                        <span class="fw-semibold text-dark"><?= htmlspecialchars($row['name']); ?></span>
                                                    </td>

                                                    <!-- Counts -->
                                                    <td class="text-center">
                                                        <span class="count-badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">
                                                            <?= $row['gallery_count']; ?>
                                                        </span>
                                                    </td>

                                                    <td class="text-center">
                                                        <span class="count-badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">
                                                            <?= $row['feature_count']; ?>
                                                        </span>
                                                    </td>

                                                    <td class="text-center">
                                                        <span class="count-badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-50">
                                                            <?= $row['application_count']; ?>
                                                        </span>
                                                    </td>

                                                    <!-- Status -->
                                                    <td class="text-center">
                                                        <?php if ($row['status']): ?>
                                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill fw-semibold">Active</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill fw-semibold">Inactive</span>
                                                        <?php endif; ?>
                                                    </td>

                                                    <!-- Actions -->
                                                    <td>
                                                        <div class="d-flex justify-content-center gap-2">
                                                            <a href="edit.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-dark d-inline-flex align-items-center gap-1 shadow-sm" title="Edit">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                                                    <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z" />
                                                                </svg>
                                                            </a>
                                                            <a href="delete.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1 shadow-sm" onclick="return confirm('Are you sure you want to delete this product?')" title="Delete">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                                                    <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z" />
                                                                </svg>
                                                            </a>
                                                        </div>
                                                    </td>

                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endforeach; ?>

                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>

    <!-- Sortable JS -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <?php if (!$disable_sorting): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Apply Sortable to EVERY category table body
            const lists = document.querySelectorAll('.sortable-products');
            
            lists.forEach(el => {
                if (el.children.length > 0) {
                    new Sortable(el, {
                        animation: 150,
                        handle: '.drag-handle', // Limits dragging to the grip icon
                        fallbackOnBody: true,
                        swapThreshold: 0.65,
                        ghostClass: 'sortable-ghost',
                        
                        onEnd: function (evt) {
                            let order = [];
                            
                            // Extract rows ONLY from the specific category table that was sorted
                            evt.to.querySelectorAll('.product-item').forEach((row, index) => {
                                if(row.dataset.id) {
                                    order.push({
                                        id: row.dataset.id,
                                        position: index + 1
                                    });
                                }
                            });

                            // Send the updated sequence to the backend script
                            fetch('update-order.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify(order)
                            }).then(response => {
                                if(response.ok) {
                                    console.log('Product sort order successfully updated for this category.');
                                }
                            }).catch(error => {
                                console.error('Error updating product order:', error);
                            });
                        }
                    });
                }
            });
        });
    </script>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>