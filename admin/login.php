<?php
// ========================================================
// BAKE'N BREW - Admin Login Page
// ========================================================

session_start();
require_once '../config/koneksi.php';

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM `admin` WHERE `username` = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $admin['username'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Username atau password salah.';
        }
    } else {
        $error = 'Silakan isi semua field.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Admin – Bake'n Brew</title>
    <link rel="icon" type="image/webp" href="../public/images/logo.webp?v=2" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
    <style>
        :root {
            --brown-dark: #4B2E2B;
            --cream: #FAF6F0;
            --cream-dark: #F5E6D3;
            --beige: #D6BFAF;
            --accent-gold: #C5A880;
            --text-dark: #2C1A18;
            --text-mid: #6E5F5D;
            --radius-md: 12px;
        }
        body {
            background-color: var(--cream);
            font-family: 'Poppins', sans-serif;
            color: var(--text-dark);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .login-card {
            background: #ffffff;
            border: 1px solid var(--cream-dark);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(75, 46, 43, 0.05);
            width: 100%;
            max-width: 420px;
            padding: 2.5rem;
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .brand-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .brand-logo h2 {
            font-family: 'Playfair Display', serif;
            color: var(--brown-dark);
            font-weight: 700;
            margin-top: 0.5rem;
        }
        .brand-logo span {
            color: var(--accent-gold);
        }
        .form-label {
            font-weight: 500;
            color: var(--brown-dark);
            font-size: 0.9rem;
        }
        .form-control {
            border: 1.5px solid var(--cream-dark);
            border-radius: var(--radius-md);
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: var(--accent-gold);
            box-shadow: 0 0 0 4px rgba(197, 168, 128, 0.15);
            outline: none;
        }
        .btn-login {
            background-color: var(--brown-dark);
            color: var(--cream);
            border: none;
            border-radius: var(--radius-md);
            padding: 0.8rem;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 1.5rem;
        }
        .btn-login:hover {
            background-color: #5c3a37;
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(75, 46, 43, 0.15);
        }
        .back-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.85rem;
        }
        .back-link a {
            color: var(--text-mid);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .back-link a:hover {
            color: var(--brown-dark);
        }
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="brand-logo">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="mb-2" style="width: 42px; height: 42px; color: var(--accent-gold);">
            <path d="M17 8h1a4 4 0 1 1 0 8h-1" />
            <path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V8z" />
            <line x1="6" y1="2" x2="6" y2="4" />
            <line x1="10" y1="2" x2="10" y2="4" />
            <line x1="14" y1="2" x2="14" y2="4" />
        </svg>
        <h2>Bake'n <span>Brew</span></h2>
        <p style="color: var(--text-mid); font-size: 0.85rem; margin-top: -5px;">Admin Control Panel</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger d-flex align-items-center gap-2" role="alert" style="border-radius: var(--radius-md); font-size: 0.85rem; padding: 0.75rem 1rem;">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div><?= htmlspecialchars($error) ?></div>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <!-- Username -->
        <div class="mb-3">
            <label for="username" class="form-label">
                <i class="bi bi-person me-1"></i>Username
            </label>
            <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username admin" required autocomplete="off" />
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">
                <i class="bi bi-lock me-1"></i>Password
            </label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required />
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn-login">
            <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
        </button>
    </form>
</div>

</body>
</html>
