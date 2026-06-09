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
    <a class="navbar-brand" href="<?= url('/') ?>">
      <?php
      $logoPath = $settings['logo_path'] ?? '';
      $logoUrl  = $logoPath ? BASE_URL . $logoPath : '';
      ?>
      <?php if ($logoUrl): ?>
      <img src="<?= htmlspecialchars($logoUrl) ?>" 
           alt="<?= Security::clean($settings['site_name'] ?? APP_NAME) ?>" 
           height="44"
           style="object-fit:contain;max-width:120px"
           onerror="this.style.display='none'">
      <?php endif; ?>
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
<footer class="site-footer">
  <!-- Footer Top -->
  <div style="background:var(--blue-dark);padding:3.5rem 0 2.5rem">
    <div class="container">
      <div class="row g-4">

        <!-- Brand -->
        <div class="col-lg-4 col-md-6">
          <div class="d-flex align-items-center gap-3 mb-3">
            <?php
            $fLogoPath = $settings['logo_path'] ?? '';
            $fLogoUrl  = $fLogoPath ? BASE_URL . $fLogoPath : '';
            ?>
            <?php if ($fLogoUrl): ?>
            <img src="<?= htmlspecialchars($fLogoUrl) ?>" alt="Logo" height="52"
                 style="object-fit:contain;border-radius:8px;background:rgba(255,255,255,.1);padding:4px"
                 onerror="this.style.display='none'">
            <?php endif; ?>
            <div>
              <h5 class="footer-brand mb-0"><?= Security::clean($settings['site_name'] ?? APP_NAME) ?></h5>
              <?php if (!empty($settings['site_kerjasama'])): ?>
              <p class="mb-0" style="font-size:.73rem;color:rgba(255,255,255,.45)"><?= Security::clean($settings['site_kerjasama']) ?></p>
              <?php endif; ?>
            </div>
          </div>
          <?php if (!empty($settings['site_tagline'])): ?>
          <p class="footer-tagline small mb-3"><?= Security::clean($settings['site_tagline']) ?></p>
          <?php endif; ?>
          <!-- Sosmed placeholder -->
          <div class="d-flex gap-2">
            <?php if (!empty($settings['site_phone'])): ?>
            <a href="https://wa.me/<?= preg_replace('/[^0-9]/','',$settings['site_phone']) ?>" target="_blank"
               style="width:36px;height:36px;border-radius:8px;background:rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.7);text-decoration:none;transition:.2s"
               onmouseover="this.style.background='#25d366';this.style.color='#fff'"
               onmouseout="this.style.background='rgba(255,255,255,.1)';this.style.color='rgba(255,255,255,.7)'">
              <i class="bi bi-whatsapp"></i>
            </a>
            <?php endif; ?>
          </div>
        </div>

        <!-- Navigasi -->
        <div class="col-lg-2 col-md-3 col-6">
          <h6 class="footer-heading">Navigasi</h6>
          <ul class="list-unstyled footer-links">
            <li><a href="<?= url('/') ?>"><i class="bi bi-chevron-right me-1" style="font-size:.65rem"></i>Beranda</a></li>
            <li><a href="<?= url('/') . '#program' ?>"><i class="bi bi-chevron-right me-1" style="font-size:.65rem"></i>Program Studi</a></li>
            <li><a href="<?= url('/') . '#biaya' ?>"><i class="bi bi-chevron-right me-1" style="font-size:.65rem"></i>Biaya</a></li>
            <li><a href="<?= url('/') . '#alur' ?>"><i class="bi bi-chevron-right me-1" style="font-size:.65rem"></i>Alur Daftar</a></li>
            <li><a href="<?= url('/') . '#faq' ?>"><i class="bi bi-chevron-right me-1" style="font-size:.65rem"></i>FAQ</a></li>
          </ul>
        </div>

        <!-- Pendaftaran -->
        <div class="col-lg-3 col-md-3 col-6">
          <h6 class="footer-heading">Pendaftaran</h6>
          <ul class="list-unstyled footer-links">
            <li><a href="<?= url('/daftar') ?>"><i class="bi bi-chevron-right me-1" style="font-size:.65rem"></i>Daftar Online</a></li>
            <li><a href="<?= url('/login') ?>"><i class="bi bi-chevron-right me-1" style="font-size:.65rem"></i>Login Pendaftar</a></li>
            <li><a href="<?= url('/') . '#persyaratan' ?>"><i class="bi bi-chevron-right me-1" style="font-size:.65rem"></i>Persyaratan</a></li>
          </ul>
        </div>

        <!-- Kontak -->
        <div class="col-lg-3 col-md-6">
          <h6 class="footer-heading">Hubungi Kami</h6>
          <ul class="list-unstyled footer-contact">
            <?php if (!empty($settings['site_phone'])): ?>
            <li>
              <i class="bi bi-whatsapp"></i>
              <a href="https://wa.me/<?= preg_replace('/[^0-9]/','',$settings['site_phone']) ?>" target="_blank">
                <?= Security::clean($settings['site_phone']) ?>
              </a>
            </li>
            <?php endif; ?>
            <?php if (!empty($settings['site_email'])): ?>
            <li>
              <i class="bi bi-envelope"></i>
              <a href="mailto:<?= Security::clean($settings['site_email']) ?>"><?= Security::clean($settings['site_email']) ?></a>
            </li>
            <?php endif; ?>
            <?php if (!empty($settings['site_website'])): ?>
            <li>
              <i class="bi bi-globe"></i>
              <a href="https://<?= Security::clean($settings['site_website']) ?>" target="_blank"><?= Security::clean($settings['site_website']) ?></a>
            </li>
            <?php endif; ?>
            <?php if (!empty($settings['site_alamat'])): ?>
            <li>
              <i class="bi bi-geo-alt"></i>
              <span><?= Security::clean($settings['site_alamat']) ?></span>
            </li>
            <?php endif; ?>
          </ul>
        </div>

      </div>
    </div>
  </div>

  <!-- Footer Bottom -->
  <div class="footer-bottom">
    <div class="container">
      <div class="row align-items-center g-2">
        <div class="col-md-6 text-center text-md-start">
          <p class="mb-0 small">&copy; <?= date('Y') ?> <?= Security::clean($settings['site_name'] ?? APP_NAME) ?>. Semua hak dilindungi.</p>
        </div>
        <div class="col-md-6 text-center text-md-end">
          <p class="mb-0 small" style="color:rgba(255,255,255,.35)">
            Sistem PMB v<?= APP_VERSION ?> &nbsp;&middot;&nbsp;
             <a href="https://wa.me/6282242853" target="_blank" 
               style="color:rgba(255,255,255,.55);text-decoration:none;font-weight:600;transition:.2s"
               onmouseover="this.style.color='var(--gold-light)'"
               onmouseout="this.style.color='rgba(255,255,255,.55)'">Padiweb Labs</a>
          </p>
        </div>
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