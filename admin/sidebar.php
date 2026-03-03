<?php
// Get current script name only (e.g., dashboard.php)
$current_page = basename($_SERVER['PHP_SELF']);

// Detect if we are inside subfolder
$in_subfolder = (
    strpos($_SERVER['PHP_SELF'], '/categories/') !== false ||
    strpos($_SERVER['PHP_SELF'], '/products/') !== false ||
    strpos($_SERVER['PHP_SELF'], '/inquiries/') !== false
);

// If inside subfolder add ../
$prefix = $in_subfolder ? '../' : '';

// Active classes
$active_dashboard  = ($current_page == 'dashboard.php') ? 'active' : '';
$active_categories = (strpos($_SERVER['PHP_SELF'], '/categories/') !== false) ? 'active' : '';
$active_products   = (strpos($_SERVER['PHP_SELF'], '/products/') !== false) ? 'active' : '';
$active_inquiries  = (strpos($_SERVER['PHP_SELF'], '/inquiries/') !== false) ? 'active' : '';
?>

<div class="sidebar d-flex flex-column">

    <!-- Logo Section -->
    <div class="sidebar-brand text-center pt-4 pb-3 px-3">
        <img src="<?= $prefix ?>../uploads/logo.png" alt="RH Enterprise" class="shadow-sm" style="width: 100%; max-width: 160px; height: auto; border-radius: 15px; background: #ffffff; padding: 12px;">
    </div>

    <ul class="nav flex-column mb-auto">

        <!-- Dashboard -->
        <li class="nav-item">
            <a href="<?= $prefix ?>dashboard.php" class="nav-link <?= $active_dashboard ?>">
                Dashboard
            </a>
        </li>

        <!-- Categories -->
        <li class="nav-item">
            <a href="<?= $prefix ?>categories/index.php" class="nav-link <?= $active_categories ?>">
                Manage Categories
            </a>
        </li>

        <!-- Products -->
        <li class="nav-item">
            <a href="<?= $prefix ?>products/index.php" class="nav-link <?= $active_products ?>">
                Manage Products
            </a>
        </li>

        <!-- Inquiries -->
        <li class="nav-item">
            <a href="<?= $prefix ?>inquiries/index.php" class="nav-link <?= $active_inquiries ?>">
                Inquiries
            </a>
        </li>

    </ul>

    <!-- Logout -->
    <div class="p-3 mt-auto">
        <a href="<?= $prefix ?>logout.php"
            class="btn btn-danger w-100 fw-semibold shadow-sm">
            Logout
        </a>
    </div>
</div>