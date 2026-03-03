<?php
require_once "includes/auth.php";
// Make sure session is started if not already handled in auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$adminName = $_SESSION['admin_name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | RH Enterprise</title>
    <!-- Fallback to CDN for guaranteed modern styling -->
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

        /* Main Content Wrapper */
        .main-content {
            margin-left: 260px;
            padding: 2rem;
            width: calc(100% - 260px);
        }

        /* Dashboard Cards */
        .stat-card {
            background: white;
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            transition: transform 0.3s ease;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
        }

        .stat-card .card-body {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .welcome-banner {
            background: linear-gradient(135deg, var(--admin-dark) 0%, #2c3e50 100%);
            color: white;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
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
            }
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <?php
    include "sidebar.php"; ?>
    <!-- MAIN CONTENT -->
    <div class="main-content">

        <!-- Welcome Banner -->
        <div class="welcome-banner d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h2 class="fw-bold mb-1">Welcome back, <?= htmlspecialchars($adminName); ?> 👋</h2>
                <p class="mb-0 text-white-50">Here is what's happening with your industrial catalog today.</p>
            </div>
            <div class="text-end">
                <p class="mb-0 fs-5 fw-semibold"><?= date("l, F j, Y"); ?></p>
            </div>
        </div>

        <!-- Dashboard Stats Row (Placeholders - wire these to DB queries!) -->
        <div class="row g-4 mb-5">

            <!-- Products Stat -->
            <div class="col-md-4">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div>
                            <p class="text-muted mb-1 fw-semibold text-uppercase" style="font-size: 0.85rem;">Total Products</p>
                            <h3 class="fw-bold mb-0 text-dark">--</h3>
                        </div>
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5 8 5.961 14.154 3.5 8.186 1.113zM15 4.239l-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923l6.5 2.6z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Categories Stat -->
            <div class="col-md-4">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div>
                            <p class="text-muted mb-1 fw-semibold text-uppercase" style="font-size: 0.85rem;">Active Categories</p>
                            <h3 class="fw-bold mb-0 text-dark">--</h3>
                        </div>
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M2 2.5A2.5 2.5 0 0 1 4.5 0h7A2.5 2.5 0 0 1 14 2.5v11a2.5 2.5 0 0 1-2.5 2.5h-7A2.5 2.5 0 0 1 2 13.5v-11zM4.5 1a1.5 1.5 0 0 0-1.5 1.5v11a1.5 1.5 0 0 0 1.5 1.5h7a1.5 1.5 0 0 0 1.5-1.5v-11A1.5 1.5 0 0 0 11.5 1h-7z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inquiries Stat -->
            <div class="col-md-4">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div>
                            <p class="text-muted mb-1 fw-semibold text-uppercase" style="font-size: 0.85rem;">New Inquiries</p>
                            <h3 class="fw-bold mb-0 text-dark">--</h3>
                        </div>
                        <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Quick Actions Panel -->
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h5 class="fw-bold text-dark">Quick Actions</h5>
            </div>
            <div class="card-body p-4">
                <div class="d-flex flex-wrap gap-3">
                    <a href="products/add.php" class="btn btn-outline-dark px-4 py-2 fw-semibold">
                        + Add New Product
                    </a>
                    <a href="categories/add.php" class="btn btn-outline-dark px-4 py-2 fw-semibold">
                        + Add New Category
                    </a>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>