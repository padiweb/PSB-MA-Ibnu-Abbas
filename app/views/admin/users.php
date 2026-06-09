<?php // views/admin/users.php
$list = $list ?? [];
$roles = ['superadmin'=>'Super Admin','admin'=>'Admin PMB','verifikator'=>'Verifikator'];
$roleColors = ['superadmin'=>'bg-danger','admin'=>'bg-primary','verifikator'=>'bg-info text-dark'];
?>
<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1><i class="bi bi-person-gear me-2"></i>Manajemen User</h1>
        <p class="text-muted mb-0" style="font-size:.82rem;">Kelola akun admin, verifikator, dan superadmin</p>
    </div>
    <button class="btn btn-sm" style="background:var(--primary);color:#fff;" data-bs-toggle="modal" data-bs-target="#modalUser">
        <i class="bi bi-person-plus me-1"></i> Tambah User
    </button>
</div>

<div class="admin-table">
    <table class="table">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Dibuat</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($list)): ?>
            <tr><td colspan="7" class="text-center py-4 text-muted">Tidak ada data user</td></tr>
            <?php else: ?>
            <?php foreach ($list as $u): ?>
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:32px;height:32px;border-radius:50%;background:var(--primary);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.78rem;font-weight:700;flex-shrink:0;">
                            <?= strtoupper(substr($u['nama'] ?? 'A', 0, 1)) ?>
                        </div>
                        <div>
                            <div class="fw-600" style="font-size:.85rem;"><?= htmlspecialchars($u['nama'] ?? '') ?></div>
                            <?php if ($u['id'] === Session::get('user_id')): ?>
                            <span style="font-size:.7rem;color:var(--accent);">● Akun Anda</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </td>
                <td><code style="font-size:.82rem;"><?= htmlspecialchars($u['username']) ?></code></td>
                <td style="font-size:.83rem;"><?= htmlspecialchars($u['email'] ?? '-') ?></td>
                <td>
                    <span class="badge <?= $roleColors[$u['role']] ?? 'bg-secondary' ?>" style="font-size:.7rem;">
                        <?= $roles[$u['role']] ?? ucfirst($u['role']) ?>
                    </span>
                </td>
                <td>
                    <span class="badge <?= $u['is_aktif'] ? 'bg-success' : 'bg-secondary' ?>" style="font-size:.7rem;">
                        <?= $u['is_aktif'] ? 'Aktif' : 'Nonaktif' ?>
                    </span>
                </td>
                <td style="font-size:.78rem;color:#64748b;"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                <td class="text-center">
                    <div class="d-flex gap-1 justify-content-center">
                        <button class="btn btn-sm btn-outline-warning" style="padding:3px 8px;font-size:.72rem;"
                                onclick="resetPw(<?= $u['id'] ?>, '<?= htmlspecialchars($u['nama']) ?>')"
                                title="Reset Password">
                            <i class="bi bi-key"></i>
                        </button>
                        <?php if ($u['id'] !== Session::get('user_id')): ?>
                        <form method="POST" action="<?= url('/admin/users/<?= $u['id'] ?>/toggle') ?>" class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?= Security::generateCsrf() ?>">
                            <button class="btn btn-sm btn-outline-<?= $u['is_aktif'] ? 'secondary' : 'success' ?>" style="padding:3px 8px;" title="<?= $u['is_aktif'] ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                <i class="bi bi-<?= $u['is_aktif'] ? 'pause-circle' : 'play-circle' ?>"></i>
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

<!-- MODAL TAMBAH USER -->
<div class="modal fade" id="modalUser" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 rounded-3">
            <div class="modal-header border-0" style="background:var(--primary);">
                <h5 class="modal-title text-white fw-700">Tambah User Admin</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= url('/admin/users') ?>">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCsrf() ?>">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-600" style="font-size:.82rem;">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600" style="font-size:.82rem;">Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control" required minlength="4"
                               pattern="[a-zA-Z0-9_]+" title="Huruf, angka, underscore">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600" style="font-size:.82rem;">Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600" style="font-size:.82rem;">Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required>
                            <option value="admin">Admin PMB</option>
                            <option value="verifikator">Verifikator</option>
                            <option value="superadmin">Super Admin</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-600" style="font-size:.82rem;">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required minlength="8"
                               placeholder="Min. 8 karakter">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm" style="background:var(--primary);color:#fff;">
                        <i class="bi bi-person-plus me-1"></i> Tambah User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL RESET PASSWORD -->
<div class="modal fade" id="modalReset" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 rounded-3">
            <div class="modal-header border-0" style="background:#b45309;">
                <h5 class="modal-title text-white fw-700">Reset Password</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="formReset" action="">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCsrf() ?>">
                <div class="modal-body p-4">
                    <p class="text-muted" style="font-size:.85rem;">Reset password untuk: <strong id="resetNama"></strong></p>
                    <label class="form-label fw-600" style="font-size:.82rem;">Password Baru <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" required minlength="8" placeholder="Min. 8 karakter">
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm" style="background:#b45309;color:#fff;">
                        <i class="bi bi-key me-1"></i> Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const BASE_URL = '<?= BASE_URL ?>';
function resetPw(id, nama) {
    document.getElementById('resetNama').textContent = nama;
    document.getElementById('formReset').action = BASE_URL + '/index.php?page=admin/users/' + id + '/reset-password';
    new bootstrap.Modal(document.getElementById('modalReset')).show();
}
</script>
