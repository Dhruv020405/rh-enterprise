<?php
require_once "config/database.php";
include "includes/header.php";
include "includes/navbar.php";
?>

<style>
    /* Custom Industrial Theme Styles */
    :root {
        --industrial-dark: #1a252f;
        --industrial-accent: #dc3545; /* Bootstrap Danger Red */
        --industrial-light: #f8f9fa;
    }

    body {
        font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    }

    /* Hero Section */
    .hero-section {
        background: linear-gradient(rgba(26, 37, 47, 0.85), rgba(26, 37, 47, 0.85)), 
                    url('https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover no-repeat;
        min-height: 70vh;
        display: flex;
        align-items: center;
    }

    /* Card Hover Effects */
    .hover-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
    }
    .hover-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }

    /* Custom Accents */
    .accent-line {
        height: 4px;
        width: 60px;
        background-color: var(--industrial-accent);
        border-radius: 2px;
    }

    /* Icon Boxes */
    .icon-box-wrapper {
        background: #ffffff;
        border-radius: 12px;
        padding: 30px 20px;
        height: 100%;
        transition: all 0.3s ease;
        border-bottom: 4px solid transparent;
    }
    .icon-box-wrapper:hover {
        border-bottom: 4px solid var(--industrial-accent);
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    }
    .icon-box-icon {
        width: 60px;
        height: 60px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background-color: rgba(220, 53, 69, 0.1);
        color: var(--industrial-accent);
        margin-bottom: 20px;
    }

    /* Dark Section Icons */
    .dark-section .icon-box-icon {
        background-color: rgba(255, 255, 255, 0.1);
        color: #ffffff;
    }
</style>

<!-- 1. HERO SECTION -->
<div class="hero-section">
    <div class="container text-center text-white z-1 py-5">
        <h1 class="display-3 fw-bold mb-3">Welcome to RH Enterprise</h1>
        <p class="lead fs-4 mb-5 text-light">
            Industrial Gearbox, Servo Systems & Automation Solutions
        </p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="#categories" class="btn btn-danger btn-lg px-5 py-3 fw-semibold shadow-sm">View Products</a>
            <a href="contact.php" class="btn btn-outline-light btn-lg px-5 py-3 fw-semibold shadow-sm">Contact Us</a>
        </div>
    </div>
</div>

<!-- 2. ABOUT SECTION -->
<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <h2 class="fw-bold" style="color: var(--industrial-dark);">About RH Enterprise</h2>
                <div class="accent-line mb-4"></div>
                <p class="text-secondary fs-5 mb-4">
                    With years of excellence in the industrial sector, RH Enterprise stands as a premier provider of top-tier industrial machinery, cutting-edge servo systems, and comprehensive automation solutions. 
                </p>
                <p class="text-secondary mb-4">
                    Our commitment to quality, precision, and innovation ensures that your manufacturing and operational processes run at absolute peak efficiency. We partner with industry leaders to bring you reliable equipment designed for rigorous industrial environments.
                </p>
                <div class="d-flex gap-4">
                    <div class="d-flex align-items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="text-danger" viewBox="0 0 16 16"><path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/></svg>
                        <span class="fw-semibold text-dark">Certified Quality</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="text-danger" viewBox="0 0 16 16"><path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/></svg>
                        <span class="fw-semibold text-dark">Expert Support</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1565514020179-026b92b2d698?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                         alt="Industrial Facility" 
                         class="img-fluid rounded-4 shadow-lg w-100" style="object-fit: cover; height: 400px;">
                    <!-- Decorative Element -->
                    <div class="position-absolute bottom-0 start-0 bg-danger text-white p-4 rounded-end-4 mb-4 shadow" style="transform: translateX(-20px);">
                        <h4 class="fw-bold mb-0">15+ Years</h4>
                        <small>Of Industrial Excellence</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3. INJECTION MOULDING SOLUTIONS -->
<section class="py-5">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="fw-bold" style="color: var(--industrial-dark);">Injection Moulding Solutions</h2>
            <div class="accent-line mx-auto mb-3"></div>
            <p class="text-secondary max-w-75 mx-auto">Discover our specialized components built for precision, durability, and high-performance injection moulding applications.</p>
        </div>

        <div class="row g-4 text-center">
            <!-- Feature 1 -->
            <div class="col-md-6 col-lg-3">
                <div class="icon-box-wrapper border">
                    <div class="icon-box-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" viewBox="0 0 16 16"><path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z"/><path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z"/></svg>
                    </div>
                    <h5 class="fw-bold text-dark">Servo Pump Package</h5>
                    <p class="text-muted small mb-0">Highly efficient power delivery with precise control mechanisms for demanding applications.</p>
                </div>
            </div>
            <!-- Feature 2 -->
            <div class="col-md-6 col-lg-3">
                <div class="icon-box-wrapper border">
                    <div class="icon-box-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" viewBox="0 0 16 16"><path d="M7.752.066a.5.5 0 0 1 .496 0l3.75 2.143a.5.5 0 0 1 .252.434v3.995l3.498 2A.5.5 0 0 1 16 9.07v4.286a.5.5 0 0 1-.252.434l-3.75 2.143a.5.5 0 0 1-.496 0l-3.502-2-3.502 2.001a.5.5 0 0 1-.496 0l-3.75-2.143A.5.5 0 0 1 0 13.356V9.071a.5.5 0 0 1 .252-.434L3.75 6.638V2.643a.5.5 0 0 1 .252-.434L7.752.066ZM4.25 7.504 1.508 9.071l2.742 1.567 2.742-1.567L4.25 7.504ZM1.508 13.356 4.25 14.923l2.742-1.567v-3.134l-2.742 1.567-2.742-1.567v3.134Zm8.492-5.852-2.742 1.567 2.742 1.567 2.742-1.567-2.742-1.567Zm-2.742 7.419 2.742 1.567 2.742-1.567v-3.134l-2.742 1.567-2.742-1.567v3.134Zm2.742-12.28-2.742 1.567 2.742 1.567 2.742-1.567-2.742-1.567Z"/></svg>
                    </div>
                    <h5 class="fw-bold text-dark">VG Internal Gear Pump</h5>
                    <p class="text-muted small mb-0">Low noise, high pressure volumetric gear pumps offering consistent fluid movement.</p>
                </div>
            </div>
            <!-- Feature 3 -->
            <div class="col-md-6 col-lg-3">
                <div class="icon-box-wrapper border">
                    <div class="icon-box-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" viewBox="0 0 16 16"><path d="M3 9.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z"/><path d="M1.5 4A1.5 1.5 0 0 0 0 5.5v5A1.5 1.5 0 0 0 1.5 12h13a1.5 1.5 0 0 0 1.5-1.5v-5A1.5 1.5 0 0 0 14.5 4h-13zM1 5.5a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 .5.5v5a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-5z"/></svg>
                    </div>
                    <h5 class="fw-bold text-dark">Support Arm</h5>
                    <p class="text-muted small mb-0">Robust structural integrations to maximize stability and minimize vibrations during operations.</p>
                </div>
            </div>
            <!-- Feature 4 -->
            <div class="col-md-6 col-lg-3">
                <div class="icon-box-wrapper border">
                    <div class="icon-box-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" viewBox="0 0 16 16"><path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5 8 5.961 14.154 3.5 8.186 1.113zM15 4.239l-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923l6.5 2.6zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464L7.443.184z"/></svg>
                    </div>
                    <h5 class="fw-bold text-dark">Talwinder Products</h5>
                    <p class="text-muted small mb-0">Authorized distribution of premium Talwinder machinery components for guaranteed reliability.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 4. CATEGORY PREVIEW (DYNAMIC PHP) -->
<section id="categories" class="py-5 bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-end mb-5">
            <div>
                <h2 class="fw-bold" style="color: var(--industrial-dark);">Our Product Categories</h2>
                <div class="accent-line"></div>
            </div>
            <div class="d-none d-md-block">
                <span class="text-muted">Browse our extensive inventory</span>
            </div>
        </div>

        <div class="row g-4">

            <?php
            // PRESERVED ORIGINAL PHP LOGIC
            $stmt = $conn->prepare("
                SELECT * FROM categories 
                WHERE parent_id IS NULL AND status=1 
                ORDER BY name ASC
            ");
            $stmt->execute();
            $result = $stmt->get_result();

            while($row = $result->fetch_assoc()):
            ?>

                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 hover-card rounded-4 overflow-hidden border-0 bg-white">

                        <?php if($row['image']): ?>
                            <div class="position-relative">
                                <img src="uploads/categories/<?= $row['image']; ?>" 
                                     class="card-img-top w-100" 
                                     style="height: 240px; object-fit: cover;"
                                     alt="<?= htmlspecialchars($row['name']); ?>">
                                <!-- Overlay Gradient for premium look -->
                                <div class="position-absolute bottom-0 w-100 h-50" style="background: linear-gradient(to top, rgba(0,0,0,0.4), transparent);"></div>
                            </div>
                        <?php else: ?>
                            <!-- Fallback image layout if no image exists -->
                            <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 240px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#e9ecef" viewBox="0 0 16 16"><path d="M4 0h5.293A1 1 0 0 1 10 .293L13.707 4a1 1 0 0 1 .293.707V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2zm5.5 1.5v2a1 1 0 0 0 1 1h2l-3-3zM2.5 14c0 .275.225.5.5.5h9c.275 0 .5-.225.5-.5V5.5H8a2 2 0 0 1-2-2V1.5H3a.5.5 0 0 0-.5.5v12z"/></svg>
                            </div>
                        <?php endif; ?>

                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <h4 class="card-title fw-bold mb-3" style="color: var(--industrial-dark);">
                                    <?= htmlspecialchars($row['name']); ?>
                                </h4>
                            </div>
                            
                            <a href="category.php?slug=<?= $row['slug']; ?>" 
                               class="btn btn-danger w-100 mt-3 py-2 fw-semibold">
                                View Products <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="ms-1" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/></svg>
                            </a>
                        </div>
                    </div>
                </div>

            <?php endwhile; ?>

        </div>
    </div>
</section>

<!-- 5. WHY CHOOSE US -->
<section class="py-5 dark-section" style="background-color: var(--industrial-dark);">
    <div class="container py-5">
        <div class="text-center text-white mb-5">
            <h2 class="fw-bold">Why Choose Us</h2>
            <div class="accent-line mx-auto mb-3"></div>
            <p class="text-light opacity-75">The right partner for your industrial automation needs.</p>
        </div>

        <div class="row g-4 text-center">
            <!-- Energy Efficient -->
            <div class="col-md-6 col-lg-3">
                <div class="icon-box-icon mx-auto shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16"><path d="M11.251.068a.5.5 0 0 1 .227.58L9.677 6.5H13a.5.5 0 0 1 .364.843l-8 8.5a.5.5 0 0 1-.842-.49L6.323 9.5H3a.5.5 0 0 1-.364-.843l8-8.5a.5.5 0 0 1 .615-.09zM4.157 8.5H7a.5.5 0 0 1 .478.647L6.11 13.59l5.732-6.09H9a.5.5 0 0 1-.478-.647L9.89 2.41 4.157 8.5z"/></svg>
                </div>
                <h5 class="fw-bold text-white mt-3">Energy Efficient</h5>
                <p class="text-light opacity-75 small">Systems designed to optimize power consumption and lower operational costs.</p>
            </div>
            
            <!-- Reliable Products -->
            <div class="col-md-6 col-lg-3">
                <div class="icon-box-icon mx-auto shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16"><path d="M5.338 1.59a61.44 61.44 0 0 0-2.837.856.481.481 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.725 10.725 0 0 0 2.287 2.233c.346.244.652.42.893.533.12.057.218.095.293.118a.55.55 0 0 0 .101.025.615.615 0 0 0 .1-.025c.076-.023.174-.061.294-.118.24-.113.547-.29.893-.533a10.726 10.726 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.775 11.775 0 0 1-2.517 2.453 7.159 7.159 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7.158 7.158 0 0 1-1.048-.625 11.777 11.777 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 62.456 62.456 0 0 1 5.072.56z"/><path d="M10.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0z"/></svg>
                </div>
                <h5 class="fw-bold text-white mt-3">Reliable Products</h5>
                <p class="text-light opacity-75 small">Sourced from top manufacturers globally, tested for longevity and precision.</p>
            </div>

            <!-- Industrial Experience -->
            <div class="col-md-6 col-lg-3">
                <div class="icon-box-icon mx-auto shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16"><path d="M14.763.075A.5.5 0 0 1 15 .5v15a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5V14h-1v1.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V10a.5.5 0 0 1 .342-.474L6 7.64V4.5a.5.5 0 0 1 .276-.447l8-4a.5.5 0 0 1 .487.022zM6 8.694 1 10.36V15h5V8.694zM7 15h2v-1.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5V15h2V1.309l-7 3.5V15z"/><path d="M2 11h1v1H2v-1zm2 0h1v1H4v-1zm-2 2h1v1H2v-1zm2 0h1v1H4v-1zm4-4h1v1H8V9zm2 0h1v1h-1V9zm-2 2h1v1H8v-1zm2 0h1v1h-1v-1zm2-2h1v1h-1V9zm0 2h1v1h-1v-1zM8 7h1v1H8V7zm2 0h1v1h-1V7zm2 0h1v1h-1V7zM8 5h1v1H8V5zm2 0h1v1h-1V5zm2 0h1v1h-1V5zm0-2h1v1h-1V3z"/></svg>
                </div>
                <h5 class="fw-bold text-white mt-3">Industry Expertise</h5>
                <p class="text-light opacity-75 small">Over 15 years of domain knowledge supporting heavy and light industries.</p>
            </div>

            <!-- Customer Support -->
            <div class="col-md-6 col-lg-3">
                <div class="icon-box-icon mx-auto shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16"><path d="M8 1a5 5 0 0 0-5 5v1h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V6a6 6 0 1 1 12 0v6a2.5 2.5 0 0 1-2.5 2.5H9.366a1 1 0 0 1-.866.5h-1a1 1 0 1 1 0-2h1a1 1 0 0 1 .866.5H11.5A1.5 1.5 0 0 0 13 12h-1a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1h1V6a5 5 0 0 0-5-5z"/></svg>
                </div>
                <h5 class="fw-bold text-white mt-3">24/7 Support</h5>
                <p class="text-light opacity-75 small">Dedicated technical assistance available round-the-clock to prevent downtime.</p>
            </div>
        </div>
    </div>
</section>

<?php include "includes/footer.php"; ?>