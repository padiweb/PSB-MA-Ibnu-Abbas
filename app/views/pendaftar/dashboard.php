<?php
// views/pendaftar/dashboard.php
$p    = $pendaftar ?? [];
$docs = $dokumen ?? [];
$logs = $verifikasi_log ?? [];

$statusColors = [
    'menunggu' => ['bg'=>'#fff7ed','border'=>'#fed7aa','text'=>'#c2410c','icon'=>'bi-hourglass-split','label'=>'Menunggu Verifikasi'],
    'diterima' => ['bg'=>'#f0fdf4','border'=>'#86efac','text'=>'#166534','icon'=>'bi-check-circle-fill','label'=>'Diterima'],
    'revisi'   => ['bg'=>'#eff6ff','border'=>'#bfdbfe','text'=>'#1d4ed8','icon'=>'bi-pencil-square','label'=>'Perlu Revisi'],
    'ditolak'  => ['bg'=>'#fff1f2','border'=>'#fecdd3','text'=>'#be123c','icon'=>'bi-x-circle-fill','label'=>'Ditolak'],
];
$st = $statusColors[$p['status_verifikasi'] ?? 'menunggu'];
?>

<!-- TOPBAR PENDAFTAR -->
<nav class="navbar" style="background:#fff;border-bottom:1px solid #e2e8f0;padding:0 0;">
    <div class="container">
        <a href="/" class="navbar-brand d-flex align-items-center gap-2">
            <div style="width:32px;height:32px;background:var(--primary);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:.85rem;">M</div>
            <span style="font-weight:700;font-size:.9rem;color:var(--primary);">Ma'had Aly PMB</span>
        </a>
        <div class="d-flex align-items-center gap-3">
            <a href="/pendaftar/cetak/<?= $p['id'] ?>" target="_blank" class="btn btn-sm btn-outline-primary" style="font-size:.78rem;">
                <i class="bi bi-printer me-1"></i> Cetak Bukti
            </a>
            <a href="/logout" class="btn btn-sm btn-outline-danger" style="font-size:.78rem;">
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
                <div class="col-md-6">
                    <div class="card border-0 rounded-3 h-100" style="box-shadow:0 2px 12px rgba(0,0,0,.06);">
                        <div class="card-body p-4">
                            <h6 class="fw-700 mb-3" style="color:var(--primary);">
                                <i class="bi bi-person-vcard me-2"></i>Data Diri
                            </h6>
                            <?php $fields = [
                                ['Nama Lengkap', $p['nama_lengkap'] ?? ''],
                                ['Tempat, Tgl Lahir', ($p['tempat_lahir'] ?? '').', '.($p['tanggal_lahir'] ? date('d M Y', strtotime($p['tanggal_lahir'])) : '')],
                                ['Nomor HP', $p['nomor_hp'] ?? ''],
                                ['Nama Ibu Kandung', $p['nama_ibu_kandung'] ?? ''],
                            ]; foreach ($fields as [$lbl, $val]): ?>
                            <div class="d-flex py-2 border-bottom" style="font-size:.83rem;">
                                <span class="text-muted" style="width:140px;flex-shrink:0;"><?= $lbl ?></span>
                                <span class="fw-500"><?= htmlspecialchars($val ?: '-') ?></span>
                            </div>
                            <?php endforeach; ?>
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
                                <span class="text-muted d-block" style="font-size:.72rem;">Jenjang</span>
                                <span class="badge <?= ($p['jenjang'] ?? '') === 'S2' ? 'bg-warning text-dark' : 'bg-primary' ?>">
                                    <?= htmlspecialchars($p['jenjang'] ?? '') ?>
                                </span>
                            </div>
                            <div class="py-2" style="font-size:.83rem;">
                                <span class="text-muted d-block" style="font-size:.72rem;">Tahun Akademik</span>
                                <span class="fw-500"><?= htmlspecialchars($p['tahun_akademik'] ?? '-') ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DOKUMEN -->
            <div class="card border-0 rounded-3 mb-4" style="box-shadow:0 2px 12px rgba(0,0,0,.06);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-700 mb-0" style="color:var(--primary);">
                            <i class="bi bi-folder2-open me-2"></i>Dokumen Saya
                        </h6>
                        <button class="btn btn-sm" style="background:var(--primary);color:#fff;font-size:.75rem;"
                                data-bs-toggle="modal" data-bs-target="#modalUpload">
                            <i class="bi bi-cloud-upload me-1"></i> Upload Dokumen
                        </button>
                    </div>

                    <?php if (empty($docs)): ?>
                    <p class="text-muted text-center py-3" style="font-size:.85rem;">
                        Belum ada dokumen yang diunggah.
                    </p>
                    <?php else: ?>
                    <div class="row g-2">
                        <?php foreach ($docs as $doc): ?>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-3 p-3 rounded-2" style="background:#f8fafc;border:1px solid #e2e8f0;">
                                <?php $ext = strtolower(pathinfo($doc['nama_file_asli'] ?? '', PATHINFO_EXTENSION)); ?>
                                <i class="bi <?= $ext === 'pdf' ? 'bi-file-earmark-pdf text-danger' : 'bi-file-earmark-image text-primary' ?>" style="font-size:1.8rem;"></i>
                                <div class="flex-fill" style="min-width:0;">
                                    <div class="fw-600" style="font-size:.8rem;"><?= htmlspecialchars($doc['jenis_dokumen']) ?></div>
                                    <div style="font-size:.7rem;color:#94a3b8;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                        <?= htmlspecialchars($doc['nama_file_asli'] ?? '') ?>
                                    </div>
                                    <div style="font-size:.7rem;color:#94a3b8;"><?= date('d M Y', strtotime($doc['created_at'])) ?></div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- RIWAYAT VERIFIKASI -->
            <?php if (!empty($logs)): ?>
            <div class="card border-0 rounded-3 mb-4" style="box-shadow:0 2px 12px rgba(0,0,0,.06);">
                <div class="card-body p-4">
                    <h6 class="fw-700 mb-3" style="color:var(--primary);">
                        <i class="bi bi-clock-history me-2"></i>Riwayat Verifikasi
                    </h6>
                    <?php foreach ($logs as $log): ?>
                    <div class="d-flex gap-3 pb-3 mb-3 border-bottom" style="font-size:.82rem;">
                        <div style="min-width:8px;width:8px;height:8px;border-radius:50%;background:var(--primary);margin-top:5px;flex-shrink:0;"></div>
                        <div>
                            <div class="fw-600">Status diubah ke <span class="text-primary"><?= ucfirst($log['status_baru']) ?></span></div>
                            <?php if ($log['catatan']): ?>
                            <div class="mt-1 text-muted"><?= htmlspecialchars($log['catatan']) ?></div>
                            <?php endif; ?>
                            <div class="text-muted mt-1" style="font-size:.72rem;"><?= date('d M Y H:i', strtotime($log['created_at'])) ?></div>
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
        <div class="modal-content border-0 rounded-3">
            <div class="modal-header border-0" style="background:var(--primary);">
                <h5 class="modal-title text-white fw-700">Upload Dokumen</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/pendaftar/upload" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-600" style="font-size:.82rem;">Jenis Dokumen</label>
                        <select name="jenis_dokumen" class="form-select form-select-sm" required>
                            <option value="">-- Pilih jenis dokumen --</option>
                            <?php $jenisDoc = ['KTP','Kartu Keluarga','Akte Kelahiran','Ijazah SMA','Transkrip SMA','Ijazah S1','Transkrip S1','Foto Resmi','Dokumen Lainnya'];
                            foreach ($jenisDoc as $j): ?>
                            <option value="<?= $j ?>"><?= $j ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-600" style="font-size:.82rem;">File</label>
                        <input type="file" name="file" class="form-control form-control-sm" required accept=".pdf,.jpg,.jpeg,.png">
                        <div class="form-text">PDF, JPG, PNG. Maks 5 MB</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm" style="background:var(--primary);color:#fff;">
                        <i class="bi bi-upload me-1"></i> Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
