<?php // app/views/public/home.php ?>

<!-- ═══════════════════════ HERO ═══════════════════════ -->
<section class="hero-section" id="home">
  <div class="container position-relative" style="z-index:2">
    <div class="row align-items-center g-4 g-lg-5">
      <div class="col-lg-6">
        <div class="hero-badge mb-3">
          <i class="bi bi-mortarboard-fill"></i>
          <?= $tahun_aktif ? Security::clean($tahun_aktif['nama']) : 'Pendaftaran Dibuka' ?>
        </div>
        <h1 class="hero-title mb-3">
          <?php
          $heroTitle = $settings['hero_title'] ?? 'Penerimaan Mahasiswa Baru';
          $titleParts = explode(' ', $heroTitle, 2);
          if (count($titleParts) === 2):
          ?>
          <?= Security::clean($titleParts[0]) ?><br>
          <span style="color:var(--gold-light)"><?= Security::clean($titleParts[1]) ?></span>
          <?php else: ?>
          <?= Security::clean($heroTitle) ?>
          <?php endif; ?>
        </h1>
        <p class="hero-subtitle mb-2">
          <?= Security::clean($settings['hero_subtitle'] ?? 'Wujudkan Impianmu Bersama Kami') ?>
        </p>
        <p class="hero-kerjasama mb-4">
          <i class="bi bi-building-check me-1"></i>
          <?= Security::clean($settings['site_kerjasama'] ?? 'Bekerjasama dengan Institut Muhammadiyah Ngawi') ?>
        </p>
        <div class="d-flex flex-wrap gap-3">
          <?php if ($tahun_aktif): ?>
          <a href="<?= url('/daftar') ?>" class="btn-primary-gold">
            <i class="bi bi-pencil-square me-2"></i> Daftar Sekarang
          </a>
          <?php endif; ?>
          <a href="#program" class="btn btn-outline-light rounded-pill px-4">
            <i class="bi bi-book me-2"></i> Lihat Program
          </a>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="row g-3">
          <?php foreach ([
            ['num'=>'7+',    'lbl'=>'Program Studi',    'icon'=>'bi-journals',         'delay'=>'0s'],
            ['num'=>'S1&S2', 'lbl'=>'Jenjang Tersedia', 'icon'=>'bi-award',             'delay'=>'.08s'],
            ['num'=>'8',     'lbl'=>'Semester S1',       'icon'=>'bi-calendar3',        'delay'=>'.16s'],
            ['num'=>'3–4',   'lbl'=>'Semester S2',       'icon'=>'bi-lightning-charge', 'delay'=>'.24s'],
          ] as $s): ?>
          <div class="col-6">
            <div class="hero-stat" style="transition-delay:<?= $s['delay'] ?>">
              <i class="bi <?= $s['icon'] ?>" style="font-size:1.6rem;color:var(--gold-light);margin-bottom:.5rem;display:block"></i>
              <span class="num"><?= $s['num'] ?></span>
              <span class="lbl"><?= $s['lbl'] ?></span>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- PROMO STRIP -->
<div style="background:linear-gradient(90deg,var(--gold-deep),var(--gold-main),var(--gold-light));padding:.6rem 0">
  <div class="container text-center">
    <p class="mb-0 fw-bold text-dark" style="font-size:.88rem">
      <i class="bi bi-gift-fill me-1"></i>
      Promo: 20 Pendaftar Pertama S2 <strong>GRATIS Biaya Pendaftaran!</strong>
      &ensp;<a href="<?= url('/daftar') ?>" class="text-dark fw-bold">Daftar sekarang →</a>
    </p>
  </div>
</div>


<?php if (!empty($settings['about_text'])): ?>
<!-- ABOUT SECTION -->
<section style="padding:3.5rem 0;background:#fff;border-bottom:1px solid var(--border)">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-lg-3 text-center">
        <?php $logoPath = $settings['logo_path'] ?? ''; ?>
        <?php if ($logoPath): ?>
        <img src="<?= htmlspecialchars(BASE_URL . $logoPath) ?>" alt="Logo"
             style="max-height:100px;max-width:200px;object-fit:contain;" onerror="this.style.display='none'">
        <?php else: ?>
        <div style="width:80px;height:80px;border-radius:50%;background:var(--blue-pale);display:flex;align-items:center;justify-content:center;margin:0 auto;font-size:2rem;color:var(--blue-main)">
          <i class="bi bi-building"></i>
        </div>
        <?php endif; ?>
      </div>
      <div class="col-lg-9">
        <h3 class="fw-bold mb-2" style="color:var(--blue-main);font-size:1.2rem">
          <?= Security::clean($settings['site_name'] ?? APP_NAME) ?>
        </h3>
        <p class="text-muted mb-0" style="font-size:.92rem;line-height:1.7">
          <?= nl2br(Security::clean($settings['about_text'])) ?>
        </p>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ═══════════════════════ PROGRAM STUDI ═══════════════════════ -->
<section id="program" style="padding:5rem 0;background:var(--off-white)">
  <div class="container">
    <div class="text-center mb-5">
      <span class="section-label">Pilihan Program</span>
      <h2 class="fw-bold mt-1">Program Studi Unggulan</h2>
      <div class="section-divider centered"></div>
      <p class="text-muted mx-auto" style="max-width:520px">
        Pilih program yang sesuai dengan minat, bakat, dan tujuan karir Anda
      </p>
    </div>

    <?php
    $s1Groups = []; $s2Groups = [];
    foreach ($prodi_grouped as $g) {
        if (($g['jenjang'] ?? 'S1') === 'S2') $s2Groups[] = $g;
        else $s1Groups[] = $g;
    }
    ?>

    <div class="row g-4">
      <div class="col-lg-8">
        <div class="card-premium h-100" style="border-top:4px solid var(--blue-main);padding:2rem">
          <div class="d-flex align-items-center gap-3 mb-4">
            <div class="card-icon blue" style="width:52px;height:52px;font-size:1.4rem"><i class="bi bi-mortarboard"></i></div>
            <div>
              <h3 class="mb-0" style="font-size:1.2rem;font-weight:700;color:var(--blue-main)">Program Sarjana (S1)</h3>
              <small class="text-muted">Masa Studi: 8 Semester</small>
            </div>
          </div>
          <?php foreach ($s1Groups as $g): ?>
          <div class="mb-3">
            <div class="d-flex align-items-center gap-2 mb-2">
              <i class="bi bi-building-fill" style="color:var(--gold-deep);font-size:.8rem"></i>
              <span class="fw-700" style="color:var(--gold-deep);font-size:.78rem;text-transform:uppercase;letter-spacing:.5px">
                <?= Security::clean($g['fakultas']) ?>
              </span>
            </div>
            <?php foreach ($g['prodi'] as $p): ?>
            <div class="d-flex align-items-center gap-3 py-2 px-3 mb-1 rounded-3"
                 style="background:var(--off-white);border:1px solid var(--border)">
              <i class="bi bi-arrow-right-circle-fill" style="color:var(--blue-main);flex-shrink:0"></i>
              <span class="fw-600 flex-grow-1" style="font-size:.92rem"><?= Security::clean($p['nama_prodi']) ?></span>
              <span class="text-muted small">(<?= Security::clean($p['singkatan']) ?>)</span>
              <span class="badge rounded-pill" style="background:var(--blue-pale);color:var(--blue-main);font-size:.7rem;white-space:nowrap">
                <?= Security::clean($p['gelar']) ?>
              </span>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endforeach; ?>
          <div class="mt-3">
            <a href="<?= url('/daftar') ?>" class="btn-primary-blue">
              <i class="bi bi-pencil-square me-2"></i> Daftar Program S1
            </a>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card-premium h-100" style="border-top:4px solid var(--gold-main);padding:2rem">
          <div class="d-flex align-items-center gap-3 mb-4">
            <div class="card-icon gold" style="width:52px;height:52px;font-size:1.4rem"><i class="bi bi-award"></i></div>
            <div>
              <h3 class="mb-0" style="font-size:1.2rem;font-weight:700;color:var(--blue-main)">Program Magister (S2)</h3>
              <small class="text-muted">Masa Studi: 3–4 Semester</small>
            </div>
          </div>
          <?php foreach ($s2Groups as $g): ?>
          <?php foreach ($g['prodi'] as $p): ?>
          <div class="d-flex align-items-center gap-3 py-2 px-3 mb-2 rounded-3"
               style="background:var(--gold-pale);border:1px solid rgba(201,162,39,.25)">
            <i class="bi bi-star-fill" style="color:var(--gold-main);flex-shrink:0"></i>
            <div class="flex-grow-1">
              <div class="fw-600" style="font-size:.92rem"><?= Security::clean($p['nama_prodi']) ?></div>
              <div style="font-size:.78rem;color:var(--text-muted)"><?= Security::clean($p['singkatan']) ?></div>
              <span class="badge" style="background:var(--gold-main);color:var(--blue-dark);font-size:.7rem"><?= Security::clean($p['gelar']) ?></span>
            </div>
          </div>
          <?php endforeach; ?>
          <?php endforeach; ?>
          <div class="mt-4 p-3 text-center rounded-3"
               style="background:linear-gradient(135deg,var(--gold-pale),#fff9e0);border:1.5px dashed var(--gold-main)">
            <i class="bi bi-gift-fill d-block mb-2" style="font-size:1.5rem;color:var(--gold-main)"></i>
            <p class="fw-bold mb-1 small" style="color:var(--gold-deep)">Promo 20 Pendaftar Pertama</p>
            <p class="text-muted mb-2" style="font-size:.78rem">Gratis Biaya Pendaftaran S2</p>
            <a href="<?= url('/daftar') ?>" class="btn-primary-gold" style="font-size:.8rem;padding:.4rem 1rem">
              <i class="bi bi-pencil-square me-1"></i> Daftar S2
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


<!-- ═══════════════════════ BIAYA ═══════════════════════ -->
<section id="biaya" style="padding:5rem 0;background:#fff">
  <div class="container">
    <div class="text-center mb-5">
      <span class="section-label">Investasi Pendidikan</span>
      <h2 class="fw-bold mt-1">Biaya Pendidikan</h2>
      <div class="section-divider centered"></div>
      <p class="text-muted">Terjangkau, transparan, tanpa biaya tersembunyi</p>
    </div>
    <div class="row g-4 justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="biaya-card h-100">
          <div class="biaya-card-header s1">
            <div class="d-flex align-items-center gap-3">
              <div style="width:52px;height:52px;border-radius:10px;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:#fff;flex-shrink:0">
                <i class="bi bi-mortarboard"></i>
              </div>
              <div>
                <h4 class="mb-0 text-white fw-bold">Program S1</h4>
                <p class="mb-0 small" style="color:rgba(255,255,255,.7)">Sarjana · 8 Semester</p>
              </div>
            </div>
          </div>
          <div class="biaya-card-body">
            <div class="biaya-item"><span class="biaya-label"><i class="bi bi-receipt me-2 text-blue"></i>Biaya Pendaftaran</span><span class="biaya-value">Rp 300.000</span></div>
            <div class="biaya-item"><span class="biaya-label"><i class="bi bi-calendar-month me-2 text-blue"></i>SPP per Bulan</span><span class="biaya-value">Rp 250.000</span></div>
            <div class="biaya-item" style="border:none"><span class="biaya-label"><i class="bi bi-clock me-2 text-blue"></i>Masa Studi</span><span class="biaya-value">8 Semester</span></div>
            <div class="mt-4">
              <a href="<?= url('/daftar') ?>" class="btn-primary-blue w-100 justify-content-center">
                <i class="bi bi-pencil-square me-2"></i> Daftar S1
              </a>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-lg-5">
        <div class="biaya-card h-100" style="position:relative">
          <div style="position:absolute;top:0;right:1.5rem;background:var(--gold-main);color:var(--blue-dark);font-size:.7rem;font-weight:700;padding:.15rem .7rem;border-radius:0 0 8px 8px;z-index:1">PROMO</div>
          <div class="biaya-card-header s2">
            <div class="d-flex align-items-center gap-3">
              <div style="width:52px;height:52px;border-radius:10px;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:#fff;flex-shrink:0">
                <i class="bi bi-award"></i>
              </div>
              <div>
                <h4 class="mb-0 text-white fw-bold">Program Magister S2</h4>
                <p class="mb-0 small" style="color:rgba(255,255,255,.7)">Pascasarjana · 3–4 Semester</p>
              </div>
            </div>
          </div>
          <div class="biaya-card-body">
            <div class="biaya-item">
              <span class="biaya-label"><i class="bi bi-receipt me-2" style="color:var(--gold-deep)"></i>Biaya Pendaftaran</span>
              <div class="text-end">
                <span class="biaya-value" style="color:var(--gold-deep)">Rp 500.000</span><br>
                <span class="badge bg-warning text-dark" style="font-size:.65rem">Promo: GRATIS!</span>
              </div>
            </div>
            <div class="biaya-item"><span class="biaya-label"><i class="bi bi-journal-bookmark me-2" style="color:var(--gold-deep)"></i>Biaya s/d Lulus</span><span class="biaya-value" style="color:var(--gold-deep)">Rp 8.000.000</span></div>
            <div class="biaya-item" style="border:none"><span class="biaya-label"><i class="bi bi-clock me-2" style="color:var(--gold-deep)"></i>Masa Studi</span><span class="biaya-value" style="color:var(--gold-deep)">3–4 Semester</span></div>
            <div class="mt-4">
              <a href="<?= url('/daftar') ?>" class="btn-primary-gold w-100 justify-content-center">
                <i class="bi bi-pencil-square me-2"></i> Daftar S2
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


<!-- ═══════════════════════ ALUR ═══════════════════════ -->
<section id="alur" style="padding:5rem 0;background:var(--off-white)">
  <div class="container">
    <div class="text-center mb-5">
      <span class="section-label">Cara Mendaftar</span>
      <h2 class="fw-bold mt-1">Alur Pendaftaran</h2>
      <div class="section-divider centered"></div>
      <p class="text-muted">Mudah dan cepat, bisa dilakukan dari mana saja</p>
    </div>
    <div class="row g-3">
      <?php
      $steps = [
        ['icon'=>'bi-pencil-square',  'title'=>'Isi Formulir Online',    'desc'=>'Daftar akun dan isi data diri secara lengkap dan benar.'],
        ['icon'=>'bi-cloud-upload',   'title'=>'Upload Dokumen',          'desc'=>'Upload scan KTP, KK, Akte, Ijazah, dan Foto Resmi.'],
        ['icon'=>'bi-shield-check',   'title'=>'Verifikasi Berkas',       'desc'=>'Tim kami memverifikasi kelengkapan dan keabsahan dokumen.'],
        ['icon'=>'bi-credit-card',    'title'=>'Pembayaran',              'desc'=>'Lakukan pembayaran biaya pendaftaran sesuai program.'],
        ['icon'=>'bi-envelope-check', 'title'=>'Konfirmasi Penerimaan',   'desc'=>'Terima konfirmasi via email atau WhatsApp.'],
      ];
      foreach ($steps as $i => $s):
      ?>
      <div class="col-sm-6 col-lg-4">
        <div class="d-flex gap-3 p-3 rounded-3 h-100"
             style="background:#fff;border:1px solid var(--border);box-shadow:var(--shadow-sm)">
          <div style="width:42px;height:42px;border-radius:50%;background:var(--blue-main);color:#fff;font-weight:700;font-size:.95rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 12px rgba(26,58,107,.25)">
            <?= $i + 1 ?>
          </div>
          <div>
            <h6 class="fw-700 mb-1" style="color:var(--blue-main);font-size:.88rem">
              <i class="bi <?= $s['icon'] ?> me-1" style="color:var(--gold-main)"></i><?= $s['title'] ?>
            </h6>
            <p class="mb-0 text-muted" style="font-size:.82rem"><?= $s['desc'] ?></p>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-5">
      <a href="<?= url('/daftar') ?>" class="btn-primary-gold">
        <i class="bi bi-pencil-square me-2"></i> Mulai Pendaftaran Sekarang
      </a>
    </div>
  </div>
</section>


<!-- ═══════════════════════ PERSYARATAN ═══════════════════════ -->
<section id="persyaratan" style="padding:5rem 0;background:#fff">
  <div class="container">
    <div class="text-center mb-5">
      <span class="section-label">Yang Perlu Disiapkan</span>
      <h2 class="fw-bold mt-1">Persyaratan Pendaftaran</h2>
      <div class="section-divider centered"></div>
    </div>
    <div class="row g-3 justify-content-center">
      <?php if (!empty($persyaratan_list)): ?>
        <?php foreach ($persyaratan_list as $p): ?>
        <?php $wajib = (bool)($p['wajib'] ?? 1); ?>
        <div class="col-sm-6 col-lg-4">
          <div class="d-flex gap-3 align-items-start p-3 rounded-3 h-100"
               style="background:<?= $wajib ? 'var(--blue-pale)' : 'var(--gold-pale)' ?>;border:1px solid <?= $wajib ? 'rgba(26,58,107,.15)' : 'rgba(201,162,39,.25)' ?>">
            <div style="width:38px;height:38px;border-radius:8px;background:<?= $wajib ? 'var(--blue-main)' : 'var(--gold-main)' ?>;color:#fff;display:flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0">
              <i class="bi <?= $wajib ? 'bi-check-lg' : 'bi-info-circle' ?>"></i>
            </div>
            <div>
              <p class="mb-1 fw-600" style="font-size:.88rem"><?= Security::clean($p['nama']) ?></p>
              <?php if (!empty($p['keterangan'])): ?>
              <p class="mb-1 text-muted" style="font-size:.78rem"><?= Security::clean($p['keterangan']) ?></p>
              <?php endif; ?>
              <?php if (!$wajib): ?>
              <span class="badge" style="background:var(--gold-main);color:var(--blue-dark);font-size:.68rem">Bisa Menyusul</span>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>

      <?php else: ?>
        <?php foreach ([
          ['nama'=>'Mengisi Formulir Pendaftaran Online', 'wajib'=>true],
          ['nama'=>'Scan Asli KTP',                       'wajib'=>true],
          ['nama'=>'Scan Asli Kartu Keluarga (KK)',        'wajib'=>true],
          ['nama'=>'Scan Asli Akte Kelahiran',             'wajib'=>true],
          ['nama'=>'Scan Asli Ijazah Terakhir & Transkrip','wajib'=>true],
          ['nama'=>'Ijazah S1 (khusus pendaftar S2)',      'wajib'=>false],
          ['nama'=>'Foto Resmi Berjas Hitam, BG Biru Muda','wajib'=>true],
          ['nama'=>'Transkrip Nilai S1 (bisa menyusul)',   'wajib'=>false],
          ['nama'=>'Nomor HP aktif (WhatsApp)',             'wajib'=>true],
        ] as $r): ?>
        <div class="col-sm-6 col-lg-4">
          <div class="d-flex gap-3 align-items-start p-3 rounded-3 h-100"
               style="background:<?= $r['wajib'] ? 'var(--blue-pale)' : 'var(--gold-pale)' ?>;border:1px solid <?= $r['wajib'] ? 'rgba(26,58,107,.15)' : 'rgba(201,162,39,.25)' ?>">
            <div style="width:38px;height:38px;border-radius:8px;background:<?= $r['wajib'] ? 'var(--blue-main)' : 'var(--gold-main)' ?>;color:#fff;display:flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0">
              <i class="bi <?= $r['wajib'] ? 'bi-check-lg' : 'bi-info-circle' ?>"></i>
            </div>
            <div>
              <p class="mb-0 fw-600" style="font-size:.88rem"><?= Security::clean($r['nama']) ?></p>
              <?php if (!$r['wajib']): ?>
              <span class="badge mt-1" style="background:var(--gold-main);color:var(--blue-dark);font-size:.68rem">Bisa Menyusul</span>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>


<!-- ═══════════════════════ FAQ ═══════════════════════ -->
<section id="faq" style="padding:5rem 0;background:var(--off-white)">
  <div class="container">
    <div class="text-center mb-5">
      <span class="section-label">Pertanyaan Umum</span>
      <h2 class="fw-bold mt-1">FAQ</h2>
      <div class="section-divider centered"></div>
    </div>
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="accordion" id="faqAccordion">
          <?php
          // Ambil FAQ dari DB jika ada, fallback ke FAQ default
          $dbFaqs = !empty($settings['faq_list']) ? json_decode($settings['faq_list'], true) : [];
          $faqs = !empty($dbFaqs) ? $dbFaqs : [
            ['q'=>'Apakah pendaftaran bisa dilakukan secara online?',
             'a'=>'Ya, seluruh proses pendaftaran dapat dilakukan secara online melalui website ini.'],
            ['q'=>'Berapa biaya pendaftaran Program S1?',
             'a'=>'Biaya pendaftaran Program S1 adalah Rp 300.000, dengan SPP Rp 250.000/bulan. Masa studi 8 semester.'],
            ['q'=>'Apakah ada promo untuk Program Magister S2?',
             'a'=>'Ya! 20 pendaftar pertama Program S2 mendapatkan GRATIS biaya pendaftaran. Segera daftar sebelum kuota habis!'],
            ['q'=>'Dokumen apa saja yang harus diupload?',
             'a'=>'KTP, Kartu Keluarga, Akte Kelahiran, Ijazah & Transkrip, dan Foto Resmi.'],
            ['q'=>'Bagaimana cara mengetahui status pendaftaran saya?',
             'a'=>'Setelah mendaftar, Anda mendapat Nomor Pendaftaran. Login ke dashboard untuk memantau status verifikasi.'],
          ];
          foreach ($faqs as $i => $f):
          ?>
          <div class="accordion-item mb-2 rounded-3 overflow-hidden"
               style="border:1px solid var(--border)!important;box-shadow:var(--shadow-sm)">
            <h2 class="accordion-header">
              <button class="accordion-button fw-600 <?= $i > 0 ? 'collapsed' : '' ?>" type="button"
                      data-bs-toggle="collapse" data-bs-target="#faq<?= $i ?>"
                      style="font-size:.92rem;color:var(--blue-main)">
                <?= Security::clean($f['q']) ?>
              </button>
            </h2>
            <div id="faq<?= $i ?>" class="accordion-collapse collapse <?= $i === 0 ? 'show' : '' ?>" data-bs-parent="#faqAccordion">
              <div class="accordion-body text-muted" style="font-size:.9rem">
                <?= Security::clean($f['a']) ?>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>


<!-- ═══════════════════════ KONTAK / CTA ═══════════════════════ -->
<section id="kontak" style="padding:5rem 0;background:linear-gradient(135deg,var(--blue-dark),var(--blue-main) 60%,var(--blue-mid))">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-6">
        <span class="section-label" style="color:var(--gold-light)">Hubungi Kami</span>
        <h2 class="fw-bold text-white mt-1 mb-3">Siap Melangkah<br>Bersama Kami?</h2>
        <p style="color:rgba(255,255,255,.75);margin-bottom:2rem">
          Hubungi tim kami untuk informasi lebih lanjut, atau langsung daftar secara online.
        </p>
        <div class="d-flex flex-column gap-3">
          <?php if (!empty($settings['site_phone'])): ?>
          <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $settings['site_phone']) ?>"
             target="_blank" class="d-flex align-items-center gap-3 text-white text-decoration-none">
            <div style="width:48px;height:48px;border-radius:12px;background:rgba(255,255,255,.12);display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0"><i class="bi bi-whatsapp"></i></div>
            <div>
              <div style="font-size:.75rem;color:rgba(255,255,255,.55)">WhatsApp / Telepon</div>
              <div class="fw-bold"><?= Security::clean($settings['site_phone']) ?></div>
            </div>
          </a>
          <?php endif; ?>
          <?php if (!empty($settings['site_email'])): ?>
          <a href="mailto:<?= Security::clean($settings['site_email']) ?>"
             class="d-flex align-items-center gap-3 text-white text-decoration-none">
            <div style="width:48px;height:48px;border-radius:12px;background:rgba(255,255,255,.12);display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0"><i class="bi bi-envelope"></i></div>
            <div>
              <div style="font-size:.75rem;color:rgba(255,255,255,.55)">Email</div>
              <div class="fw-bold"><?= Security::clean($settings['site_email']) ?></div>
            </div>
          </a>
          <?php endif; ?>
          <?php if (!empty($settings['site_website'])): ?>
          <a href="https://<?= Security::clean($settings['site_website']) ?>" target="_blank"
             class="d-flex align-items-center gap-3 text-white text-decoration-none">
            <div style="width:48px;height:48px;border-radius:12px;background:rgba(255,255,255,.12);display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0"><i class="bi bi-globe"></i></div>
            <div>
              <div style="font-size:.75rem;color:rgba(255,255,255,.55)">Website</div>
              <div class="fw-bold"><?= Security::clean($settings['site_website']) ?></div>
            </div>
          </a>
          <?php endif; ?>
          <?php if (!empty($settings['site_alamat'])): ?>
          <div class="d-flex align-items-center gap-3">
            <div style="width:48px;height:48px;border-radius:12px;background:rgba(255,255,255,.12);display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0"><i class="bi bi-geo-alt-fill"></i></div>
            <div>
              <div style="font-size:.75rem;color:rgba(255,255,255,.55)">Alamat</div>
              <div class="fw-bold" style="font-size:.9rem"><?= Security::clean($settings['site_alamat']) ?></div>
            </div>
          </div>
          <?php endif; ?>
        </div>

        <?php if (!empty($settings['maps_url'])): ?>
        <!-- Google Maps Embed -->
        <div class="mt-4 rounded-3 overflow-hidden" style="height:200px;border:2px solid rgba(255,255,255,.2)">
          <iframe src="<?= htmlspecialchars($settings['maps_url']) ?>"
                  width="100%" height="200" style="border:0;display:block"
                  allowfullscreen="" loading="lazy"
                  referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
        <?php endif; ?>
      </div>

      <div class="col-lg-5 offset-lg-1">
        <div class="p-4 rounded-4" style="background:rgba(255,255,255,.09);border:1.5px solid rgba(255,255,255,.18)">
          <h4 class="text-white fw-bold mb-1">Daftar Sekarang</h4>
          <p style="color:rgba(255,255,255,.6);font-size:.85rem;margin-bottom:1.5rem">Proses cepat, mudah, 100% online</p>
          <?php if ($tahun_aktif): ?>
          <a href="<?= url('/daftar') ?>" class="btn-primary-gold d-flex align-items-center justify-content-center w-100 mb-3" style="padding:.75rem">
            <i class="bi bi-pencil-square me-2"></i> Mulai Pendaftaran
          </a>
          <?php endif; ?>
          <a href="<?= url('/login') ?>" class="btn btn-outline-light rounded-pill w-100" style="font-size:.9rem">
            <i class="bi bi-box-arrow-in-right me-2"></i> Login Pendaftar
          </a>
          <hr style="border-color:rgba(255,255,255,.15);margin:1.25rem 0">
          <div class="row g-2 text-center">
            <div class="col-4">
              <div style="font-size:1.3rem;font-weight:700;color:var(--gold-light)">7+</div>
              <div style="font-size:.7rem;color:rgba(255,255,255,.5)">Prodi</div>
            </div>
            <div class="col-4">
              <div style="font-size:1.1rem;font-weight:700;color:var(--gold-light)">S1&S2</div>
              <div style="font-size:.7rem;color:rgba(255,255,255,.5)">Jenjang</div>
            </div>
            <div class="col-4">
              <div style="font-size:1.3rem;font-weight:700;color:var(--gold-light)">100%</div>
              <div style="font-size:.7rem;color:rgba(255,255,255,.5)">Online</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>