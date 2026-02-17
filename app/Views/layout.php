<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galaxy Pharmacy | Pro Management</title>
    <!-- Google Fonts: Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #a855f7;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #06b6d4;
            --dark: #1e293b;
            --light-bg: #f8fafc;
            --sidebar-grad: linear-gradient(180deg, #1e1b4b 0%, #312e81 100%);
            --card-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
            --neon-shadow: 0 0 20px rgba(99, 102, 241, 0.3);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--light-bg);
            color: #334155;
            overflow-x: hidden;
        }

        /* Animated Background Elements */
        .bg-blob {
            position: fixed;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.08) 0%, rgba(168, 85, 247, 0.05) 100%);
            filter: blur(80px);
            border-radius: 50%;
            z-index: -1;
        }
        .blob-1 { top: -200px; right: -100px; }
        .blob-2 { bottom: -200px; left: -100px; }

        #wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        #sidebar {
            min-width: 280px;
            max-width: 280px;
            background: var(--sidebar-grad);
            color: #fff;
            position: fixed;
            height: 100vh;
            z-index: 1000;
            transition: all 0.4s;
            box-shadow: 10px 0 30px rgba(0,0,0,0.1);
        }

        .sidebar-header {
            padding: 40px 30px;
            text-align: left;
        }

        .sidebar-header h3 {
            font-weight: 800;
            font-size: 1.8rem;
            letter-spacing: -1px;
            background: linear-gradient(to right, #fff, #a5b4fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
        }

        #sidebar ul.components {
            padding: 10px 20px;
        }

        #sidebar ul li { margin-bottom: 5px; }

        #sidebar ul li a {
            padding: 14px 20px;
            font-size: 1rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            border-radius: 16px;
            transition: all 0.3s;
        }

        #sidebar ul li a i {
            margin-right: 15px;
            font-size: 1.2rem;
            transition: transform 0.3s;
        }

        #sidebar ul li.active > a, #sidebar ul li a:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
        }

        #sidebar ul li.active > a {
            background: linear-gradient(90deg, var(--primary) 0%, rgba(99, 102, 241, 0.5) 100%);
            box-shadow: var(--neon-shadow);
        }

        #sidebar ul li a:hover i {
            transform: translateX(3px);
        }

        /* Content Area */
        #content {
            flex: 1;
            margin-left: 280px;
            padding: 30px 50px;
            transition: all 0.4s;
        }

        /* Glass Floating Navbar */
        .glass-nav {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border-radius: 24px;
            padding: 15px 30px;
            margin-bottom: 40px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: var(--card-shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .user-chip {
            background: white;
            padding: 8px 20px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
        }

        .avatar {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.8rem;
        }

        /* Premium Forms & Inputs */
        .form-control, .form-select {
            border-radius: 12px;
            padding: 12px 18px;
            border: 2px solid #f1f5f9;
            background: #f8fafc;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .btn-vibrant {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            padding: 12px 28px;
            border-radius: 16px;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.4);
        }

        .btn-vibrant:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 20px 25px -5px rgba(99, 102, 241, 0.5);
            color: white;
        }

        .premium-table-card {
            background: white;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            border: 1px solid #f1f5f9;
        }

        .table thead th {
            background: #f8fafc;
            padding: 20px;
            font-weight: 700;
            color: var(--dark);
            border-bottom: 2px solid #f1f5f9;
        }

        .table tbody td {
            padding: 20px;
            border-bottom: 1px solid #f1f5f9;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-wow {
            animation: slideUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body>

<div class="bg-blob blob-1"></div>
<div class="bg-blob blob-2"></div>

<div id="wrapper">
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3>Galaxy <span class="fw-light">Pharmacy</span></h3>
        </div>

        <ul class="list-unstyled components">
            <li class="<?= (url_is('products') || url_is('/')) ? 'active' : '' ?>">
                <a href="<?= base_url('products') ?>"><i class="fas fa-grid-2"></i> Dashboard</a>
            </li>
            <li class="<?= url_is('categories') ? 'active' : '' ?>">
                <a href="<?= base_url('categories') ?>"><i class="fas fa-shapes"></i> Categories</a>
            </li>
            <li class="<?= url_is('vendors') ? 'active' : '' ?>">
                <a href="<?= base_url('vendors') ?>"><i class="fas fa-handshake"></i> Suppliers</a>
            </li>
            <li class="<?= url_is('stocks/purchase') ? 'active' : '' ?>">
                <a href="<?= base_url('stocks/purchase') ?>"><i class="fas fa-box-open"></i> Inventory Log</a>
            </li>
            <li class="<?= url_is('stocks/sales') ? 'active' : '' ?>">
                <a href="<?= base_url('stocks/sales') ?>"><i class="fas fa-shopping-bag"></i> Retail POS</a>
            </li>
            <li class="<?= url_is('stocks/report') ? 'active' : '' ?>">
                <a href="<?= base_url('stocks/report') ?>"><i class="fas fa-chart-pie"></i> Sales Audit</a>
            </li>
            
            <div style="margin-top: 100px; padding: 0 10px;">
                <li class="mt-auto">
                    <a href="<?= base_url('auth/logout') ?>" style="background: rgba(239, 68, 68, 0.1); color: var(--danger);">
                        <i class="fas fa-power-off"></i> Secure Logout
                    </a>
                </li>
            </div>
        </ul>
    </nav>

    <div id="content">
        <header class="glass-nav animate-wow">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar"><?= substr(session()->get('username') ?: 'A', 0, 1) ?></div>
                <div class="lh-1">
                    <h5 class="m-0 fw-bold">Hello, <?= session()->get('username') ?: 'Guest' ?></h5>
                    <small class="text-muted"><?= date('l, d M Y') ?></small>
                </div>
            </div>
            
            <div class="d-flex gap-4 align-items-center">
                <div class="d-none d-lg-block">
                    <span class="badge rounded-pill bg-light text-dark py-2 px-3 border">
                        <i class="fas fa-shield-halved text-primary me-2"></i>System Active
                    </span>
                </div>
                <div class="user-chip">
                    <i class="fas fa-moon text-muted"></i>
                    <div class="vr mx-1" style="height: 15px;"></div>
                    <i class="fas fa-bell text-muted"></i>
                </div>
            </div>
        </header>

        <div class="container-fluid p-0">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success border-0 shadow-lg animate-wow rounded-4 p-3 d-flex align-items-center mb-4" role="alert">
                    <div class="bg-success rounded-circle p-2 me-3 text-white"><i class="fas fa-check"></i></div>
                    <div><?= session()->getFlashdata('success') ?></div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger border-0 shadow-lg animate-wow rounded-4 p-3 d-flex align-items-center mb-4" role="alert">
                    <div class="bg-danger rounded-circle p-2 me-3 text-white"><i class="fas fa-xmark"></i></div>
                    <div><?= session()->getFlashdata('error') ?></div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>
