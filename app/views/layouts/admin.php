<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Dashboard') ?> — Admin PMB Ma'had Aly</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= url('/assets/css/vendor/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= url('/assets/css/vendor/bootstrap-icons.min.css') ?>" rel="stylesheet">
    <link href="<?= url('/assets/css/app.css') ?>" rel="stylesheet">
    <style>
        :root {
            --admin-sidebar-w: 260px;
            --admin-topbar-h: 60px;
            --admin-bg: #f0f4f8;
            --sidebar-bg: #0f2447;
            --sidebar-hover: rgba(201,162,39,.12);
            --sidebar-active: rgba(201,162,39,.20);
            --sidebar-border: rgba(201,162,39,.35);
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--admin-bg); }

        /* SIDEBAR */
        #adminSidebar {
            position: fixed; top: 0; left: 0;
            width: var(--admin-sidebar-w); height: 100vh;
            background: var(--sidebar-bg);
            overflow-y: auto; z-index: 1040;
            transition: transform .3s ease;
            display: flex; flex-direction: column;
        }
        .sidebar-brand {
            padding: 20px 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,.08);
            text-decoration: none;
        }
        .sidebar-brand-icon {
            width: 40px; height: 40px; border-radius: 10px;
            background: linear-gradient(135deg, var(--accent) 0%, #e8b93a 100%);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; color: #fff; font-weight: 700; flex-shrink: 0;
        }
        .sidebar-brand-text { color: #fff; font-weight: 700; font-size: .88rem; line-height: 1.3; }
        .sidebar-brand-sub { color: rgba(255,255,255,.5); font-size: .72rem; }

        .sidebar-nav { padding: 12px 0; flex: 1; }
        .nav-section-label {
            padding: 8px 20px 4px;
            font-size: .65rem; font-weight: 700;
            color: rgba(255,255,255,.3); text-transform: uppercase; letter-spacing: .08em;
        }
        .sidebar-link {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 20px; color: rgba(255,255,255,.7);
            text-decoration: none; font-size: .83rem; font-weight: 500;
            transition: all .2s; border-left: 3px solid transparent;
        }
        .sidebar-link:hover {
            background: var(--sidebar-hover); color: #fff;
            border-left-color: var(--sidebar-border);
        }
        .sidebar-link.active {
            background: var(--sidebar-active); color: #fff;
            border-left-color: var(--accent); font-weight: 600;
        }
        .sidebar-link .bi { font-size: 1rem; width: 20px; text-align: center; }

        /* TOPBAR */
        #adminTopbar {
            position: fixed; top: 0;
            left: var(--admin-sidebar-w); right: 0;
            height: var(--admin-topbar-h);
            background: #fff; border-bottom: 1px solid #e2e8f0;
            display: flex; align-items: center;
            padding: 0 24px; z-index: 1030;
            gap: 12px;
        }
        .topbar-toggle { display: none; }

        /* MAIN */
        #adminMain {
            margin-left: var(--admin-sidebar-w);
            margin-top: var(--admin-topbar-h);
            padding: 24px;
            min-height: calc(100vh - var(--admin-topbar-h));
        }
        .page-header { margin-bottom: 24px; }
        .page-header h1 { font-size: 1.4rem; font-weight: 700; color: var(--primary); margin: 0; }
        .page-header .breadcrumb { margin: 4px 0 0; font-size: .78rem; }

        /* CARDS */
        .stat-card {
            border: none; border-radius: 12px;
            overflow: hidden; transition: transform .2s, box-shadow .2s;
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.1); }
        .stat-card .card-body { padding: 20px; }
        .stat-icon {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
        }

        /* TABLE */
        .admin-table { background: #fff; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; }
        .admin-table .table { margin: 0; }
        .admin-table .table thead th {
            background: #f8fafc; font-size: .75rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .05em; color: #64748b;
            border-bottom: 1px solid #e2e8f0; padding: 12px 16px;
        }
        .admin-table .table tbody td { padding: 12px 16px; font-size: .85rem; vertical-align: middle; }
        .admin-table .table tbody tr:hover { background: #f8fafc; }

        /* OVERLAY */
        #sidebarOverlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.5); z-index: 1039;
        }

        @media (max-width: 991px) {
            #adminSidebar { transform: translateX(-100%); }
            #adminSidebar.show { transform: translateX(0); }
            #adminMain { margin-left: 0; }
            #adminTopbar { left: 0; }
            .topbar-toggle { display: flex; }
            #sidebarOverlay.show { display: block; }
        }
    </style>
</head>
<body>

<!-- Sidebar Overlay (mobile) -->
<div id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- SIDEBAR -->
<nav id="adminSidebar">
    <a href="<?= url('/admin') ?>" class="sidebar-brand d-flex align-items-center gap-2">
        <div class="sidebar-brand-icon">M</div>
        <div>
            <div class="sidebar-brand-text">Ma'had Aly Ibnu Abbas</div>
            <div class="sidebar-brand-sub">Admin Panel PMB</div>
        </div>
    </a>

    <div class="sidebar-nav">
        <?php $uri = $_SERVER['REQUEST_URI']; ?>

        <div class="nav-section-label">Utama</div>
        <a href="<?= url('/admin') ?>" class="sidebar-link <?= $uri === '/admin' ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2"></i> Dashboard
        </a>

        <div class="nav-section-label">Pendaftaran</div>
        <a href="<?= url('/admin/pendaftar') ?>" class="sidebar-link <?= strpos($uri,'/admin/pendaftar') === 0 ? 'active' : '' ?>">
            <i class="bi bi-people"></i> Data Pendaftar
        </a>
        <a href="<?= url('/admin/pendaftar', ['status'=>'menunggu']) ?>" class="sidebar-link <?= strpos($uri,'/admin/verifikasi') === 0 ? 'active' : '' ?>">
            <i class="bi bi-shield-check"></i> Verifikasi Berkas
        </a>
        <a href="<?= url('/admin/export') ?>" class="sidebar-link <?= strpos($uri,'/admin/export') === 0 ? 'active' : '' ?>">
            <i class="bi bi-download"></i> Export Data
        </a>

        <div class="nav-section-label">Pengaturan</div>
        <a href="<?= url('/admin/tahun-akademik') ?>" class="sidebar-link <?= strpos($uri,'/admin/tahun-akademik') === 0 ? 'active' : '' ?>">
            <i class="bi bi-calendar3"></i> Tahun Akademik
        </a>
        <a href="<?= url('/admin/prodi') ?>" class="sidebar-link <?= strpos($uri,'/admin/prodi') === 0 ? 'active' : '' ?>">
            <i class="bi bi-mortarboard"></i> Program Studi
        </a>
        <a href="<?= url('/admin/biaya') ?>" class="sidebar-link <?= strpos($uri,'/admin/biaya') === 0 ? 'active' : '' ?>">
            <i class="bi bi-currency-dollar"></i> Biaya
        </a>
        <a href="<?= url('/admin/persyaratan') ?>" class="sidebar-link <?= strpos($uri,'/admin/persyaratan') === 0 ? 'active' : '' ?>">
            <i class="bi bi-list-check"></i> Persyaratan
        </a>
        <a href="<?= url('/admin/pengaturan') ?>" class="sidebar-link <?= strpos($uri,'/admin/pengaturan') === 0 ? 'active' : '' ?>">
            <i class="bi bi-gear"></i> Pengaturan CMS
        </a>
        <?php if (Session::get('role') === 'superadmin'): ?>
        <a href="<?= url('/admin/users') ?>" class="sidebar-link <?= strpos($uri,'/admin/users') === 0 ? 'active' : '' ?>">
            <i class="bi bi-person-gear"></i> Manajemen User
        </a>
        <?php endif; ?>
    </div>

    <div class="p-3 border-top" style="border-color:rgba(255,255,255,.08)!important;">
        <a href="<?= url('/') ?>" target="_blank" class="sidebar-link" style="font-size:.78rem; padding: 7px 8px;">
            <i class="bi bi-box-arrow-up-right"></i> Lihat Website
        </a>
        <a href="<?= url('/logout') ?>" class="sidebar-link" style="font-size:.78rem; padding: 7px 8px; color:rgba(255,100,100,.8);">
            <i class="bi bi-power"></i> Keluar
        </a>
    </div>
</nav>

<!-- TOPBAR -->
<header id="adminTopbar">
    <button class="btn btn-sm btn-light topbar-toggle" onclick="openSidebar()">
        <i class="bi bi-list fs-5"></i>
    </button>
    <div class="flex-fill">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size:.78rem;">
                <li class="breadcrumb-item"><a href="<?= url('/admin') ?>" class="text-decoration-none">Admin</a></li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($page_title ?? '') ?></li>
            </ol>
        </nav>
    </div>
    <div class="d-flex align-items-center gap-3">
        <div class="d-none d-md-block text-end">
            <div style="font-size:.82rem; font-weight:600; color:var(--primary);">
                <?= htmlspecialchars(Session::get('nama') ?? Session::get('username') ?? '') ?>
            </div>
            <div style="font-size:.72rem; color:#64748b; text-transform:uppercase;">
                <?= htmlspecialchars(Session::get('role') ?? '') ?>
            </div>
        </div>
        <div style="width:34px;height:34px;border-radius:50%;background:var(--primary);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.8rem;font-weight:700;">
            <?= strtoupper(substr(Session::get('nama') ?? Session::get('username') ?? 'A', 0, 1)) ?>
        </div>
    </div>
</header>

<!-- MAIN CONTENT -->
<main id="adminMain">

    <?php if ($flash = Session::getFlash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 mb-4" role="alert">
        <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($flash) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if ($flash = Session::getFlash('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($flash) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if ($flash = Session::getFlash('info')): ?>
    <div class="alert alert-info alert-dismissible fade show border-0 rounded-3 mb-4" role="alert">
        <i class="bi bi-info-circle me-2"></i><?= htmlspecialchars($flash) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?= $content ?>
</main>

<script src="<?= url('/assets/js/bootstrap.bundle.min.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script src="<?= url('/assets/js/app.js') ?>"></script>
<script>
function openSidebar() {
    document.getElementById('adminSidebar').classList.add('show');
    document.getElementById('sidebarOverlay').classList.add('show');
}
function closeSidebar() {
    document.getElementById('adminSidebar').classList.remove('show');
    document.getElementById('sidebarOverlay').classList.remove('show');
}
</script>
<?php if (isset($extra_js)): ?>
<?= $extra_js ?>
<?php endif; ?>
</body>
</html>
