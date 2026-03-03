<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galaxy Pharmacy | Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #0ea5e9;
            --primary-dark: #0284c7;
            --secondary: #6366f1;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #06b6d4;
            --dark: #0f172a;
            --light-bg: #f8fafc;
            --sidebar-grad: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            --card-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.03), 0 10px 10px -5px rgba(0, 0, 0, 0.01);
            --neon-shadow: 0 0 20px rgba(14, 165, 233, 0.2);
        }

        .text-orange { color: #f97316; }

        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .animate-wow { animation: fadeInUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }
        .animate-up { animation: fadeInUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--light-bg);
            color: #334155;
            overflow-x: hidden;
        }

        .bg-blob {
            position: fixed;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.08) 0%, rgba(99, 102, 241, 0.05) 100%);
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
            display: flex;
            flex-direction: column;
        }

        .sidebar-content {
            flex: 1;
            overflow-y: hidden;
            display: flex;
            flex-direction: column;
        }

        #sidebar:hover .sidebar-content {
            overflow-y: auto;
        }

        /* Custom slim scrollbar for sidebar content */
        .sidebar-content::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-content::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-content::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
        }
        .sidebar-content::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }


        .sidebar-header {
            padding: 40px 30px;
            text-align: left;
        }

        .sidebar-header h3 {
            font-weight: 800;
            font-size: 1.8rem;
            letter-spacing: -1px;
            background: linear-gradient(to right, #0ea5e9, #a5b4fc);
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
            background: linear-gradient(90deg, var(--primary) 0%, rgba(14, 165, 233, 0.5) 100%);
            box-shadow: var(--neon-shadow);
        }

        #sidebar ul li a:hover i {
            transform: translateX(3px);
        }

        .dropdown-toggle::after {
            content: '\f107';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            margin-left: auto;
            transition: transform 0.3s;
            font-size: 0.8rem;
        }
        .dropdown-toggle[aria-expanded="true"]::after {
            transform: rotate(180deg);
        }
        
        .sub-menu {
            margin-left: 15px;
            margin-top: 5px;
            border-left: 2px solid rgba(255, 255, 255, 0.1);
        }
        .sub-menu li a {
            padding: 10px 20px !important;
            font-size: 0.9rem !important;
            border-radius: 12px !important;
        }

        #content {
            flex: 1;
            margin-left: 280px;
            padding: 30px 50px;
            transition: all 0.4s;
        }

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

        .premium-card, .premium-list {
            background: #ffffff;
            border-radius: 28px;
            box-shadow: 0 10px 40px -12px rgba(0, 0, 0, 0.05), 0 4px 15px -5px rgba(0, 0, 0, 0.02);
            border: 1px solid #eef2f6;
            overflow: visible;
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            padding: 24px;
        }

        .transition-all { transition: all 0.3s ease; }
        .hover-lift:hover { transform: translateY(-5px); box-shadow: 0 15px 30px -10px rgba(0,0,0,0.1) !important; }

        .premium-card::after, .premium-list::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #0ea5e9, #6366f1);
            opacity: 0.9;
            border-radius: 28px 28px 0 0;
        }

        .premium-card:hover, .premium-list:hover {
            transform: translateY(-8px);
            box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.1);
            border-color: #e2e8f0;
        }

        .premium-table-card {
            background: #ffffff;
            border-radius: 32px;
            box-shadow: 0 10px 40px -12px rgba(0, 0, 0, 0.05), 0 4px 15px -5px rgba(0, 0, 0, 0.02);
            border: 1px solid #f1f5f9;
            overflow: visible;
            position: relative;
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        .premium-table-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.1);
        }

        .table thead th {
            background: #f8fafc;
            padding: 20px;
            font-weight: 700;
            color: #0f172a;
            border-bottom: 2px solid #f1f5f9;
        }

        .table tbody tr {
            background-color: #ffffff;
            color: #334155;
            transition: all 0.2s;
        }
        
        .table tbody tr:hover {
            background-color: #f8fafc !important;
        }

        .table tbody td {
            padding: 20px;
            border-bottom: 1px solid #f1f5f9;
        }



        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

        .glass-card {
            background: #ffffff;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 10px 40px -12px rgba(0, 0, 0, 0.05);
            border: 1px solid #eef2f6;
            position: relative;
        }

        .glass-card::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 5px;
            background: linear-gradient(90deg, #0ea5e9, #6366f1);
        }

        .card-header-premium {
            padding: 22px 28px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
        }

        .btn-premium {
            background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 100%);
            color: white;
            border: none;
            padding: 11px 24px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            box-shadow: 0 8px 20px -5px rgba(14, 165, 233, 0.3);
        }

        .btn-premium:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 15px 25px -5px rgba(14, 165, 233, 0.45);
            color: white;
        }

        .btn-outline-emerald {
            color: var(--success);
            border: 1px solid var(--success);
            background: transparent;
            border-radius: 20px;
            padding: 6px 16px;
            font-size: 0.85rem;
            transition: all 0.3s;
        }

        .btn-outline-emerald:hover {
            background: var(--success);
            color: white;
        }

        /* Fix Modal Shaking */
        body.modal-open {
            padding-right: 0 !important;
            overflow: hidden;
        }
        .modal {
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(8px);
        }

        /* Dark Mode */
        body.dark-mode {
            background-color: #0f172a;
            color: #cbd5e1;
        }
        body.dark-mode #sidebar { background: linear-gradient(180deg, #020617 0%, #0f172a 100%); }
        body.dark-mode .glass-nav { background: rgba(15,23,42,0.8); border-color: rgba(255,255,255,0.05); }
        body.dark-mode .premium-card,
        body.dark-mode .premium-list,
        body.dark-mode .glass-card,
        body.dark-mode .kpi-card-premium,
        body.dark-mode .table-container { background: #1e293b; border-color: #334155; }
        body.dark-mode .table tbody tr { background-color: #1e293b; color: #cbd5e1; }
        body.dark-mode .table thead th { background: #0f172a; color: #94a3b8; }
        body.dark-mode .table tbody tr:hover { background-color: #334155 !important; }
        body.dark-mode .bg-light { background-color: #0f172a !important; }
        body.dark-mode .bg-white { background-color: #1e293b !important; }
        body.dark-mode .text-dark { color: #e2e8f0 !important; }
        body.dark-mode .form-control, body.dark-mode .form-select { background: #0f172a; border-color: #334155; color: #cbd5e1; }
        body.dark-mode .user-chip { background: #1e293b; border-color: #334155; }

        /* Expiry Alert Banner */
        .expiry-alert-bar {
            background: linear-gradient(90deg, #ef4444, #dc2626);
            color: white;
            padding: 12px 24px;
            border-radius: 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            animation: fadeInUp 0.5s ease;
        }
        .expiry-alert-bar .badge-count {
            background: rgba(255,255,255,0.2);
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>

<div class="bg-blob blob-1"></div>
<div class="bg-blob blob-2"></div>

<div id="wrapper">
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3><?= esc(get_setting('pharmacy_name', 'Galaxy Pharmacy')) ?></h3>
        </div>

        <div class="sidebar-content">
            <ul class="list-unstyled components">
                <li class="<?= url_is('analytics') ? 'active' : '' ?>">
                    <a href="<?= base_url('analytics') ?>"><i class="fas fa-chart-pie"></i> Business Intelligence</a>
                </li>
                <li class="<?= url_is('/') ? 'active' : '' ?>">
                    <a href="<?= base_url('/') ?>"><i class="fas fa-house"></i> Dashboard</a>
                </li>
                <li class="<?= url_is('products*') ? 'active' : '' ?>">
                    <a href="#productsSubmenu" data-bs-toggle="collapse" class="dropdown-toggle <?= url_is('products*') ? '' : 'collapsed' ?>">
                        <i class="fas fa-capsules"></i> Products
                    </a>
                    <ul class="collapse list-unstyled sub-menu <?= url_is('products*') ? 'show' : '' ?>" id="productsSubmenu">
                        <li>
                            <a href="<?= base_url('products') ?>" class="<?= url_is('products') ? 'text-white fw-bold' : '' ?>">
                                <i class="fas fa-list small me-2"></i> All Products
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('products/add') ?>" class="<?= url_is('products/add') ? 'text-white fw-bold' : '' ?>">
                                <i class="fas fa-plus small me-2"></i> Add Product
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('products/shortage') ?>" class="<?= url_is('products/shortage') ? 'text-white fw-bold' : '' ?>">
                                <i class="fas fa-exclamation-circle small me-2 text-warning"></i> Shortage List
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="<?= url_is('categories') ? 'active' : '' ?>">
                    <a href="<?= base_url('categories') ?>"><i class="fas fa-shapes"></i> Categories</a>
                </li>
                <li class="<?= url_is('vendors') ? 'active' : '' ?>">
                    <a href="<?= base_url('vendors') ?>"><i class="fas fa-truck"></i> Vendors</a>
                </li>
                <!-- Purchases Module -->
                <li class="<?= url_is('purchases*') ? 'active' : '' ?>">
                    <a href="#purchaseSubmenu" data-bs-toggle="collapse" class="dropdown-toggle <?= url_is('purchases*') ? '' : 'collapsed' ?>">
                        <i class="fas fa-cart-shopping"></i> Purchases
                    </a>
                    <ul class="collapse list-unstyled sub-menu <?= url_is('purchases*') ? 'show' : '' ?>" id="purchaseSubmenu">
                        <li>
                            <a href="<?= base_url('purchases/select_vendor') ?>" class="<?= url_is('purchases/select_vendor') ? 'text-white fw-bold' : '' ?>">
                                <i class="fas fa-plus small me-2"></i> Record Purchase
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('purchases') ?>" class="<?= url_is('purchases') ? 'text-white fw-bold' : '' ?>">
                                <i class="fas fa-history small me-2"></i> Purchase Log
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('purchases/dues') ?>" class="<?= url_is('purchases/dues') ? 'text-white fw-bold' : '' ?>">
                                <i class="fas fa-hand-holding-dollar small me-2"></i> Vendor Dues
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Sales Module -->
                <li class="<?= url_is('sales*') ? 'active' : '' ?>">
                    <a href="#salesSubmenu" data-bs-toggle="collapse" class="dropdown-toggle <?= url_is('sales*') ? '' : 'collapsed' ?>">
                        <i class="fas fa-shopping-bag"></i> Sales & Stock
                    </a>
                    <ul class="collapse list-unstyled sub-menu <?= url_is('sales*') ? 'show' : '' ?>" id="salesSubmenu">
                        <li>
                            <a href="<?= base_url('sales') ?>" class="<?= url_is('sales') ? 'text-white fw-bold' : '' ?>">
                                <i class="fas fa-cash-register small me-2"></i> Sales Terminal
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('sales/inventory') ?>" class="<?= url_is('sales/inventory') ? 'text-white fw-bold' : '' ?>">
                                <i class="fas fa-boxes small me-2"></i> Available Stock
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('sales/report') ?>" class="<?= url_is('sales/report') ? 'text-white fw-bold' : '' ?>">
                                <i class="fas fa-chart-pie small me-2"></i> Sales Report
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('sales/history') ?>" class="<?= url_is('sales/history') ? 'text-white fw-bold' : '' ?>">
                                <i class="fas fa-history small me-2"></i> Sales History
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="<?= url_is('doctors*') ? 'active' : '' ?>">
                    <a href="#doctorSubmenu" data-bs-toggle="collapse" class="<?= !url_is('doctors*') ? 'collapsed' : '' ?> dropdown-toggle">
                        <i class="fas fa-user-md"></i> Doctor Hub
                    </a>
                    <ul class="collapse list-unstyled <?= url_is('doctors*') ? 'show' : '' ?>" id="doctorSubmenu">
                        <li>
                            <a href="<?= base_url('doctors') ?>" class="<?= url_is('doctors') ? 'text-white fw-bold' : '' ?>">
                                <i class="fas fa-list small me-2"></i> Doctor Network
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('doctors/payments') ?>" class="<?= url_is('doctors/payments') ? 'text-white fw-bold' : '' ?>">
                                <i class="fas fa-receipt small me-2"></i> Payment Logs
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="<?= url_is('expenses') ? 'active' : '' ?>">
                    <a href="<?= base_url('expenses') ?>"><i class="fas fa-wallet"></i> Expenses</a>
                </li>
                
                <div style="margin-top: 30px; padding: 0 10px; margin-bottom: 20px;">
                    <li class="<?= url_is('settings') ? 'active' : '' ?>">
                        <a href="<?= base_url('settings') ?>" style="background: rgba(99, 102, 241, 0.1); color: var(--indigo); margin-bottom: 8px;">
                            <i class="fas fa-cog"></i> System Settings
                        </a>
                    </li>
                    <li class="<?= url_is('auth/profile') ? 'active' : '' ?>">
                        <a href="<?= base_url('auth/profile') ?>" style="background: rgba(99, 102, 241, 0.1); color: var(--indigo); margin-bottom: 8px;">
                            <i class="fas fa-user-cog"></i> Profile Settings
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('auth/logout') ?>" style="background: rgba(239, 68, 68, 0.1); color: var(--danger);">
                            <i class="fas fa-power-off"></i> Logout
                        </a>
                    </li>
                </div>
            </ul>
        </div>
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
                        <i class="fas fa-circle-check text-success me-2"></i>System Active
                    </span>
                </div>
                <div class="user-chip">
                    <i class="fas fa-moon text-muted" id="darkModeToggle" style="cursor:pointer;" title="Toggle Dark Mode"></i>
                    <div class="vr mx-1" style="height: 15px;"></div>
                    <?php
                        // Count expiring soon items (within 60 days) - Using dynamic stock check
                        $db_temp = \Config\Database::connect();
                        $expiring_count = $db_temp->query("SELECT COUNT(*) as cnt FROM stock_purchase 
                                                         WHERE (initial_qty - (SELECT COALESCE(SUM(qty), 0) FROM sales WHERE stock_id = stock_purchase.id)) > 0 
                                                         AND expiry_date <= DATE_ADD(NOW(), INTERVAL 60 DAY)")->getRow()->cnt ?? 0;
                    ?>
                    <i class="fas fa-bell text-muted position-relative" style="cursor:pointer;" title="<?= $expiring_count ?> medicines expiring soon">
                        <?php if($expiring_count > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:8px;padding:2px 4px;"><?= $expiring_count ?></span>
                        <?php endif; ?>
                    </i>
                </div>
            </div>
        </header>

        <div class="container-fluid p-0">
            <?php
            // Expiry warning banner (medicines expiring in 30 days) - Using dynamic stock check
            $db_exp = \Config\Database::connect();
            $soon_expiring = $db_exp->query("SELECT COUNT(*) as cnt FROM stock_purchase 
                                            WHERE (initial_qty - (SELECT COALESCE(SUM(qty), 0) FROM sales WHERE stock_id = stock_purchase.id)) > 0 
                                            AND expiry_date <= DATE_ADD(NOW(), INTERVAL 30 DAY) 
                                            AND expiry_date >= NOW()")->getRow()->cnt ?? 0;
                                            
            $already_expired = $db_exp->query("SELECT COUNT(*) as cnt FROM stock_purchase 
                                             WHERE (initial_qty - (SELECT COALESCE(SUM(qty), 0) FROM sales WHERE stock_id = stock_purchase.id)) > 0 
                                             AND expiry_date < NOW()")->getRow()->cnt ?? 0;
        ?>
        <?php if($already_expired > 0): ?>
            <div class="expiry-alert-bar">
                <i class="fas fa-skull-crossbones fa-lg"></i>
                <div>
                    <strong>EXPIRED STOCK ALERT!</strong>
                    You have <span class="badge-count"><?= $already_expired ?> batch(es)</span> that have already <strong>expired</strong> but still have stock. Remove them immediately!
                </div>
                <a href="<?= base_url('sales/inventory') ?>" class="btn btn-sm btn-light rounded-pill ms-auto px-3">View Stock</a>
                <button class="btn btn-sm btn-outline-light rounded-pill" onclick="this.parentElement.style.display='none'"><i class="fas fa-times"></i></button>
            </div>
        <?php endif; ?>
        <?php if($soon_expiring > 0): ?>
            <div class="expiry-alert-bar" style="background: linear-gradient(90deg, #f59e0b, #d97706);">
                <i class="fas fa-triangle-exclamation fa-lg"></i>
                <div>
                    <strong>Expiry Warning!</strong>
                    <span class="badge-count"><?= $soon_expiring ?> batch(es)</span> will expire within <strong>30 days</strong>. Sell or return them soon.
                </div>
                <a href="<?= base_url('sales/inventory') ?>" class="btn btn-sm btn-light rounded-pill ms-auto px-3">View Stock</a>
                <button class="btn btn-sm btn-outline-light rounded-pill" onclick="this.parentElement.style.display='none'"><i class="fas fa-times"></i></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success border-0 shadow-lg animate-wow rounded-4 p-3 d-flex align-items-center mb-4" role="alert">
                    <div class="bg-success rounded-circle p-2 me-3 text-white"><i class="fas fa-check"></i></div>
                    <div><?= session()->getFlashdata('success') ?></div>
                    <?php if(session()->getFlashdata('last_sale_id')): ?>
                        <a href="<?= base_url('sales/invoice/'.session()->getFlashdata('last_sale_id')) ?>" target="_blank" class="btn btn-sm btn-dark rounded-pill ms-3 px-3">
                            <i class="fas fa-print me-2"></i> Print Invoice
                        </a>
                    <?php endif; ?>
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
<script>
    // Dark Mode Toggle
    const darkToggle = document.getElementById('darkModeToggle');
    if (localStorage.getItem('darkMode') === 'on') {
        document.body.classList.add('dark-mode');
        if (darkToggle) darkToggle.classList.replace('fa-moon', 'fa-sun');
    }
    if (darkToggle) {
        darkToggle.addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            localStorage.setItem('darkMode', isDark ? 'on' : 'off');
            this.classList.toggle('fa-moon', !isDark);
            this.classList.toggle('fa-sun', isDark);
        });
    }

    // Phone Number Input Restriction (11 Digits, Numeric only)
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('phone-input') || e.target.name === 'phone' || e.target.name === 'customer_phone' || e.target.name === 'pharmacy_phone') {
            // Remove non-numeric characters
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
            // Limit to 11 digits
            if (e.target.value.length > 11) {
                e.target.value = e.target.value.slice(0, 11);
            }
        }
    });
</script>
</body>
</html>
