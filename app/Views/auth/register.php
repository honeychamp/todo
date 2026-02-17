<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration | Galaxy Pharmacy</title>
    
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
            overflow: hidden;
        }

        .register-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 30px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 500px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .register-logo {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #10b981 0%, #14b8a6 100%);
            color: white;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 15px;
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.4);
        }

        .register-header h2 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            color: #064e3b;
        }

        .form-control {
            border-radius: 12px;
            padding: 10px 15px;
            border: 2px solid #f1f5f9;
            background: #f8fafc;
            transition: all 0.3s;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #10b981;
            background: #fff;
        }

        .btn-register {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
            color: #fff;
            width: 100%;
            margin-top: 10px;
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.4);
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(16, 185, 129, 0.5);
            color: #fff;
        }

        .footer-links {
            text-align: center;
            margin-top: 25px;
            font-size: 0.9rem;
        }

        .footer-links a {
            color: #059669;
            text-decoration: none;
            font-weight: 700;
        }
    </style>
</head>
<body>

<div class="register-card animate-up">
    <div class="register-header">
        <div class="register-logo">
            <i class="fas fa-user-plus"></i>
        </div>
        <h2>Operator <span class="fw-light">Reg</span></h2>
        <p class="text-muted small">Establish your administrative credentials</p>
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
                <label class="form-label small fw-bold text-muted">WORK EMAIL</label>
                <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">ACCESS PIN (PWD)</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">REPEAT ACCESS PIN</label>
                <input type="password" name="confpassword" class="form-control" required>
            </div>
        </div>
        <button type="submit" class="btn-register">Initialize Profile</button>
    </form>
    
    <div class="footer-links">
        <p>Already authorized? <a href="<?= base_url('auth/login') ?>">Sign In Terminal</a></p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
