<?php
require_once "../includes/auth.php";
require_once "../../config/database.php";

$result = $conn->query("
    SELECT * FROM brand_clients
    ORDER BY sort_order ASC, id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Brands & Clients | RH Enterprise Admin</title>
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
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .admin-card-body {
            padding: 0;
        }

        .accent-line {
            height: 4px;
            width: 50px;
            background-color: var(--admin-accent);
            border-radius: 2px;
            margin-top: 0.5rem;
        }

        /* --- Custom Tree Table Grid (Replaces standard table) --- */
        .table-header {
            background-color: #f8f9fa;
            color: var(--admin-dark);
            font-weight: 600;
            border-bottom: 2px solid #e9ecef;
            padding: 1rem 0;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .hover-bg-light:hover {
            background-color: #f8f9fa !important;
        }

        .transition-base {
            transition: background-color 0.2s ease;
        }

        /* Sortable States */
        .drag-handle:active {
            cursor: grabbing !important;
        }

        .sortable-ghost > .table-row {
            background-color: #f1f3f5 !important;
            opacity: 0.8;
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
                        <h3 class="fw-bold mb-0 text-dark">Partners & Clients</h3>
                        <div class="accent-line"></div>
                    </div>
                    <a href="add.php" class="btn btn-danger d-inline-flex align-items-center gap-2 shadow-sm fw-semibold rounded-pill px-4 py-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/></svg>
                        Add Brand / Client
                    </a>
                </div>

                <!-- Pseudo Table Card -->
                <div class="card admin-card">
                    <div class="admin-card-body table-responsive">
                        
                        <!-- Minimum width prevents grid from breaking on phones -->
                        <div style="min-width: 800px;">
                            
                            <!-- Header Grid -->
                            <div class="table-header d-flex align-items-center pe-3">
                                <div class="text-center" style="width: 5%;"></div> <!-- Empty Drag Col -->
                                <div style="width: 15%; padding-left: 0.5rem;">Logo</div>
                                <div style="width: 30%;">Entity Name</div>
                                <div class="text-center" style="width: 15%;">Type</div>
                                <div class="text-center" style="width: 15%;">Status</div>
                                <div class="text-center" style="width: 20%;">Actions</div>
                            </div>

                            <!-- Body Grid (Sortable List) -->
                            <ul class="list-unstyled mb-0 w-100" id="sortable-brands">
                                <?php if($result->num_rows > 0): ?>
                                    <?php while($row = $result->fetch_assoc()): ?>
                                        <li class="brand-item w-100" data-id="<?= $row['id']; ?>">
                                            <div class="table-row d-flex align-items-center border-bottom bg-white py-2 pe-3 hover-bg-light transition-base">
                                                
                                                <!-- Drag Handle (5%) -->
                                                <div class="drag-handle text-muted text-center" style="cursor:grab; width: 5%;" title="Drag to reorder">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M7 2a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM7 5a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM7 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm-3 3a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm-3 3a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/></svg>
                                                </div>

                                                <!-- Logo (15%) -->
                                                <div style="width: 15%; padding-left: 0.5rem;">
                                                    <?php if(!empty($row['logo'])): ?>
                                                        <img src="../../uploads/brands/<?= htmlspecialchars($row['logo']); ?>" 
                                                             class="rounded shadow-sm border bg-white p-1" 
                                                             style="width: 70px; height: 50px; object-fit: contain;" alt="Logo">
                                                    <?php else: ?>
                                                        <div class="bg-light text-muted border rounded d-flex align-items-center justify-content-center" style="width: 70px; height: 50px; font-size: 0.7rem; font-weight: 500;">No Img</div>
                                                    <?php endif; ?>
                                                </div>

                                                <!-- Name (30%) -->
                                                <div style="width: 30%;" class="fw-semibold text-dark">
                                                    <?= htmlspecialchars($row['name']); ?>
                                                </div>

                                                <!-- Type (15%) -->
                                                <div class="text-center" style="width: 15%;">
                                                    <?php if(strtolower($row['type']) == 'partner'): ?>
                                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-1 rounded-1 fw-semibold">Partner</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3 py-1 rounded-1 fw-semibold">Client</span>
                                                    <?php endif; ?>
                                                </div>

                                                <!-- Status (15%) -->
                                                <div class="text-center" style="width: 15%;">
                                                    <?php if($row['status']): ?>
                                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill fw-semibold">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-2 rounded-pill fw-semibold">Inactive</span>
                                                    <?php endif; ?>
                                                </div>

                                                <!-- Actions (20%) -->
                                                <div class="text-center" style="width: 20%;">
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <a href="edit.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-dark d-inline-flex align-items-center gap-1 shadow-sm" title="Edit">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/></svg>
                                                        </a>
                                                        <a href="delete.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1 shadow-sm" onclick="return confirm('Are you sure you want to delete this record?')" title="Delete">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/></svg>
                                                        </a>
                                                    </div>
                                                </div>

                                            </div>
                                        </li>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <div class="text-muted">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="mb-3 opacity-50" viewBox="0 0 16 16"><path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5 8 5.961 14.154 3.5 8.186 1.113zM15 4.239l-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923l6.5 2.6zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464L7.443.184z"/></svg>
                                            <p class="mb-0 fw-semibold">No Brands or Clients found.</p>
                                            <small>Click 'Add Brand / Client' to create your first entry.</small>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </ul>

                        </div>

                    </div>
                </div>

                <!-- Helper Text -->
                <div class="text-muted small mt-3 ms-2 d-flex align-items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/></svg>
                    Tip: You can reorder the items by clicking and dragging the grip icon. This order will reflect on the front-end website.
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Sortable JS -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const el = document.getElementById('sortable-brands');
        
        if (el && el.children.length > 0 && el.querySelector('.brand-item')) {
            new Sortable(el, {
                animation: 150,
                handle: '.drag-handle', // Lock dragging strictly to the grip icon
                fallbackOnBody: true,
                swapThreshold: 0.65,
                ghostClass: 'sortable-ghost',
                
                onEnd: function () {
                    let order = [];
                    document.querySelectorAll('.brand-item').forEach((row, index) => {
                        if(row.dataset.id) {
                            order.push({
                                id: row.dataset.id,
                                position: index + 1
                            });
                        }
                    });

                    // Push updated order to your backend API
                    // Make sure you create 'update-order.php' in the brands folder if it doesn't exist yet!
                    fetch('update-order.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(order)
                    }).then(response => {
                        if(response.ok) {
                            console.log('Brand order successfully updated.');
                        }
                    }).catch(error => {
                        console.error('Error updating order:', error);
                    });
                }
            });
        }
    });
</script>

</body>
</html>