<?php
/**
 * PMB Ma'had Aly Ibnu Abbas Karanganyar
 * Core Configuration
 *
 * ⚠️  UNTUK LOCAL DEVELOPMENT:
 *      APP_ENV  = 'development'
 *      APP_URL  = 'http://localhost/nama-folder-project'  ← sesuaikan
 *
 * ⚠️  UNTUK PRODUCTION (upload ke server):
 *      APP_ENV  = 'production'
 *      APP_URL  = 'https://pmb.ibnuabbass.com'
 */

// ── Environment ────────────────────────────────────────────────
// Ubah 'development' → 'production' saat upload ke server
define('APP_ENV',     getenv('APP_ENV') ?: 'development'); // ← DIUBAH: production → development
define('APP_VERSION', '1.0.0');
define('APP_NAME',    "PMB Ma'had Aly Ibnu Abbas");

// ── BASE_URL: deteksi otomatis local vs production ──────────────
// Jika ada env variable APP_URL (di server production), pakai itu.
// Jika tidak ada (local), deteksi otomatis dari $_SERVER.
if (getenv('APP_URL')) {
    define('BASE_URL', rtrim(getenv('APP_URL'), '/'));
} else {
    // Auto-detect untuk local XAMPP/WAMP/Laragon
    $scheme   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
    // Ambil subfolder sebelum /public/ dari SCRIPT_NAME
    // Misal: /PSB_MA_IBNU_ABBAS/public/index.php → /PSB_MA_IBNU_ABBAS
    $base = '';
    if (!empty($_SERVER['SCRIPT_NAME'])) {
        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
        // Cari posisi /public/ lalu ambil semua yang sebelumnya
        $publicPos = strrpos($scriptName, '/public/');
        if ($publicPos !== false) {
            $base = substr($scriptName, 0, $publicPos);
        } elseif (substr_count($scriptName, '/') > 1) {
            // Fallback: ambil folder pertama saja
            $parts = explode('/', trim($scriptName, '/'));
            $base = '/' . $parts[0];
        }
    }
    define('BASE_URL', $scheme . '://' . $host . $base);
}

// ── Paths ───────────────────────────────────────────────────────
// ROOT_PATH mungkin sudah didefinisikan di index.php sebelum require config
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}
define('APP_PATH',     ROOT_PATH . '/app');
define('CONFIG_PATH',  ROOT_PATH . '/config');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('UPLOAD_PATH',  STORAGE_PATH . '/uploads');
define('LOG_PATH',     STORAGE_PATH . '/logs');
define('PUBLIC_PATH',  ROOT_PATH . '/public');

// ── Database ────────────────────────────────────────────────────
define('DB_HOST',    getenv('DB_HOST')    ?: 'localhost');
define('DB_PORT',    getenv('DB_PORT')    ?: '3306');
define('DB_NAME',    getenv('DB_NAME')    ?: 'pmb_mahadaly');
define('DB_USER',    getenv('DB_USER')    ?: 'root');
define('DB_PASS',    getenv('DB_PASS')    ?: '');       // ← kosong = default XAMPP
define('DB_CHARSET', 'utf8mb4');

// ── Session ─────────────────────────────────────────────────────
define('SESSION_NAME',     'PMBSESSID');
define('SESSION_LIFETIME', 7200); // 2 jam
// ← DIPERBAIKI: local tidak pakai HTTPS, jadi SESSION_SECURE harus false
define('SESSION_SECURE',   APP_ENV === 'production');

// ── Security ────────────────────────────────────────────────────
define('BCRYPT_COST',         12);
define('MAX_LOGIN_ATTEMPTS',  5);
define('LOCKOUT_DURATION',    900); // 15 menit dalam detik
define('CSRF_TOKEN_LENGTH',   32);
define('CSRF_EXPIRE',         3600); // 1 jam

// ── Rate Limiting ───────────────────────────────────────────────
define('RATE_LOGIN_MAX',        10);
define('RATE_LOGIN_WINDOW',     900);   // 15 menit
define('RATE_REGISTER_MAX',     5);
define('RATE_REGISTER_WINDOW',  3600);  // 1 jam
define('RATE_UPLOAD_MAX',       20);
define('RATE_UPLOAD_WINDOW',    600);   // 10 menit

// ── File Upload ─────────────────────────────────────────────────
define('UPLOAD_MAX_SIZE',       5 * 1024 * 1024); // 5 MB
define('UPLOAD_ALLOWED_MIME', [
    'image/jpeg',
    'image/jpg',
    'image/png',
    'application/pdf',
]);
define('UPLOAD_ALLOWED_EXT', ['jpg','jpeg','png','pdf']);

// ── Nomor Pendaftaran ───────────────────────────────────────────
define('NOMOR_PREFIX', 'PMB');
define('NOMOR_DIGITS', 6);

// ── Timezone ────────────────────────────────────────────────────
date_default_timezone_set('Asia/Jakarta');
setlocale(LC_TIME, 'id_ID.UTF-8', 'id_ID', 'id');

// ── Error Reporting ─────────────────────────────────────────────
// development = tampilkan semua error (memudahkan debugging)
// production  = sembunyikan error dari user
if (APP_ENV === 'development') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

// ── Security Headers ────────────────────────────────────────────
if (!headers_sent()) {
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; img-src 'self' data: blob:; connect-src 'self'; frame-ancestors 'none';");
    header("X-Frame-Options: DENY");
    header("X-Content-Type-Options: nosniff");
    header("Referrer-Policy: strict-origin-when-cross-origin");
    header("Permissions-Policy: camera=(), microphone=(), geolocation=()");
    // HSTS hanya untuk production (HTTPS), tidak untuk local
    if (APP_ENV === 'production') {
        header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
    }
}