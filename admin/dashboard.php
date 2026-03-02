<?php
require_once "includes/auth.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container-fluid">
    <div class="row">

        <!-- Sidebar -->
        <div class="col-md-2 bg-dark text-white min-vh-100 p-3">
            <h4 class="mb-4">Admin Panel</h4>

            <ul class="nav flex-column">

                <li class="nav-item mb-2">
                    <a href="dashboard.php" class="nav-link text-white">
                        Dashboard
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="categories/index.php" class="nav-link text-white">
                        Manage Categories
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="products/index.php" class="nav-link text-white">
                        Manage Products
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="inquiries/index.php" class="nav-link text-white">
                        Inquiries
                    </a>
                </li>

                <li class="nav-item mt-4">
                    <a href="logout.php" class="btn btn-danger w-100">
                        Logout
                    </a>
                </li>

            </ul>
        </div>

        <!-- Main Content -->
        <div class="col-md-10 p-4">
            <h2>Welcome, <?= $_SESSION['admin_name']; ?> 👋</h2>
            <p class="mt-3">Use the left menu to manage website content.</p>
        </div>

    </div>
</div>

</body>
</html>