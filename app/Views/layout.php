<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= esc($title ?? 'Kuya EDs Meatshop') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #FF2E2E; /* Fast food red */
            --secondary-color: #FFD43B; /* Fast food yellow */
            --brand-yellow-rgb: 255, 212, 59;
            --brand-red-rgb: 255, 46, 46;
            --accent-shadow: 0 2px 18px 0 rgba(255,46,46,0.11);
            --cta-shadow: 0 3px 12px 0 rgba(255,212,59,0.11);
        }
        body {
            background: #fffbe9;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .navbar-brand {
            font-family: 'Bree Serif', cursive, Arial, sans-serif;
            font-size: 2rem;
            font-weight: bold;
            color: #fff;
            letter-spacing: 2px;
            text-shadow: 0 2px 10px rgba(var(--brand-yellow-rgb),0.7);
        }
        .navbar {
            background: linear-gradient(90deg, var(--primary-color) 70%, #b9042b 100%) !important;
            box-shadow: 0 2px 8px 0 rgba(255,46,46,0.15);
            border-bottom: 4px solid var(--secondary-color);
            position: sticky;
            top: 0;
            z-index: 1030;
        }
        .navbar-nav .nav-link {
            color: #fff !important;
            font-weight: 600;
            letter-spacing: 1px;
        }
        .navbar-nav .nav-link.active, .navbar-nav .nav-link.fw-bold {
            color: #FFD43B !important;
            border-bottom: 2px solid #FFD43B;
        }
        .navbar-nav .nav-link:hover {
            color: var(--secondary-color) !important;
            text-shadow: 0 2px 8px rgba(var(--brand-yellow-rgb),0.21);
        }
        .navbar-nav .btn, .btn-primary, .btn-success, .btn-outline-primary {
            font-weight: 700;
            border-radius: 2rem !important;
            padding-left: 2rem;
            padding-right: 2rem;
            font-size: 1.11rem;
        }
        .btn-primary, .btn-success {
            background-color: var(--secondary-color);
            border-color: var(--brand-red-rgb);
            color: #B9042B;
            box-shadow: var(--cta-shadow);
        }
        .btn-primary:hover, .btn-success:hover {
            background-color: #ffe066;
            color: #B9042B;
        }
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: #fff;
        }
        .product-card {
            border-radius: 2rem !important;
            box-shadow: 0 4px 24px 0 rgba(var(--brand-red-rgb),0.07), 0 1.5px 10px 0 rgba(255,212,40,0.09);
            border: 2px solid #FFF4C6;
            position: relative;
        }
        .product-card:hover {
            box-shadow: 0 8px 32px 0 rgba(var(--brand-red-rgb),0.13), 0 2px 18px 0 rgba(255,212,59,0.12);
            border-color: var(--secondary-color);
        }
        .product-image-wrapper {
            border-bottom: 3px solid #FFD43B;
        }
        .badge {
            border-radius: 1.2rem;
            box-shadow: 0 2px 6px 0 rgba(255,212,59,0.09);
            letter-spacing: 0.5px;
        }
        .cart-badge-count {
            background: #ffd43b !important;
            color: #B9042B !important;
            box-shadow: 0 2px 10px 0 rgba(255,212,59,0.18);
            border: 2px solid #fff;
        }
        .btn, .form-control, .input-group-text {
            border-radius: 2rem !important;
        }
        .alert-success {
            background: linear-gradient(90deg,#ffd43b 65%,#ffe679 100%);
            color: #b11f16;
            box-shadow: 0 2px 8px 0 rgba(255,46,46,.08);
            border-radius: 1.7rem;
            border: none;
            font-weight: 600;
        }
        .alert-danger {
            border-radius: 1.7rem;
            font-weight: 500;
        }
        .form-label {
            font-weight: 600;
            color: #b20609;
        }
        .table > :not(:last-child) > :last-child > * {
            border-bottom-color: #FFD43B;
        }
        .table tr {
            border-bottom: 2px solid #fff4c6 !important;
        }
        .card-body {
            background: linear-gradient(90deg,#fff 90%,#fff9e5 100%);
        }
        .product-card, .card {
            transition: transform 0.25s cubic-bezier(.19,1,.22,1), box-shadow 0.22s cubic-bezier(.19,1,.22,1);
        }
        .product-card:hover, .card:hover {
            transform: translateY(-12px) scale(1.04);
            z-index: 2;
        }
        @media (max-width: 768px) {
            .navbar-nav .nav-link { font-size: 1rem; }
            .product-card { border-radius: 26px !important; }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg mb-4 pb-0">
    <?php
    $segment = '';
    try {
        $request = \Config\Services::request();
        $segment = is_object($request->uri) ? $request->uri->getSegment(1) : '';
    } catch (\Throwable $e) {
        $segment = '';
    }
    ?>
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= site_url('/') ?>">Kuya EDs</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link<?= $segment === 'order' ? ' active' : '' ?>" href="<?= site_url('order') ?>">
                        <i class="bi bi-menu-button-wide"></i> Order
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= $segment === 'products' ? ' active' : '' ?>" href="<?= site_url('products') ?>">Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= $segment === 'inventory' ? ' active' : '' ?>" href="<?= site_url('inventory') ?>">Inventory</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= $segment === 'sales' ? ' active' : '' ?>" href="<?= site_url('sales/create') ?>">Record Sale</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= $segment === 'reports' ? ' active' : '' ?>" href="<?= site_url('reports/sales') ?>">Sales Reports</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= $segment === 'reports' ? ' active' : '' ?>" href="<?= site_url('reports/alerts') ?>">Alerts</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link position-relative" href="<?= site_url('order/cart') ?>">
                        <i class="bi bi-cart3" style="font-size: 1.2rem;"></i>
                        <?php 
                        $cart = session()->get('cart') ?? [];
                        $cartCount = 0;
                        foreach ($cart as $item) {
                            $cartCount += $item['quantity'];
                        }
                        ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-badge-count" style="font-size: 0.7rem; <?= $cartCount > 0 ? '' : 'display: none;' ?>">
                            <?= $cartCount > 0 ? $cartCount : '' ?>
                        </span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mb-5">
    <?= $content ?? '' ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


