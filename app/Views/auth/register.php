<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - To-Do List</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #153677, #4e085f);
        }

        .auth-card {
            background: #fff;
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 380px;
            text-align: center;
        }

        .auth-card h2 {
            color: #002765;
            margin-bottom: 30px;
            font-weight: 600;
            font-size: 28px;
        }

        .auth-input {
            width: 100%;
            border: 1.5px solid #e1e4e8;
            padding: 12px 20px;
            border-radius: 12px;
            margin-bottom: 15px;
            background: #fdfdfd;
            outline: none;
            transition: all 0.3s ease;
            font-size: 15px;
        }

        .auth-input:focus {
            border-color: #ff5945;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(255, 89, 69, 0.1);
        }

        .btn-auth {
            background: #ff5945;
            color: white;
            padding: 12px;
            border-radius: 12px;
            border: none;
            width: 100%;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn-auth:hover {
            background: #e04836;
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(255, 89, 69, 0.3);
        }

        .auth-footer {
            margin-top: 25px;
            font-size: 14px;
            color: #666;
        }

        .auth-footer a {
            color: #ff5945;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="auth-card">
    <h2>Sign Up</h2>
    
    <?php if(session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger p-2 mb-3" style="font-size: 14px;">
            <?php foreach(session()->getFlashdata('errors') as $error): ?>
                <p><?= esc($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('auth/registerProcess') ?>" method="POST">
        <input type="text" name="username" class="auth-input" placeholder="Username" value="<?= old('username') ?>" required>
        <input type="email" name="email" class="auth-input" placeholder="Email Address" value="<?= old('email') ?>" required>
        <input type="password" name="password" class="auth-input" placeholder="Password" required>
        <input type="password" name="confpassword" class="auth-input" placeholder="Confirm Password" required>
        <button type="submit" class="btn-auth">Register</button>
    </form>

    <div class="auth-footer">
        Already have an account? <a href="<?= base_url('auth/login') ?>">Login</a>
        <div class="mt-3">
            <a href="<?= base_url('/') ?>" style="color: #666; font-size: 13px;">&larr; Back to Home</a>
        </div>
    </div>
</div>

</body>
</html>
