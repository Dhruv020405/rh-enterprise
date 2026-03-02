<style>
    /* Industrial Footer Styling */
    .industrial-footer {
        background-color: #1a252f; /* Matches Deep Charcoal/Navy of Navbar */
        color: #ffffff;
        border-top: 4px solid #dc3545; /* Industrial Red accent */
        margin-top: 5rem;
    }
    
    .industrial-footer .brand-text {
        font-size: 1.5rem;
        font-weight: 800;
        letter-spacing: 0.5px;
    }
    
    .industrial-footer .brand-text span {
        color: #dc3545;
    }

    .industrial-footer .footer-links {
        padding-left: 0;
        list-style: none;
    }

    .industrial-footer .footer-links li {
        margin-bottom: 0.6rem;
    }

    .industrial-footer .footer-links a {
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-block;
    }

    .industrial-footer .footer-links a:hover {
        color: #dc3545;
        transform: translateX(5px); /* Smooth hover indent */
    }

    .industrial-footer .contact-info strong {
        color: #ffffff;
        font-weight: 600;
        min-width: 70px;
        display: inline-block;
    }

    .industrial-footer .social-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        background-color: rgba(255, 255, 255, 0.05);
        color: rgba(255, 255, 255, 0.8);
        border-radius: 50%;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .industrial-footer .social-icon:hover {
        background-color: #dc3545;
        color: #ffffff;
        transform: translateY(-3px);
    }
    
    .industrial-footer .copyright-bar {
        background-color: #141c24; /* Slightly darker than footer bg */
        padding: 1.2rem 0;
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.9rem;
    }
</style>

<footer class="industrial-footer">
    <div class="container py-5">
        <div class="row gy-4">
            
            <!-- Column 1: Brand & About -->
            <div class="col-lg-4 col-md-6 pe-lg-4">
                <div class="brand-text mb-3">
                    RH <span>Enterprise</span>
                </div>
                <p class="text-muted small mb-4" style="line-height: 1.6;">
                    Your trusted partner for industrial gearboxes, cutting-edge servo systems, and comprehensive automation solutions. Built for precision and durability.
                </p>
                <div class="d-flex gap-3">
                    <a href="#" class="social-icon" aria-label="LinkedIn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854V1.146zm4.943 12.248V6.169H2.542v7.225h2.401zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248-.822 0-1.359.54-1.359 1.248 0 .694.521 1.248 1.327 1.248h.016zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016a5.54 5.54 0 0 1 .016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225h2.4z"/></svg>
                    </a>
                    <a href="#" class="social-icon" aria-label="Twitter">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865l8.875 11.633Z"/></svg>
                    </a>
                </div>
            </div>
            
            <!-- Column 2: Quick Links -->
            <div class="col-lg-3 col-md-6">
                <h5 class="text-white fw-bold mb-4 fs-6 text-uppercase tracking-wide">Quick Links</h5>
                <ul class="footer-links">
                    <li><a href="/rh-enterprise/index.php">Home</a></li>
                    <li><a href="/rh-enterprise/about.php">About Us</a></li>
                    <li><a href="/rh-enterprise/products.php">All Products</a></li>
                    <li><a href="/rh-enterprise/contact.php">Contact Support</a></li>
                </ul>
            </div>
            
            <!-- Column 3: Contact Info -->
            <div class="col-lg-5 col-md-12">
                <h5 class="text-white fw-bold mb-4 fs-6 text-uppercase tracking-wide">Get In Touch</h5>
                <ul class="list-unstyled text-muted small" style="line-height: 2;">
                    <li class="d-flex mb-2">
                        <strong class="text-white">Email:</strong> 
                        <span class="ms-2">info@rhenterprise.com</span>
                    </li>
                    <li class="d-flex mb-2">
                        <strong class="text-white">Phone:</strong> 
                        <span class="ms-2">+1 (234) 567-8900</span>
                    </li>
                    <li class="d-flex mb-2">
                        <strong class="text-white">Address:</strong> 
                        <span class="ms-2">123 Industrial Way, Automation Sector, Tech City, 10010</span>
                    </li>
                </ul>
            </div>
            
        </div>
    </div>
    
    <!-- Copyright Bar -->
    <div class="copyright-bar text-center">
        <div class="container">
            &copy; <?= date("Y"); ?> RH Enterprise | All Rights Reserved.
        </div>
    </div>
</footer>

<!-- Bootstrap JS CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>