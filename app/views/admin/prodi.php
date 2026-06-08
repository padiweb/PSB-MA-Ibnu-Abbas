<?php // views/admin/prodi.php
$list = $list ?? [];
?>
<div class="page-header d-flex align-items-center justify-content-between">
    <h1><i class="bi bi-mortarboard me-2"></i>Program Studi</h1>
    <button class="btn btn-sm" style="background:var(--primary);color:#fff;" data-bs-toggle="modal" data-bs-target="#modalProdi">
        <i class="bi bi-plus-circle me-1"></i> Tambah Program Studi
    </button>
</div>

<div class="admin-table">
    <table class="table">
        <thead>
            <tr>
                <th>Nama Program Studi</th>
                <th>Jenjang</th>
                <th>Fakultas</th>
                <th>Gelar</th>
                <th>Status</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($list)): ?>
            <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada data program studi</td></tr>
            <?php else: ?>
            <?php foreach ($list as $pr): ?>
            <tr>
                <td>
                    <div class="fw-600"><?= htmlspecialchars($pr['nama_prodi']) ?></div>
                    <div style="font-size:.72rem;color:#64748b;"><?= htmlspecialchars($pr['singkatan'] ?? '') ?></div>
                </td>
                <td>
                    <span class="badge <?= $pr['jenjang'] === 'S2' ? 'bg-warning text-dark' : 'bg-primary' ?>" style="font-size:.72rem;">
                        <?= htmlspecialchars($pr['jenjang']) ?>
                    </span>
                </td>
                <td style="font-size:.83rem;"><?= htmlspecialchars($pr['nama_fakultas'] ?? '-') ?></td>
                <td><code style="font-size:.8rem;"><?= htmlspecialchars($pr['gelar'] ?? '') ?></code></td>
                <td>
                    <span class="badge <?= $pr['is_aktif'] ? 'bg-success' : 'bg-secondary' ?>" style="font-size:.7rem;">
                        <?= $pr['is_aktif'] ? 'Aktif' : 'Nonaktif' ?>
                    </span>
                </td>
                <td class="text-center">
                    <div class="d-flex gap-1 justify-content-center">
                        <button class="btn btn-sm btn-outline-primary" style="padding:3px 8px;"
                                onclick="editProdi(<?= htmlspecialchars(json_encode($pr)) ?>)">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form method="POST" action="<?= BASE_URL ?>/admin/prodi/<?= $pr['id'] ?>/toggle" class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?= Security::generateCsrf() ?>">
                            <button class="btn btn-sm btn-outline-<?= $pr['is_aktif'] ? 'warning' : 'success' ?>" style="padding:3px 8px;font-size:.72rem;">
                                <i class="bi bi-<?= $pr['is_aktif'] ? 'pause-circle' : 'play-circle' ?>"></i>
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
<div class="modal fade" id="modalProdi" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 rounded-3">
            <div class="modal-header border-0" style="background:var(--primary);">
                <h5 class="modal-title text-white fw-700" id="modalProdiTitle">Tambah Program Studi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="formProdi" action="<?= BASE_URL ?>/admin/prodi">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCsrf() ?>">
                <input type="hidden" name="id" id="prodiId" value="">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-600" style="font-size:.82rem;">Nama Program Studi <span class="text-danger">*</span></label>
                        <input type="text" name="nama_prodi" id="prodiNama" class="form-control" required
                               placeholder="Pendidikan Agama Islam">
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-600" style="font-size:.82rem;">Singkatan</label>
                            <input type="text" name="singkatan" id="prodiSingkatan" class="form-control" placeholder="PAI" maxlength="10">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-600" style="font-size:.82rem;">Jenjang <span class="text-danger">*</span></label>
                            <select name="jenjang" id="prodiJenjang" class="form-select" required>
                                <option value="S1">S1</option>
                                <option value="S2">S2</option>
                                <option value="D3">D3</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-600" style="font-size:.82rem;">Gelar <span class="text-danger">*</span></label>
                            <input type="text" name="gelar" id="prodiGelar" class="form-control" placeholder="S.Pd." required maxlength="20">
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <label class="form-label fw-600" style="font-size:.82rem;">Fakultas</label>
                        <input type="text" name="nama_fakultas" id="prodiFakultas" class="form-control"
                               placeholder="Tarbiyah / Ekonomi / Hukum">
                    </div>
                    <div class="mb-0">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_aktif" id="prodiAktif" value="1" checked>
                            <label class="form-check-label" for="prodiAktif" style="font-size:.85rem;">Aktif (tersedia untuk pendaftaran)</label>
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
const BASE_URL = '<?= BASE_URL ?>';
function editProdi(data) {
    document.getElementById('modalProdiTitle').textContent = 'Edit Program Studi';
    document.getElementById('prodiId').value       = data.id;
    document.getElementById('prodiNama').value      = data.nama_prodi;
    document.getElementById('prodiSingkatan').value = data.singkatan || '';
    document.getElementById('prodiJenjang').value   = data.jenjang;
    document.getElementById('prodiGelar').value     = data.gelar || '';
    document.getElementById('prodiFakultas').value  = data.nama_fakultas || '';
    document.getElementById('prodiAktif').checked   = data.is_aktif == 1;
    document.getElementById('formProdi').action = BASE_URL + '/admin/prodi/' + data.id + '/update';
    new bootstrap.Modal(document.getElementById('modalProdi')).show();
}
document.getElementById('modalProdi').addEventListener('hidden.bs.modal', function () {
    document.getElementById('modalProdiTitle').textContent = 'Tambah Program Studi';
    document.getElementById('formProdi').reset();
    document.getElementById('formProdi').action = BASE_URL + '/admin/prodi';
    document.getElementById('prodiId').value = '';
    document.getElementById('prodiAktif').checked = true;
});
</script>
