<?php
require_once "../includes/auth.php";
require_once "../../config/database.php";

function displayCategories($parent_id = NULL, $level = 0) {
    global $conn;

    if ($parent_id === NULL) {
        $stmt = $conn->prepare("
            SELECT * FROM categories 
            WHERE parent_id IS NULL 
            ORDER BY sort_order ASC, name ASC
        ");
    } else {
        $stmt = $conn->prepare("
            SELECT * FROM categories 
            WHERE parent_id = ? 
            ORDER BY sort_order ASC, name ASC
        ");
        $stmt->bind_param("i", $parent_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {

        // Format Date
        $date = date("M d, Y", strtotime($row['created_at']));

        echo "<tr data-id='{$row['id']}' class='align-middle bg-white border-bottom'>";

        // Drag Handle
        echo "<td class='drag-handle text-muted text-center' style='cursor:grab; width: 5%;' title='Drag to reorder'>";
        echo "<svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' fill='currentColor' class='bi bi-grip-vertical' viewBox='0 0 16 16'><path d='M7 2a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM7 5a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM7 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm-3 3a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm-3 3a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z'/></svg>";
        echo "</td>";

        // Category Name with Hierarchy Indentation
        echo "<td style='width: 35%;'>";
        if ($level > 0) {
            echo "<span class='text-muted'>" . str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;↳ ", $level) . "</span>";
        }
        echo "<span class='fw-semibold text-dark'>" . htmlspecialchars($row['name']) . "</span>";
        echo "</td>";

        // Image
        echo "<td style='width: 15%;'>";
        if ($row['image']) {
            echo "<img src='../../uploads/categories/{$row['image']}' class='rounded shadow-sm border' style='width: 45px; height: 45px; object-fit: cover;' alt='Image'>";
        } else {
            echo "<div class='bg-light text-muted border rounded d-flex align-items-center justify-content-center' style='width: 45px; height: 45px; font-size: 0.7rem; font-weight: 500;'>No Img</div>";
        }
        echo "</td>";

        // Status
        echo "<td style='width: 15%;'>";
        if ($row['status']) {
            echo "<span class='badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill fw-semibold'>Active</span>";
        } else {
            echo "<span class='badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-2 rounded-pill fw-semibold'>Inactive</span>";
        }
        echo "</td>";

        // Created Date
        echo "<td class='text-muted small' style='width: 15%;'>{$date}</td>";

        // Actions
        echo "<td style='width: 15%;'>
                <div class='d-flex gap-2'>
                    <a href='edit.php?id={$row['id']}' class='btn btn-sm btn-outline-dark d-inline-flex align-items-center gap-1 shadow-sm'>
                        <svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='currentColor' viewBox='0 0 16 16'><path d='M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z'/></svg> 
                        Edit
                    </a>
                    <a href='delete.php?id={$row['id']}' class='btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1 shadow-sm' onclick=\"return confirm('Are you sure you want to delete this category?')\">
                        <svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='currentColor' viewBox='0 0 16 16'><path d='M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z'/></svg>
                    </a>
              </div>
              </td>";

        echo "</tr>";

        // Recursive call
        displayCategories($row['id'], $level + 1);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories | RH Enterprise Admin</title>
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

        /* Sidebar Styling */
        .sidebar {
            background-color: var(--admin-dark);
            min-height: 100vh;
            width: 260px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
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

        .nav-link:hover, .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.05);
            border-left-color: var(--admin-accent);
        }

        .nav-link svg {
            opacity: 0.7;
            transition: opacity 0.3s ease;
        }

        .nav-link:hover svg, .nav-link.active svg {
            opacity: 1;
        }

        /* Main Content Wrapper */
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
            padding: 0; /* Removing padding to let the table span full width */
        }

        .accent-line {
            height: 4px;
            width: 50px;
            background-color: var(--admin-accent);
            border-radius: 2px;
            margin-top: 0.5rem;
        }

        /* Table Styling */
        .table-custom {
            margin-bottom: 0;
        }
        
        .table-custom thead th {
            background-color: #f8f9fa;
            color: var(--admin-dark);
            font-weight: 600;
            border-bottom: 2px solid #e9ecef;
            padding: 1rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-custom tbody td {
            padding: 1rem;
            vertical-align: middle;
            color: #495057;
        }

        .table-custom tbody tr:hover {
            background-color: #f8f9fa !important;
        }

        /* Drag Handle State */
        .drag-handle:active {
            cursor: grabbing !important;
        }
        
        .sortable-ghost {
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

<!-- SIDEBAR -->
<?php include "../sidebar.php"; ?>

<!-- MAIN CONTENT -->
<div class="main-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                
                <!-- Header & Navigation -->
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                    <div>
                        <h3 class="fw-bold mb-0 text-dark">Category Tree</h3>
                        <div class="accent-line"></div>
                    </div>
                    <a href="add.php" class="btn btn-danger d-inline-flex align-items-center gap-2 shadow-sm fw-semibold rounded-pill px-4 py-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/></svg>
                        Add Category
                    </a>
                </div>

                <!-- Table Card -->
                <div class="card admin-card">
                    <div class="admin-card-body table-responsive">
                        <table class="table table-custom border-0">
                            <thead>
                                <tr>
                                    <th></th> <!-- Empty header for Drag Handle -->
                                    <th>Category Name</th>
                                    <th>Image</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>

                            <tbody id="sortable-categories">
                                <?php displayCategories(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Helper Text -->
                <div class="text-muted small mt-3 ms-2 d-flex align-items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/></svg>
                    Tip: You can reorder categories by clicking and dragging the grip icon on the left of each row.
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Sortable JS -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let sortable = new Sortable(document.getElementById('sortable-categories'), {
            animation: 150,
            handle: '.drag-handle', // Restricts dragging strictly to the icon
            ghostClass: 'sortable-ghost', // Adds a smooth background to the row being dragged
            onEnd: function () {
                let order = [];
                document.querySelectorAll('#sortable-categories tr').forEach((row, index) => {
                    if(row.dataset.id) {
                        order.push({
                            id: row.dataset.id,
                            position: index + 1
                        });
                    }
                });

                fetch('update-order.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(order)
                }).then(response => {
                    if(response.ok) {
                        console.log('Order updated successfully.');
                    }
                }).catch(error => {
                    console.error('Error updating order:', error);
                });
            }
        });
    });
</script>

</body>
</html>