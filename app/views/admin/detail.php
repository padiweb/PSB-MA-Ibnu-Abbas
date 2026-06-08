<?php
// views/admin/detail.php
$p    = $pendaftar ?? [];
$docs = $dokumen ?? [];
$logs = $verifikasi_log ?? [];

$statusOptions = ['menunggu','diterima','revisi','ditolak'];
$statusColors  = ['menunggu'=>'warning','diterima'=>'success','revisi'=>'info','ditolak'=>'danger'];
?>
<div class="page-header d-flex align-items-center gap-3 justify-content-between">
    <div class="d-flex align-items-center gap-3">
        <a href="<?= BASE_URL ?>/admin/pendaftar" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 style="font-size:1.25rem;">Detail Pendaftar</h1>
            <code style="font-size:.82rem;background:#f1f5f9;padding:2px 10px;border-radius:4px;color:var(--primary);">
                <?= htmlspecialchars($p['nomor_pendaftaran'] ?? '') ?>
            </code>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>/admin/pendaftar/<?= $p['id'] ?>/cetak" target="_blank" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-printer me-1"></i> Cetak
        </a>
    </div>
</div>

<div class="row g-3">
    <!-- LEFT: Data Pendaftar -->
    <div class="col-lg-8">
        <!-- Data Diri -->
        <div class="card border-0 rounded-3 mb-3" style="box-shadow:0 2px 12px rgba(0,0,0,.06);">
            <div class="card-body p-0">
                <div class="px-4 py-3 border-bottom d-flex align-items-center gap-2">
                    <i class="bi bi-person-vcard" style="color:var(--primary);"></i>
                    <h6 class="mb-0 fw-700" style="color:var(--primary);">Data Diri</h6>
                </div>
                <div class="p-4">
                    <div class="row g-3" style="font-size:.85rem;">
                        <?php $fields = [
                            ['Nama Lengkap',    $p['nama_lengkap'] ?? ''],
                            ['Tempat Lahir',     $p['tempat_lahir'] ?? ''],
                            ['Tanggal Lahir',    $p['tanggal_lahir'] ? date('d F Y', strtotime($p['tanggal_lahir'])) : ''],
                            ['Nomor HP',         $p['nomor_hp'] ?? ''],
                            ['Nama Ibu Kandung', $p['nama_ibu_kandung'] ?? ''],
                            ['Alamat KTP',       $p['alamat_ktp'] ?? ''],
                        ]; foreach ($fields as [$lbl, $val]): ?>
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;"><?= $lbl ?></div>
                            <div class="fw-500 mt-1"><?= htmlspecialchars($val ?: '-') ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Program Studi -->
        <div class="card border-0 rounded-3 mb-3" style="box-shadow:0 2px 12px rgba(0,0,0,.06);">
            <div class="card-body p-0">
                <div class="px-4 py-3 border-bottom d-flex align-items-center gap-2">
                    <i class="bi bi-mortarboard" style="color:var(--primary);"></i>
                    <h6 class="mb-0 fw-700" style="color:var(--primary);">Program Studi</h6>
                </div>
                <div class="p-4">
                    <div class="row g-3" style="font-size:.85rem;">
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size:.72rem;font-weight:600;text-transform:uppercase;">Program Studi</div>
                            <div class="fw-600 mt-1"><?= htmlspecialchars($p['nama_prodi'] ?? '-') ?></div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted" style="font-size:.72rem;font-weight:600;text-transform:uppercase;">Jenjang</div>
                            <div class="mt-1">
                                <span class="badge <?= ($p['jenjang'] ?? '') === 'S2' ? 'bg-warning text-dark' : 'bg-primary' ?>">
                                    <?= htmlspecialchars($p['jenjang'] ?? '-') ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted" style="font-size:.72rem;font-weight:600;text-transform:uppercase;">Tahun Akademik</div>
                            <div class="fw-500 mt-1"><?= htmlspecialchars($p['tahun_akademik'] ?? '-') ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dokumen -->
        <div class="card border-0 rounded-3 mb-3" style="box-shadow:0 2px 12px rgba(0,0,0,.06);">
            <div class="card-body p-0">
                <div class="px-4 py-3 border-bottom d-flex align-items-center gap-2">
                    <i class="bi bi-folder2-open" style="color:var(--primary);"></i>
                    <h6 class="mb-0 fw-700" style="color:var(--primary);">Dokumen Upload</h6>
                </div>
                <div class="p-3">
                    <?php if (empty($docs)): ?>
                    <p class="text-muted text-center py-3 mb-0" style="font-size:.85rem;">
                        <i class="bi bi-cloud-upload me-1"></i> Belum ada dokumen yang diunggah
                    </p>
                    <?php else: ?>
                    <div class="row g-2">
                        <?php foreach ($docs as $doc): ?>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-3 p-3 rounded-2" style="background:#f8fafc;border:1px solid #e2e8f0;">
                                <div style="font-size:1.8rem;color:#64748b;">
                                    <?php $ext = strtolower(pathinfo($doc['nama_file_asli'] ?? '', PATHINFO_EXTENSION)); ?>
                                    <i class="bi <?= $ext === 'pdf' ? 'bi-file-earmark-pdf text-danger' : 'bi-file-earmark-image text-primary' ?>"></i>
                                </div>
                                <div class="flex-fill min-width-0">
                                    <div class="fw-600" style="font-size:.8rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                        <?= htmlspecialchars($doc['jenis_dokumen']) ?>
                                    </div>
                                    <div style="font-size:.7rem;color:#94a3b8;"><?= htmlspecialchars($doc['nama_file_asli'] ?? '') ?></div>
                                    <div style="font-size:.7rem;color:#94a3b8;"><?= date('d M Y', strtotime($doc['created_at'])) ?></div>
                                </div>
                                <a href="<?= BASE_URL ?>/admin/dokumen/<?= $doc['id'] ?>/download"
                                   class="btn btn-sm btn-outline-primary" style="padding:3px 8px;font-size:.72rem;">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Riwayat Verifikasi -->
        <?php if (!empty($logs)): ?>
        <div class="card border-0 rounded-3" style="box-shadow:0 2px 12px rgba(0,0,0,.06);">
            <div class="card-body p-0">
                <div class="px-4 py-3 border-bottom">
                    <h6 class="mb-0 fw-700" style="color:var(--primary);">
                        <i class="bi bi-clock-history me-2"></i>Riwayat Verifikasi
                    </h6>
                </div>
                <div class="p-3">
                    <?php foreach ($logs as $log): ?>
                    <div class="d-flex gap-3 pb-3 mb-3 border-bottom" style="font-size:.82rem;">
                        <div style="min-width:8px;width:8px;height:8px;border-radius:50%;background:var(--primary);margin-top:5px;flex-shrink:0;"></div>
                        <div class="flex-fill">
                            <div class="fw-600">
                                <?= htmlspecialchars($log['admin_nama'] ?? 'Admin') ?>
                                <span class="text-muted fw-400">mengubah status ke</span>
                                <span class="badge bg-<?= $statusColors[$log['status_baru']] ?? 'secondary' ?> ms-1" style="font-size:.68rem;">
                                    <?= ucfirst($log['status_baru']) ?>
                                </span>
                            </div>
                            <?php if ($log['catatan']): ?>
                            <div class="mt-1 text-muted"><?= htmlspecialchars($log['catatan']) ?></div>
                            <?php endif; ?>
                            <div class="text-muted mt-1" style="font-size:.72rem;">
                                <?= date('d M Y H:i', strtotime($log['created_at'])) ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- RIGHT: Verifikasi Panel -->
    <div class="col-lg-4">
        <div class="card border-0 rounded-3 sticky-top" style="box-shadow:0 4px 20px rgba(0,0,0,.1);top:72px;">
            <div class="card-body p-0">
                <div class="p-4 border-bottom" style="background:linear-gradient(135deg,var(--primary),#2563eb);border-radius:12px 12px 0 0;">
                    <h6 class="text-white mb-1 fw-700">
                        <i class="bi bi-shield-check me-2"></i>Panel Verifikasi
                    </h6>
                    <div class="d-flex align-items-center gap-2 mt-2">
                        <span class="text-white-50" style="font-size:.78rem;">Status saat ini:</span>
                        <span class="badge bg-<?= $statusColors[$p['status_verifikasi'] ?? 'menunggu'] ?> text-white" style="font-size:.75rem;">
                            <?= ucfirst($p['status_verifikasi'] ?? 'menunggu') ?>
                        </span>
                    </div>
                </div>

                <?php if (in_array(Session::get('role'), ['superadmin','admin','verifikator'])): ?>
                <div class="p-4">
                    <form method="POST" action="<?= BASE_URL ?>/admin/verifikasi/<?= $p['id'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= Security::generateCsrf() ?>">

                        <div class="mb-3">
                            <label class="form-label fw-600" style="font-size:.8rem;">Ubah Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <?php foreach ($statusOptions as $opt): ?>
                                <option value="<?= $opt ?>" <?= ($p['status_verifikasi'] ?? '') === $opt ? 'selected' : '' ?>>
                                    <?= ucfirst($opt) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-600" style="font-size:.8rem;">Catatan (opsional)</label>
                            <textarea name="catatan" class="form-control form-control-sm" rows="3"
                                      placeholder="Catatan untuk pendaftar..."></textarea>
                            <div style="font-size:.72rem;color:#94a3b8;margin-top:4px;">
                                Catatan akan terlihat oleh pendaftar di dashboard mereka.
                            </div>
                        </div>

                        <button type="submit" class="btn w-100" style="background:var(--primary);color:#fff;font-size:.85rem;">
                            <i class="bi bi-check2-circle me-1"></i> Simpan Verifikasi
                        </button>
                    </form>
                </div>
                <?php endif; ?>

                <!-- Info -->
                <div class="p-4 pt-0">
                    <hr class="my-0 mb-3">
                    <div style="font-size:.78rem;color:#64748b;">
                        <div class="d-flex justify-content-between py-1">
                            <span>Tanggal Daftar</span>
                            <strong><?= date('d M Y', strtotime($p['created_at'] ?? 'now')) ?></strong>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span>Dokumen Terupload</span>
                            <strong><?= count($docs) ?> berkas</strong>
                        </div>
                        <?php if ($p['promo_digunakan'] ?? false): ?>
                        <div class="mt-2 p-2 rounded-2" style="background:#fffbeb;border:1px solid #fde68a;">
                            <i class="bi bi-gift text-warning me-1"></i>
                            <span style="color:#92400e;font-weight:600;font-size:.75rem;">Menggunakan promo gratis pendaftaran</span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
