<?php
require_once "../includes/auth.php";
require_once "../../config/database.php";

/* =========================
   SEARCH + FILTER INPUT
========================= */

$search = $_GET['search'] ?? '';
$product = $_GET['product'] ?? '';

$where = [];
$params = [];
$types = "";

/* Search filter */

if($search){
    $where[] = "(name LIKE ? OR email LIKE ? OR phone LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "sss";
}

/* Product filter */

if($product){
    $where[] = "product_name = ?";
    $params[] = $product;
    $types .= "s";
}

$sql = "SELECT * FROM inquiries";

if($where){
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);

if($params){
    $stmt->bind_param($types,...$params);
}

$stmt->execute();
$result = $stmt->get_result();

/* =========================
   PRODUCT FILTER LIST
========================= */

$productList = $conn->query("
    SELECT DISTINCT product_name
    FROM inquiries
    WHERE product_name IS NOT NULL
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Inquiries | RH Enterprise Admin</title>
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

        /* Sidebar Placeholder (Matches Sidebar Include) */
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
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
        }

        .nav-link:hover,
        .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.05);
            border-left-color: var(--admin-accent);
        }

        .nav-link svg {
            opacity: 0.7;
            transition: opacity 0.3s ease;
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

        /* Card Styling */
        .admin-card {
            background: #ffffff;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .accent-line {
            height: 4px;
            width: 50px;
            background-color: var(--admin-accent);
            border-radius: 2px;
            margin-top: 0.5rem;
        }

        /* Form Controls */
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
            background-color: #f8f9fa;
        }

        /* Unread Row Styling */
        .unread-row td {
            background-color: rgba(220, 53, 69, 0.04);
            font-weight: 600;
            color: var(--admin-dark);
        }
        
        .unread-row:hover td {
            background-color: rgba(220, 53, 69, 0.08) !important;
        }

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
                
                <!-- Header -->
                <div class="mb-4">
                    <h3 class="fw-bold mb-0 text-dark">Customer Inquiries</h3>
                    <div class="accent-line"></div>
                </div>

                <!-- Search & Filter Card -->
                <div class="card admin-card mb-4">
                    <div class="card-body p-4">
                        <form method="GET" class="row g-3 align-items-end">
                            
                            <div class="col-md-5 col-lg-4">
                                <label class="form-label small fw-semibold text-muted mb-1">Search Details</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>
                                    </span>
                                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Name, email, or phone..." value="<?= htmlspecialchars($search) ?>">
                                </div>
                            </div>

                            <div class="col-md-4 col-lg-4">
                                <label class="form-label small fw-semibold text-muted mb-1">Filter by Product</label>
                                <select name="product" class="form-select">
                                    <option value="">All Products</option>
                                    <?php while($p = $productList->fetch_assoc()): ?>
                                        <option value="<?= htmlspecialchars($p['product_name']); ?>" <?= ($product == $p['product_name']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($p['product_name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="col-md-3 col-lg-4 d-flex gap-2">
                                <button type="submit" class="btn btn-dark px-4 fw-semibold shadow-sm flex-grow-1 flex-md-grow-0">
                                    Apply Filter
                                </button>
                                <?php if(!empty($search) || !empty($product)): ?>
                                    <a href="index.php" class="btn btn-light border px-3 fw-semibold shadow-sm text-secondary" title="Clear Filters">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 3a5 5 0 1 1-4.546 2.914.5.5 0 0 0-.908-.417A6 6 0 1 0 8 2v1z"/><path d="M8 4.466V.534a.25.25 0 0 0-.41-.192L5.23 2.308a.25.25 0 0 0 0 .384l2.36 1.966A.25.25 0 0 0 8 4.466z"/></svg>
                                    </a>
                                <?php endif; ?>
                            </div>

                        </form>
                    </div>
                </div>

                <!-- Table Card -->
                <div class="card admin-card">
                    <div class="card-body p-0 table-responsive">
                        
                        <table class="table table-custom border-0">
                            <thead>
                                <tr>
                                    <th width="5%">ID</th>
                                    <th width="12%">Status</th>
                                    <th width="15%">Date</th>
                                    <th width="18%">Customer Name</th>
                                    <th width="20%">Product Inquiry</th>
                                    <th width="20%">Contact Details</th>
                                    <th width="10%" class="text-center">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if($result->num_rows > 0): ?>
                                    <?php while($row = $result->fetch_assoc()): ?>
                                        <tr class="<?= ($row['status']==0) ? 'unread-row' : '' ?> border-bottom">
                                            
                                            <td class="text-muted fw-bold">#<?= $row['id']; ?></td>

                                            <td>
                                                <?php if($row['status']==0): ?>
                                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill fw-semibold">New</span>
                                                <?php elseif($row['status']==1): ?>
                                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-50 px-3 py-2 rounded-pill fw-semibold">Contacted</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill fw-semibold">Closed</span>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <?= date("d M Y", strtotime($row['created_at'])); ?><br>
                                                <small class="text-muted opacity-75"><?= date("h:i A", strtotime($row['created_at'])); ?></small>
                                            </td>

                                            <td>
                                                <div class="fw-semibold text-dark mb-1"><?= htmlspecialchars($row['name']); ?></div>
                                                <?php if(!empty($row['company'])): ?>
                                                    <div class="small text-muted d-flex align-items-center gap-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16"><path d="M14.763.075A.5.5 0 0 1 15 .5v15a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5V14h-1v1.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V10a.5.5 0 0 1 .342-.474L6 7.64V4.5a.5.5 0 0 1 .276-.447l8-4a.5.5 0 0 1 .487.022zM6 8.694 1 10.36V15h5V8.694zM7 15h2v-1.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5V15h2V1.309l-7 3.5V15z"/><path d="M2 11h1v1H2v-1zm2 0h1v1H4v-1zm-2 2h1v1H2v-1zm2 0h1v1H4v-1zm4-4h1v1H8V9zm2 0h1v1h-1V9zm-2 2h1v1H8v-1zm2 0h1v1h-1v-1zm2-2h1v1h-1V9zm0 2h1v1h-1v-1zM8 7h1v1H8V7zm2 0h1v1h-1V7zm2 0h1v1h-1V7zM8 5h1v1H8V5zm2 0h1v1h-1V5zm2 0h1v1h-1V5zm0-2h1v1h-1V3z"/></svg>
                                                        <?= htmlspecialchars($row['company']); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>

                                            <td class="text-wrap" style="min-width: 200px;">
                                                <?= htmlspecialchars($row['product_name'] ?? 'General Inquiry'); ?>
                                            </td>

                                            <td>
                                                <div class="mb-1 text-dark">
                                                    <a href="mailto:<?= htmlspecialchars($row['email']); ?>" class="text-decoration-none text-dark hover-accent">
                                                        <?= htmlspecialchars($row['email']); ?>
                                                    </a>
                                                </div>
                                                <div class="small text-muted">
                                                    <?= htmlspecialchars($row['phone']); ?>
                                                </div>
                                            </td>

                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a href="view.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-dark shadow-sm d-inline-flex align-items-center gap-1" title="View Details">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/><path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/></svg>
                                                    </a>
                                                    <a href="delete.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-danger shadow-sm d-inline-flex align-items-center gap-1" onclick="return confirm('Are you sure you want to delete this inquiry?')" title="Delete">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/></svg>
                                                    </a>
                                                </div>
                                            </td>

                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="text-muted">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="mb-3 opacity-50" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/></svg>
                                                <p class="mb-0 fw-semibold">No inquiries found.</p>
                                                <small>Try adjusting your search filters.</small>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>