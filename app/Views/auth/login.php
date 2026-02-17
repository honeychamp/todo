<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | Galaxy Pharmacy</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #064e3b 0%, #10b981 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 50px;
            border-radius: 30px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 480px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-logo {
            width: 80px;
            height: 80px;
            background: var(--primary-emerald);
            color: white;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #10b981 0%, #14b8a6 100%);
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.4);
        }

        .login-header h2 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            color: #064e3b;
            letter-spacing: -1px;
        }

        .form-control {
            border-radius: 15px;
            padding: 14px 20px;
            border: 2px solid #f1f5f9;
            background: #f8fafc;
            transition: all 0.3s;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #10b981;
            background: #fff;
        }

        .btn-login {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            border-radius: 15px;
            padding: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
            color: #fff;
            width: 100%;
            margin-top: 15px;
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.4);
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 25px -5px rgba(16, 185, 129, 0.5);
            color: #fff;
        }

        .footer-links {
            text-align: center;
            margin-top: 30px;
            font-size: 0.95rem;
        }

        .footer-links a {
            color: #059669;
            text-decoration: none;
            font-weight: 700;
        }

        .footer-links a:hover {
            text-decoration: underline;
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
        <p class="text-muted">High-Performance Pharmacy Management</p>
    </div>
    
    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger border-0 rounded-4 p-3 show" role="alert">
            <i class="fas fa-times-circle me-2"></i> <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('auth/loginProcess') ?>" method="POST">
        <div class="mb-3">
            <label class="form-label small fw-bold text-muted">IDENTIFICATION</label>
            <input type="text" name="username" class="form-control" placeholder="Username or Email" required>
        </div>
        <div class="mb-4">
            <label class="form-label small fw-bold text-muted">SECURED PASSWORD</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>
        <button type="submit" class="btn-login">Authorized Access</button>
    </form>
    
    <div class="footer-links">
        <p class="text-muted">Cloud deployment v2.4.0</p>
        <p>New operator? <a href="<?= base_url('auth/register') ?>">Request Account</a></p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
