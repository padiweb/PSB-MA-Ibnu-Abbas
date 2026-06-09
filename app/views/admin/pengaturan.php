<?php // views/admin/pengaturan.php
// $settings dari BaseController sudah berupa array ['key_name' => 'value']
$s = $settings ?? [];
?>
<div class="page-header">
    <h1><i class="bi bi-gear me-2"></i>Pengaturan Landing Page</h1>
    <p class="text-muted mb-0" style="font-size:.85rem;">Ubah konten website tanpa coding</p>
</div>

<form method="POST" action="<?= url('/admin/pengaturan') ?>" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCsrf() ?>">

    <div class="row g-3">
        <!-- Identitas -->
        <div class="col-lg-6">
            <div class="card border-0 rounded-3" style="box-shadow:0 2px 12px rgba(0,0,0,.06);">
                <div class="card-body p-4">
                    <h6 class="fw-700 mb-3" style="color:var(--primary);">
                        <i class="bi bi-building me-2"></i>Identitas Institusi
                    </h6>
                    <div class="mb-3">
                        <label class="form-label fw-600" style="font-size:.82rem;">Nama Institusi</label>
                        <input type="text" name="settings[site_name]" class="form-control form-control-sm"
                               value="<?= htmlspecialchars($s['site_name'] ?? "Ma'had Aly Ibnu Abbas Karanganyar") ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600" style="font-size:.82rem;">Tagline / Motto</label>
                        <input type="text" name="settings[site_tagline]" class="form-control form-control-sm"
                               value="<?= htmlspecialchars($s['site_tagline'] ?? 'Mencetak Generasi Rabbani') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600" style="font-size:.82rem;">Kerjasama dengan</label>
                        <input type="text" name="settings[kerjasama]" class="form-control form-control-sm"
                               value="<?= htmlspecialchars($s['kerjasama'] ?? 'Institut Muhammadiyah Ngawi') ?>">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-600" style="font-size:.82rem;">Upload Logo</label>
                        <input type="file" name="logo" class="form-control form-control-sm" accept="image/*">
                        <?php if (!empty($s['logo_path'])): ?>
                        <div class="mt-2">
                            <img src="<?= htmlspecialchars($s['logo_path']) ?>" alt="Logo" style="height:48px;" class="border rounded">
                            <div style="font-size:.72rem;color:#94a3b8;">Logo saat ini</div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hero Section -->
        <div class="col-lg-6">
            <div class="card border-0 rounded-3" style="box-shadow:0 2px 12px rgba(0,0,0,.06);">
                <div class="card-body p-4">
                    <h6 class="fw-700 mb-3" style="color:var(--primary);">
                        <i class="bi bi-layout-text-window me-2"></i>Hero Section
                    </h6>
                    <div class="mb-3">
                        <label class="form-label fw-600" style="font-size:.82rem;">Judul Hero</label>
                        <input type="text" name="settings[hero_title]" class="form-control form-control-sm"
                               value="<?= htmlspecialchars($s['hero_title'] ?? 'Penerimaan Mahasiswa Baru') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600" style="font-size:.82rem;">Subjudul Hero</label>
                        <textarea name="settings[hero_subtitle]" class="form-control form-control-sm" rows="2"
                                  ><?= htmlspecialchars($s['hero_subtitle'] ?? 'Tahun Akademik 2026/2027') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600" style="font-size:.82rem;">Teks Tombol Daftar</label>
                        <input type="text" name="settings[hero_btn_text]" class="form-control form-control-sm"
                               value="<?= htmlspecialchars($s['hero_btn_text'] ?? 'Daftar Sekarang') ?>">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-600" style="font-size:.82rem;">Upload Banner Hero</label>
                        <input type="file" name="banner" class="form-control form-control-sm" accept="image/*">
                    </div>
                </div>
            </div>
        </div>

        <!-- Tentang Kampus -->
        <div class="col-12">
            <div class="card border-0 rounded-3" style="box-shadow:0 2px 12px rgba(0,0,0,.06);">
                <div class="card-body p-4">
                    <h6 class="fw-700 mb-3" style="color:var(--primary);">
                        <i class="bi bi-info-circle me-2"></i>Tentang Kampus
                    </h6>
                    <div class="mb-0">
                        <label class="form-label fw-600" style="font-size:.82rem;">Deskripsi Singkat</label>
                        <textarea name="settings[about_text]" class="form-control form-control-sm" rows="4"
                                  placeholder="Deskripsi kampus yang ditampilkan di landing page..."
                                  ><?= htmlspecialchars($s['about_text'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kontak -->
        <div class="col-lg-6">
            <div class="card border-0 rounded-3" style="box-shadow:0 2px 12px rgba(0,0,0,.06);">
                <div class="card-body p-4">
                    <h6 class="fw-700 mb-3" style="color:var(--primary);">
                        <i class="bi bi-telephone me-2"></i>Kontak
                    </h6>
                    <?php $contactFields = [
                        ['kontak_telepon','Nomor WhatsApp/Telepon','0856-1464-905'],
                        ['kontak_email','Email','info@ibnuabbass.com'],
                        ['kontak_alamat','Alamat','Karanganyar, Jawa Tengah'],
                        ['kontak_website','Website','www.ibnuabbass.com'],
                    ];
                    foreach ($contactFields as [$key, $lbl, $def]): ?>
                    <div class="mb-3">
                        <label class="form-label fw-600" style="font-size:.82rem;"><?= $lbl ?></label>
                        <input type="text" name="settings[<?= $key ?>]" class="form-control form-control-sm"
                               value="<?= htmlspecialchars($s[$key] ?? $def) ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Warna & Tampilan -->
        <div class="col-lg-6">
            <div class="card border-0 rounded-3" style="box-shadow:0 2px 12px rgba(0,0,0,.06);">
                <div class="card-body p-4">
                    <h6 class="fw-700 mb-3" style="color:var(--primary);">
                        <i class="bi bi-palette me-2"></i>Warna & Tampilan
                    </h6>
                    <div class="mb-3">
                        <label class="form-label fw-600" style="font-size:.82rem;">Warna Utama</label>
                        <div class="d-flex gap-2 align-items-center">
                            <input type="color" name="settings[color_primary]" class="form-control form-control-sm form-control-color"
                                   value="<?= htmlspecialchars($s['color_primary'] ?? '#1a3a6b') ?>" style="width:50px;height:36px;">
                            <input type="text" name="settings[color_primary_hex]" class="form-control form-control-sm"
                                   value="<?= htmlspecialchars($s['color_primary'] ?? '#1a3a6b') ?>" placeholder="#1a3a6b">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600" style="font-size:.82rem;">Warna Aksen (Emas)</label>
                        <div class="d-flex gap-2 align-items-center">
                            <input type="color" name="settings[color_accent]" class="form-control form-control-sm form-control-color"
                                   value="<?= htmlspecialchars($s['color_accent'] ?? '#c9a227') ?>" style="width:50px;height:36px;">
                            <input type="text" name="settings[color_accent_hex]" class="form-control form-control-sm"
                                   value="<?= htmlspecialchars($s['color_accent'] ?? '#c9a227') ?>" placeholder="#c9a227">
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-600" style="font-size:.82rem;">Google Maps Embed URL</label>
                        <input type="text" name="settings[maps_url]" class="form-control form-control-sm"
                               value="<?= htmlspecialchars($s['maps_url'] ?? '') ?>" placeholder="URL embed Google Maps...">
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <div class="col-12">
            <div class="card border-0 rounded-3" style="box-shadow:0 2px 12px rgba(0,0,0,.06);">
                <div class="card-body p-4">
                    <h6 class="fw-700 mb-3" style="color:var(--primary);">
                        <i class="bi bi-question-circle me-2"></i>FAQ (Pertanyaan Umum)
                    </h6>
                    <div id="faqList">
                        <?php
                        $faqs = json_decode($s['faq_list'] ?? '[]', true) ?: [];
                        if (empty($faqs)) $faqs = [['q'=>'','a'=>''],['q'=>'','a'=>'']];
                        foreach ($faqs as $i => $faq): ?>
                        <div class="faq-item border rounded-2 p-3 mb-2" style="background:#f8fafc;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-600" style="font-size:.8rem;color:#64748b;">FAQ #<?= $i+1 ?></span>
                                <button type="button" class="btn btn-sm btn-outline-danger" style="padding:2px 8px;font-size:.72rem;"
                                        onclick="removeFaq(this)">Hapus</button>
                            </div>
                            <input type="text" name="faq_q[]" class="form-control form-control-sm mb-2"
                                   placeholder="Pertanyaan..." value="<?= htmlspecialchars($faq['q'] ?? '') ?>">
                            <textarea name="faq_a[]" class="form-control form-control-sm" rows="2"
                                      placeholder="Jawaban..."><?= htmlspecialchars($faq['a'] ?? '') ?></textarea>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addFaq()">
                        <i class="bi bi-plus-circle me-1"></i> Tambah FAQ
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-end">
        <button type="submit" class="btn px-4" style="background:var(--primary);color:#fff;">
            <i class="bi bi-save me-2"></i> Simpan Semua Pengaturan
        </button>
    </div>
</form>

<script>
let faqCount = <?= count($faqs) ?>;
function addFaq() {
    faqCount++;
    const div = document.createElement('div');
    div.className = 'faq-item border rounded-2 p-3 mb-2';
    div.style.background = '#f8fafc';
    div.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-600" style="font-size:.8rem;color:#64748b;">FAQ #${faqCount}</span>
            <button type="button" class="btn btn-sm btn-outline-danger" style="padding:2px 8px;font-size:.72rem;" onclick="removeFaq(this)">Hapus</button>
        </div>
        <input type="text" name="faq_q[]" class="form-control form-control-sm mb-2" placeholder="Pertanyaan...">
        <textarea name="faq_a[]" class="form-control form-control-sm" rows="2" placeholder="Jawaban..."></textarea>
    `;
    document.getElementById('faqList').appendChild(div);
}
function removeFaq(btn) {
    btn.closest('.faq-item').remove();
}
</script>
