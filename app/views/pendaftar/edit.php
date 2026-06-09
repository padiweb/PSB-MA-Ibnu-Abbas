<?php // views/pendaftar/edit.php
$p = $pendaftar ?? [];
?>

<!-- TOPBAR -->
<nav class="navbar" style="background:#fff;border-bottom:1px solid #e2e8f0;padding:0;">
    <div class="container">
        <a href="<?= url('/') ?>" class="navbar-brand d-flex align-items-center gap-2">
            <div style="width:32px;height:32px;background:var(--primary);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:.85rem;">M</div>
            <span style="font-weight:700;font-size:.9rem;color:var(--primary);">Ma'had Aly PMB</span>
        </a>
        <div class="d-flex gap-2">
            <a href="<?= url('/pendaftar') ?>" class="btn btn-sm btn-outline-secondary" style="font-size:.78rem;">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
            <a href="<?= url('/logout') ?>" class="btn btn-sm btn-outline-danger" style="font-size:.78rem;">
                <i class="bi bi-power me-1"></i>Keluar
            </a>
        </div>
    </div>
</nav>

<div class="container py-4" style="max-width:720px">
    <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-body p-0">
            <!-- Header -->
            <div class="p-4 rounded-top-4" style="background:linear-gradient(135deg,var(--primary),#2563eb)">
                <h5 class="text-white fw-700 mb-1"><i class="bi bi-pencil-square me-2"></i>Edit Data Pendaftaran</h5>
                <p class="text-white-50 mb-0 small">Nomor: <?= htmlspecialchars($p['nomor_pendaftaran'] ?? '') ?></p>
            </div>

            <?php $err = Session::getFlash('error'); ?>
            <?php if ($err): ?>
            <div class="alert alert-danger rounded-0 py-2 px-4 mb-0 border-0" style="font-size:.85rem;">
                <i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($err) ?>
            </div>
            <?php endif; ?>

            <div class="p-4">
                <div class="alert alert-warning rounded-3 py-2 px-3 mb-4" style="font-size:.82rem;">
                    <i class="bi bi-info-circle me-1"></i>
                    Perubahan data hanya bisa dilakukan selama status <strong>Draft, Menunggu, atau Revisi</strong>.
                </div>

                <form method="POST" action="<?= url('/pendaftar/edit') ?>">
                    <input type="hidden" name="csrf_token" value="<?= Security::generateCsrf() ?>">

                    <h6 class="fw-700 mb-3" style="color:var(--primary);font-size:.85rem;text-transform:uppercase;letter-spacing:.04em;">
                        <i class="bi bi-person me-1"></i>Data Diri
                    </h6>
                    <div class="row g-3 mb-4">
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
                            <label class="form-label fw-600 small">Nomor HP / WhatsApp <span class="text-danger">*</span></label>
                            <input type="tel" name="nomor_hp" class="form-control"
                                   value="<?= htmlspecialchars($p['nomor_hp'] ?? '') ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-600 small">Email</label>
                            <input type="email" name="email" class="form-control"
                                   value="<?= htmlspecialchars($p['email'] ?? '') ?>"
                                   placeholder="email@gmail.com">
                            <div class="form-text">Email untuk login. Kosongkan jika tidak ingin mengubah.</div>
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

                    <div class="d-flex gap-3 justify-content-end">
                        <a href="<?= url('/pendaftar') ?>" class="btn btn-outline-secondary rounded-pill px-4">
                            Batal
                        </a>
                        <button type="submit" class="btn rounded-pill px-4 fw-600" style="background:var(--primary);color:#fff;">
                            <i class="bi bi-check-circle me-1"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>