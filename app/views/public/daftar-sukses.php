<?php
// views/public/daftar-sukses.php
$p     = $pendaftar ?? [];
$promo = $promo_digunakan ?? false;
?>
<section style="background:linear-gradient(135deg,var(--primary) 0%,#1e5ca8 100%);padding:64px 0;">
    <div class="container text-center">
        <div style="font-size:4rem;color:#86efac;margin-bottom:16px;">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        <h1 class="text-white fw-700 mb-2" style="font-family:'Playfair Display',serif;">
            Pendaftaran Berhasil!
        </h1>
        <p class="text-white mb-0" style="opacity:.8;">
            Ma'had Aly Ibnu Abbas Karanganyar — Tahun Akademik <?= htmlspecialchars($p['tahun_akademik'] ?? '') ?>
        </p>
    </div>
</section>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <!-- Nomor Pendaftaran -->
            <div class="card border-0 rounded-4 shadow-lg mb-4 text-center overflow-hidden">
                <div class="card-body p-5">
                    <div class="text-muted mb-2" style="font-size:.85rem;letter-spacing:.06em;text-transform:uppercase;">Nomor Pendaftaran Anda</div>
                    <div class="d-inline-block px-5 py-3 rounded-3 mb-4"
                         style="background:linear-gradient(135deg,#f0f9ff,#e0f2fe);border:2px solid #7dd3fc;">
                        <span style="font-size:1.8rem;font-weight:800;color:var(--primary);letter-spacing:.12em;">
                            <?= htmlspecialchars($p['nomor_pendaftaran'] ?? '') ?>
                        </span>
                    </div>

                    <?php if ($promo): ?>
                    <div class="d-flex align-items-center justify-content-center gap-2 p-3 rounded-3 mb-4"
                         style="background:linear-gradient(135deg,#fffbeb,#fef3c7);border:1px solid #fde68a;">
                        <i class="bi bi-gift" style="color:var(--accent);font-size:1.2rem;"></i>
                        <span style="color:#92400e;font-weight:600;font-size:.88rem;">
                            Selamat! Anda mendapatkan promo gratis biaya pendaftaran S2
                        </span>
                    </div>
                    <?php endif; ?>

                    <div class="alert" style="background:#f0fdf4;border:1px solid #86efac;border-radius:12px;text-align:left;">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-info-circle text-success mt-1"></i>
                            <div style="font-size:.83rem;color:#166534;">
                                Simpan nomor pendaftaran ini. Gunakan nomor ini bersama password untuk login ke
                                <strong>Dashboard Pendaftar</strong> dan memantau status pendaftaran Anda.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ringkasan -->
            <div class="card border-0 rounded-4 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-700 mb-3" style="color:var(--primary);">
                        <i class="bi bi-person-check me-2"></i>Ringkasan Pendaftaran
                    </h6>
                    <div class="row g-2" style="font-size:.85rem;">
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;">Nama Lengkap</div>
                            <div class="fw-600"><?= htmlspecialchars($p['nama_lengkap'] ?? '-') ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;">Program Studi</div>
                            <div class="fw-600"><?= htmlspecialchars($p['nama_prodi'] ?? '-') ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;">Jenjang</div>
                            <div>
                                <span class="badge <?= ($p['jenjang'] ?? '') === 'S2' ? 'bg-warning text-dark' : 'bg-primary' ?>">
                                    <?= htmlspecialchars($p['jenjang'] ?? '-') ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;">Status</div>
                            <div>
                                <span class="badge bg-warning text-dark" style="font-size:.75rem;">Menunggu Verifikasi</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Langkah Selanjutnya -->
            <div class="card border-0 rounded-4 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-700 mb-3" style="color:var(--primary);">
                        <i class="bi bi-list-check me-2"></i>Langkah Selanjutnya
                    </h6>
                    <?php $steps = [
                        ['icon'=>'bi-1-circle-fill','text'=>'Tim admin akan memverifikasi berkas dokumen yang Anda upload.','color'=>'#2563eb'],
                        ['icon'=>'bi-2-circle-fill','text'=>'Pantau status verifikasi melalui Dashboard Pendaftar.','color'=>'#7c3aed'],
                        ['icon'=>'bi-3-circle-fill','text'=>'Jika ada dokumen yang perlu direvisi, Anda akan diberitahu.','color'=>'#d97706'],
                        ['icon'=>'bi-4-circle-fill','text'=>'Setelah dokumen diverifikasi, status akan berubah menjadi "Diterima".','color'=>'#16a34a'],
                    ]; foreach ($steps as $step): ?>
                    <div class="d-flex align-items-start gap-3 mb-3" style="font-size:.85rem;">
                        <i class="bi <?= $step['icon'] ?>" style="color:<?= $step['color'] ?>;font-size:1.2rem;flex-shrink:0;margin-top:1px;"></i>
                        <div><?= $step['text'] ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- CTA Buttons -->
            <div class="d-flex flex-column flex-sm-row gap-3">
                <a href="<?= url('/login') ?>" class="btn flex-fill py-3 fw-600"
                   style="background:var(--primary);color:#fff;border-radius:12px;">
                    <i class="bi bi-speedometer2 me-2"></i>Masuk ke Dashboard
                </a>
                <a href="<?= url('/') ?>" class="btn flex-fill py-3 fw-600 btn-outline-secondary" style="border-radius:12px;">
                    <i class="bi bi-house me-2"></i>Ke Beranda
                </a>
            </div>

            <!-- Kontak -->
            <div class="text-center mt-4" style="font-size:.82rem;color:#64748b;">
                Ada pertanyaan? Hubungi kami via WhatsApp:
                <a href="https://wa.me/6285614649050" target="_blank" class="fw-600 ms-1" style="color:var(--primary);">
                    <i class="bi bi-whatsapp me-1"></i>0856-1464-905
                </a>
            </div>
        </div>
    </div>
</div>
