<?php
// views/pendaftar/dashboard.php
$p    = $pendaftar ?? [];
$docs = $dokumen ?? [];
$logs = $riwayat ?? $verifikasi_log ?? [];

$statusColors = [
    'menunggu' => ['bg'=>'#fff7ed','border'=>'#fed7aa','text'=>'#c2410c','icon'=>'bi-hourglass-split','label'=>'Menunggu Verifikasi'],
    'diterima' => ['bg'=>'#f0fdf4','border'=>'#86efac','text'=>'#166534','icon'=>'bi-check-circle-fill','label'=>'Diterima'],
    'revisi'   => ['bg'=>'#eff6ff','border'=>'#bfdbfe','text'=>'#1d4ed8','icon'=>'bi-pencil-square','label'=>'Perlu Revisi'],
    'ditolak'  => ['bg'=>'#fff1f2','border'=>'#fecdd3','text'=>'#be123c','icon'=>'bi-x-circle-fill','label'=>'Ditolak'],
];
$st = $statusColors[$p['status'] ?? 'menunggu'];
?>

<!-- TOPBAR PENDAFTAR -->
<nav class="navbar" style="background:#fff;border-bottom:1px solid #e2e8f0;padding:0 0;">
    <div class="container">
        <a href="<?= url('/') ?>" class="navbar-brand d-flex align-items-center gap-2">
            <div style="width:32px;height:32px;background:var(--primary);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:.85rem;">M</div>
            <span style="font-weight:700;font-size:.9rem;color:var(--primary);">Ma'had Aly PMB</span>
        </a>
        <div class="d-flex align-items-center gap-3">
            <a href="<?= url('/pendaftar/cetak/' . $p['id']) ?>" target="_blank" class="btn btn-sm btn-outline-primary" style="font-size:.78rem;">
                <i class="bi bi-printer me-1"></i> Cetak Bukti
            </a>
            <?php if (in_array($p['status'] ?? '', ['draft','menunggu','revisi'])): ?>
            <a href="<?= url('/pendaftar/edit') ?>" class="btn btn-sm btn-outline-warning" style="font-size:.78rem;">
                <i class="bi bi-pencil-square me-1"></i> Edit Data
            </a>
            <?php endif; ?>
            <a href="<?= url('/logout') ?>" class="btn btn-sm btn-outline-danger" style="font-size:.78rem;">
                <i class="bi bi-power me-1"></i> Keluar
            </a>
        </div>
    </div>
</nav>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">

            <!-- GREETING -->
            <div class="mb-4">
                <h4 class="fw-700" style="color:var(--primary);">Assalamu'alaikum, <?= htmlspecialchars(explode(' ', $p['nama_lengkap'] ?? 'Pendaftar')[0]) ?></h4>
                <p class="text-muted mb-0" style="font-size:.88rem;">Dashboard Pendaftar PMB Ma'had Aly Ibnu Abbas</p>
            </div>

            <!-- STATUS CARD -->
            <div class="card border-0 rounded-4 mb-4 overflow-hidden"
                 style="background:<?= $st['bg'] ?>;border:2px solid <?= $st['border'] ?>!important;box-shadow:0 4px 20px rgba(0,0,0,.08);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-4">
                        <div style="font-size:3rem;color:<?= $st['text'] ?>;"><i class="bi <?= $st['icon'] ?>"></i></div>
                        <div class="flex-fill">
                            <div style="font-size:.75rem;color:<?= $st['text'] ?>;font-weight:700;text-transform:uppercase;letter-spacing:.06em;">Status Pendaftaran</div>
                            <div style="font-size:1.4rem;font-weight:800;color:<?= $st['text'] ?>;line-height:1.2;"><?= $st['label'] ?></div>
                            <?php if (!empty($logs) && $logs[0]['catatan']): ?>
                            <div class="mt-1" style="font-size:.82rem;color:<?= $st['text'] ?>;opacity:.8;">
                                <i class="bi bi-chat-left-text me-1"></i><?= htmlspecialchars($logs[0]['catatan']) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="d-none d-md-block text-end">
                            <div style="font-size:.72rem;color:<?= $st['text'] ?>;font-weight:600;">Nomor Pendaftaran</div>
                            <code style="font-size:1.1rem;font-weight:700;color:<?= $st['text'] ?>;">
                                <?= htmlspecialchars($p['nomor_pendaftaran'] ?? '') ?>
                            </code>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DATA RINGKASAN -->
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <div class="card border-0 rounded-3" style="box-shadow:0 2px 12px rgba(0,0,0,.06);">
                        <div class="card-body p-4">
                            <h6 class="fw-700 mb-3" style="color:var(--primary);">
                                <i class="bi bi-person-vcard me-2"></i>Data Diri Lengkap
                            </h6>
                            <div class="row g-0">
                                <?php
                                $jk = ($p['jenis_kelamin'] ?? '') === 'L' ? 'Laki-laki' : (($p['jenis_kelamin'] ?? '') === 'P' ? 'Perempuan' : '-');
                                $allFields = [
                                    ['Nama Lengkap',     $p['nama_lengkap']      ?? ''],
                                    ['Nomor Pendaftaran',$p['nomor_pendaftaran'] ?? ''],
                                    ['Tempat Lahir',     $p['tempat_lahir']      ?? ''],
                                    ['Tanggal Lahir',    $p['tanggal_lahir'] ? date('d F Y', strtotime($p['tanggal_lahir'])) : ''],
                                    ['Jenis Kelamin',    $jk],
                                    ['Nomor HP',         $p['nomor_hp']          ?? ''],
                                    ['Email',            $p['email']             ?? ''],
                                    ['Nama Ibu Kandung', $p['nama_ibu_kandung']  ?? ''],
                                    ['Alamat KTP',       $p['alamat']            ?? ''],
                                ];
                                foreach ($allFields as [$lbl, $val]):
                                ?>
                                <div class="col-md-6">
                                    <div class="d-flex py-2 border-bottom" style="font-size:.83rem;">
                                        <span class="text-muted" style="min-width:145px;flex-shrink:0;font-size:.78rem;"><?= $lbl ?></span>
                                        <span class="fw-500"><?= htmlspecialchars($val ?: '-') ?></span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-0 rounded-3 h-100" style="box-shadow:0 2px 12px rgba(0,0,0,.06);">
                        <div class="card-body p-4">
                            <h6 class="fw-700 mb-3" style="color:var(--primary);">
                                <i class="bi bi-mortarboard me-2"></i>Program Studi
                            </h6>
                            <div class="py-2 border-bottom" style="font-size:.83rem;">
                                <span class="text-muted d-block" style="font-size:.72rem;">Program Studi</span>
                                <span class="fw-700"><?= htmlspecialchars($p['nama_prodi'] ?? '-') ?></span>
                            </div>
                            <div class="py-2 border-bottom" style="font-size:.83rem;">
                                <span class="text-muted d-block" style="font-size:.72rem;">Fakultas</span>
                                <span class="fw-500"><?= htmlspecialchars($p['fakultas'] ?? '-') ?></span>
                            </div>
                            <div class="py-2 border-bottom" style="font-size:.83rem;">
                                <span class="text-muted d-block" style="font-size:.72rem;">Jenjang</span>
                                <span class="badge <?= ($p['jenjang'] ?? '') === 'S2' ? 'bg-warning text-dark' : 'bg-primary' ?>">
                                    <?= htmlspecialchars($p['jenjang'] ?? '') ?>
                                </span>
                            </div>
                            <div class="py-2 border-bottom" style="font-size:.83rem;">
                                <span class="text-muted d-block" style="font-size:.72rem;">Gelar</span>
                                <span class="fw-500"><?= htmlspecialchars($p['gelar'] ?? '-') ?></span>
                            </div>
                            <div class="py-2" style="font-size:.83rem;">
                                <span class="text-muted d-block" style="font-size:.72rem;">Tahun Akademik</span>
                                <span class="fw-500"><?= htmlspecialchars($p['ta_nama'] ?? $p['ta_kode'] ?? '-') ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-0 rounded-3 h-100" style="box-shadow:0 2px 12px rgba(0,0,0,.06);">
                        <div class="card-body p-4">
                            <h6 class="fw-700 mb-3" style="color:var(--primary);">
                                <i class="bi bi-info-circle me-2"></i>Info Akun
                            </h6>
                            <div class="py-2 border-bottom" style="font-size:.83rem;">
                                <span class="text-muted d-block" style="font-size:.72rem;">Nomor Pendaftaran</span>
                                <code class="fw-700" style="color:var(--primary);font-size:.95rem;"><?= htmlspecialchars($p['nomor_pendaftaran'] ?? '-') ?></code>
                            </div>
                            <div class="py-2 border-bottom" style="font-size:.83rem;">
                                <span class="text-muted d-block" style="font-size:.72rem;">Email Login</span>
                                <span class="fw-500"><?= htmlspecialchars($p['email'] ?? '-') ?></span>
                            </div>
                            <div class="py-2 border-bottom" style="font-size:.83rem;">
                                <span class="text-muted d-block" style="font-size:.72rem;">Tanggal Daftar</span>
                                <span class="fw-500"><?= isset($p['created_at']) ? date('d M Y, H:i', strtotime($p['created_at'])) : '-' ?></span>
                            </div>
                            <div class="py-2" style="font-size:.83rem;">
                                <span class="text-muted d-block" style="font-size:.72rem;">Status Pendaftaran</span>
                                <span class="badge" style="background:<?= $st['bg'] ?? '#f1f5f9' ?>;color:<?= $st['text'] ?? '#64748b' ?>;border:1px solid <?= $st['border'] ?? '#e2e8f0' ?>;font-size:.75rem;">
                                    <i class="bi <?= $st['icon'] ?? 'bi-circle' ?> me-1"></i><?= $st['label'] ?? ucfirst($p['status'] ?? '-') ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DOKUMEN -->
            <?php
            $allTypes  = (new DokumenModel())->getDokumenTypes();
            $uploaded  = [];
            foreach ($docs as $d) { $uploaded[$d['jenis_dokumen']] = $d; }
            $isS2      = ($p['jenjang'] ?? '') === 'S2';
            $s1Only    = ['ijazah_s1','transkrip_s1'];
            $total     = count($allTypes);
            $doneCnt   = count($uploaded);
            $pct       = $total > 0 ? round($doneCnt/$total*100) : 0;
            ?>
            <div class="card border-0 rounded-3 mb-4" style="box-shadow:0 2px 12px rgba(0,0,0,.06);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-700 mb-0" style="color:var(--primary);">
                            <i class="bi bi-folder2-open me-2"></i>Kelengkapan Berkas
                            <span class="badge rounded-pill ms-2" style="background:var(--primary);font-size:.68rem;"><?= $doneCnt ?>/<?= $total ?></span>
                        </h6>
                    </div>

                    <!-- Progress bar -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1" style="font-size:.75rem;">
                            <span class="text-muted">Progress Upload</span>
                            <span class="fw-600" style="color:var(--primary)"><?= $pct ?>%</span>
                        </div>
                        <div class="progress" style="height:6px;border-radius:3px;">
                            <div class="progress-bar" style="width:<?= $pct ?>%;background:linear-gradient(90deg,var(--primary),#2563eb);border-radius:3px;"></div>
                        </div>
                    </div>

                    <!-- Grid semua jenis dokumen -->
                    <div class="row g-2">
                        <?php foreach ($allTypes as $jKey => $jLabel): ?>
                        <?php
                            $isUploaded = isset($uploaded[$jKey]);
                            $docData    = $uploaded[$jKey] ?? null;
                            $isS1Doc    = in_array($jKey, $s1Only, true);
                            $isOptional = !$isS2 && $isS1Doc;
                            $ext        = $isUploaded ? strtolower(pathinfo($docData['nama_file_asli'] ?? '', PATHINFO_EXTENSION)) : '';
                            $uploadedAt = $isUploaded ? ($docData['uploaded_at'] ?? null) : null;
                        ?>
                        <div class="col-md-6">
                            <div class="rounded-3 p-3 h-100 position-relative"
                                 style="border:1.5px solid <?= $isUploaded ? '#86efac' : ($isOptional ? '#fde68a' : '#fecaca') ?>;
                                        background:<?= $isUploaded ? '#f0fdf4' : ($isOptional ? '#fffbeb' : '#fff5f5') ?>;">
                                <div class="d-flex align-items-start gap-3">
                                    <!-- Icon status -->
                                    <div style="width:38px;height:38px;border-radius:8px;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:1.1rem;
                                                background:<?= $isUploaded ? '#dcfce7' : ($isOptional ? '#fef9c3' : '#fee2e2') ?>;
                                                color:<?= $isUploaded ? '#16a34a' : ($isOptional ? '#ca8a04' : '#dc2626') ?>;">
                                        <i class="bi <?= $isUploaded ? 'bi-check-circle-fill' : ($isOptional ? 'bi-dash-circle' : 'bi-exclamation-circle') ?>"></i>
                                    </div>
                                    <div class="flex-fill" style="min-width:0;">
                                        <div class="fw-600" style="font-size:.83rem;color:#1e293b;"><?= $jLabel ?></div>
                                        <?php if ($isUploaded): ?>
                                        <div class="d-flex align-items-center gap-1 mt-1">
                                            <i class="bi <?= $ext === 'pdf' ? 'bi-file-earmark-pdf text-danger' : 'bi-file-earmark-image text-primary' ?>" style="font-size:.85rem;"></i>
                                            <span style="font-size:.72rem;color:#64748b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:120px;">
                                                <?= htmlspecialchars($docData['nama_file_asli'] ?? '') ?>
                                            </span>
                                        </div>
                                        <?php if ($uploadedAt): ?>
                                        <div style="font-size:.68rem;color:#94a3b8;margin-top:2px;">
                                            <i class="bi bi-calendar3 me-1"></i><?= date('d M Y', strtotime($uploadedAt)) ?>
                                        </div>
                                        <?php endif; ?>
                                        <?php else: ?>
                                        <div style="font-size:.72rem;color:<?= $isOptional ? '#ca8a04' : '#dc2626' ?>;margin-top:2px;">
                                            <?= $isOptional ? 'Opsional (bisa menyusul)' : 'Belum diunggah' ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <!-- Tombol upload/ganti -->
                                    <?php if (in_array($p['status'] ?? '', ['draft','menunggu','revisi'])): ?>
                                    <button class="btn btn-sm rounded-2 flex-shrink-0"
                                            style="font-size:.7rem;padding:3px 8px;
                                                   background:<?= $isUploaded ? '#fff' : 'var(--primary)' ?>;
                                                   color:<?= $isUploaded ? 'var(--primary)' : '#fff' ?>;
                                                   border:1px solid var(--primary);"
                                            onclick="openUpload('<?= $jKey ?>', '<?= htmlspecialchars($jLabel) ?>')">
                                        <i class="bi <?= $isUploaded ? 'bi-arrow-repeat' : 'bi-upload' ?> me-1"></i>
                                        <?= $isUploaded ? 'Ganti' : 'Upload' ?>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- RIWAYAT VERIFIKASI -->
            <?php if (!empty($logs)): ?>
            <div class="card border-0 rounded-3 mb-4" style="box-shadow:0 2px 12px rgba(0,0,0,.06);">
                <div class="card-body p-4">
                    <h6 class="fw-700 mb-3" style="color:var(--primary);">
                        <i class="bi bi-clock-history me-2"></i>Riwayat Verifikasi
                    </h6>
                    <?php
                    $statusIcons = [
                        'menunggu' => ['icon'=>'bi-hourglass-split','color'=>'#d97706','bg'=>'#fff7ed'],
                        'diterima' => ['icon'=>'bi-check-circle-fill','color'=>'#16a34a','bg'=>'#f0fdf4'],
                        'revisi'   => ['icon'=>'bi-pencil-square','color'=>'#2563eb','bg'=>'#eff6ff'],
                        'ditolak'  => ['icon'=>'bi-x-circle-fill','color'=>'#dc2626','bg'=>'#fff5f5'],
                    ];
                    foreach ($logs as $i => $log):
                        $si = $statusIcons[$log['status_sesudah'] ?? 'menunggu'] ?? $statusIcons['menunggu'];
                    ?>
                    <div class="d-flex gap-3 <?= $i < count($logs)-1 ? 'pb-3 mb-3 border-bottom' : '' ?>">
                        <!-- Icon -->
                        <div style="width:36px;height:36px;border-radius:50%;background:<?= $si['bg'] ?>;
                                    display:flex;align-items:center;justify-content:center;flex-shrink:0;border:2px solid <?= $si['color'] ?>20;">
                            <i class="bi <?= $si['icon'] ?>" style="color:<?= $si['color'] ?>;font-size:.9rem;"></i>
                        </div>
                        <div class="flex-fill">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="fw-600" style="font-size:.83rem;">Status berubah menjadi</span>
                                <span class="badge rounded-pill" style="background:<?= $si['bg'] ?>;color:<?= $si['color'] ?>;font-size:.7rem;border:1px solid <?= $si['color'] ?>40;">
                                    <?= ucfirst($log['status_sesudah'] ?? '-') ?>
                                </span>
                            </div>
                            <?php if (!empty($log['catatan'])): ?>
                            <div class="p-2 rounded-2 mb-1" style="background:<?= $si['bg'] ?>;border-left:3px solid <?= $si['color'] ?>;font-size:.82rem;color:#374151;">
                                <i class="bi bi-chat-quote me-1" style="color:<?= $si['color'] ?>"></i>
                                <?= nl2br(htmlspecialchars($log['catatan'])) ?>
                            </div>
                            <?php endif; ?>
                            <div style="font-size:.72rem;color:#9ca3af;">
                                <i class="bi bi-clock me-1"></i>
                                <?= date('d M Y, H:i', strtotime($log['created_at'])) ?>
                                <?php if (!empty($log['nama_verifikator'])): ?>
                                · oleh <?= htmlspecialchars($log['nama_verifikator']) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- MODAL UPLOAD DOKUMEN -->
<div class="modal fade" id="modalUpload" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 rounded-3 overflow-hidden">
            <div class="modal-header border-0 p-4" style="background:linear-gradient(135deg,var(--primary),#2563eb);">
                <div>
                    <h5 class="modal-title text-white fw-700 mb-0" id="modalUploadTitle">Upload Dokumen</h5>
                    <p class="text-white-50 mb-0 mt-1" style="font-size:.75rem;" id="modalUploadSub">Pilih file PDF, JPG, atau PNG (maks 5 MB)</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="uploadForm">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCsrf() ?>">
                <input type="hidden" name="jenis_dokumen" id="jenisInput" value="">
                <div class="modal-body p-4">
                    <!-- Drop zone -->
                    <div id="dropZone" class="rounded-3 text-center p-4 mb-3"
                         style="border:2px dashed #cbd5e1;background:#f8fafc;cursor:pointer;transition:.2s"
                         onclick="document.getElementById('dokumenFile').click()"
                         ondragover="event.preventDefault();this.style.borderColor='var(--primary)';this.style.background='#eff6ff'"
                         ondragleave="this.style.borderColor='#cbd5e1';this.style.background='#f8fafc'"
                         ondrop="handleDropFile(event)">
                        <i class="bi bi-cloud-upload" style="font-size:2rem;color:#94a3b8;"></i>
                        <p class="mb-0 mt-2" style="font-size:.82rem;color:#64748b;">
                            Klik atau seret file ke sini<br>
                            <span style="font-size:.72rem;color:#94a3b8;">PDF, JPG, PNG — Maks 5 MB</span>
                        </p>
                    </div>
                    <input type="file" name="dokumen" id="dokumenFile" class="d-none" accept=".pdf,.jpg,.jpeg,.png" onchange="previewDokumen(this)">

                    <!-- Preview file -->
                    <div id="filePreview" class="rounded-3 p-3 mb-3" style="background:#f0fdf4;border:1px solid #86efac;display:none;">
                        <div class="d-flex align-items-center gap-3">
                            <i id="fileIcon" class="bi bi-file-earmark-image" style="font-size:1.6rem;color:#16a34a;"></i>
                            <div class="flex-fill">
                                <div id="fileName" class="fw-600" style="font-size:.83rem;"></div>
                                <div id="fileSize" style="font-size:.72rem;color:#64748b;"></div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearFile()">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Progress -->
                    <div id="uploadProgress" style="display:none" class="mb-2">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span style="font-size:.78rem;color:var(--primary);font-weight:600;">Mengunggah...</span>
                            <span id="uploadPct" style="font-size:.75rem;color:#64748b;">0%</span>
                        </div>
                        <div class="progress" style="height:6px;border-radius:3px;">
                            <div id="uploadBar" class="progress-bar" style="width:0%;background:var(--primary);border-radius:3px;transition:.1s"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-sm rounded-pill px-4" id="btnUpload"
                            style="background:var(--primary);color:#fff;" onclick="submitUpload()">
                        <i class="bi bi-upload me-1"></i> Upload Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const BASE_URL = '<?= BASE_URL ?>';

// Buka modal upload dengan jenis tertentu
function openUpload(jenis, label) {
    document.getElementById('jenisInput').value  = jenis;
    document.getElementById('modalUploadTitle').textContent = 'Upload: ' + label;
    clearFile();
    const m = new bootstrap.Modal(document.getElementById('modalUpload'));
    m.show();
}

function previewDokumen(input) {
    const file = input.files[0];
    if (!file) return;
    const maxSize = 5 * 1024 * 1024;
    const ext = file.name.split('.').pop().toLowerCase();
    if (!['pdf','jpg','jpeg','png'].includes(ext)) {
        showToastDashboard('Format tidak didukung. Gunakan PDF, JPG, atau PNG.', 'danger');
        input.value = ''; return;
    }
    if (file.size > maxSize) {
        showToastDashboard('Ukuran file melebihi 5 MB.', 'danger');
        input.value = ''; return;
    }
    // Tampilkan preview
    const isPdf = ext === 'pdf';
    document.getElementById('dropZone').style.display   = 'none';
    document.getElementById('filePreview').style.display = 'block';
    document.getElementById('fileName').textContent = file.name;
    document.getElementById('fileSize').textContent = (file.size / 1024).toFixed(1) + ' KB';
    document.getElementById('fileIcon').className   = 'bi ' + (isPdf ? 'bi-file-earmark-pdf text-danger' : 'bi-file-earmark-image text-success');
}

function clearFile() {
    document.getElementById('dokumenFile').value    = '';
    document.getElementById('filePreview').style.display = 'none';
    document.getElementById('dropZone').style.display   = 'block';
    document.getElementById('uploadProgress').style.display = 'none';
    document.getElementById('uploadBar').style.width = '0%';
}

function handleDropFile(e) {
    e.preventDefault();
    e.currentTarget.style.borderColor = '#cbd5e1';
    e.currentTarget.style.background  = '#f8fafc';
    const file = e.dataTransfer.files[0];
    if (!file) return;
    const dt = new DataTransfer();
    dt.items.add(file);
    const inp = document.getElementById('dokumenFile');
    inp.files = dt.files;
    previewDokumen(inp);
}

function submitUpload() {
    const jenis    = document.getElementById('jenisInput').value;
    const fileEl   = document.getElementById('dokumenFile');
    const file     = fileEl.files[0];
    const btnUpload= document.getElementById('btnUpload');
    const progress = document.getElementById('uploadProgress');
    const bar      = document.getElementById('uploadBar');
    const pct      = document.getElementById('uploadPct');

    if (!jenis) { showToastDashboard('Jenis dokumen tidak diketahui.', 'warning'); return; }
    if (!file)  { showToastDashboard('Pilih file terlebih dahulu.', 'warning'); return; }

    const fd = new FormData(document.getElementById('uploadForm'));
    const xhr = new XMLHttpRequest();
    xhr.open('POST', BASE_URL + '/index.php?page=pendaftar/upload');

    btnUpload.disabled = true;
    btnUpload.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Uploading...';
    progress.style.display = 'block';

    xhr.upload.onprogress = (e) => {
        if (e.lengthComputable) {
            const p = Math.round(e.loaded / e.total * 100);
            bar.style.width = p + '%';
            if (pct) pct.textContent = p + '%';
        }
    };

    xhr.onload = () => {
        btnUpload.disabled = false;
        btnUpload.innerHTML = '<i class="bi bi-upload me-1"></i> Upload Sekarang';
        progress.style.display = 'none';

        let res = {};
        try { res = JSON.parse(xhr.responseText); } catch(e) {}

        if (res.success) {
            showToastDashboard('Berkas berhasil diunggah!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('modalUpload'))?.hide();
            clearFile();
            setTimeout(() => location.reload(), 900);
        } else {
            showToastDashboard(res.message || 'Gagal mengunggah berkas.', 'danger');
        }
    };

    xhr.onerror = () => {
        btnUpload.disabled = false;
        btnUpload.innerHTML = '<i class="bi bi-upload me-1"></i> Upload Sekarang';
        progress.style.display = 'none';
        showToastDashboard('Koneksi terputus. Coba lagi.', 'danger');
    };

    xhr.send(fd);
}

function showToastDashboard(msg, type = 'info') {
    const colors = { success:'#16a34a', danger:'#dc2626', warning:'#d97706', info:'#1a3a6b' };
    const icons  = { success:'check-circle-fill', danger:'x-circle-fill', warning:'exclamation-triangle-fill', info:'info-circle-fill' };
    const el = document.createElement('div');
    el.style.cssText = `position:fixed;bottom:1.5rem;right:1.5rem;z-index:9999;background:#fff;
        padding:.8rem 1.1rem;border-radius:.6rem;box-shadow:0 4px 20px rgba(0,0,0,.15);
        border-left:4px solid ${colors[type]||colors.info};font-size:.84rem;font-weight:500;
        display:flex;align-items:center;gap:.6rem;min-width:260px;max-width:340px;
        animation:slideUp .3s ease`;
    el.innerHTML = `<i class="bi bi-${icons[type]||'info-circle-fill'}" style="color:${colors[type]};font-size:1.1rem;flex-shrink:0"></i><span>${msg}</span>`;
    document.body.appendChild(el);
    setTimeout(() => { el.style.opacity='0'; el.style.transform='translateY(8px)'; el.style.transition='.3s'; setTimeout(()=>el.remove(),300); }, 3500);
}
</script>
<style>
@keyframes slideUp { from { transform:translateY(12px); opacity:0 } to { transform:translateY(0); opacity:1 } }
</style>