<?php // views/admin/tahun-akademik.php
$list = $list ?? [];
?>
<div class="page-header d-flex align-items-center justify-content-between">
    <h1><i class="bi bi-calendar3 me-2"></i>Tahun Akademik</h1>
    <button class="btn btn-sm" style="background:var(--primary);color:#fff;" data-bs-toggle="modal" data-bs-target="#modalTA">
        <i class="bi bi-plus-circle me-1"></i> Tambah Tahun Akademik
    </button>
</div>

<div class="admin-table">
    <table class="table">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Kode</th>
                <th>Tanggal Mulai</th>
                <th>Tanggal Tutup</th>
                <th>Status</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($list)): ?>
            <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada data tahun akademik</td></tr>
            <?php else: ?>
            <?php foreach ($list as $ta): ?>
            <tr>
                <td class="fw-600"><?= htmlspecialchars($ta['nama']) ?></td>
                <td><code style="font-size:.8rem;"><?= htmlspecialchars($ta['kode']) ?></code></td>
                <td style="font-size:.83rem;"><?= $ta['tanggal_mulai'] ? date('d M Y', strtotime($ta['tanggal_mulai'])) : '-' ?></td>
                <td style="font-size:.83rem;"><?= $ta['tanggal_tutup'] ? date('d M Y', strtotime($ta['tanggal_tutup'])) : 'Belum ditentukan' ?></td>
                <td>
                    <?php if ($ta['aktif']): ?>
                        <span class="badge bg-success" style="font-size:.72rem;">Aktif</span>
                    <?php else: ?>
                        <span class="badge bg-secondary" style="font-size:.72rem;">Tidak Aktif</span>
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <div class="d-flex gap-1 justify-content-center">
                        <?php if (!$ta['aktif']): ?>
                        <form method="POST" action="/admin/tahun-akademik/<?= $ta['id'] ?>/aktifkan" class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">
                            <button class="btn btn-sm btn-outline-success" style="padding:3px 8px;font-size:.72rem;"
                                    onclick="return confirm('Aktifkan tahun akademik ini? PMB sebelumnya akan ditutup.')">
                                <i class="bi bi-check-circle"></i> Aktifkan
                            </button>
                        </form>
                        <?php endif; ?>
                        <button class="btn btn-sm btn-outline-primary" style="padding:3px 8px;"
                                onclick="editTA(<?= htmlspecialchars(json_encode($ta)) ?>)">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <?php if (!$ta['aktif']): ?>
                        <form method="POST" action="/admin/tahun-akademik/<?= $ta['id'] ?>/hapus" class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">
                            <button class="btn btn-sm btn-outline-danger" style="padding:3px 8px;"
                                    onclick="return confirm('Hapus tahun akademik ini?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- MODAL TAMBAH/EDIT -->
<div class="modal fade" id="modalTA" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 rounded-3">
            <div class="modal-header border-0" style="background:var(--primary);">
                <h5 class="modal-title text-white fw-700" id="modalTATitle">Tambah Tahun Akademik</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="formTA" action="/admin/tahun-akademik/simpan">
                <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">
                <input type="hidden" name="id" id="taId" value="">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-600" style="font-size:.82rem;">Nama Tahun Akademik <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="taNama" class="form-control" placeholder="Contoh: 2026/2027" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600" style="font-size:.82rem;">Kode <span class="text-danger">*</span></label>
                        <input type="text" name="kode" id="taKode" class="form-control" placeholder="Contoh: 2026" required maxlength="10">
                        <div class="form-text">Kode digunakan untuk format nomor pendaftaran, misal: PMB-2026-000001</div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-600" style="font-size:.82rem;">Tanggal Mulai Pendaftaran</label>
                            <input type="date" name="tanggal_mulai" id="taMulai" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600" style="font-size:.82rem;">Tanggal Tutup Pendaftaran</label>
                            <input type="date" name="tanggal_tutup" id="taTutup" class="form-control">
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
function editTA(data) {
    document.getElementById('modalTATitle').textContent = 'Edit Tahun Akademik';
    document.getElementById('taId').value   = data.id;
    document.getElementById('taNama').value = data.nama;
    document.getElementById('taKode').value = data.kode;
    document.getElementById('taMulai').value= data.tanggal_mulai || '';
    document.getElementById('taTutup').value= data.tanggal_tutup || '';
    document.getElementById('formTA').action = '/admin/tahun-akademik/' + data.id + '/update';
    new bootstrap.Modal(document.getElementById('modalTA')).show();
}
document.getElementById('modalTA').addEventListener('hidden.bs.modal', function () {
    document.getElementById('modalTATitle').textContent = 'Tambah Tahun Akademik';
    document.getElementById('formTA').reset();
    document.getElementById('formTA').action = '/admin/tahun-akademik/simpan';
    document.getElementById('taId').value = '';
});
</script>
