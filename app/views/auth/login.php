<?php // app/views/auth/login.php ?>
<section class="py-5" style="min-height: calc(100vh - 140px); display:flex; align-items:center; background: linear-gradient(135deg, var(--blue-dark) 0%, var(--blue-main) 100%);">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-sm-10 col-md-7 col-lg-5 col-xl-4">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
          <div class="py-4 px-4 text-center" style="background: linear-gradient(135deg, var(--blue-dark), var(--blue-main));">
            <img src="<?= url('/assets/images/logo.png') ?>" alt="Logo" height="52" class="mb-2" onerror="this.style.display='none'">
            <h5 class="text-white mb-0 fw-bold" style="font-family:var(--font-heading)"><?= Security::clean($settings['site_name'] ?? APP_NAME) ?></h5>
            <p class="text-white-50 small mb-0">Portal Mahasiswa &amp; Admin</p>
          </div>
          <div class="card-body p-4">
            <h5 class="fw-bold mb-1 text-center" style="color:var(--blue-main)">Masuk ke Akun</h5>
            <p class="text-muted text-center small mb-4">Gunakan Nomor Pendaftaran atau Email</p>

            <form id="loginForm" method="POST" action="<?= url('/login') ?>" novalidate>
              <input type="hidden" name="_token" value="<?= Security::clean($csrf) ?>">

              <div class="mb-3">
                <label class="form-label">Nomor Pendaftaran / Email</label>
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                  <input type="text" name="credential" class="form-control border-start-0 ps-0" placeholder="PMB-2026-000001 atau email" required>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                  <input type="password" name="password" id="passwordInput" class="form-control border-start-0 ps-0" placeholder="Password" required>
                  <button type="button" class="input-group-text bg-light border-start-0" onclick="togglePassword()">
                    <i class="bi bi-eye" id="toggleIcon"></i>
                  </button>
                </div>
              </div>

              <button type="submit" class="btn-primary-blue w-100 justify-content-center mb-3" id="btnLogin">
                <i class="bi bi-box-arrow-in-right me-2"></i> Masuk
              </button>

              <hr>
              <p class="text-center small mb-0 text-muted">
                Belum mendaftar?
                <a href="<?= url('/daftar') ?>" class="fw-bold" style="color:var(--blue-main)">Daftar Sekarang</a>
              </p>
            </form>
          </div>
        </div>
        <p class="text-center text-white-50 small mt-3">
          <a href="<?= BASE_URL ?>" class="text-white-50 text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Kembali ke Beranda</a>
        </p>
      </div>
    </div>
  </div>
</section>

<?php $extra_scripts = '<script>
function togglePassword() {
  const inp  = document.getElementById("passwordInput");
  const icon = document.getElementById("toggleIcon");
  if (inp.type === "password") { inp.type = "text"; icon.className = "bi bi-eye-slash"; }
  else { inp.type = "password"; icon.className = "bi bi-eye"; }
}
document.getElementById("loginForm").addEventListener("submit", function(e) {
  const btn = document.getElementById("btnLogin");
  btn.innerHTML = \'<span class="loading-spinner me-2"></span> Memproses...\';
  btn.disabled = true;
});
</script>'; ?>
