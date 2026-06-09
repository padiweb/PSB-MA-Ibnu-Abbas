<?php // views/admin/edit-pendaftar.php
$p    = $pendaftar ?? [];
$prodi= $prodi_list ?? [];
$statusOptions = ['draft','menunggu','diterima','revisi','ditolak'];
?>

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= url('/admin/pendaftar/' . $p['id']) ?>" class="btn btn-sm btn-outline-secondary rounded-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h4 class="fw-700 mb-0" style="color:var(--primary)"><i class="bi bi-pencil-square me-2"></i>Edit Pendaftar</h4>
        <p class="text-muted mb-0" style="font-size:.8rem"><?= htmlspecialchars($p['nomor_pendaftaran'] ?? '') ?> — <?= htmlspecialchars($p['nama_lengkap'] ?? '') ?></p>
    </div>
</div>

<?php $err = Session::getFlash('error'); $ok = Session::getFlash('success'); ?>
<?php if ($err): ?><div class="alert alert-danger rounded-3 py-2 px-3 mb-4" style="font-size:.85rem;"><i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($err) ?></div><?php endif; ?>
<?php if ($ok):  ?><div class="alert alert-success rounded-3 py-2 px-3 mb-4" style="font-size:.85rem;"><i class="bi bi-check-circle me-1"></i><?= htmlspecialchars($ok)  ?></div><?php endif; ?>

<form method="POST" action="<?= url('/admin/pendaftar/' . $p['id'] . '/edit') ?>">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCsrf() ?>">

    <div class="row g-4">
        <!-- Kiri: Data Diri -->
        <div class="col-lg-8">
            <div class="card border-0 rounded-3 mb-4" style="box-shadow:0 2px 12px rgba(0,0,0,.06)">
                <div class="card-body p-4">
                    <h6 class="fw-700 mb-4" style="color:var(--primary)"><i class="bi bi-person me-2"></i>Data Diri</h6>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-600 small">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap" class="form-control"
                                   value="<?= htmlspecialchars($p['nama_lengkap'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-600 small">Tempat Lahir <span class="text-danger">*</span></label>
                            <input type="text" name="tempat_lahir" class="form-control"
                                   value="<?= htmlspecialchars($p['tempat_lahir'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label fw-600 small">Tanggal Lahir <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_lahir" class="form-control"
                                   value="<?= htmlspecialchars($p['tanggal_lahir'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600 small">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="">-- Pilih --</option>
                                <option value="L" <?= ($p['jenis_kelamin'] ?? '') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                <option value="P" <?= ($p['jenis_kelamin'] ?? '') === 'P' ? 'selected' : '' ?>>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600 small">Nomor HP <span class="text-danger">*</span></label>
                            <input type="tel" name="nomor_hp" class="form-control"
                                   value="<?= htmlspecialchars($p['nomor_hp'] ?? '') ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-600 small">Email</label>
                            <input type="email" name="email" class="form-control"
                                   value="<?= htmlspecialchars($p['email'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-600 small">Nama Ibu Kandung <span class="text-danger">*</span></label>
                            <input type="text" name="nama_ibu_kandung" class="form-control"
                                   value="<?= htmlspecialchars($p['nama_ibu_kandung'] ?? '') ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-600 small">Alamat Lengkap (KTP) <span class="text-danger">*</span></label>
                            <textarea name="alamat" class="form-control" rows="2" required><?= htmlspecialchars($p['alamat'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kanan: Program & Status -->
        <div class="col-lg-4">
            <div class="card border-0 rounded-3 mb-4" style="box-shadow:0 2px 12px rgba(0,0,0,.06)">
                <div class="card-body p-4">
                    <h6 class="fw-700 mb-4" style="color:var(--primary)"><i class="bi bi-mortarboard me-2"></i>Program Studi</h6>
                    <div class="mb-3">
                        <label class="form-label fw-600 small">Program Studi <span class="text-danger">*</span></label>
                        <select name="program_studi_id" class="form-select" required>
                            <?php foreach ($prodi as $pr): ?>
                            <option value="<?= $pr['id'] ?>" <?= ($p['program_studi_id'] ?? 0) == $pr['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($pr['nama_prodi']) ?> (<?= $pr['jenjang'] ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <h6 class="fw-700 mb-3 mt-4" style="color:var(--primary)"><i class="bi bi-flag me-2"></i>Status</h6>
                    <div class="mb-0">
                        <label class="form-label fw-600 small">Status Pendaftaran</label>
                        <select name="status" class="form-select">
                            <?php foreach ($statusOptions as $opt): ?>
                            <option value="<?= $opt ?>" <?= ($p['status'] ?? '') === $opt ? 'selected' : '' ?>>
                                <?= ucfirst($opt) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Mengubah status tidak mencatat riwayat verifikasi.</div>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn rounded-3 fw-600" style="background:var(--primary);color:#fff;padding:.65rem">
                    <i class="bi bi-check-circle me-1"></i>Simpan Perubahan
                </button>
                <a href="<?= url('/admin/pendaftar/' . $p['id']) ?>" class="btn btn-outline-secondary rounded-3">
                    Batal
                </a>
            </div>
        </div>
    </div>
</form>