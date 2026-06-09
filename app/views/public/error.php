<?php
// views/public/error.php
$code = $error_code ?? 404;
$message = $error_message ?? 'Halaman tidak ditemukan';
?>
<section style="background:linear-gradient(135deg,var(--primary) 0%,#1e5ca8 100%);min-height:100vh;display:flex;align-items:center;">
    <div class="container text-center py-5">
        <div style="font-size:6rem;font-weight:900;color:rgba(255,255,255,.15);line-height:1;font-family:'Playfair Display',serif;">
            <?= $code ?>
        </div>
        <div style="margin-top:-40px;">
            <?php if ($code === 404): ?>
            <div style="font-size:3.5rem;color:rgba(255,255,255,.7);"><i class="bi bi-compass"></i></div>
            <h2 class="text-white fw-700 mt-3" style="font-family:'Playfair Display',serif;">Halaman Tidak Ditemukan</h2>
            <p class="text-white mb-4" style="opacity:.7;max-width:400px;margin:0 auto;">
                Halaman yang Anda cari tidak ada atau sudah dipindahkan.
            </p>
            <?php elseif ($code === 403): ?>
            <div style="font-size:3.5rem;color:rgba(255,255,255,.7);"><i class="bi bi-shield-x"></i></div>
            <h2 class="text-white fw-700 mt-3" style="font-family:'Playfair Display',serif;">Akses Ditolak</h2>
            <p class="text-white mb-4" style="opacity:.7;max-width:400px;margin:0 auto;">
                Anda tidak memiliki izin untuk mengakses halaman ini.
            </p>
            <?php else: ?>
            <div style="font-size:3.5rem;color:rgba(255,255,255,.7);"><i class="bi bi-exclamation-triangle"></i></div>
            <h2 class="text-white fw-700 mt-3" style="font-family:'Playfair Display',serif;">Terjadi Kesalahan</h2>
            <p class="text-white mb-4" style="opacity:.7;max-width:400px;margin:0 auto;">
                <?= htmlspecialchars($message) ?>
            </p>
            <?php endif; ?>

            <div class="d-flex gap-3 justify-content-center">
                <a href="<?= url('/') ?>" class="btn px-4 py-2 fw-600" style="background:#fff;color:var(--primary);border-radius:10px;">
                    <i class="bi bi-house me-2"></i>Ke Beranda
                </a>
                <button onclick="history.back()" class="btn px-4 py-2 fw-600" style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:10px;">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </button>
            </div>
        </div>
    </div>
</section>
