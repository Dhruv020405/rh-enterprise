<?php
session_start();
require_once "../config/database.php";

// If admin is already logged in, redirect directly to dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();

        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid password! Please try again.";
        }
    } else {
        $error = "Admin account not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | RH Enterprise</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --admin-dark: #1a252f;
            --admin-darker: #11181f;
            --admin-accent: #dc3545;
        }

        body {
            /* Deep industrial gradient background */
            background: linear-gradient(135deg, var(--admin-dark) 0%, var(--admin-darker) 100%);
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 15px;
        }

        .login-card {
            background: #ffffff;
            border: none;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            overflow: hidden;
            position: relative;
        }

        /* Top red accent line */
        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background-color: var(--admin-accent);
        }

        .card-body {
            padding: 2.5rem;
        }

        .brand-text {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--admin-dark);
            text-align: center;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .brand-text span {
            color: var(--admin-accent);
        }

        .form-label {
            font-weight: 600;
            color: var(--admin-dark);
            font-size: 0.9rem;
            margin-bottom: 0.4rem;
        }

        .form-control {
            padding: 0.8rem 1rem;
            border-radius: 8px;
            border: 1px solid #ced4da;
            background-color: #f8f9fa;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            background-color: #ffffff;
            border-color: var(--admin-accent);
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.15);
        }

        .btn-login {
            background-color: var(--admin-accent);
            color: white;
            font-weight: 600;
            padding: 0.8rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-login:hover {
            background-color: #bb2d3b;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        }
        
        .alert-custom {
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">
        <div class="card-body">
            
            <!-- Branding -->
            <div class="text-center mb-4 pb-2">
                <div class="brand-text">
                    RH <span>Admin</span>
                </div>
                <p class="text-muted small mb-0">Secure Portal Access</p>
            </div>

            <!-- Error Message -->
            <?php if(isset($error)): ?>
                <div class="alert alert-danger alert-custom p-3 mb-4" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/></svg>
                    <div><?= htmlspecialchars($error); ?></div>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" action="">
                
                <div class="mb-4">
                    <label class="form-label" for="emailInput">Administrator Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z"/></svg>
                        </span>
                        <input type="email" id="emailInput" name="email" class="form-control border-start-0 ps-0" placeholder="admin@rhenterprise.com" required autofocus>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="form-label" for="passwordInput">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/></svg>
                        </span>
                        <input type="password" id="passwordInput" name="password" class="form-control border-start-0 ps-0" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-login w-100 d-flex justify-content-center align-items-center gap-2">
                    Secure Login
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/></svg>
                </button>

            </form>

        </div>
    </div>
    
    <!-- Footer/Copyright Text outside the card -->
    <div class="text-center mt-4">
        <p class="text-white-50 small mb-0">&copy; <?= date('Y'); ?> RH Enterprise. All rights reserved.</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>