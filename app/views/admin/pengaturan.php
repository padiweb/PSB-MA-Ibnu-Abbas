<?php // views/admin/pengaturan.php
$s = $settings ?? [];

// Flash messages
$ok  = Session::getFlash('success');
$err = Session::getFlash('error');
?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1><i class="bi bi-gear me-2"></i>Pengaturan Landing Page</h1>
        <p class="text-muted mb-0" style="font-size:.85rem;">Ubah konten website tanpa coding</p>
    </div>
    <a href="<?= url('/') ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-3">
        <i class="bi bi-box-arrow-up-right me-1"></i> Lihat Website
    </a>
</div>

<?php if ($ok): ?>
<div class="alert alert-success rounded-3 py-2 px-3 mb-3" style="font-size:.85rem;">
    <i class="bi bi-check-circle me-1"></i><?= htmlspecialchars($ok) ?>
</div>
<?php endif; ?>
<?php if ($err): ?>
<div class="alert alert-danger rounded-3 py-2 px-3 mb-3" style="font-size:.85rem;">
    <i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($err) ?>
</div>
<?php endif; ?>

<form method="POST" action="<?= url('/admin/pengaturan') ?>" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCsrf() ?>">

    <div class="row g-3">

        <!-- IDENTITAS INSTITUSI -->
        <div class="col-lg-6">
            <div class="card border-0 rounded-3 h-100" style="box-shadow:0 2px 12px rgba(0,0,0,.06);">
                <div class="card-body p-4">
                    <h6 class="fw-700 mb-3" style="color:var(--primary);">
                        <i class="bi bi-building me-2"></i>Identitas Institusi
                    </h6>
                    <div class="mb-3">
                        <label class="form-label fw-600 small">Nama Institusi</label>
                        <input type="text" name="settings[site_name]" class="form-control form-control-sm"
                               value="<?= htmlspecialchars($s['site_name'] ?? "Ma'had Aly Ibnu Abbas Karanganyar") ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600 small">Tagline / Motto</label>
                        <input type="text" name="settings[site_tagline]" class="form-control form-control-sm"
                               value="<?= htmlspecialchars($s['site_tagline'] ?? 'Mencetak Generasi Rabbani, Unggul Dalam Ilmu, Berakhlak Mulia dan Berkemajuan') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600 small">Kerjasama dengan</label>
                        <input type="text" name="settings[site_kerjasama]" class="form-control form-control-sm"
                               value="<?= htmlspecialchars($s['site_kerjasama'] ?? 'Bekerjasama dengan Institut Muhammadiyah Ngawi') ?>">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-600 small">Upload Logo</label>
                        <input type="file" name="logo" class="form-control form-control-sm" accept="image/*">
                        <?php if (!empty($s['logo_path'])): ?>
                        <div class="mt-2 d-flex align-items-center gap-2">
                            <img src="<?= htmlspecialchars(BASE_URL . $s['logo_path']) ?>" alt="Logo"
                                 style="height:44px;border-radius:6px;border:1px solid #e2e8f0;">
                            <span style="font-size:.72rem;color:#94a3b8;">Logo saat ini</span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- HERO SECTION -->
        <div class="col-lg-6">
            <div class="card border-0 rounded-3 h-100" style="box-shadow:0 2px 12px rgba(0,0,0,.06);">
                <div class="card-body p-4">
                    <h6 class="fw-700 mb-3" style="color:var(--primary);">
                        <i class="bi bi-layout-text-window me-2"></i>Hero Section
                    </h6>
                    <div class="mb-3">
                        <label class="form-label fw-600 small">Judul Hero</label>
                        <input type="text" name="settings[hero_title]" class="form-control form-control-sm"
                               value="<?= htmlspecialchars($s['hero_title'] ?? 'Penerimaan Mahasiswa Baru') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600 small">Subjudul Hero</label>
                        <input type="text" name="settings[hero_subtitle]" class="form-control form-control-sm"
                               value="<?= htmlspecialchars($s['hero_subtitle'] ?? 'Wujudkan Impianmu Bersama Kami') ?>">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-600 small">Upload Banner Hero</label>
                        <input type="file" name="banner" class="form-control form-control-sm" accept="image/*">
                        <div class="form-text">Gambar latar belakang hero section (opsional)</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KONTAK -->
        <div class="col-lg-6">
            <div class="card border-0 rounded-3 h-100" style="box-shadow:0 2px 12px rgba(0,0,0,.06);">
                <div class="card-body p-4">
                    <h6 class="fw-700 mb-3" style="color:var(--primary);">
                        <i class="bi bi-telephone me-2"></i>Kontak
                    </h6>
                    <div class="mb-3">
                        <label class="form-label fw-600 small">Nomor WhatsApp/Telepon</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="bi bi-whatsapp"></i></span>
                            <input type="text" name="settings[site_phone]" class="form-control"
                                   value="<?= htmlspecialchars($s['site_phone'] ?? '0856-1464-905') ?>"
                                   placeholder="0856-1464-905">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600 small">Email</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="settings[site_email]" class="form-control"
                                   value="<?= htmlspecialchars($s['site_email'] ?? 'info@ibnuabbass.com') ?>"
                                   placeholder="info@ibnuabbass.com">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600 small">Website</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="bi bi-globe"></i></span>
                            <input type="text" name="settings[site_website]" class="form-control"
                                   value="<?= htmlspecialchars($s['site_website'] ?? 'www.ibnuabbass.com') ?>"
                                   placeholder="www.ibnuabbass.com">
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-600 small">Alamat</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                            <input type="text" name="settings[site_alamat]" class="form-control"
                                   value="<?= htmlspecialchars($s['site_alamat'] ?? 'Karanganyar, Jawa Tengah') ?>"
                                   placeholder="Karanganyar, Jawa Tengah">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- WARNA & TAMPILAN -->
        <div class="col-lg-6">
            <div class="card border-0 rounded-3 h-100" style="box-shadow:0 2px 12px rgba(0,0,0,.06);">
                <div class="card-body p-4">
                    <h6 class="fw-700 mb-3" style="color:var(--primary);">
                        <i class="bi bi-palette me-2"></i>Warna & Tampilan
                    </h6>
                    <div class="mb-3">
                        <label class="form-label fw-600 small">Warna Utama</label>
                        <div class="d-flex gap-2 align-items-center">
                            <input type="color" name="settings[color_primary]" class="form-control-color rounded"
                                   value="<?= htmlspecialchars($s['color_primary'] ?? '#1a3a6b') ?>"
                                   style="width:44px;height:36px;border:1px solid #e2e8f0;cursor:pointer;"
                                   oninput="document.getElementById('cp_hex').value=this.value">
                            <input type="text" id="cp_hex" class="form-control form-control-sm"
                                   value="<?= htmlspecialchars($s['color_primary'] ?? '#1a3a6b') ?>"
                                   placeholder="#1a3a6b" style="max-width:120px;"
                                   oninput="document.querySelector('[name=\'settings[color_primary]\']').value=this.value">
                            <span style="font-size:.75rem;color:#64748b;">Warna navbar & tombol</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600 small">Warna Aksen (Emas)</label>
                        <div class="d-flex gap-2 align-items-center">
                            <input type="color" name="settings[color_accent]" class="form-control-color rounded"
                                   value="<?= htmlspecialchars($s['color_accent'] ?? '#c9a227') ?>"
                                   style="width:44px;height:36px;border:1px solid #e2e8f0;cursor:pointer;"
                                   oninput="document.getElementById('ca_hex').value=this.value">
                            <input type="text" id="ca_hex" class="form-control form-control-sm"
                                   value="<?= htmlspecialchars($s['color_accent'] ?? '#c9a227') ?>"
                                   placeholder="#c9a227" style="max-width:120px;"
                                   oninput="document.querySelector('[name=\'settings[color_accent]\']').value=this.value">
                            <span style="font-size:.75rem;color:#64748b;">Warna emas aksen</span>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-600 small">Google Maps Embed URL</label>
                        <textarea name="settings[maps_url]" class="form-control form-control-sm" rows="2"
                                  placeholder="Paste URL embed Google Maps di sini..."><?= htmlspecialchars($s['maps_url'] ?? '') ?></textarea>
                        <div class="form-text">Dari Google Maps → Share → Embed a map → Copy URL iframe src</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TENTANG KAMPUS -->
        <div class="col-12">
            <div class="card border-0 rounded-3" style="box-shadow:0 2px 12px rgba(0,0,0,.06);">
                <div class="card-body p-4">
                    <h6 class="fw-700 mb-3" style="color:var(--primary);">
                        <i class="bi bi-info-circle me-2"></i>Tentang Kampus
                    </h6>
                    <textarea name="settings[about_text]" class="form-control form-control-sm" rows="3"
                              placeholder="Deskripsi singkat kampus yang ditampilkan di landing page..."><?= htmlspecialchars($s['about_text'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <div class="col-12">
            <div class="card border-0 rounded-3" style="box-shadow:0 2px 12px rgba(0,0,0,.06);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-700 mb-0" style="color:var(--primary);">
                            <i class="bi bi-question-circle me-2"></i>FAQ (Pertanyaan Umum)
                        </h6>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-3" onclick="addFaq()">
                            <i class="bi bi-plus-circle me-1"></i>Tambah FAQ
                        </button>
                    </div>
                    <div id="faqList">
                        <?php
                        $faqs = json_decode($s['faq_list'] ?? '[]', true) ?: [];
                        if (empty($faqs)) $faqs = [
                            ['q'=>'Apakah pendaftaran bisa dilakukan secara online?','a'=>'Ya, seluruh proses pendaftaran dapat dilakukan secara online melalui website ini.'],
                            ['q'=>'Berapa biaya pendaftaran Program S1?','a'=>'Biaya pendaftaran Program S1 adalah Rp 300.000.'],
                        ];
                        foreach ($faqs as $i => $faq): ?>
                        <div class="faq-item border rounded-3 p-3 mb-2" style="background:#f8fafc;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-600" style="font-size:.78rem;color:#64748b;">FAQ #<?= $i+1 ?></span>
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-3" style="padding:2px 8px;font-size:.72rem;" onclick="removeFaq(this)">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </div>
                            <input type="text" name="faq_q[]" class="form-control form-control-sm mb-2"
                                   placeholder="Pertanyaan..." value="<?= htmlspecialchars($faq['q'] ?? '') ?>">
                            <textarea name="faq_a[]" class="form-control form-control-sm" rows="2"
                                      placeholder="Jawaban..."><?= htmlspecialchars($faq['a'] ?? '') ?></textarea>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- end row -->

    <div class="mt-4 pb-4 d-flex justify-content-end gap-2">
        <a href="<?= url('/admin') ?>" class="btn btn-outline-secondary rounded-3 px-4">Batal</a>
        <button type="submit" class="btn rounded-3 px-5 fw-600" style="background:var(--primary);color:#fff;">
            <i class="bi bi-save me-2"></i>Simpan Semua Pengaturan
        </button>
    </div>
</form>

<script>
let faqCount = <?= count($faqs) ?>;
function addFaq() {
    faqCount++;
    const div = document.createElement('div');
    div.className = 'faq-item border rounded-3 p-3 mb-2';
    div.style.background = '#f8fafc';
    div.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-600" style="font-size:.78rem;color:#64748b;">FAQ #${faqCount}</span>
            <button type="button" class="btn btn-sm btn-outline-danger rounded-3" style="padding:2px 8px;font-size:.72rem;" onclick="removeFaq(this)">
                <i class="bi bi-trash"></i> Hapus
            </button>
        </div>
        <input type="text" name="faq_q[]" class="form-control form-control-sm mb-2" placeholder="Pertanyaan...">
        <textarea name="faq_a[]" class="form-control form-control-sm" rows="2" placeholder="Jawaban..."></textarea>
    `;
    document.getElementById('faqList').appendChild(div);
    div.querySelector('input').focus();
}
function removeFaq(btn) {
    if (document.querySelectorAll('.faq-item').length <= 1) {
        alert('Minimal harus ada 1 FAQ.');
        return;
    }
    if (confirm('Hapus FAQ ini?')) btn.closest('.faq-item').remove();
}
</script>