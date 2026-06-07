<?php
// views/public/daftar.php
$prodiList  = $prodi_list ?? [];
$promoInfo  = $promo_info ?? null;
$tahunAktif = $tahun_aktif ?? null;
?>
<section style="background:linear-gradient(135deg,var(--primary) 0%,#1e5ca8 100%);padding:48px 0 80px;">
    <div class="container text-center">
        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3"
             style="background:rgba(201,162,39,.2);border:1px solid rgba(201,162,39,.4);">
            <span style="color:var(--accent);font-size:.8rem;font-weight:600;">
                PMB <?= htmlspecialchars($tahunAktif['nama'] ?? '2026/2027') ?>
            </span>
        </div>
        <h1 class="text-white fw-700 mb-2" style="font-family:'Playfair Display',serif;font-size:clamp(1.5rem,4vw,2.2rem);">
            Formulir Pendaftaran Mahasiswa Baru
        </h1>
        <p class="text-white mb-0" style="opacity:.8;font-size:.92rem;">
            Ma'had Aly Ibnu Abbas Karanganyar
        </p>
    </div>
</section>

<div class="container" style="margin-top:-40px;padding-bottom:64px;">
    <div class="row justify-content-center">
        <div class="col-lg-9">

            <!-- STEP INDICATOR -->
            <div class="card border-0 rounded-4 shadow mb-4 p-4">
                <div class="d-flex align-items-center justify-content-between" id="stepIndicator">
                    <?php $steps = ['Data Diri','Pilih Program','Upload Dokumen','Review','Kirim']; ?>
                    <?php foreach ($steps as $i => $step): ?>
                    <div class="step-item d-flex flex-column align-items-center flex-fill <?= $i === 0 ? 'active' : '' ?>" data-step="<?= $i+1 ?>">
                        <div class="step-circle <?= $i === 0 ? 'active' : '' ?>">
                            <span class="step-num"><?= $i+1 ?></span>
                            <i class="bi bi-check-lg step-check" style="display:none;"></i>
                        </div>
                        <div class="step-label d-none d-md-block"><?= $step ?></div>
                    </div>
                    <?php if ($i < 4): ?>
                    <div class="step-line flex-fill" style="height:2px;background:#e2e8f0;margin:0 4px;position:relative;top:-18px;" id="line-<?= $i+1 ?>"></div>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- FORM CONTAINER -->
            <div class="card border-0 rounded-4 shadow">
                <form id="regForm" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">
                    <input type="hidden" name="tahun_akademik_id" value="<?= $tahunAktif['id'] ?? '' ?>">

                    <!-- STEP 1: DATA DIRI -->
                    <div class="form-step active" data-step="1">
                        <div class="card-body p-4 p-lg-5">
                            <h5 class="fw-700 mb-4" style="color:var(--primary);">
                                <i class="bi bi-person-fill me-2"></i>Data Diri Pendaftar
                            </h5>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-600 required">Nama Lengkap</label>
                                    <input type="text" name="nama_lengkap" class="form-control" required
                                           placeholder="Sesuai Ijazah / Dikti">
                                    <div class="form-text">Nama sesuai ijazah/Dikti S1 untuk program S2, atau sesuai ijazah SMA</div>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-600 required">Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir" class="form-control" required placeholder="Kota">
                                </div>
                                <div class="col-md-7">
                                    <label class="form-label fw-600 required">Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-600 required">Nomor HP / WhatsApp</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                        <input type="tel" name="nomor_hp" class="form-control" required
                                               placeholder="08xxxxxxxxxx">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-600 required">Nama Ibu Kandung</label>
                                    <input type="text" name="nama_ibu_kandung" class="form-control" required placeholder="Nama ibu kandung">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-600 required">Alamat Lengkap (sesuai KTP)</label>
                                    <textarea name="alamat_ktp" class="form-control" rows="2" required
                                              placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota/Kabupaten, Provinsi"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-600 required">Password Akun</label>
                                    <div class="input-group">
                                        <input type="password" name="password" class="form-control" id="pw1" required minlength="8"
                                               placeholder="Min. 8 karakter">
                                        <button type="button" class="btn btn-outline-secondary" onclick="togglePw('pw1',this)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">Minimal 8 karakter, kombinasi huruf dan angka</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-600 required">Konfirmasi Password</label>
                                    <div class="input-group">
                                        <input type="password" name="password_confirm" class="form-control" id="pw2" required
                                               placeholder="Ulangi password">
                                        <button type="button" class="btn btn-outline-secondary" onclick="togglePw('pw2',this)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent p-4 border-top d-flex justify-content-end">
                            <button type="button" class="btn btn-next px-4" style="background:var(--primary);color:#fff;">
                                Berikutnya <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 2: PILIH PROGRAM -->
                    <div class="form-step" data-step="2" style="display:none;">
                        <div class="card-body p-4 p-lg-5">
                            <h5 class="fw-700 mb-4" style="color:var(--primary);">
                                <i class="bi bi-mortarboard me-2"></i>Pilih Program Studi
                            </h5>

                            <?php if ($promoInfo): ?>
                            <div class="alert d-flex align-items-center gap-3 mb-4" style="background:linear-gradient(135deg,#fffbeb,#fef3c7);border:1px solid #fde68a;border-radius:12px;">
                                <i class="bi bi-gift" style="font-size:1.5rem;color:var(--accent);"></i>
                                <div>
                                    <div class="fw-700" style="color:#92400e;"><?= htmlspecialchars($promoInfo['nama_promo']) ?></div>
                                    <div style="font-size:.82rem;color:#b45309;"><?= htmlspecialchars($promoInfo['deskripsi']) ?>
                                        — Sisa kuota: <strong><?= $promoInfo['sisa_kuota'] ?></strong></div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div id="prodiCards" class="row g-3">
                                <?php
                                $grouped = [];
                                foreach ($prodiList as $pr) {
                                    $grouped[$pr['jenjang']][] = $pr;
                                }
                                foreach ($grouped as $jenjang => $prodis): ?>
                                <div class="col-12">
                                    <h6 class="fw-700 mb-2" style="color:var(--primary);font-size:.85rem;text-transform:uppercase;letter-spacing:.06em;">
                                        Program <?= htmlspecialchars($jenjang) ?>
                                    </h6>
                                </div>
                                <?php foreach ($prodis as $pr): ?>
                                <div class="col-md-6">
                                    <label class="prodi-card" for="prodi_<?= $pr['id'] ?>">
                                        <input type="radio" name="program_studi_id" id="prodi_<?= $pr['id'] ?>"
                                               value="<?= $pr['id'] ?>" class="prodi-radio" required
                                               data-jenjang="<?= htmlspecialchars($pr['jenjang']) ?>"
                                               data-nama="<?= htmlspecialchars($pr['nama_prodi']) ?>"
                                               data-biaya-daftar="<?= $pr['biaya_pendaftaran'] ?? 0 ?>">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="prodi-check">
                                                <i class="bi bi-check-circle-fill" style="font-size:1.2rem;color:var(--primary);opacity:0;transition:.2s;"></i>
                                            </div>
                                            <div class="flex-fill">
                                                <div class="fw-700" style="font-size:.9rem;color:var(--primary);">
                                                    <?= htmlspecialchars($pr['nama_prodi']) ?>
                                                </div>
                                                <div style="font-size:.75rem;color:#64748b;margin-top:2px;">
                                                    <?= htmlspecialchars($pr['nama_fakultas'] ?? '') ?>
                                                    &bull; Gelar: <?= htmlspecialchars($pr['gelar'] ?? '') ?>
                                                </div>
                                                <div class="mt-2 d-flex gap-3" style="font-size:.78rem;">
                                                    <span><i class="bi bi-receipt me-1" style="color:var(--accent);"></i>
                                                        Daftar: <strong>Rp <?= number_format($pr['biaya_pendaftaran'] ?? 0) ?></strong>
                                                    </span>
                                                    <span class="badge <?= $jenjang === 'S2' ? 'bg-warning text-dark' : 'bg-primary' ?>"><?= $jenjang ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                                <?php endforeach; ?>
                            </div>

                            <div id="biayaInfo" class="mt-4 p-3 rounded-3" style="background:#f0f9ff;border:1px solid #bae6fd;display:none;">
                                <h6 class="fw-700 mb-2" style="color:#0369a1;">Rincian Biaya Program Dipilih</h6>
                                <div id="biayaDetail" style="font-size:.85rem;"></div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent p-4 border-top d-flex justify-content-between">
                            <button type="button" class="btn btn-prev btn-outline-secondary px-4">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </button>
                            <button type="button" class="btn btn-next px-4" style="background:var(--primary);color:#fff;">
                                Berikutnya <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 3: UPLOAD DOKUMEN -->
                    <div class="form-step" data-step="3" style="display:none;">
                        <div class="card-body p-4 p-lg-5">
                            <h5 class="fw-700 mb-2" style="color:var(--primary);">
                                <i class="bi bi-cloud-upload me-2"></i>Upload Dokumen
                            </h5>
                            <p class="text-muted mb-4" style="font-size:.85rem;">
                                Format: PDF, JPG, JPEG, PNG. Maksimal 5 MB per file.
                            </p>

                            <div class="row g-3" id="uploadFields">
                                <?php
                                $requiredDocs = [
                                    ['key'=>'ktp',       'label'=>'Scan KTP Asli',           'required'=>true],
                                    ['key'=>'kk',        'label'=>'Scan Kartu Keluarga',      'required'=>true],
                                    ['key'=>'akte',      'label'=>'Scan Akte Kelahiran',      'required'=>true],
                                    ['key'=>'ijazah_sma','label'=>'Scan Ijazah SMA + Transkrip', 'required'=>true],
                                    ['key'=>'ijazah_s1', 'label'=>'Scan Ijazah S1 + Transkrip',  'required'=>false],
                                    ['key'=>'foto',      'label'=>'Foto Resmi (Jas Hitam, Background Biru)', 'required'=>true],
                                ];
                                foreach ($requiredDocs as $doc): ?>
                                <div class="col-md-6">
                                    <div class="upload-area rounded-3 p-3" style="border:2px dashed #e2e8f0;transition:.2s;"
                                         ondragover="handleDragOver(event,this)" ondragleave="handleDragLeave(this)"
                                         ondrop="handleDrop(event,'doc_<?= $doc['key'] ?>')">
                                        <label class="form-label fw-600 mb-2" style="font-size:.82rem;">
                                            <?= $doc['label'] ?>
                                            <?php if ($doc['required']): ?>
                                            <span class="text-danger">*</span>
                                            <?php else: ?>
                                            <span class="text-muted fw-400">(boleh menyusul)</span>
                                            <?php endif; ?>
                                        </label>
                                        <input type="file" name="doc_<?= $doc['key'] ?>" id="doc_<?= $doc['key'] ?>"
                                               class="form-control form-control-sm"
                                               accept=".pdf,.jpg,.jpeg,.png"
                                               <?= $doc['required'] ? 'required' : '' ?>
                                               onchange="previewFile(this,'prev_<?= $doc['key'] ?>')">
                                        <div id="prev_<?= $doc['key'] ?>" class="mt-2" style="display:none;"></div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent p-4 border-top d-flex justify-content-between">
                            <button type="button" class="btn btn-prev btn-outline-secondary px-4">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </button>
                            <button type="button" class="btn btn-next px-4" style="background:var(--primary);color:#fff;">
                                Berikutnya <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 4: REVIEW -->
                    <div class="form-step" data-step="4" style="display:none;">
                        <div class="card-body p-4 p-lg-5">
                            <h5 class="fw-700 mb-4" style="color:var(--primary);">
                                <i class="bi bi-clipboard-check me-2"></i>Review Data Pendaftaran
                            </h5>

                            <div class="row g-3 mb-3" id="reviewContent">
                                <!-- Diisi via JS -->
                                <div class="col-12 text-center py-3">
                                    <div class="text-muted">Memuat data review...</div>
                                </div>
                            </div>

                            <div class="alert" style="background:#fffbeb;border:1px solid #fde68a;border-radius:12px;">
                                <div class="d-flex align-items-start gap-2">
                                    <i class="bi bi-exclamation-triangle text-warning mt-1"></i>
                                    <div style="font-size:.83rem;color:#92400e;">
                                        Periksa kembali semua data sebelum submit. Pastikan dokumen yang diunggah
                                        jelas dan dapat dibaca. Data yang sudah dikirim akan diverifikasi oleh tim admin.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent p-4 border-top d-flex justify-content-between">
                            <button type="button" class="btn btn-prev btn-outline-secondary px-4">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </button>
                            <button type="button" class="btn btn-next px-4" style="background:var(--primary);color:#fff;">
                                Konfirmasi & Kirim <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 5: SUBMIT -->
                    <div class="form-step" data-step="5" style="display:none;">
                        <div class="card-body p-4 p-lg-5 text-center">
                            <div id="submitProgress" class="py-4">
                                <div class="mb-4">
                                    <div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem;"></div>
                                </div>
                                <h5 class="fw-700" style="color:var(--primary);">Mengirim Pendaftaran...</h5>
                                <p class="text-muted">Mohon tunggu, jangan tutup halaman ini.</p>
                            </div>
                            <div id="submitResult" style="display:none;"></div>
                        </div>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>

<style>
.form-label.required::after { content: ' *'; color: #dc2626; }
.prodi-card {
    display: block; padding: 16px; border: 2px solid #e2e8f0;
    border-radius: 12px; cursor: pointer; transition: .2s;
}
.prodi-card:hover { border-color: var(--primary); background: #f8fafc; }
.prodi-card.selected { border-color: var(--primary); background: #eff6ff; }
.prodi-radio { position: absolute; opacity: 0; pointer-events: none; }
.step-circle {
    width: 36px; height: 36px; border-radius: 50%;
    border: 2px solid #e2e8f0; display: flex; align-items: center;
    justify-content: center; font-weight: 700; font-size: .85rem;
    background: #fff; color: #94a3b8; transition: .3s;
}
.step-circle.active { border-color: var(--primary); background: var(--primary); color: #fff; }
.step-circle.done { border-color: #16a34a; background: #16a34a; color: #fff; }
.step-label { font-size: .72rem; font-weight: 600; color: #94a3b8; margin-top: 6px; text-align: center; }
.step-item.active .step-label { color: var(--primary); }
.step-item.done .step-label { color: #16a34a; }
.upload-area:hover { border-color: var(--primary) !important; }
</style>

<script>
// Multi-step form logic
let currentStep = 1;
const totalSteps = 5;
const formData = {};

function showStep(step) {
    document.querySelectorAll('.form-step').forEach(s => s.style.display = 'none');
    document.querySelector(`.form-step[data-step="${step}"]`).style.display = 'block';

    // Update step indicators
    document.querySelectorAll('.step-item').forEach((item, i) => {
        const n = i < 1 ? 0 : Math.floor(i / 2); // account for line dividers
        item.classList.remove('active','done');
    });
    document.querySelectorAll('.step-circle').forEach((c, i) => {
        const stepNum = i + 1;
        c.classList.remove('active','done');
        c.querySelector('.step-num').style.display = 'inline';
        c.querySelector('.step-check').style.display = 'none';
        if (stepNum < step) {
            c.classList.add('done');
            c.querySelector('.step-num').style.display = 'none';
            c.querySelector('.step-check').style.display = 'inline';
        } else if (stepNum === step) {
            c.classList.add('active');
        }
    });
    document.querySelectorAll('.step-item').forEach((item, i) => {
        item.classList.remove('active','done');
        const sn = i + 1;
        if (sn < step) item.classList.add('done');
        if (sn === step) item.classList.add('active');
    });

    if (step === 4) buildReview();
    if (step === 5) submitForm();

    currentStep = step;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function validateStep(step) {
    const stepEl = document.querySelector(`.form-step[data-step="${step}"]`);
    const inputs = stepEl.querySelectorAll('[required]');
    let valid = true;
    inputs.forEach(input => {
        input.classList.remove('is-invalid');
        if (!input.value || (input.type === 'file' && input.files.length === 0)) {
            input.classList.add('is-invalid');
            valid = false;
        }
    });
    if (step === 1) {
        const pw1 = document.querySelector('[name=password]').value;
        const pw2 = document.querySelector('[name=password_confirm]').value;
        if (pw1 !== pw2) {
            document.querySelector('[name=password_confirm]').classList.add('is-invalid');
            showToast('Password dan konfirmasi password tidak cocok', 'danger');
            return false;
        }
    }
    if (!valid) showToast('Lengkapi semua field yang wajib diisi', 'danger');
    return valid;
}

document.querySelectorAll('.btn-next').forEach(btn => {
    btn.addEventListener('click', function () {
        if (currentStep < totalSteps && validateStep(currentStep)) {
            showStep(currentStep + 1);
        }
    });
});
document.querySelectorAll('.btn-prev').forEach(btn => {
    btn.addEventListener('click', function () {
        if (currentStep > 1) showStep(currentStep - 1);
    });
});

// Prodi selection
document.querySelectorAll('.prodi-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.prodi-card').forEach(c => c.classList.remove('selected'));
        this.closest('.prodi-card').classList.add('selected');
        this.closest('.prodi-card').querySelector('.bi-check-circle-fill').style.opacity = '1';
        updateBiayaInfo(this.dataset);
    });
});

function updateBiayaInfo(data) {
    const box = document.getElementById('biayaInfo');
    const detail = document.getElementById('biayaDetail');
    box.style.display = 'block';
    const biaya = parseInt(data.biayaDaftar || 0);
    detail.innerHTML = `
        <div><strong>Program:</strong> ${data.nama} (${data.jenjang})</div>
        <div class="mt-1"><strong>Biaya Pendaftaran:</strong> Rp ${biaya.toLocaleString('id')}</div>
    `;
}

// File preview
function previewFile(input, previewId) {
    const prev = document.getElementById(previewId);
    const file = input.files[0];
    if (!file) { prev.style.display = 'none'; return; }
    if (file.size > 5 * 1024 * 1024) {
        input.value = '';
        showToast('Ukuran file maksimal 5 MB', 'danger');
        return;
    }
    const ext = file.name.split('.').pop().toLowerCase();
    if (!['pdf','jpg','jpeg','png'].includes(ext)) {
        input.value = '';
        showToast('Format file tidak didukung (PDF, JPG, PNG)', 'danger');
        return;
    }
    prev.style.display = 'block';
    if (ext === 'pdf') {
        prev.innerHTML = `<div class="d-flex align-items-center gap-2 p-2 rounded" style="background:#fff1f2;border:1px solid #fecdd3;font-size:.78rem;">
            <i class="bi bi-file-earmark-pdf text-danger fs-5"></i>
            <span class="fw-600">${file.name}</span>
            <span class="text-muted">(${(file.size/1024/1024).toFixed(1)} MB)</span>
        </div>`;
    } else {
        const reader = new FileReader();
        reader.onload = e => {
            prev.innerHTML = `<img src="${e.target.result}" style="max-height:80px;max-width:100%;border-radius:6px;border:1px solid #e2e8f0;">`;
        };
        reader.readAsDataURL(file);
    }
}

// Drag & drop
function handleDragOver(e, el) { e.preventDefault(); el.style.borderColor = 'var(--primary)'; el.style.background = '#f0f9ff'; }
function handleDragLeave(el) { el.style.borderColor = '#e2e8f0'; el.style.background = ''; }
function handleDrop(e, inputId) {
    e.preventDefault();
    const input = document.getElementById(inputId);
    const file = e.dataTransfer.files[0];
    if (file) {
        const dt = new DataTransfer(); dt.items.add(file);
        input.files = dt.files;
        previewFile(input, 'prev_' + inputId.replace('doc_', ''));
    }
    handleDragLeave(e.currentTarget);
}

// Build review
function buildReview() {
    const form = document.getElementById('regForm');
    const fd = new FormData(form);
    const prodiRadio = document.querySelector('.prodi-radio:checked');
    let html = `
        <div class="col-md-6">
            <div class="p-3 rounded-3" style="background:#f8fafc;border:1px solid #e2e8f0;font-size:.85rem;">
                <div class="fw-700 mb-2" style="color:var(--primary);"><i class="bi bi-person me-1"></i> Data Diri</div>
                <table style="width:100%;">
                    <tr><td style="color:#64748b;width:130px;">Nama</td><td class="fw-600">${fd.get('nama_lengkap')||'-'}</td></tr>
                    <tr><td style="color:#64748b;">Tempat, Tgl Lahir</td><td>${fd.get('tempat_lahir')||''}, ${fd.get('tanggal_lahir')||''}</td></tr>
                    <tr><td style="color:#64748b;">No. HP</td><td>${fd.get('nomor_hp')||'-'}</td></tr>
                    <tr><td style="color:#64748b;">Nama Ibu</td><td>${fd.get('nama_ibu_kandung')||'-'}</td></tr>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <div class="p-3 rounded-3" style="background:#f8fafc;border:1px solid #e2e8f0;font-size:.85rem;">
                <div class="fw-700 mb-2" style="color:var(--primary);"><i class="bi bi-mortarboard me-1"></i> Program Studi</div>
                <div class="fw-600">${prodiRadio ? prodiRadio.dataset.nama : '-'}</div>
                <div class="text-muted mt-1">${prodiRadio ? prodiRadio.dataset.jenjang : ''}</div>
                <div class="mt-2">Biaya Pendaftaran: <strong>Rp ${prodiRadio ? parseInt(prodiRadio.dataset.biayaDaftar).toLocaleString('id') : '-'}</strong></div>
            </div>
        </div>`;
    document.getElementById('reviewContent').innerHTML = html;
}

// Toggle password
function togglePw(id, btn) {
    const input = document.getElementById(id);
    const show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    btn.innerHTML = show ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
}

// Submit
function submitForm() {
    const form = document.getElementById('regForm');
    const fd = new FormData(form);
    fetch('/daftar/submit', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            document.getElementById('submitProgress').style.display = 'none';
            const result = document.getElementById('submitResult');
            result.style.display = 'block';
            if (data.success) {
                result.innerHTML = `
                    <div class="py-2">
                        <div style="font-size:3rem;color:#16a34a;"><i class="bi bi-check-circle-fill"></i></div>
                        <h4 class="fw-700 mt-3" style="color:var(--primary);">Pendaftaran Berhasil!</h4>
                        <p class="text-muted">Nomor pendaftaran Anda:</p>
                        <div class="d-inline-block px-4 py-2 rounded-3 mb-3" style="background:#f0f9ff;border:2px solid #bae6fd;">
                            <span style="font-size:1.4rem;font-weight:700;color:var(--primary);letter-spacing:.08em;">${data.nomor}</span>
                        </div>
                        <p class="text-muted mb-4" style="font-size:.85rem;">
                            Simpan nomor pendaftaran ini. Gunakan untuk login ke dashboard pendaftar.
                        </p>
                        <div class="d-flex gap-3 justify-content-center">
                            <a href="/daftar/sukses/${data.nomor}" class="btn px-4" style="background:var(--primary);color:#fff;">
                                <i class="bi bi-house me-1"></i> Lihat Status
                            </a>
                        </div>
                    </div>`;
            } else {
                result.innerHTML = `
                    <div class="py-2">
                        <div style="font-size:3rem;color:#dc2626;"><i class="bi bi-x-circle-fill"></i></div>
                        <h5 class="fw-700 mt-3 text-danger">Pendaftaran Gagal</h5>
                        <p class="text-muted">${data.message || 'Terjadi kesalahan. Silakan coba lagi.'}</p>
                        <button class="btn btn-outline-primary mt-2" onclick="showStep(4)">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </button>
                    </div>`;
            }
        })
        .catch(() => {
            document.getElementById('submitProgress').style.display = 'none';
            document.getElementById('submitResult').style.display = 'block';
            document.getElementById('submitResult').innerHTML = `
                <div class="py-2">
                    <div style="font-size:3rem;color:#dc2626;"><i class="bi bi-wifi-off"></i></div>
                    <h5 class="fw-700 mt-3 text-danger">Koneksi Terputus</h5>
                    <p class="text-muted">Periksa koneksi internet Anda dan coba lagi.</p>
                    <button class="btn btn-outline-primary mt-2" onclick="showStep(4)">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </button>
                </div>`;
        });
}
</script>
