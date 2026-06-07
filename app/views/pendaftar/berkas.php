<?php
/**
 * View: Kelola Berkas Pendaftar
 * URL: /pendaftar/berkas
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title) ?> — PMB Ma'had Aly Ibnu Abbas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap">
    <style>
        :root {
            --primary:  #1a3a6b;
            --accent:   #c9a227;
            --success:  #198754;
            --warning:  #fd7e14;
            --danger:   #dc3545;
            --bg:       #f4f6fb;
            --sidebar-w: 260px;
        }
        * { box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); min-height: 100vh; }

        /* ── Sidebar ── */
        .sidebar {
            position: fixed; top: 0; left: 0; width: var(--sidebar-w);
            height: 100vh; background: #0f2447; z-index: 1000;
            overflow-y: auto; transition: transform .3s ease;
            display: flex; flex-direction: column;
        }
        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,.1);
            text-decoration: none;
        }
        .sidebar-brand .brand-title { font-size: .85rem; font-weight: 700; color: #fff; line-height: 1.3; }
        .sidebar-brand .brand-sub   { font-size: .7rem; color: rgba(255,255,255,.5); }
        .sidebar-user {
            padding: 1rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,.08);
            display: flex; align-items: center; gap: .75rem;
        }
        .sidebar-user .avatar {
            width: 38px; height: 38px; border-radius: 50%;
            background: var(--accent); display: grid; place-items: center;
            font-size: .9rem; font-weight: 700; color: #fff; flex-shrink: 0;
        }
        .sidebar-user .user-info .name  { font-size: .8rem; font-weight: 600; color: #fff; }
        .sidebar-user .user-info .nomor { font-size: .7rem; color: rgba(255,255,255,.5); }
        .sidebar-nav { padding: 1rem 0; flex: 1; }
        .nav-label { font-size: .65rem; text-transform: uppercase; letter-spacing: .08em;
                     color: rgba(255,255,255,.35); padding: .5rem 1.5rem; margin-top: .5rem; }
        .nav-item a {
            display: flex; align-items: center; gap: .75rem;
            padding: .6rem 1.5rem; color: rgba(255,255,255,.65);
            text-decoration: none; font-size: .82rem; font-weight: 500;
            border-radius: 0; transition: all .2s;
            border-left: 3px solid transparent;
        }
        .nav-item a:hover, .nav-item a.active {
            background: rgba(255,255,255,.07);
            color: #fff; border-left-color: var(--accent);
        }
        .nav-item a i { font-size: 1rem; width: 1.2rem; text-align: center; }
        .sidebar-footer { padding: 1rem 1.5rem; border-top: 1px solid rgba(255,255,255,.08); }
        .sidebar-footer a { display: flex; align-items: center; gap: .5rem;
                            color: rgba(255,255,255,.5); font-size: .8rem; text-decoration: none; }
        .sidebar-footer a:hover { color: #fff; }

        /* ── Main ── */
        .main { margin-left: var(--sidebar-w); min-height: 100vh; }
        .topbar {
            background: #fff; padding: .9rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 100;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
        }
        .topbar-title { font-size: 1rem; font-weight: 600; color: var(--primary); }
        .page-content { padding: 1.75rem; }

        /* ── Status Badge ── */
        .status-badge { display: inline-flex; align-items: center; gap: .4rem;
                        font-size: .75rem; font-weight: 600; padding: .3rem .75rem;
                        border-radius: 2rem; letter-spacing: .02em; }
        .status-draft     { background: #e9ecef; color: #495057; }
        .status-menunggu  { background: #fff3cd; color: #856404; }
        .status-diterima  { background: #d1e7dd; color: #0a3622; }
        .status-revisi    { background: #fff3cd; color: #856404; }
        .status-ditolak   { background: #f8d7da; color: #58151c; }

        /* ── Document Grid ── */
        .dokumen-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.25rem;
        }
        .dokumen-card {
            background: #fff; border-radius: .75rem;
            border: 2px dashed #dee2e6;
            padding: 1.5rem; text-align: center;
            transition: all .25s ease; position: relative; cursor: pointer;
        }
        .dokumen-card:hover { border-color: var(--primary); background: #f8f9ff; }
        .dokumen-card.uploaded { border-style: solid; border-color: #198754; background: #f0fdf4; cursor: default; }
        .dokumen-card.required-miss { border-color: var(--danger); background: #fff5f5; }
        .dokumen-card .doc-icon {
            width: 56px; height: 56px; border-radius: .75rem;
            background: var(--bg); display: grid; place-items: center;
            font-size: 1.6rem; margin: 0 auto 1rem; transition: background .2s;
        }
        .dokumen-card.uploaded .doc-icon { background: #d1e7dd; color: #198754; }
        .dokumen-card .doc-title { font-size: .88rem; font-weight: 600; color: var(--primary); margin-bottom: .35rem; }
        .dokumen-card .doc-status { font-size: .75rem; }
        .dokumen-card .doc-meta { font-size: .72rem; color: #6c757d; margin-top: .3rem; }
        .upload-btn-overlay {
            position: absolute; inset: 0; border-radius: .65rem;
            display: flex; align-items: center; justify-content: center;
            opacity: 0; transition: opacity .2s;
            background: rgba(26,58,107,.85); color: #fff; font-size: .82rem; font-weight: 600;
        }
        .dokumen-card:not(.uploaded):hover .upload-btn-overlay { opacity: 1; }
        input[type="file"].hidden-input { position: absolute; opacity: 0; width: 0; height: 0; }

        /* ── Upload Progress ── */
        .upload-progress { margin-top: .75rem; display: none; }
        .progress { height: .45rem; border-radius: 2rem; }

        /* ── Toast ── */
        #toast-container { position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 9999; min-width: 280px; }
        .toast-item {
            display: flex; align-items: flex-start; gap: .75rem;
            padding: .9rem 1.1rem; border-radius: .6rem; margin-top: .5rem;
            background: #fff; box-shadow: 0 4px 20px rgba(0,0,0,.12);
            border-left: 4px solid var(--primary); font-size: .82rem; font-weight: 500;
            animation: slideIn .3s ease;
        }
        .toast-item.success { border-left-color: var(--success); }
        .toast-item.error   { border-left-color: var(--danger); }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main { margin-left: 0; }
            .dokumen-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <a href="<?= BASE_URL ?>/" class="sidebar-brand d-flex align-items-center gap-2">
        <div>
            <div class="brand-title">Ma'had Aly Ibnu Abbas</div>
            <div class="brand-sub">Portal Pendaftar</div>
        </div>
    </a>

    <div class="sidebar-user">
        <div class="avatar"><?= strtoupper(substr($pendaftar['nama_lengkap'] ?? 'P', 0, 1)) ?></div>
        <div class="user-info">
            <div class="name"><?= e(substr($pendaftar['nama_lengkap'] ?? '', 0, 22)) ?></div>
            <div class="nomor"><?= e($pendaftar['nomor_pendaftaran'] ?? '-') ?></div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-label">Menu</div>
        <ul class="list-unstyled mb-0">
            <li class="nav-item">
                <a href="<?= BASE_URL ?>/pendaftar">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= BASE_URL ?>/pendaftar/berkas" class="active">
                    <i class="bi bi-folder2-open"></i> Kelola Berkas
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= BASE_URL ?>/pendaftar/cetak/<?= $pendaftar['id'] ?>" target="_blank">
                    <i class="bi bi-printer"></i> Cetak Bukti
                </a>
            </li>
        </ul>
    </nav>

    <div class="sidebar-footer">
        <a href="<?= BASE_URL ?>/logout">
            <i class="bi bi-box-arrow-left"></i> Keluar
        </a>
    </div>
</aside>

<!-- Main Content -->
<div class="main">
    <!-- Topbar -->
    <div class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-outline-secondary d-md-none" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            <span class="topbar-title">Kelola Berkas Pendaftaran</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="status-badge status-<?= $pendaftar['status'] ?>">
                <i class="bi bi-circle-fill" style="font-size:.4rem"></i>
                <?= ucfirst($pendaftar['status']) ?>
            </span>
        </div>
    </div>

    <div class="page-content">

        <!-- Flash Messages -->
        <?php if ($flash = Session::getFlash('success')): ?>
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
            <i class="bi bi-check-circle-fill"></i> <?= e($flash) ?>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        <?php if ($flash = Session::getFlash('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
            <i class="bi bi-exclamation-circle-fill"></i> <?= e($flash) ?>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Info Status -->
        <?php if ($pendaftar['status'] === 'revisi'): ?>
        <div class="alert alert-warning d-flex align-items-start gap-2 mb-4">
            <i class="bi bi-exclamation-triangle-fill mt-1 flex-shrink-0"></i>
            <div>
                <strong>Berkas Perlu Direvisi</strong>
                <?php if (!empty($pendaftar['catatan_verifikasi'])): ?>
                <p class="mb-0 mt-1 small"><?= e($pendaftar['catatan_verifikasi']) ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php elseif ($pendaftar['status'] === 'diterima'): ?>
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-patch-check-fill"></i>
            <strong>Pendaftaran Anda telah diterima.</strong> Selamat datang di Ma'had Aly Ibnu Abbas!
        </div>
        <?php elseif ($pendaftar['status'] === 'ditolak'): ?>
        <div class="alert alert-danger d-flex align-items-start gap-2 mb-4">
            <i class="bi bi-x-circle-fill mt-1 flex-shrink-0"></i>
            <div>
                <strong>Pendaftaran Ditolak</strong>
                <?php if (!empty($pendaftar['catatan_verifikasi'])): ?>
                <p class="mb-0 mt-1 small"><?= e($pendaftar['catatan_verifikasi']) ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Petunjuk Upload -->
        <?php if ($can_upload): ?>
        <div class="card border-0 shadow-sm mb-4" style="border-radius:.75rem;">
            <div class="card-body d-flex align-items-start gap-3 p-3">
                <div class="flex-shrink-0 text-primary" style="font-size:1.4rem"><i class="bi bi-info-circle-fill"></i></div>
                <div>
                    <p class="mb-1 small fw-semibold text-primary">Petunjuk Upload Berkas</p>
                    <ul class="mb-0 small text-muted ps-3">
                        <li>Format yang diterima: <strong>PDF, JPG, JPEG, PNG</strong></li>
                        <li>Ukuran maksimal per file: <strong>5 MB</strong></li>
                        <li>Pastikan dokumen <strong>asli dan terbaca jelas</strong></li>
                        <li>Klik kartu dokumen atau seret file ke area upload</li>
                        <li>Berkas yang sudah diupload akan otomatis tergantikan jika diupload ulang</li>
                    </ul>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="alert alert-info d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-lock-fill"></i>
            <span>Upload berkas tidak tersedia. Status pendaftaran Anda: <strong><?= ucfirst($pendaftar['status']) ?></strong>.</span>
        </div>
        <?php endif; ?>

        <!-- Progress Berkas -->
        <?php
        $total    = count($dokumen_types);
        $uploaded_count = count($uploaded);
        $pct      = $total > 0 ? round(($uploaded_count / $total) * 100) : 0;
        $isS2     = ($pendaftar['jenjang'] ?? '') === 'S2';
        ?>
        <div class="card border-0 shadow-sm mb-4" style="border-radius:.75rem;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold small" style="color:var(--primary)">
                        <i class="bi bi-files me-1"></i>Progress Upload Berkas
                    </span>
                    <span class="fw-bold small" style="color:var(--primary)"><?= $uploaded_count ?>/<?= $total ?> berkas</span>
                </div>
                <div class="progress" style="height:.6rem; background:#e9ecef; border-radius:2rem;">
                    <div class="progress-bar" role="progressbar"
                         style="width:<?= $pct ?>%; background: linear-gradient(90deg,var(--primary),var(--accent)); border-radius:2rem;"
                         aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="text-muted small mt-1"><?= $pct ?>% selesai</div>
            </div>
        </div>

        <!-- Grid Dokumen -->
        <div class="dokumen-grid">
            <?php foreach ($dokumen_types as $jenisKey => $jenisLabel): ?>
            <?php
                $isUploaded = isset($uploaded[$jenisKey]);
                $docData    = $uploaded[$jenisKey] ?? null;
                $isS1Doc    = in_array($jenisKey, ['ijazah_s1','transkrip_s1'], true);
                $isRequired = !($isS2 && $isS1Doc); // S1 wajib upload ijazah S1, S2 boleh menyusul
            ?>
            <div class="dokumen-card <?= $isUploaded ? 'uploaded' : (!$isRequired ? '' : '') ?>"
                 id="card-<?= $jenisKey ?>"
                 <?php if ($can_upload && !$isUploaded): ?>
                    onclick="triggerUpload('<?= $jenisKey ?>')"
                    title="Klik untuk upload <?= e($jenisLabel) ?>"
                 <?php endif; ?>>

                <?php if ($can_upload && !$isUploaded): ?>
                <input type="file" class="hidden-input" id="input-<?= $jenisKey ?>"
                       accept=".pdf,.jpg,.jpeg,.png" onchange="handleUpload('<?= $jenisKey ?>', this)">
                <?php endif; ?>

                <div class="doc-icon">
                    <?php if ($isUploaded): ?>
                        <i class="bi bi-check-circle-fill" style="color:#198754;"></i>
                    <?php elseif (!$isRequired): ?>
                        <i class="bi bi-file-earmark-plus" style="color:#6c757d;"></i>
                    <?php else: ?>
                        <i class="bi bi-file-earmark-arrow-up" style="color:var(--primary);"></i>
                    <?php endif; ?>
                </div>

                <div class="doc-title"><?= e($jenisLabel) ?></div>

                <?php if ($isUploaded): ?>
                    <div class="doc-status text-success fw-semibold">
                        <i class="bi bi-check2 me-1"></i>Sudah diunggah
                    </div>
                    <div class="doc-meta">
                        <?= round(($docData['ukuran_file'] ?? 0) / 1024) ?> KB •
                        <?= strtoupper(pathinfo($docData['nama_file'] ?? '', PATHINFO_EXTENSION)) ?> •
                        <?= $docData['uploaded_at'] ? date('d M Y', strtotime($docData['uploaded_at'])) : '' ?>
                    </div>
                    <?php if ($can_upload): ?>
                    <button class="btn btn-sm btn-outline-warning mt-2"
                            onclick="event.stopPropagation(); triggerUpload('<?= $jenisKey ?>')">
                        <i class="bi bi-arrow-repeat me-1"></i>Ganti File
                    </button>
                    <?php endif; ?>
                <?php elseif (!$isRequired): ?>
                    <div class="doc-status text-muted">Opsional / Bisa menyusul</div>
                <?php else: ?>
                    <div class="doc-status" style="color:var(--primary)">Belum diunggah</div>
                <?php endif; ?>

                <!-- Upload Progress -->
                <div class="upload-progress" id="progress-<?= $jenisKey ?>">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                             style="width:0%; background:var(--primary);"
                             id="pbar-<?= $jenisKey ?>"></div>
                    </div>
                    <div class="text-muted small mt-1">Mengunggah...</div>
                </div>

                <?php if ($can_upload && !$isUploaded): ?>
                <div class="upload-btn-overlay">
                    <i class="bi bi-cloud-upload me-2"></i>Upload
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Tombol Aksi -->
        <div class="d-flex gap-2 mt-4 flex-wrap">
            <a href="<?= BASE_URL ?>/pendaftar" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Dashboard
            </a>
            <a href="<?= BASE_URL ?>/pendaftar/cetak/<?= $pendaftar['id'] ?>" target="_blank"
               class="btn btn-outline-primary">
                <i class="bi bi-printer me-1"></i>Cetak Bukti
            </a>
        </div>

    </div><!-- /page-content -->
</div><!-- /main -->

<!-- Toast Container -->
<div id="toast-container"></div>

<!-- Hidden CSRF -->
<input type="hidden" id="csrf_token" value="<?= e($csrf) ?>">

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script>
const BASE_URL  = '<?= BASE_URL ?>';
const CSRF_TOKEN = document.getElementById('csrf_token').value;

/* ── Toast ── */
function showToast(msg, type = 'success') {
    const icons = { success:'check-circle-fill', error:'x-circle-fill', info:'info-circle-fill' };
    const colors = { success:'#198754', error:'#dc3545', info:'#1a3a6b' };
    const el = document.createElement('div');
    el.className = `toast-item ${type}`;
    el.innerHTML = `<i class="bi bi-${icons[type]||'info-circle-fill'}" style="color:${colors[type]||'#1a3a6b'};font-size:1.1rem;flex-shrink:0"></i><span>${msg}</span>`;
    document.getElementById('toast-container').appendChild(el);
    setTimeout(() => { el.style.opacity='0'; el.style.transform='translateX(100%)'; el.style.transition='.3s'; setTimeout(()=>el.remove(),350); }, 3500);
}

/* ── Trigger file input ── */
function triggerUpload(jenis) {
    const inp = document.getElementById('input-' + jenis);
    if (inp) inp.click();
}

/* ── Handle file change ── */
function handleUpload(jenis, inputEl) {
    const file = inputEl.files[0];
    if (!file) return;

    const allowedTypes = ['application/pdf','image/jpeg','image/jpg','image/png'];
    const allowedExts  = ['pdf','jpg','jpeg','png'];
    const ext = file.name.split('.').pop().toLowerCase();
    const maxSize = 5 * 1024 * 1024;

    if (!allowedTypes.includes(file.type) || !allowedExts.includes(ext)) {
        showToast('Format tidak didukung. Gunakan PDF, JPG, atau PNG.', 'error');
        inputEl.value = '';
        return;
    }
    if (file.size > maxSize) {
        showToast('Ukuran file melebihi 5MB.', 'error');
        inputEl.value = '';
        return;
    }

    uploadFile(jenis, file);
    inputEl.value = '';
}

/* ── Upload via XMLHttpRequest (supports progress) ── */
function uploadFile(jenis, file) {
    const progressEl = document.getElementById('progress-' + jenis);
    const pbar = document.getElementById('pbar-' + jenis);
    const card = document.getElementById('card-' + jenis);

    progressEl.style.display = 'block';
    card.style.pointerEvents  = 'none';
    card.style.opacity        = '.8';

    const fd = new FormData();
    fd.append('_token',       CSRF_TOKEN);
    fd.append('jenis_dokumen',jenis);
    fd.append('dokumen',      file);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', BASE_URL + '/pendaftar/upload');

    xhr.upload.onprogress = (e) => {
        if (e.lengthComputable) {
            const pct = Math.round((e.loaded / e.total) * 100);
            if (pbar) pbar.style.width = pct + '%';
        }
    };

    xhr.onload = () => {
        progressEl.style.display = 'none';
        card.style.pointerEvents  = '';
        card.style.opacity        = '1';

        let res = {};
        try { res = JSON.parse(xhr.responseText); } catch(e) {}

        if (res.success) {
            showToast(res.message || 'Berkas berhasil diunggah.', 'success');
            // Reload page to reflect updated state
            setTimeout(() => location.reload(), 1200);
        } else {
            showToast(res.message || 'Gagal mengunggah berkas.', 'error');
        }
    };

    xhr.onerror = () => {
        progressEl.style.display = 'none';
        card.style.pointerEvents  = '';
        card.style.opacity        = '1';
        showToast('Terjadi kesalahan jaringan.', 'error');
    };

    xhr.send(fd);
}

/* ── Drag & Drop global ── */
document.querySelectorAll('.dokumen-card:not(.uploaded)').forEach(card => {
    card.addEventListener('dragover', (e) => { e.preventDefault(); card.style.background='#eef2ff'; });
    card.addEventListener('dragleave', () => { card.style.background=''; });
    card.addEventListener('drop', (e) => {
        e.preventDefault();
        card.style.background = '';
        const jenis = card.id.replace('card-', '');
        const file  = e.dataTransfer.files[0];
        if (file) {
            const allowedTypes = ['application/pdf','image/jpeg','image/jpg','image/png'];
            const allowedExts  = ['pdf','jpg','jpeg','png'];
            const ext = file.name.split('.').pop().toLowerCase();
            if (!allowedTypes.includes(file.type) || !allowedExts.includes(ext)) {
                showToast('Format tidak didukung.', 'error'); return;
            }
            if (file.size > 5*1024*1024) {
                showToast('Ukuran file melebihi 5MB.', 'error'); return;
            }
            uploadFile(jenis, file);
        }
    });
});

/* ── Sidebar toggle (mobile) ── */
document.getElementById('sidebarToggle')?.addEventListener('click', () => {
    document.getElementById('sidebar').classList.toggle('open');
});
</script>
</body>
</html>
