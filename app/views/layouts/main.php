<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= Security::clean($meta_desc ?? 'Penerimaan Mahasiswa Baru ' . ($settings['site_name'] ?? APP_NAME)) ?>">
  <title><?= Security::clean($page_title ?? 'PMB') ?> | <?= Security::clean($settings['site_name'] ?? APP_NAME) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= url('/assets/css/vendor/bootstrap.min.css') ?>">
  <link rel="stylesheet" href="<?= url('/assets/css/vendor/bootstrap-icons.min.css') ?>">
  <link rel="stylesheet" href="<?= url('/assets/css/app.css') ?>">
  <?= $extra_head ?? '' ?>
</head>
<body class="site-body">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-main sticky-top" id="mainNav">
  <div class="container">
    <a class="navbar-brand" href="<?= BASE_URL ?>">
      <img src="<?= url('/assets/images/logo.png') ?>" alt="<?= Security::clean($settings['site_name'] ?? APP_NAME) ?>" height="48" onerror="this.style.display='none'">
      <span class="brand-text"><?= Security::clean($settings['site_name'] ?? APP_NAME) ?></span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <i class="bi bi-list"></i>
    </button>
    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
        <li class="nav-item"><a class="nav-link" href="<?= url('/') . '#program' ?>">Program Studi</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= url('/') . '#biaya' ?>">Biaya</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= url('/') . '#alur' ?>">Alur Daftar</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= url('/') . '#faq' ?>">FAQ</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= url('/') . '#kontak' ?>">Kontak</a></li>
        <?php if (Auth::check()): ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
            <span class="avatar-sm"><i class="bi bi-person-circle"></i></span>
            <?= Security::clean(Auth::user()['nama'] ?? '') ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <?php if (Auth::is('pendaftar')): ?>
            <li><a class="dropdown-item" href="<?= url('/pendaftar') ?>"><i class="bi bi-speedometer2 me-2"></i>Dashboard Saya</a></li>
            <?php else: ?>
            <li><a class="dropdown-item" href="<?= url('/admin') ?>"><i class="bi bi-speedometer2 me-2"></i>Admin Panel</a></li>
            <?php endif; ?>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="<?= url('/logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a></li>
          </ul>
        </li>
        <?php else: ?>
        <li class="nav-item"><a class="nav-link" href="<?= url('/login') ?>">Masuk</a></li>
        <li class="nav-item">
          <a class="btn btn-primary-gold ms-2" href="<?= url('/daftar') ?>">
            <i class="bi bi-pencil-square me-1"></i> Daftar Sekarang
          </a>
        </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- Flash Messages -->
<?php
$flashSuccess = Session::getFlash('success');
$flashError   = Session::getFlash('error');
$flashInfo    = Session::getFlash('info');
?>
<?php if ($flashSuccess || $flashError || $flashInfo): ?>
<div class="container mt-3">
  <?php if ($flashSuccess): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i><?= Security::clean($flashSuccess) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>
  <?php if ($flashError): ?>
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-circle-fill me-2"></i><?= Security::clean($flashError) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>
  <?php if ($flashInfo): ?>
  <div class="alert alert-info alert-dismissible fade show" role="alert">
    <i class="bi bi-info-circle-fill me-2"></i><?= Security::clean($flashInfo) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>
</div>
<?php endif; ?>

<!-- Main Content -->
<main>
  <?= $content ?? '' ?>
</main>

<!-- Footer -->
<footer class="site-footer mt-5">
  <div class="container">
    <div class="row g-4 py-5">
      <div class="col-lg-4">
        <img src="<?= url('/assets/images/logo.png') ?>" alt="Logo" height="56" class="mb-3" onerror="this.style.display='none'">
        <h5 class="footer-brand"><?= Security::clean($settings['site_name'] ?? APP_NAME) ?></h5>
        <p class="footer-tagline small"><?= Security::clean($settings['site_tagline'] ?? '') ?></p>
        <?php if (!empty($settings['site_kerjasama'])): ?>
        <p class="small text-muted"><?= Security::clean($settings['site_kerjasama']) ?></p>
        <?php endif; ?>
      </div>
      <div class="col-lg-2 col-6">
        <h6 class="footer-heading">Navigasi</h6>
        <ul class="list-unstyled footer-links">
          <li><a href="<?= BASE_URL ?>">Beranda</a></li>
          <li><a href="<?= url('/') . '#program' ?>">Program Studi</a></li>
          <li><a href="<?= url('/') . '#biaya' ?>">Biaya Pendidikan</a></li>
          <li><a href="<?= url('/') . '#alur' ?>">Alur Pendaftaran</a></li>
          <li><a href="<?= url('/') . '#faq' ?>">FAQ</a></li>
        </ul>
      </div>
      <div class="col-lg-3 col-6">
        <h6 class="footer-heading">Pendaftaran</h6>
        <ul class="list-unstyled footer-links">
          <li><a href="<?= url('/daftar') ?>">Daftar Online</a></li>
          <li><a href="<?= url('/login') ?>">Login Pendaftar</a></li>
          <li><a href="<?= url('/') . '#persyaratan' ?>">Persyaratan</a></li>
        </ul>
      </div>
      <div class="col-lg-3">
        <h6 class="footer-heading">Kontak</h6>
        <ul class="list-unstyled footer-contact">
          <?php if (!empty($settings['site_phone'])): ?>
          <li><i class="bi bi-whatsapp me-2"></i><a href="https://wa.me/<?= preg_replace('/[^0-9]/','',$settings['site_phone']) ?>"><?= Security::clean($settings['site_phone']) ?></a></li>
          <?php endif; ?>
          <?php if (!empty($settings['site_website'])): ?>
          <li><i class="bi bi-globe me-2"></i><a href="https://<?= Security::clean($settings['site_website']) ?>"><?= Security::clean($settings['site_website']) ?></a></li>
          <?php endif; ?>
          <?php if (!empty($settings['site_email'])): ?>
          <li><i class="bi bi-envelope me-2"></i><a href="mailto:<?= Security::clean($settings['site_email']) ?>"><?= Security::clean($settings['site_email']) ?></a></li>
          <?php endif; ?>
          <?php if (!empty($settings['site_alamat'])): ?>
          <li><i class="bi bi-geo-alt me-2"></i><?= Security::clean($settings['site_alamat']) ?></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <div class="row align-items-center">
        <div class="col-md-6">
          <p class="mb-0 small">&copy; <?= date('Y') ?> <?= Security::clean($settings['site_name'] ?? APP_NAME) ?>. Semua hak dilindungi.</p>
        </div>
        <div class="col-md-6 text-md-end">
          <p class="mb-0 small">Sistem PMB v<?= APP_VERSION ?></p>
        </div>
      </div>
    </div>
  </div>
</footer>

<script src="<?= url('/assets/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= url('/assets/js/app.js') ?>"></script>
<?= $extra_scripts ?? '' ?>
</body>
</html>
