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
        <a href="<?= url('/admin/pendaftar') ?>" class="btn btn-sm btn-outline-secondary">
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
        <a href="<?= url('/admin/pendaftar/' . $p['id'] . '/cetak') ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
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
                            ['Alamat KTP',       $p['alamat'] ?? ''],
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
                            <div class="fw-500 mt-1"><?= htmlspecialchars($p['ta_nama'] ?? $p['ta_kode'] ?? '-' ?? '-') ?></div>
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
                    <?php
                    $allDocTypes  = (new DokumenModel())->getDokumenTypes();
                    $uploadedMap  = [];
                    foreach ($docs as $d) { $uploadedMap[$d['jenis_dokumen']] = $d; }
                    $isS2p        = ($p['jenjang'] ?? '') === 'S2';
                    $s1OnlyKeys   = ['ijazah_s1','transkrip_s1'];
                    $uploadedCnt  = count($uploadedMap);
                    $totalCnt     = count($allDocTypes);
                    $pctDone      = $totalCnt > 0 ? round($uploadedCnt/$totalCnt*100) : 0;
                    ?>
                    <!-- Progress kelengkapan -->
                    <div class="mb-3 px-1">
                        <div class="d-flex justify-content-between mb-1" style="font-size:.75rem;">
                            <span class="text-muted">Kelengkapan Berkas</span>
                            <span class="fw-600" style="color:var(--primary)"><?= $uploadedCnt ?>/<?= $totalCnt ?> (<?= $pctDone ?>%)</span>
                        </div>
                        <div class="progress" style="height:5px;border-radius:3px;">
                            <div class="progress-bar" style="width:<?= $pctDone ?>%;background:<?= $pctDone >= 100 ? '#16a34a' : 'var(--primary)' ?>;border-radius:3px;"></div>
                        </div>
                    </div>

                    <div class="row g-2">
                        <?php foreach ($allDocTypes as $jKey => $jLabel): ?>
                        <?php
                            $hasDok   = isset($uploadedMap[$jKey]);
                            $dokItem  = $uploadedMap[$jKey] ?? null;
                            $isOpt    = !$isS2p && in_array($jKey, $s1OnlyKeys);
                            $fExt     = $hasDok ? strtolower(pathinfo($dokItem['nama_file_asli'] ?? '', PATHINFO_EXTENSION)) : '';
                            $isPdf    = $fExt === 'pdf';
                        ?>
                        <div class="col-12">
                            <div class="rounded-3 p-3" style="border:1.5px solid <?= $hasDok ? '#86efac' : ($isOpt ? '#fde68a' : '#fecaca') ?>;
                                                                                   background:<?= $hasDok ? '#f0fdf4' : ($isOpt ? '#fffbeb' : '#fff5f5') ?>;">
                                <div class="d-flex align-items-start gap-3">
                                    <!-- Status icon -->
                                    <div style="width:34px;height:34px;border-radius:8px;flex-shrink:0;display:flex;align-items:center;justify-content:center;
                                                background:<?= $hasDok ? '#dcfce7' : ($isOpt ? '#fef9c3' : '#fee2e2') ?>;
                                                color:<?= $hasDok ? '#16a34a' : ($isOpt ? '#ca8a04' : '#dc2626') ?>;">
                                        <i class="bi <?= $hasDok ? 'bi-check-circle-fill' : ($isOpt ? 'bi-dash-circle' : 'bi-exclamation-circle-fill') ?>"></i>
                                    </div>
                                    <div class="flex-fill" style="min-width:0;">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="fw-600" style="font-size:.82rem;"><?= $jLabel ?></span>
                                            <?php if (!$hasDok): ?>
                                            <span class="badge" style="font-size:.62rem;background:<?= $isOpt ? '#fef9c3' : '#fee2e2' ?>;color:<?= $isOpt ? '#ca8a04' : '#dc2626' ?>;">
                                                <?= $isOpt ? 'Opsional' : 'Belum Upload' ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($hasDok): ?>
                                        <div class="d-flex align-items-center gap-2">
                                            <?php if (!$isPdf): ?>
                                            <img src="<?= url('/admin/dokumen/' . $dokItem['id'] . '/download') ?>"
                                                 style="width:40px;height:40px;object-fit:cover;border-radius:5px;border:1px solid #e2e8f0;cursor:pointer;flex-shrink:0;"
                                                 onclick="viewImage('<?= url('/admin/dokumen/' . $dokItem['id'] . '/download') ?>','<?= htmlspecialchars($jLabel) ?>')"
                                                 onerror="this.style.display='none'">
                                            <?php else: ?>
                                            <div style="width:40px;height:40px;border-radius:5px;background:#fee2e2;flex-shrink:0;display:flex;align-items:center;justify-content:center;">
                                                <i class="bi bi-file-earmark-pdf text-danger"></i>
                                            </div>
                                            <?php endif; ?>
                                            <div style="flex:1;min-width:0;">
                                                <div style="font-size:.72rem;font-weight:600;color:#374151;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                                    <?= htmlspecialchars($dokItem['nama_file_asli'] ?? '') ?>
                                                </div>
                                                <div style="font-size:.68rem;color:#9ca3af;">
                                                    <?= $dokItem['uploaded_at'] ? date('d M Y H:i', strtotime($dokItem['uploaded_at'])) : '' ?>
                                                </div>
                                            </div>
                                            <!-- Tombol -->
                                            <div class="d-flex gap-1 flex-shrink-0">
                                                <?php if (!$isPdf): ?>
                                                <button class="btn btn-sm btn-outline-primary" style="padding:2px 7px;font-size:.7rem;"
                                                        title="Lihat"
                                                        onclick="viewImage('<?= url('/admin/dokumen/' . $dokItem['id'] . '/download') ?>','<?= htmlspecialchars($jLabel) ?>')">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <?php endif; ?>
                                                <a href="<?= url('/admin/dokumen/' . $dokItem['id'] . '/download') ?>"
                                                   class="btn btn-sm btn-outline-secondary" style="padding:2px 7px;font-size:.7rem;" title="Download">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <?php else: ?>
                                        <div style="font-size:.75rem;color:<?= $isOpt ? '#92400e' : '#dc2626' ?>;">
                                            <?= $isOpt ? 'Tidak wajib untuk S1' : 'Pendaftar belum mengupload berkas ini' ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
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
                                <span class="badge bg-<?= $statusColors[$log['status_sesudah']] ?? 'secondary' ?> ms-1" style="font-size:.68rem;">
                                    <?= ucfirst($log['status_sesudah']) ?>
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
                        <?php $curStatus = $p['status'] ?? 'menunggu'; ?>
                        <span class="badge bg-<?= $statusColors[$curStatus] ?? 'secondary' ?> text-white" style="font-size:.75rem;">
                            <?= ucfirst($curStatus) ?>
                        </span>
                    </div>
                </div>

                <?php if (in_array(Session::get('role'), ['superadmin','admin','verifikator'])): ?>
                <div class="p-4">
                    <p class="text-muted mb-3" style="font-size:.78rem;">
                        <i class="bi bi-info-circle me-1"></i>
                        Pilih status, tambah catatan jika perlu, lalu klik <strong>Simpan</strong>.
                        Catatan akan tampil di dashboard pendaftar.
                    </p>
                    <form method="POST" action="<?= url('/admin/verifikasi/' . $p['id']) ?>">
                        <input type="hidden" name="csrf_token" value="<?= Security::generateCsrf() ?>">

                        <div class="mb-3">
                            <label class="form-label fw-600" style="font-size:.8rem;">Ubah Status Pendaftar</label>
                            <div class="d-grid gap-2">
                                <?php
                                $statusOpts = [
                                    'menunggu' => ['label'=>'Menunggu',  'icon'=>'bi-hourglass-split',    'color'=>'#d97706','bg'=>'#fffbeb','border'=>'#fde68a'],
                                    'diterima' => ['label'=>'Diterima',  'icon'=>'bi-check-circle-fill',  'color'=>'#16a34a','bg'=>'#f0fdf4','border'=>'#86efac'],
                                    'revisi'   => ['label'=>'Perlu Revisi','icon'=>'bi-pencil-square',    'color'=>'#2563eb','bg'=>'#eff6ff','border'=>'#bfdbfe'],
                                    'ditolak'  => ['label'=>'Ditolak',   'icon'=>'bi-x-circle-fill',      'color'=>'#dc2626','bg'=>'#fff5f5','border'=>'#fecaca'],
                                ];
                                foreach ($statusOpts as $val => $opt):
                                $isSelected = ($p['status'] ?? '') === $val;
                                ?>
                                <label class="d-flex align-items-center gap-2 p-2 rounded-3 cursor-pointer"
                                       style="border:2px solid <?= $isSelected ? $opt['border'] : '#e2e8f0' ?>;
                                              background:<?= $isSelected ? $opt['bg'] : '#fff' ?>;
                                              cursor:pointer;transition:.15s">
                                    <input type="radio" name="status" value="<?= $val ?>"
                                           <?= $isSelected ? 'checked' : '' ?>
                                           style="accent-color:<?= $opt['color'] ?>"
                                           onchange="this.closest('.d-grid').querySelectorAll('label').forEach(l=>{l.style.border='2px solid #e2e8f0';l.style.background='#fff'});this.closest('label').style.border='2px solid <?= $opt['border'] ?>';this.closest('label').style.background='<?= $opt['bg'] ?>'">
                                    <i class="bi <?= $opt['icon'] ?>" style="color:<?= $opt['color'] ?>;font-size:.95rem;"></i>
                                    <span class="fw-600" style="font-size:.82rem;color:<?= $opt['color'] ?>"><?= $opt['label'] ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-600" style="font-size:.8rem;">Catatan untuk Pendaftar</label>
                            <textarea name="catatan" class="form-control form-control-sm" rows="3"
                                      placeholder="Contoh: Silakan upload ulang foto dengan background biru..."></textarea>
                            <div style="font-size:.7rem;color:#94a3b8;margin-top:4px;">
                                <i class="bi bi-eye me-1"></i>Catatan ini akan terlihat oleh pendaftar.
                            </div>
                        </div>

                        <button type="submit" class="btn w-100 rounded-3" style="background:var(--primary);color:#fff;font-size:.85rem;padding:.65rem;">
                            <i class="bi bi-check2-circle me-2"></i>Simpan Verifikasi
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
                        <?php if ($p['promo_id'] ?? false): ?>
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

<!-- Modal Preview Gambar -->
<div class="modal fade" id="imgModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-3 overflow-hidden">
            <div class="modal-header border-0 p-3" style="background:var(--primary);">
                <h6 class="modal-title text-white fw-700 mb-0" id="imgModalTitle">Preview Dokumen</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 text-center" style="background:#1e293b;min-height:300px;">
                <img id="imgModalSrc" src="" alt="" style="max-width:100%;max-height:75vh;object-fit:contain;">
            </div>
        </div>
    </div>
</div>

<script>
function viewImage(src, title) {
    document.getElementById('imgModalSrc').src   = src;
    document.getElementById('imgModalTitle').textContent = title;
    new bootstrap.Modal(document.getElementById('imgModal')).show();
}
</script>