<?php
require_once __DIR__ . "/../config/database.php";

/* -------------------------
   Recursive Category Menu
------------------------- */
function buildMenu($parent_id = NULL) {
    global $conn;

    if ($parent_id === NULL) {
        $stmt = $conn->prepare("SELECT * FROM categories WHERE parent_id IS NULL AND status=1 ORDER BY name ASC");
    } else {
        $stmt = $conn->prepare("SELECT * FROM categories WHERE parent_id = ? AND status=1 ORDER BY name ASC");
        $stmt->bind_param("i", $parent_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $check = $conn->prepare("SELECT id FROM categories WHERE parent_id = ? AND status=1");
        $check->bind_param("i", $row['id']);
        $check->execute();
        $hasChild = $check->get_result()->num_rows > 0;

        if ($hasChild) {
            echo '<li class="dropdown-submenu">';
            // Removed data-bs-toggle="dropdown" to prevent Bootstrap from closing the main menu early
            echo '<a class="dropdown-item dropdown-toggle" href="#">' . htmlspecialchars($row['name']) . '</a>';
            echo '<ul class="dropdown-menu">';
            buildMenu($row['id']);
            echo '</ul>';
            echo '</li>';
        } else {
            echo '<li>';
            echo '<a class="dropdown-item" href="/rh-enterprise/category.php?slug=' . urlencode($row['slug']) . '">' . htmlspecialchars($row['name']) . '</a>';
            echo '</li>';
        }
    }
}
?>

<style>
    /* Industrial Navbar Styling */
    :root {
        --nav-bg: #1a252f; /* Deep Charcoal / Navy */
        --nav-accent: #dc3545; /* Industrial Red */
        --nav-text: #ffffff;
        --nav-text-muted: rgba(255, 255, 255, 0.85);
    }

    .industrial-navbar {
        background-color: var(--nav-bg);
        transition: all 0.3s ease-in-out;
        padding-top: 0.8rem;
        padding-bottom: 0.8rem;
    }

    /* Subtle shadow applied via JS when scrolling */
    .industrial-navbar.scrolled {
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }

    /* Brand Logo */
    .industrial-navbar .navbar-brand {
        padding: 0;
        margin-right: 2rem;
    }
    
    .navbar-logo {
        max-height: 70px; /* Sized perfectly for the navbar */
        width: auto;
        border-radius: 10px;
        background-color: #ffffff;
        padding: 6px 12px;
        transition: transform 0.3s ease;
    }

    .navbar-logo:hover {
        transform: scale(1.05);
    }

    /* Nav Links */
    .industrial-navbar .nav-link {
        color: var(--nav-text-muted) !important;
        font-weight: 500;
        font-size: 1.05rem;
        padding: 0.5rem 1.25rem !important;
        transition: color 0.3s ease;
    }

    .industrial-navbar .nav-link:hover,
    .industrial-navbar .nav-link.active {
        color: var(--nav-accent) !important;
    }

    /* Standard Dropdown Styling */
    .dropdown-menu {
        border-radius: 8px;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        padding: 0.5rem 0;
        margin-top: 0;
    }

    .dropdown-item {
        padding: 0.6rem 1.5rem;
        font-weight: 500;
        color: #333;
        transition: all 0.2s ease;
    }

    .dropdown-item:hover,
    .dropdown-item:focus {
        background-color: rgba(220, 53, 69, 0.08);
        color: var(--nav-accent);
        padding-left: 1.8rem; /* Smooth indent on hover */
    }

    /* Multi-Level Submenu Logic */
    .dropdown-submenu {
        position: relative;
    }

    .dropdown-submenu > .dropdown-menu {
        top: 0;
        left: 100%;
        margin-top: -0.5rem;
        margin-left: 0;
        display: none;
    }

    .dropdown-submenu.show > .dropdown-menu {
        display: block;
    }

    /* Hover functionality for desktop devices */
    @media (min-width: 992px) {
        /* Open primary dropdown on hover */
        .industrial-navbar .dropdown:hover > .dropdown-menu {
            display: block;
            border-top: 3px solid var(--nav-accent);
        }
        
        /* Open secondary dropdown on hover */
        .industrial-navbar .dropdown-submenu:hover > .dropdown-menu {
            display: block;
            border-top: none;
        }
    }
</style>

<!-- Sticky Top Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top industrial-navbar" id="mainNavbar">
    <div class="container">
        
        <!-- Brand Logo -->
        <a class="navbar-brand" href="/rh-enterprise/index.php">
            <img src="uploads/logo.png" alt="RH Enterprise" class="navbar-logo shadow-sm">
        </a>

        <!-- Mobile Toggler -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Links -->
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="/rh-enterprise/index.php">Home</a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="/rh-enterprise/about.php">About Us</a>
                </li>
                
                <!-- Dynamic Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="productsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Products
                    </a>
                    <!-- FIX: Missing ul wrapper added here to contain the li elements generated by buildMenu() -->
                    <ul class="dropdown-menu" aria-labelledby="productsDropdown">
                        <?php buildMenu(); ?>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/rh-enterprise/contact.php">Contact</a>
                </li>
            </ul>

            <!-- Right Side CTA -->
            <div class="d-flex align-items-center mt-3 mt-lg-0">
                <a href="/rh-enterprise/contact.php" class="btn btn-danger px-4 py-2 fw-semibold rounded-pill shadow-sm d-flex align-items-center gap-2">
                    Get Quote
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                    </svg>
                </a>
            </div>
        </div>
        
    </div>
</nav>

<script>
    // Handles toggling nested submenus (especially for mobile/click events)
    document.addEventListener("DOMContentLoaded", function() {
        let submenuToggles = document.querySelectorAll('.dropdown-submenu > a');
        
        submenuToggles.forEach(function(element) {
            element.addEventListener("click", function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Find the parent submenu list item
                let parentLi = this.parentElement;
                
                // Toggle the 'show' class which triggers the CSS display:block
                parentLi.classList.toggle('show');
            });
        });

        // Sticky Navbar shadow effect
        const navbar = document.getElementById("mainNavbar");
        window.addEventListener("scroll", function() {
            if (window.scrollY > 50) {
                navbar.classList.add("scrolled");
            } else {
                navbar.classList.remove("scrolled");
            }
        });
    });
</script>