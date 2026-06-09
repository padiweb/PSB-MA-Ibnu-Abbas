<?php // views/admin/persyaratan.php
$list   = $list ?? [];
$taList = $ta_list ?? [];
$taId   = $ta_id ?? 0;
?>
<div class="page-header d-flex align-items-center justify-content-between">
    <h1><i class="bi bi-list-check me-2"></i>Persyaratan PMB</h1>
    <button class="btn btn-sm" style="background:var(--primary);color:#fff;" data-bs-toggle="modal" data-bs-target="#modalPersyaratan">
        <i class="bi bi-plus-circle me-1"></i> Tambah Persyaratan
    </button>
</div>

<!-- Filter Tahun -->
<div class="card border-0 rounded-3 mb-4" style="box-shadow:0 2px 12px rgba(0,0,0,.06);">
    <div class="card-body p-3">
        <form method="GET" action="<?= BASE_URL ?>/index.php">
                <input type="hidden" name="page" value="admin/persyaratan">
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
                <th style="width:60px;">Urutan</th>
                <th>Persyaratan</th>
                <th>Keterangan</th>
                <th style="width:80px;">Wajib</th>
                <th class="text-center" style="width:100px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($list)): ?>
            <tr>
                <td colspan="5" class="text-center py-5">
                    <i class="bi bi-clipboard-x" style="font-size:2.5rem;color:#cbd5e1;"></i>
                    <div class="mt-2 text-muted">Belum ada persyaratan untuk tahun akademik ini</div>
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($list as $p): ?>
            <tr>
                <td class="text-center">
                    <span class="badge bg-secondary"><?= $p['urutan'] ?></span>
                </td>
                <td class="fw-600"><?= htmlspecialchars($p['nama']) ?></td>
                <td style="font-size:.83rem;color:#64748b;"><?= htmlspecialchars($p['keterangan'] ?? '-') ?></td>
                <td class="text-center">
                    <?php if ($p['wajib']): ?>
                    <span class="badge bg-danger" style="font-size:.7rem;">Wajib</span>
                    <?php else: ?>
                    <span class="badge bg-info text-dark" style="font-size:.7rem;">Opsional</span>
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <div class="d-flex gap-1 justify-content-center">
                        <button class="btn btn-sm btn-outline-primary" style="padding:3px 8px;"
                                onclick="editPersyaratan(<?= htmlspecialchars(json_encode($p)) ?>, <?= $taId ?>)">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <a href="<?= url('/admin/persyaratan/' . $p['id'] . '/hapus?ta=' . $taId) ?>"
                           class="btn btn-sm btn-outline-danger" style="padding:3px 8px;"
                           onclick="return confirm('Hapus persyaratan ini?')">
                            <i class="bi bi-trash"></i>
                        </a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- MODAL TAMBAH/EDIT -->
<div class="modal fade" id="modalPersyaratan" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 rounded-3">
            <div class="modal-header border-0" style="background:var(--primary);">
                <h5 class="modal-title text-white fw-700" id="modalPersTitle">Tambah Persyaratan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="formPers" action="<?= url('/admin/persyaratan') ?>">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCsrf() ?>">
                <input type="hidden" name="id" id="persId" value="">
                <input type="hidden" name="tahun_akademik_id" id="persTaId" value="<?= $taId ?>">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-600" style="font-size:.82rem;">Nama Persyaratan <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="persNama" class="form-control" required
                               placeholder="Scan Asli KTP">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600" style="font-size:.82rem;">Keterangan</label>
                        <textarea name="keterangan" id="persKet" class="form-control" rows="2"
                                  placeholder="Keterangan tambahan..."></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-600" style="font-size:.82rem;">Urutan</label>
                            <input type="number" name="urutan" id="persUrutan" class="form-control" min="1" value="1">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="is_wajib" id="persWajib" value="1" checked>
                                <label class="form-check-label" for="persWajib" style="font-size:.85rem;">Persyaratan Wajib</label>
                            </div>
                        </div>
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
function editPersyaratan(data, taId) {
    document.getElementById('modalPersTitle').textContent = 'Edit Persyaratan';
    document.getElementById('persId').value    = data.id;
    document.getElementById('persNama').value  = data.nama;
    document.getElementById('persKet').value   = data.keterangan || '';
    document.getElementById('persUrutan').value= data.urutan || 1;
    document.getElementById('persWajib').checked = data.wajib == 1;
    document.getElementById('persTaId').value  = taId;
    document.getElementById('formPers').action = '/admin/persyaratan/' + data.id + '/update';
    new bootstrap.Modal(document.getElementById('modalPersyaratan')).show();
}
document.getElementById('modalPersyaratan').addEventListener('hidden.bs.modal', function () {
    document.getElementById('modalPersTitle').textContent = 'Tambah Persyaratan';
    document.getElementById('formPers').reset();
    document.getElementById('formPers').action = '/admin/persyaratan/simpan';
    document.getElementById('persId').value = '';
    document.getElementById('persWajib').checked = true;
});
</script>