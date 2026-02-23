<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Galaxy Pharmacy</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #020617 0%, #1e293b 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.1) 0%, transparent 70%);
            bottom: -200px;
            left: -200px;
            filter: blur(80px);
        }

        .register-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            padding: 40px;
            border-radius: 40px;
            box-shadow: 0 40px 100px -20px rgba(0, 0, 0, 0.4);
            width: 100%;
            max-width: 540px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            z-index: 1;
        }

        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .register-logo {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 100%);
            color: white;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 15px;
            box-shadow: 0 10px 15px -3px rgba(14, 165, 233, 0.3);
        }

        .register-header h2 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            color: #1e293b;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 18px;
            border: 2px solid #f1f5f9;
            background: #f8fafc;
            transition: all 0.3s;
        }

        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
            border-color: #0ea5e9;
            background: #fff;
        }

        .btn-register {
            background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 100%);
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
            color: #fff;
            width: 100%;
            margin-top: 20px;
            box-shadow: 0 10px 20px -5px rgba(14, 165, 233, 0.4);
        }

        .btn-register:hover {
            transform: translateY(-2px) scale(1.01);
            box-shadow: 0 20px 30px -10px rgba(14, 165, 233, 0.5);
            color: #fff;
        }

        .footer-links {
            text-align: center;
            margin-top: 25px;
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

<div class="register-card animate-up">
    <div class="register-header">
        <div class="register-logo">
            <i class="fas fa-user-plus"></i>
        </div>
        <h2>Create <span class="fw-light">Account</span></h2>
        <p class="text-muted small">Fill in the details below to register</p>
    </div>
    
    <?php if(session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger border-0 rounded-4 p-3 small mb-4" role="alert">
            <ul class="mb-0 ps-3">
                <?php foreach(session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('auth/registerProcess') ?>" method="POST">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">USERNAME</label>
                <input type="text" name="username" class="form-control" value="<?= old('username') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">EMAIL</label>
                <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">PASSWORD</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">CONFIRM PASSWORD</label>
                <input type="password" name="confpassword" class="form-control" required>
            </div>
        </div>
        <button type="submit" class="btn-register">Register</button>
    </form>
    
    <div class="footer-links">
        <p>Already have an account? <a href="<?= base_url('auth/login') ?>">Login here</a></p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
