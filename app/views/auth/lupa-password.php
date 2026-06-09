<?php // app/views/auth/lupa-password.php ?>

<section class="py-5" style="min-height:calc(100vh - 140px);display:flex;align-items:center;background:linear-gradient(135deg,var(--blue-dark) 0%,var(--blue-main) 100%);">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-sm-10 col-md-7 col-lg-5 col-xl-4">

        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
          <!-- Header -->
          <div class="py-4 px-4 text-center" style="background:linear-gradient(135deg,var(--blue-dark),var(--blue-main));">
            <?php $logoPath = $settings['logo_path'] ?? ''; ?>
            <?php if ($logoPath): ?>
            <img src="<?= htmlspecialchars(BASE_URL . $logoPath) ?>" alt="Logo" height="48" class="mb-2" onerror="this.style.display='none'">
            <?php endif; ?>
            <h5 class="text-white mb-0 fw-bold" style="font-family:var(--font-heading)"><?= Security::clean($settings['site_name'] ?? APP_NAME) ?></h5>
            <p class="text-white-50 small mb-0">Reset Password Pendaftar</p>
          </div>

          <div class="card-body p-4">
            <div class="text-center mb-4">
              <div style="width:56px;height:56px;border-radius:50%;background:var(--blue-pale);display:flex;align-items:center;justify-content:center;margin:0 auto .75rem;">
                <i class="bi bi-key-fill" style="font-size:1.4rem;color:var(--blue-main)"></i>
              </div>
              <h5 class="fw-bold mb-1" style="color:var(--blue-main)">Lupa Password?</h5>
              <p class="text-muted small mb-0">Masukkan nomor pendaftaran atau email, lalu buat password baru</p>
            </div>

            <!-- Flash error/success -->
            <?php $err = Session::getFlash('error'); $ok = Session::getFlash('success'); ?>
            <?php if ($err): ?>
            <div class="alert alert-danger rounded-3 py-2 px-3 mb-3" style="font-size:.85rem;">
              <i class="bi bi-exclamation-circle me-1"></i><?= Security::clean($err) ?>
            </div>
            <?php endif; ?>
            <?php if ($ok): ?>
            <div class="alert alert-success rounded-3 py-2 px-3 mb-3" style="font-size:.85rem;">
              <i class="bi bi-check-circle me-1"></i><?= Security::clean($ok) ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="<?= url('/lupa-password') ?>" id="resetForm">
              <input type="hidden" name="_token" value="<?= Security::generateCsrf() ?>">

              <!-- Nomor pendaftaran -->
              <div class="mb-3">
                <label class="form-label fw-600" style="font-size:.85rem;">Nomor Pendaftaran / Email</label>
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0"><i class="bi bi-person-badge text-muted"></i></span>
                  <input type="text" name="nomor_pendaftaran" class="form-control border-start-0 ps-0"
                         placeholder="PMB-2026-000001 atau email@gmail.com" required
                         style="text-transform:uppercase"
                         oninput="this.value=this.value.toUpperCase()">
                </div>
                <div class="form-text">Nomor pendaftaran atau email yang didaftarkan</div>
              </div>

              <!-- Password baru -->
              <div class="mb-3">
                <label class="form-label fw-600" style="font-size:.85rem;">Password Baru</label>
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                  <input type="password" name="new_password" id="pw1" class="form-control border-start-0 ps-0"
                         placeholder="Min. 8 karakter" required minlength="8">
                  <button type="button" class="input-group-text bg-light border-start-0" onclick="togglePw('pw1','ic1')">
                    <i class="bi bi-eye" id="ic1"></i>
                  </button>
                </div>
              </div>

              <!-- Konfirmasi -->
              <div class="mb-4">
                <label class="form-label fw-600" style="font-size:.85rem;">Konfirmasi Password Baru</label>
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock-fill text-muted"></i></span>
                  <input type="password" name="confirm_password" id="pw2" class="form-control border-start-0 ps-0"
                         placeholder="Ulangi password baru" required oninput="checkMatch()">
                  <button type="button" class="input-group-text bg-light border-start-0" onclick="togglePw('pw2','ic2')">
                    <i class="bi bi-eye" id="ic2"></i>
                  </button>
                </div>
                <div id="matchMsg" style="font-size:.75rem;margin-top:4px;display:none"></div>
              </div>

              <button type="submit" class="btn-primary-blue w-100 justify-content-center mb-3" id="btnReset">
                <i class="bi bi-check-circle me-2"></i> Reset Password
              </button>

              <hr>
              <p class="text-center small mb-0 text-muted">
                Ingat password?
                <a href="<?= url('/login') ?>" class="fw-bold" style="color:var(--blue-main)">Login di sini</a>
              </p>
            </form>
          </div>
        </div>

        <p class="text-center text-white-50 small mt-3">
          <a href="<?= url('/') ?>" class="text-white-50 text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Beranda
          </a>
        </p>

      </div>
    </div>
  </div>
</section>

<script>
function togglePw(id, icId) {
  const inp = document.getElementById(id);
  const ic  = document.getElementById(icId);
  if (inp.type === 'password') { inp.type = 'text'; ic.className = 'bi bi-eye-slash'; }
  else { inp.type = 'password'; ic.className = 'bi bi-eye'; }
}
function checkMatch() {
  const p1  = document.getElementById('pw1').value;
  const p2  = document.getElementById('pw2').value;
  const msg = document.getElementById('matchMsg');
  if (!p2) { msg.style.display='none'; return; }
  msg.style.display = 'block';
  if (p1 === p2) {
    msg.innerHTML = '<i class="bi bi-check-circle-fill text-success me-1"></i><span class="text-success">Password cocok</span>';
  } else {
    msg.innerHTML = '<i class="bi bi-x-circle-fill text-danger me-1"></i><span class="text-danger">Password tidak cocok</span>';
  }
}
document.getElementById('resetForm').addEventListener('submit', function(e) {
  const p1 = document.getElementById('pw1').value;
  const p2 = document.getElementById('pw2').value;
  if (p1 !== p2) {
    e.preventDefault();
    document.getElementById('pw2').classList.add('is-invalid');
    return;
  }
  const btn = document.getElementById('btnReset');
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
  btn.disabled = true;
});
</script>