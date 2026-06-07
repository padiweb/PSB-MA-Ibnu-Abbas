<?php
// app/views/public/home.php
?>
<!-- HERO SECTION -->
<section class="hero-section" id="home">
  <div class="container position-relative">
    <div class="row align-items-center g-5">
      <div class="col-lg-7" data-aos="fade-up">
        <div class="hero-badge">
          <i class="bi bi-mortarboard-fill"></i>
          <?= $tahun_aktif ? Security::clean($tahun_aktif['nama']) : 'Pendaftaran Dibuka' ?>
        </div>
        <h1 class="hero-title">
          Penerimaan<br>Mahasiswa Baru
        </h1>
        <p class="hero-subtitle">
          <?= Security::clean($settings['hero_subtitle'] ?? "Wujudkan Impianmu di " . ($settings['site_name'] ?? '')) ?>
        </p>
        <p class="hero-kerjasama">
          <i class="bi bi-building-check me-1"></i>
          <?= Security::clean($settings['site_kerjasama'] ?? '') ?>
        </p>
        <div class="d-flex flex-wrap gap-3">
          <?php if ($tahun_aktif): ?>
          <a href="<?= BASE_URL ?>/daftar" class="btn-primary-gold">
            <i class="bi bi-pencil-square me-2"></i> Daftar Sekarang
          </a>
          <?php endif; ?>
          <a href="#program" class="btn btn-outline-light px-4 rounded-pill">
            <i class="bi bi-book me-2"></i> Lihat Program
          </a>
        </div>
      </div>
      <div class="col-lg-5">
        <div class="row g-3 hero-stats">
          <div class="col-6">
            <div class="hero-stat fade-up">
              <span class="num">7+</span>
              <span class="lbl">Program Studi</span>
            </div>
          </div>
          <div class="col-6">
            <div class="hero-stat fade-up" style="transition-delay:.1s">
              <span class="num">S1 & S2</span>
              <span class="lbl">Jenjang Tersedia</span>
            </div>
          </div>
          <div class="col-6">
            <div class="hero-stat fade-up" style="transition-delay:.2s">
              <span class="num">8</span>
              <span class="lbl">Semester S1</span>
            </div>
          </div>
          <div class="col-6">
            <div class="hero-stat fade-up" style="transition-delay:.3s">
              <span class="num">3-4</span>
              <span class="lbl">Semester S2</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- PROMO BANNER (S2) -->
<div class="bg-warning py-2 text-center" style="background: linear-gradient(90deg, #c9a227, #e8c547) !important;">
  <div class="container">
    <p class="mb-0 fw-bold text-dark small">
      <i class="bi bi-gift-fill me-1"></i>
      Promo Spesial: 20 Pendaftar Pertama Program Magister S2 <strong>GRATIS Biaya Pendaftaran!</strong>
      &nbsp;
      <a href="<?= BASE_URL ?>/daftar" class="text-dark fw-bold text-decoration-underline">Daftar sekarang &rarr;</a>
    </p>
  </div>
</div>

<!-- PROGRAM STUDI -->
<section class="py-6" id="program" style="padding: 5rem 0;">
  <div class="container">
    <div class="text-center mb-5 fade-up">
      <span class="section-label">Pilihan Program</span>
      <h2 class="fw-bold">Program Studi Unggulan</h2>
      <div class="section-divider centered"></div>
      <p class="text-muted mx-auto" style="max-width:520px">Pilih program studi yang sesuai dengan minat, bakat, dan tujuan karir Anda</p>
    </div>

    <?php
    $s1Groups = [];
    $s2Groups = [];
    foreach ($prodi_grouped as $g) {
        if ($g['jenjang'] === 'S1') $s1Groups[] = $g;
        else                         $s2Groups[] = $g;
    }
    ?>

    <div class="row g-4">
      <!-- S1 -->
      <div class="col-lg-8">
        <div class="card-premium" style="border-top: 4px solid var(--blue-main);">
          <div class="d-flex align-items-center gap-3 mb-4">
            <div class="card-icon blue"><i class="bi bi-mortarboard"></i></div>
            <div>
              <h3 class="mb-0" style="font-size:1.3rem">Program Sarjana (S1)</h3>
              <small class="text-muted">Masa Studi: 8 Semester</small>
            </div>
          </div>
          <?php foreach ($s1Groups as $g): ?>
          <div class="mb-3">
            <h6 class="text-gold fw-bold mb-2">
              <i class="bi bi-building me-1"></i><?= Security::clean($g['fakultas']) ?>
            </h6>
            <div class="ps-3">
              <?php foreach ($g['prodi'] as $p): ?>
              <div class="d-flex align-items-center gap-2 py-2 border-bottom">
                <i class="bi bi-arrow-right-circle-fill text-blue"></i>
                <div>
                  <span class="fw-500"><?= Security::clean($p['nama']) ?></span>
                  <span class="text-muted small ms-1">(<?= Security::clean($p['singkatan']) ?>)</span>
                  <span class="badge bg-light text-dark ms-1 border"><?= Security::clean($p['gelar']) ?></span>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- S2 -->
      <div class="col-lg-4">
        <div class="card-premium" style="border-top: 4px solid var(--gold-main); height:100%">
          <div class="d-flex align-items-center gap-3 mb-4">
            <div class="card-icon gold"><i class="bi bi-award"></i></div>
            <div>
              <h3 class="mb-0" style="font-size:1.3rem">Program Magister (S2)</h3>
              <small class="text-muted">Masa Studi: 3–4 Semester</small>
            </div>
          </div>
          <?php foreach ($s2Groups as $g): ?>
          <?php foreach ($g['prodi'] as $p): ?>
          <div class="d-flex align-items-center gap-2 py-2 border-bottom mb-2">
            <i class="bi bi-star-fill text-warning"></i>
            <div>
              <span class="fw-500"><?= Security::clean($p['nama']) ?></span>
              <span class="text-muted small ms-1">(<?= Security::clean($p['singkatan']) ?>)</span>
              <br><span class="badge" style="background:var(--gold-pale);color:var(--gold-deep)"><?= Security::clean($p['gelar']) ?></span>
            </div>
          </div>
          <?php endforeach; ?>
          <?php endforeach; ?>
          <!-- Promo box -->
          <div class="mt-3 p-3 rounded-3 text-center" style="background:var(--gold-pale);border:1.5px dashed var(--gold-main)">
            <i class="bi bi-gift-fill text-warning fs-4 mb-2 d-block"></i>
            <p class="small fw-bold mb-1" style="color:var(--gold-deep)">Promo 20 Pendaftar Pertama</p>
            <p class="small mb-0 text-muted">Gratis Biaya Pendaftaran S2</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- BIAYA PENDIDIKAN -->
<section class="py-5 bg-light" id="biaya">
  <div class="container">
    <div class="text-center mb-5 fade-up">
      <span class="section-label">Investasi Pendidikan</span>
      <h2 class="fw-bold">Biaya Pendidikan</h2>
      <div class="section-divider centered"></div>
    </div>
    <div class="row g-4 justify-content-center">
      <!-- S1 -->
      <div class="col-lg-5">
        <div class="biaya-card fade-up">
          <div class="biaya-card-header s1">
            <div class="d-flex align-items-center gap-3 mb-3">
              <div class="card-icon" style="background:rgba(255,255,255,.2);color:white;font-size:1.5rem;width:52px;height:52px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <i class="bi bi-mortarboard"></i>
              </div>
              <div>
                <h4 class="mb-0 text-white">Program S1</h4>
                <p class="mb-0 text-white-50 small">Sarjana - 8 Semester</p>
              </div>
            </div>
          </div>
          <div class="biaya-card-body">
            <div class="biaya-item">
              <span class="biaya-label"><i class="bi bi-receipt me-2"></i>Biaya Pendaftaran</span>
              <span class="biaya-value">Rp 300.000</span>
            </div>
            <div class="biaya-item">
              <span class="biaya-label"><i class="bi bi-calendar-month me-2"></i>SPP per Bulan</span>
              <span class="biaya-value">Rp 250.000</span>
            </div>
            <div class="biaya-item">
              <span class="biaya-label"><i class="bi bi-clock me-2"></i>Masa Studi</span>
              <span class="biaya-value">8 Semester</span>
            </div>
            <div class="mt-3">
              <a href="<?= BASE_URL ?>/daftar" class="btn-primary-blue w-100 justify-content-center">
                <i class="bi bi-pencil-square me-2"></i> Daftar Program S1
              </a>
            </div>
          </div>
        </div>
      </div>
      <!-- S2 -->
      <div class="col-lg-5">
        <div class="biaya-card fade-up" style="transition-delay:.1s">
          <div class="biaya-card-header s2">
            <div class="d-flex align-items-center gap-3 mb-3">
              <div class="card-icon" style="background:rgba(255,255,255,.2);color:white;font-size:1.5rem;width:52px;height:52px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <i class="bi bi-award"></i>
              </div>
              <div>
                <h4 class="mb-0 text-white">Program Magister S2</h4>
                <p class="mb-0 text-white-50 small">Pascasarjana - 3-4 Semester</p>
              </div>
            </div>
          </div>
          <div class="biaya-card-body">
            <div class="biaya-item">
              <span class="biaya-label"><i class="bi bi-receipt me-2"></i>Biaya Pendaftaran</span>
              <span class="biaya-value" style="color:var(--gold-deep)">Rp 500.000 <span class="badge bg-warning text-dark small">Promo!</span></span>
            </div>
            <div class="biaya-item">
              <span class="biaya-label"><i class="bi bi-journal-bookmark me-2"></i>Biaya Pendidikan</span>
              <span class="biaya-value" style="color:var(--gold-deep)">Rp 8.000.000 (s/d lulus)</span>
            </div>
            <div class="biaya-item">
              <span class="biaya-label"><i class="bi bi-calendar-month me-2"></i>Atau per Bulan</span>
              <span class="biaya-value" style="color:var(--gold-deep)">Rp 500.000 / bulan</span>
            </div>
            <div class="biaya-item">
              <span class="biaya-label"><i class="bi bi-clock me-2"></i>Masa Studi</span>
              <span class="biaya-value" style="color:var(--gold-deep)">3–4 Semester</span>
            </div>
            <div class="mt-3">
              <a href="<?= BASE_URL ?>/daftar" class="btn-primary-gold w-100 justify-content-center">
                <i class="bi bi-pencil-square me-2"></i> Daftar Program S2
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ALUR PENDAFTARAN -->
<section class="py-5" id="alur">
  <div class="container">
    <div class="text-center mb-5 fade-up">
      <span class="section-label">Cara Mendaftar</span>
      <h2 class="fw-bold">Alur Pendaftaran</h2>
      <div class="section-divider centered"></div>
    </div>
    <div class="row justify-content-center">
      <div class="col-lg-7">
        <div class="alur-steps fade-up">
          <?php
          $steps = [
            ['icon'=>'bi-pc-display','title'=>'Isi Formulir Online','desc'=>'Kunjungi website dan isi formulir pendaftaran secara lengkap dan benar.'],
            ['icon'=>'bi-upload','title'=>'Upload Dokumen','desc'=>'Upload scan dokumen persyaratan (KTP, KK, Akte, Ijazah, Foto).'],
            ['icon'=>'bi-check2-circle','title'=>'Verifikasi Berkas','desc'=>'Tim kami akan memverifikasi kelengkapan dokumen Anda.'],
            ['icon'=>'bi-credit-card','title'=>'Pembayaran Biaya Pendaftaran','desc'=>'Lakukan pembayaran biaya pendaftaran sesuai program yang dipilih.'],
            ['icon'=>'bi-envelope-check','title'=>'Terima Konfirmasi','desc'=>'Anda akan menerima konfirmasi penerimaan melalui email atau WhatsApp.'],
          ];
          foreach ($steps as $i => $s):
          ?>
          <div class="alur-step">
            <div class="alur-step-num"><?= $i+1 ?></div>
            <?php if ($i < count($steps)-1): ?>
            <div class="alur-step-line"></div>
            <?php endif; ?>
            <div class="alur-step-content">
              <h5><i class="<?= $s['icon'] ?> me-2 text-gold"></i><?= $s['title'] ?></h5>
              <p><?= $s['desc'] ?></p>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
          <a href="<?= BASE_URL ?>/daftar" class="btn-primary-gold">
            <i class="bi bi-pencil-square me-2"></i> Mulai Pendaftaran
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- PERSYARATAN -->
<section class="py-5 bg-light" id="persyaratan">
  <div class="container">
    <div class="text-center mb-5 fade-up">
      <span class="section-label">Yang Perlu Disiapkan</span>
      <h2 class="fw-bold">Persyaratan Pendaftaran</h2>
      <div class="section-divider centered"></div>
    </div>
    <div class="row g-3 justify-content-center">
      <?php if (!empty($persyaratan_list)): ?>
        <?php foreach ($persyaratan_list as $i => $p): ?>
        <div class="col-md-6 col-lg-4 fade-up" style="transition-delay:<?= $i * 0.06 ?>s">
          <div class="card-premium d-flex gap-3 align-items-start p-3">
            <div class="card-icon <?= $p['wajib'] ? 'blue' : 'gold' ?>" style="width:40px;height:40px;border-radius:8px;font-size:1rem;flex-shrink:0">
              <i class="bi <?= $p['wajib'] ? 'bi-check-circle-fill' : 'bi-info-circle' ?>"></i>
            </div>
            <div>
              <p class="mb-0 fw-500 small"><?= Security::clean($p['nama']) ?></p>
              <?php if ($p['keterangan']): ?>
              <p class="mb-0 text-muted" style="font-size:.82rem"><?= Security::clean($p['keterangan']) ?></p>
              <?php endif; ?>
              <?php if (!$p['wajib']): ?>
              <span class="badge bg-light text-muted border" style="font-size:.72rem">Bisa Menyusul</span>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      <?php else: ?>
        <?php
        $reqs = ['Mengisi Formulir Pendaftaran Online','Scan Asli KTP','Scan Asli Kartu Keluarga (KK)','Scan Asli Akte Kelahiran','Scan Asli Ijazah Terakhir & Transkrip Nilai','Foto Resmi Berjas Hitam Background Biru Muda'];
        foreach ($reqs as $i => $r):
        ?>
        <div class="col-md-6 col-lg-4 fade-up" style="transition-delay:<?= $i * 0.06 ?>s">
          <div class="card-premium d-flex gap-3 align-items-center p-3">
            <div class="card-icon blue" style="width:40px;height:40px;border-radius:8px;font-size:1rem;flex-shrink:0">
              <i class="bi bi-check-circle-fill"></i>
            </div>
            <p class="mb-0 fw-500 small"><?= Security::clean($r) ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- FAQ -->
<section class="py-5" id="faq">
  <div class="container">
    <div class="text-center mb-5 fade-up">
      <span class="section-label">Pertanyaan Umum</span>
      <h2 class="fw-bold">FAQ</h2>
      <div class="section-divider centered"></div>
    </div>
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="accordion accordion-premium" id="faqAccordion">
          <?php
          $faqs = [
            ['q'=>'Apakah pendaftaran bisa dilakukan secara online?','a'=>'Ya, seluruh proses pendaftaran dapat dilakukan secara online melalui website ini. Upload dokumen, isi formulir, dan pantau status pendaftaran dari mana saja.'],
            ['q'=>'Berapa biaya pendaftaran Program S1?','a'=>'Biaya pendaftaran Program S1 adalah Rp 300.000, dengan SPP Rp 250.000/bulan. Masa studi 8 semester.'],
            ['q'=>'Apakah ada promo untuk Program Magister S2?','a'=>'Ya! 20 pendaftar pertama Program Magister S2 mendapatkan GRATIS biaya pendaftaran (senilai Rp 500.000). Segera daftar sebelum kuota habis!'],
            ['q'=>'Dokumen apa saja yang harus diupload?','a'=>'KTP, Kartu Keluarga (KK), Akte Kelahiran, Ijazah Terakhir & Transkrip Nilai, dan Foto Resmi. Untuk S2, ijazah S1 bisa menyusul.'],
            ['q'=>'Bagaimana cara mengetahui status pendaftaran saya?','a'=>'Setelah mendaftar, Anda akan mendapat Nomor Pendaftaran. Login ke dashboard menggunakan Nomor Pendaftaran dan password untuk memantau status verifikasi.'],
            ['q'=>'Apakah Ma\'had Aly Ibnu Abbas Karanganyar terdaftar resmi?','a'=>'Ya, Ma\'had Aly Ibnu Abbas Karanganyar bekerjasama dengan Institut Muhammadiyah Ngawi dan telah terdaftar resmi dalam sistem pendidikan tinggi Indonesia.'],
          ];
          foreach ($faqs as $i => $f):
          ?>
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button <?= $i>0?'collapsed':'' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#faq<?=$i?>">
                <?= Security::clean($f['q']) ?>
              </button>
            </h2>
            <div id="faq<?=$i?>" class="accordion-collapse collapse <?= $i===0?'show':'' ?>" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
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

<!-- KONTAK -->
<section class="py-5" id="kontak" style="background: linear-gradient(135deg, var(--blue-dark), var(--blue-main)); color:white;">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-6 fade-up">
        <span class="section-label" style="color:var(--gold-light)">Hubungi Kami</span>
        <h2 class="fw-bold text-white mb-3">Siap Mendaftar?</h2>
        <p class="text-white-75 mb-4">Hubungi kami untuk informasi lebih lanjut atau langsung daftar secara online.</p>
        <div class="d-flex flex-column gap-3">
          <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $settings['site_phone'] ?? '085614649050') ?>" class="d-flex align-items-center gap-3 text-white text-decoration-none">
            <div class="card-icon" style="background:rgba(255,255,255,.1);color:white;width:48px;height:48px">
              <i class="bi bi-whatsapp"></i>
            </div>
            <div>
              <div class="small text-white-50">WhatsApp / Telepon</div>
              <div class="fw-bold"><?= Security::clean($settings['site_phone'] ?? '0856-1464-905') ?></div>
            </div>
          </a>
          <a href="https://<?= Security::clean($settings['site_website'] ?? 'www.ibnuabbass.com') ?>" class="d-flex align-items-center gap-3 text-white text-decoration-none">
            <div class="card-icon" style="background:rgba(255,255,255,.1);color:white;width:48px;height:48px">
              <i class="bi bi-globe"></i>
            </div>
            <div>
              <div class="small text-white-50">Website</div>
              <div class="fw-bold"><?= Security::clean($settings['site_website'] ?? 'www.ibnuabbass.com') ?></div>
            </div>
          </a>
        </div>
      </div>
      <div class="col-lg-4 offset-lg-2 text-center fade-up" style="transition-delay:.15s">
        <div class="p-4 rounded-4" style="background:rgba(255,255,255,.08);border:1.5px solid rgba(255,255,255,.15)">
          <h4 class="text-white mb-1">Daftar Sekarang</h4>
          <p class="text-white-50 small mb-4">Proses cepat, mudah, online</p>
          <a href="<?= BASE_URL ?>/daftar" class="btn-primary-gold d-inline-flex w-100 justify-content-center mb-3">
            <i class="bi bi-pencil-square me-2"></i> Mulai Pendaftaran
          </a>
          <a href="<?= BASE_URL ?>/login" class="btn btn-outline-light rounded-pill w-100">
            <i class="bi bi-box-arrow-in-right me-2"></i> Login Pendaftar
          </a>
        </div>
      </div>
    </div>
  </div>
</section>
