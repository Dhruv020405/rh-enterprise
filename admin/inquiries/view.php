<?php
require_once "../includes/auth.php";
require_once "../../config/database.php";

$id = intval($_GET['id']);

/* MARK AS VIEWED */

$stmt = $conn->prepare("
UPDATE inquiries 
SET status = 1 
WHERE id = ? AND status = 0
");

$stmt->bind_param("i", $id);
$stmt->execute();

/* FETCH DATA */

$stmt = $conn->prepare("
SELECT * FROM inquiries 
WHERE id = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();

$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    die("Inquiry not found");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiry Details | RH Enterprise Admin</title>
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
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            height: 100%;
        }

        .admin-card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            padding: 1.25rem 1.5rem;
            font-weight: 700;
            color: var(--admin-dark);
        }

        .admin-card-body {
            padding: 1.5rem;
        }

        .accent-line {
            height: 4px;
            width: 50px;
            background-color: var(--admin-accent);
            border-radius: 2px;
            margin-top: 0.5rem;
        }

        /* Detail List Styling */
        .detail-group {
            margin-bottom: 1.25rem;
        }

        .detail-label {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .detail-value {
            font-size: 1.05rem;
            color: var(--admin-dark);
            font-weight: 500;
        }

        .message-box {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1.5rem;
            font-size: 1.05rem;
            color: #495057;
            line-height: 1.6;
            white-space: pre-wrap;
            /* Preserves line breaks */
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
                            <h3 class="fw-bold mb-0 text-dark">Inquiry Details</h3>
                            <div class="accent-line"></div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="index.php" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm rounded-pill px-4">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z" />
                                </svg>
                                Back to List
                            </a>
                            <a target="_blank"
href="https://mail.google.com/mail/?view=cm&fs=1&to=<?= urlencode($data['email']); ?>&su=<?= urlencode('RE: Inquiry about '.($data['product_name'] ?? 'RH Enterprise')); ?>&body=<?= urlencode("Hello ".$data['name'].",

Thank you for your inquiry about ".($data['product_name'] ?? 'our product').".
Our team will get back to you shortly.

Best Regards,
RH Enterprise"); ?>"

class="btn btn-danger d-inline-flex align-items-center gap-2 shadow-sm rounded-pill px-4">

Reply via Email
</a>
                        </div>
                    </div>

                    <div class="row g-4">

                        <!-- Left Column: Customer Information -->
                        <div class="col-lg-4">
                            <div class="card admin-card">
                                <div class="admin-card-header d-flex justify-content-between align-items-center">
                                    Customer Info
                                    <?php if (isset($data['status'])): ?>
                                        <?php if ($data['status'] == 0): ?>
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 rounded-1">New</span>
                                        <?php elseif ($data['status'] == 1): ?>
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-50 px-2 py-1 rounded-1">Contacted</span>
                                        <?php else: ?>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-1">Closed</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <div class="admin-card-body">

                                    <div class="detail-group">
                                        <div class="detail-label">Full Name</div>
                                        <div class="detail-value"><?= htmlspecialchars($data['name']); ?></div>
                                    </div>

                                    <div class="detail-group">
                                        <div class="detail-label">Email Address</div>
                                        <div class="detail-value">
                                            <a href="mailto:<?= htmlspecialchars($data['email']); ?>" class="text-decoration-none text-danger fw-semibold">
                                                <?= htmlspecialchars($data['email']); ?>
                                            </a>
                                        </div>
                                    </div>

                                    <div class="detail-group">
                                        <div class="detail-label">Phone Number</div>
                                        <div class="detail-value">
                                            <a href="tel:<?= htmlspecialchars($data['phone']); ?>" class="text-decoration-none text-dark">
                                                <?= htmlspecialchars($data['phone']); ?>
                                            </a>
                                        </div>
                                    </div>

                                    <div class="detail-group">
                                        <div class="detail-label">Company</div>
                                        <div class="detail-value">
                                            <?= !empty($data['company']) ? htmlspecialchars($data['company']) : '<span class="text-muted fst-italic">Not Provided</span>'; ?>
                                        </div>
                                    </div>

                                    <hr class="border-secondary opacity-25 my-4">

                                    <div class="detail-group mb-0">
                                        <div class="detail-label">Metadata</div>
                                        <div class="text-muted small">
                                            <strong>Date:</strong> <?= date("d M Y, h:i A", strtotime($data['created_at'] ?? 'now')); ?><br>
                                            <strong>IP Address:</strong> <?= htmlspecialchars($data['ip_address']); ?>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Message & Product Details -->
                        <div class="col-lg-8">
                            <div class="card admin-card">
                                <div class="admin-card-header">
                                    Inquiry Message
                                </div>
                                <div class="admin-card-body">

                                    <div class="detail-group mb-4">
                                        <div class="detail-label">Inquiring About Product</div>
                                        <div class="detail-value fs-4 fw-bold text-dark">
                                            <?= !empty($data['product_name']) ? htmlspecialchars($data['product_name']) : '<span class="text-muted fst-italic">General Inquiry</span>'; ?>
                                        </div>
                                    </div>

                                    <div class="detail-group mb-0">
                                        <div class="detail-label mb-3">Message Content</div>
                                        <div class="message-box shadow-sm">
                                            <?= htmlspecialchars($data['message']); ?>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>