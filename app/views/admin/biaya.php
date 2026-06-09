<?php // views/admin/biaya.php
$list      = $list ?? [];
$taList    = $ta_list ?? [];
$prodiList = $prodi_list ?? [];
$taId      = $ta_id ?? 0;
?>
<div class="page-header d-flex align-items-center justify-content-between">
    <h1><i class="bi bi-currency-dollar me-2"></i>Pengaturan Biaya</h1>
    <button class="btn btn-sm" style="background:var(--primary);color:#fff;" data-bs-toggle="modal" data-bs-target="#modalBiaya">
        <i class="bi bi-plus-circle me-1"></i> Tambah / Edit Biaya
    </button>
</div>

<!-- Filter Tahun -->
<div class="card border-0 rounded-3 mb-4" style="box-shadow:0 2px 12px rgba(0,0,0,.06);">
    <div class="card-body p-3">
        <form method="GET" action="<?= BASE_URL ?>/index.php">
                <input type="hidden" name="page" value="admin/biaya">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label mb-1 fw-600" style="font-size:.75rem;color:#64748b;">TAHUN AKADEMIK</label>
                    <select name="ta" class="form-select form-select-sm" onchange="this.form.submit()">
                        <?php foreach ($taList as $ta): ?>
                        <option value="<?= $ta['id'] ?>" <?= $ta['id'] == $taId ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ta['nama']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="admin-table">
    <table class="table">
        <thead>
            <tr>
                <th>Program Studi</th>
                <th>Jenjang</th>
                <th>Biaya Pendaftaran</th>
                <th>SPP/Bulan</th>
                <th>Biaya Pendidikan Total</th>
                <th>Keterangan</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($list)): ?>
            <tr>
                <td colspan="7" class="text-center py-5">
                    <i class="bi bi-inbox" style="font-size:2.5rem;color:#cbd5e1;"></i>
                    <div class="mt-2 text-muted">Belum ada data biaya untuk tahun akademik ini</div>
                    <button class="btn btn-sm mt-2" style="background:var(--primary);color:#fff;"
                            data-bs-toggle="modal" data-bs-target="#modalBiaya">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Sekarang
                    </button>
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($list as $b): ?>
            <tr>
                <td>
                    <div class="fw-600"><?= htmlspecialchars($b['nama_prodi'] ?? '-') ?></div>
                    <div style="font-size:.72rem;color:#64748b;"><?= htmlspecialchars($b['nama_fakultas'] ?? '') ?></div>
                </td>
                <td>
                    <span class="badge <?= ($b['jenjang'] ?? '') === 'S2' ? 'bg-warning text-dark' : 'bg-primary' ?>" style="font-size:.7rem;">
                        <?= htmlspecialchars($b['jenjang'] ?? '-') ?>
                    </span>
                </td>
                <td class="fw-700" style="color:var(--primary);">Rp <?= number_format($b['biaya_pendaftaran'] ?? 0) ?></td>
                <td>
                    <?php if ($b['biaya_spp'] ?? 0): ?>
                    Rp <?= number_format($b['biaya_spp']) ?>/bln
                    <?php else: ?>
                    <span class="text-muted">-</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($b['biaya_pendidikan'] ?? 0): ?>
                    Rp <?= number_format($b['biaya_pendidikan']) ?>
                    <?php else: ?>
                    <span class="text-muted">-</span>
                    <?php endif; ?>
                </td>
                <td style="font-size:.8rem;color:#64748b;"><?= htmlspecialchars($b['keterangan'] ?? '-') ?></td>
                <td class="text-center">
                    <div class="d-flex gap-1 justify-content-center">
                        <button class="btn btn-sm btn-outline-primary" style="padding:3px 8px;"
                                onclick="editBiaya(<?= htmlspecialchars(json_encode($b)) ?>)">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form method="POST" action="<?= url('/admin/biaya/' . $b['id'] . '/hapus') ?>" class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?= Security::generateCsrf() ?>">
                            <button class="btn btn-sm btn-outline-danger" style="padding:3px 8px;"
                                    onclick="return confirm('Hapus data biaya ini?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- MODAL -->
<div class="modal fade" id="modalBiaya" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 rounded-3">
            <div class="modal-header border-0" style="background:var(--primary);">
                <h5 class="modal-title text-white fw-700" id="modalBiayaTitle">Tambah / Edit Biaya</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="formBiaya" action="<?= url('/admin/biaya') ?>">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCsrf() ?>">
                <input type="hidden" name="tahun_akademik_id" value="<?= $taId ?>">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-600" style="font-size:.82rem;">Program Studi <span class="text-danger">*</span></label>
                        <select name="program_studi_id" id="biayaProdi" class="form-select form-select-sm" required>
                            <option value="">-- Pilih Program Studi --</option>
                            <?php foreach ($prodiList as $pr): ?>
                            <option value="<?= $pr['id'] ?>"><?= htmlspecialchars($pr['nama_prodi']) ?> (<?= $pr['jenjang'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600" style="font-size:.82rem;">Biaya Pendaftaran (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="biaya_pendaftaran" id="biayaDaftar" class="form-control form-control-sm" required min="0" placeholder="300000">
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-600" style="font-size:.82rem;">SPP per Bulan (Rp)</label>
                            <input type="number" name="biaya_spp" id="biayaSpp" class="form-control form-control-sm" min="0" placeholder="250000">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600" style="font-size:.82rem;">Biaya Pendidikan Total (Rp)</label>
                            <input type="number" name="biaya_pendidikan" id="biayaPendidikan" class="form-control form-control-sm" min="0" placeholder="8000000">
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label fw-600" style="font-size:.82rem;">Keterangan</label>
                        <input type="text" name="keterangan" id="biayaKet" class="form-control form-control-sm"
                               placeholder="Contoh: atau Rp 500.000/bulan">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm" style="background:var(--primary);color:#fff;">
                        <i class="bi bi-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editBiaya(data) {
    document.getElementById('biayaProdi').value = data.program_studi_id;
    document.getElementById('biayaDaftar').value = data.biaya_pendaftaran || 0;
    document.getElementById('biayaSpp').value = data.biaya_spp || 0;
    document.getElementById('biayaPendidikan').value = data.biaya_pendidikan || 0;
    document.getElementById('biayaKet').value = data.keterangan || '';
    new bootstrap.Modal(document.getElementById('modalBiaya')).show();
}
</script>