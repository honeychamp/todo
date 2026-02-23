<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Galaxy Pharmacy</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #020617 0%, #0f172a 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.15) 0%, transparent 70%);
            top: -100px;
            right: -100px;
            filter: blur(50px);
        }

        .login-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            padding: 50px;
            border-radius: 40px;
            box-shadow: 0 40px 100px -20px rgba(0, 0, 0, 0.4);
            width: 100%;
            max-width: 460px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            z-index: 1;
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 100%);
            color: white;
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 20px;
            box-shadow: 0 15px 30px -5px rgba(14, 165, 233, 0.4);
        }

        .login-header h2 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -1px;
        }

        .form-control {
            border-radius: 12px;
            padding: 14px 20px;
            border: 2px solid #f1f5f9;
            background: #f8fafc;
            transition: all 0.3s;
        }

        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
            border-color: #0ea5e9;
            background: #fff;
        }

        .btn-login {
            background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 100%);
            border: none;
            border-radius: 12px;
            padding: 16px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
            color: #fff;
            width: 100%;
            margin-top: 15px;
            box-shadow: 0 10px 15px -3px rgba(14, 165, 233, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-3px) scale(1.01);
            box-shadow: 0 20px 30px -10px rgba(14, 165, 233, 0.5);
            color: #fff;
        }

        .footer-links {
            text-align: center;
            margin-top: 30px;
            font-size: 0.9rem;
        }

        .footer-links a {
            color: #0ea5e9;
            text-decoration: none;
            font-weight: 700;
        }

        .footer-links a:hover {
            color: #6366f1;
        }
    </style>
</head>
<body>

<div class="login-card animate-up">
    <div class="login-header">
        <div class="login-logo">
            <i class="fas fa-hand-holding-medical"></i>
        </div>
        <h2>Galaxy <span class="fw-light">Pharmacy</span></h2>
        <p class="text-muted">Pharmacy Management System</p>
    </div>
    
    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger border-0 rounded-4 p-3 show" role="alert">
            <i class="fas fa-times-circle me-2"></i> <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('auth/loginProcess') ?>" method="POST">
        <div class="mb-3">
            <label class="form-label small fw-bold text-muted">USERNAME / EMAIL</label>
            <input type="text" name="username" class="form-control" placeholder="Enter username or email" required>
        </div>
        <div class="mb-4">
            <label class="form-label small fw-bold text-muted">PASSWORD</label>
            <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
        </div>
        <button type="submit" class="btn-login">Login</button>
    </form>
    
    <div class="footer-links">
        <p>Don't have an account? <a href="<?= base_url('auth/register') ?>">Register here</a></p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
