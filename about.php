<?php
require_once "config/database.php";
include "includes/header.php";
include "includes/navbar.php";
?>

<style>
    /* Custom Industrial Theme Styles */
    :root {
        --industrial-dark: #1a252f;
        --industrial-darker: #11181f;
        --industrial-accent: #dc3545; /* Bootstrap Danger Red */
        --industrial-light: #f8f9fa;
    }

    body {
        font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        background-color: var(--industrial-light);
    }

    /* Smooth Fade In Animation */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .fade-in-up { animation: fadeInUp 0.8s ease-out forwards; }
    .delay-1 { animation-delay: 0.2s; }
    .delay-2 { animation-delay: 0.4s; }

    /* Hero Section */
    .about-hero {
        background: linear-gradient(rgba(17, 24, 31, 0.85), rgba(26, 37, 47, 0.9)), 
                    url('https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover no-repeat;
        min-height: 50vh;
        display: flex;
        align-items: center;
        position: relative;
    }

    /* Custom Accents */
    .accent-line {
        height: 4px;
        width: 60px;
        background-color: var(--industrial-accent);
        border-radius: 2px;
    }
    
    .accent-line-center {
        margin-left: auto;
        margin-right: auto;
    }

    /* Card Hover Effects */
    .hover-card {
        transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.4s ease;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .hover-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1) !important;
    }

    /* Glassmorphism Year Badge */
    .glass-badge {
        background: rgba(220, 53, 69, 0.9);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    /* Logo Grids (Partners & Customers) */
    .logo-box {
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 2rem;
        height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        filter: grayscale(100%);
        opacity: 0.7;
    }

    .logo-box:hover {
        filter: grayscale(0%);
        opacity: 1;
        border-color: var(--industrial-accent);
        box-shadow: 0 10px 25px rgba(220, 53, 69, 0.1);
        transform: translateY(-3px);
    }

    .logo-box img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
    
    /* Custom List styling for About text */
    .about-list li {
        margin-bottom: 1rem;
    }
</style>

<!-- 1. HERO SECTION -->
<div class="about-hero">
    <div class="container text-center text-white z-1 py-5 mt-4">
        <span class="badge bg-danger bg-opacity-75 px-3 py-2 rounded-pill mb-4 fade-in-up" style="font-size: 0.9rem; letter-spacing: 1px;">
            OUR STORY
        </span>
        <h1 class="display-4 fw-bold mb-3 fade-in-up delay-1">About R.H. Enterprise</h1>
        <p class="lead fs-5 mb-0 text-light fade-in-up delay-2 max-w-75 mx-auto opacity-75">
            Driving industrial innovation with precision automation products since 2017.
        </p>
    </div>
</div>

<!-- 2. MAIN STORY SECTION -->
<section class="py-5 bg-white">
    <div class="container py-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 pe-lg-5">
                <h2 class="fw-bold display-6 mb-3" style="color: var(--industrial-dark);">Company Overview</h2>
                <div class="accent-line mb-4"></div>
                
                <p class="text-secondary fs-5 mb-4" style="line-height: 1.7;">
                    <strong>R.H. Enterprise</strong> is a premier industrial products supplier and machinery components provider. Established in Ahmedabad in 2017, we specialize in delivering high-quality engineering equipment to manufacturers, industrial buyers, and machinery suppliers.
                </p>

                <ul class="list-unstyled text-secondary about-list" style="line-height: 1.6;">
                    <li>
                        <strong class="text-dark">Our Mission:</strong> To empower industries with robust, precision-driven automation and mechanical solutions that optimize production and minimize operational downtime.
                    </li>
                    <li>
                        <strong class="text-dark">What We Offer:</strong> We supply a comprehensive range of industrial equipment, including advanced gearboxes, gear motors, AC drives, PLCs, HMIs, and mechanical components used across diverse manufacturing applications.
                    </li>
                    <li>
                        <strong class="text-dark">Why Choose Us:</strong> With an unwavering commitment to technical excellence, we partner with top global brands to bring reliable, industry-standard technology directly to your factory floor.
                    </li>
                    <li>
                        <strong class="text-dark">Customer Focus:</strong> Our intuitive online platform allows clients to seamlessly browse product categories, explore detailed specifications, and submit direct inquiries. We prioritize rapid response times and dedicated technical support.
                    </li>
                    <li>
                        <strong class="text-dark">Future Vision:</strong> As we look ahead, R.H. Enterprise aims to continuously expand our automation portfolio, driving sustainable and efficient industrial growth across the region.
                    </li>
                </ul>
                
                <div class="d-flex align-items-center gap-3 mt-4 pt-2">
                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="var(--industrial-accent)" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/></svg>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">Premium Quality Assured</h5>
                        <p class="text-muted small mb-0">Delivering only industry-standard components.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="position-relative ps-lg-4">
                    <img src="https://images.unsplash.com/photo-1504917595217-d4dc5ebe6122?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                        alt="RH Enterprise Facility"
                        class="img-fluid rounded-4 shadow-lg w-100" style="object-fit: cover; height: 500px;">
                    
                    <!-- Decorative Glass Element -->
                    <div class="position-absolute bottom-0 start-0 text-white p-4 rounded-end-4 mb-5 shadow-lg glass-badge" style="transform: translateX(-20px);">
                        <h3 class="fw-bold mb-0 display-6">2017</h3>
                        <span class="fw-semibold tracking-wide text-uppercase" style="font-size: 0.85rem;">Year Established</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3. OUR PARTNERS -->
<section class="py-5 bg-light border-top">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold display-6" style="color: var(--industrial-dark);">Brands We Partner With</h2>
            <div class="accent-line accent-line-center mb-4"></div>
            <p class="text-secondary fs-5 max-w-75 mx-auto">We are proud channel partners of globally recognized brands, bringing Italian standards and cutting-edge tech to local industries.</p>
        </div>

        <div class="row g-4 justify-content-center">
            
            <?php
            $stmtPartners = $conn->query("
                SELECT * FROM brand_clients
                WHERE type='partner' AND status=1
                ORDER BY sort_order ASC, id ASC
            ");

            if ($stmtPartners && $stmtPartners->num_rows > 0) {
                while ($partner = $stmtPartners->fetch_assoc()) {
            ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="logo-box">
                            <img src="uploads/brands/<?= htmlspecialchars($partner['logo']) ?>"
                                 alt="<?= htmlspecialchars($partner['name']) ?>">
                        </div>
                    </div>
            <?php
                }
            } else {
                echo '<div class="col-12 text-center text-muted">Partner brands will be updated soon.</div>';
            }
            ?>

        </div>
    </div>
</section>

<!-- 4. OUR CUSTOMERS -->
<section class="py-5 bg-white">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold display-6" style="color: var(--industrial-dark);">Our Valued Clients</h2>
            <div class="accent-line accent-line-center mb-4"></div>
            <p class="text-secondary fs-5 max-w-75 mx-auto">Trusted by leading manufacturing, chemical, and automation facilities across the region.</p>
        </div>

        <!-- The missing row div has been restored here -->
        <div class="row g-4 justify-content-center">
            <?php
            $stmtClients = $conn->query("
                SELECT * FROM brand_clients
                WHERE type='client' AND status=1
                ORDER BY sort_order ASC, id ASC
                LIMIT 8
            ");

            if ($stmtClients && $stmtClients->num_rows > 0) {
                while ($client = $stmtClients->fetch_assoc()) {
            ?>
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <div class="logo-box" style="height:100px;padding:1.5rem;">
                            <img src="uploads/brands/<?= htmlspecialchars($client['logo']) ?>"
                                 alt="<?= htmlspecialchars($client['name']) ?>">
                        </div>
                    </div>
            <?php
                }
            } else {
                echo '<div class="col-12 text-center text-muted">Valued clients will be updated soon.</div>';
            }
            ?>
        </div>
        
        <div class="text-center mt-5 pt-3">
            <a href="contact.php" class="btn btn-danger btn-lg px-5 py-3 fw-semibold shadow-sm rounded-pill">
                Request an Inquiry Today
            </a>
        </div>
    </div>
</section>

<?php include "includes/footer.php"; ?>