#!/usr/bin/env php
<?php
/**
 * ====================================================================
 * PMB Ma'had Aly Ibnu Abbas — Setup Installer
 * ====================================================================
 * Jalankan sekali saat pertama kali deploy di server.
 * Cara: php setup.php  (dari direktori root project)
 * ====================================================================
 */

define('ROOT_PATH', __DIR__);

// ── Helper output ─────────────────────────────────────────────────
function info(string $msg): void  { echo "\033[32m[INFO]\033[0m  $msg\n"; }
function warn(string $msg): void  { echo "\033[33m[WARN]\033[0m  $msg\n"; }
function error(string $msg): void { echo "\033[31m[ERROR]\033[0m $msg\n"; }
function head(string $msg): void  { echo "\n\033[34m=== $msg ===\033[0m\n"; }
function ok(string $msg): void    { echo "\033[32m[OK]\033[0m    $msg\n"; }
function ask(string $prompt, string $default = ''): string {
    echo "\033[36m[INPUT]\033[0m $prompt" . ($default ? " [$default]" : '') . ': ';
    $in = trim(fgets(STDIN) ?: '');
    return $in === '' ? $default : $in;
}
function askSecret(string $prompt): string {
    if (PHP_OS_FAMILY !== 'Windows') {
        system('stty -echo');
        echo "\033[36m[INPUT]\033[0m $prompt: ";
        $val = trim(fgets(STDIN) ?: '');
        system('stty echo');
        echo "\n";
        return $val;
    }
    return ask($prompt);
}

// ── Cek versi PHP ─────────────────────────────────────────────────
head('CEK KEBUTUHAN SISTEM');
if (version_compare(PHP_VERSION, '8.1.0', '<')) {
    error('PHP 8.1+ diperlukan. Versi saat ini: ' . PHP_VERSION);
    exit(1);
}
ok('PHP ' . PHP_VERSION);

$exts = ['pdo_mysql','mbstring','json','fileinfo','openssl'];
$missing = [];
foreach ($exts as $ext) {
    if (!extension_loaded($ext)) $missing[] = $ext;
}
if ($missing) {
    error('Ekstensi PHP tidak aktif: ' . implode(', ', $missing));
    exit(1);
}
ok('Semua ekstensi PHP tersedia: ' . implode(', ', $exts));

// ── Konfigurasi database ──────────────────────────────────────────
head('KONFIGURASI DATABASE');
$dbHost = ask('Host database', '127.0.0.1');
$dbPort = ask('Port database', '3306');
$dbName = ask('Nama database');
$dbUser = ask('Username database');
$dbPass = askSecret('Password database');

// Test koneksi
try {
    $pdo = new PDO(
        "mysql:host={$dbHost};port={$dbPort};charset=utf8mb4",
        $dbUser, $dbPass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    ok("Koneksi ke MySQL berhasil.");
} catch (PDOException $e) {
    error("Gagal konek ke database: " . $e->getMessage());
    exit(1);
}

// Cek / buat database
$pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$pdo->exec("USE `{$dbName}`");
ok("Database '{$dbName}' siap.");

// ── URL aplikasi ──────────────────────────────────────────────────
head('KONFIGURASI URL');
$baseUrl = ask('URL aplikasi (contoh: https://pmb.ibnuabbass.com)');
$baseUrl = rtrim($baseUrl, '/');

// ── Import SQL ────────────────────────────────────────────────────
head('IMPORT DATABASE');
$sqlFile = ROOT_PATH . '/database.sql';
if (!file_exists($sqlFile)) {
    error("File database.sql tidak ditemukan di: $sqlFile");
    exit(1);
}

$sql = file_get_contents($sqlFile);
// Pisah per statement
$statements = array_filter(array_map('trim', preg_split('/;\s*\n/', $sql)));

$imported = 0;
foreach ($statements as $stmt) {
    if (empty($stmt) || strpos($stmt, '--') === 0) continue;
    try {
        $pdo->exec($stmt);
        $imported++;
    } catch (PDOException $e) {
        // Lewati error duplikat (tabel sudah ada)
        if ($e->getCode() !== '42S01') {
            warn("Statement dilewati ({$e->getMessage()}): " . substr($stmt, 0, 80));
        }
    }
}
ok("{$imported} SQL statements diimport.");

// ── Buat direktori storage ────────────────────────────────────────
head('BUAT DIREKTORI STORAGE');
$dirs = [
    ROOT_PATH . '/storage',
    ROOT_PATH . '/storage/uploads',
    ROOT_PATH . '/storage/uploads/dokumen',
    ROOT_PATH . '/storage/logs',
    ROOT_PATH . '/storage/sessions',
];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        ok("Dibuat: $dir");
    } else {
        info("Sudah ada: $dir");
    }
}

// Buat .htaccess di storage agar tidak bisa diakses web
$htaccess = "Order Deny,Allow\nDeny from all\n";
foreach ([ROOT_PATH . '/storage', ROOT_PATH . '/storage/uploads'] as $d) {
    file_put_contents($d . '/.htaccess', $htaccess);
}
ok('.htaccess protection ditambahkan ke direktori storage.');

// ── Generate config.php ───────────────────────────────────────────
head('GENERATE CONFIG.PHP');

$appKey  = bin2hex(random_bytes(32)); // 64 char hex
$envKey  = bin2hex(random_bytes(16)); // 32 char hex

$configContent = <<<PHP
<?php
/**
 * PMB Ma'had Aly Ibnu Abbas Karanganyar
 * Konfigurasi Aplikasi — Di-generate oleh setup.php
 * JANGAN commit file ini ke version control!
 */

// ── Paths ────────────────────────────────────────────────────────
define('APP_PATH',     ROOT_PATH . '/app');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('UPLOAD_PATH',  STORAGE_PATH . '/uploads');
define('LOG_PATH',     STORAGE_PATH . '/logs');

// ── URL ──────────────────────────────────────────────────────────
define('BASE_URL',    '{$baseUrl}');

// ── Application ──────────────────────────────────────────────────
define('APP_NAME',    "PMB Ma'had Aly Ibnu Abbas");
define('APP_ENV',     'production');   // development | production
define('APP_DEBUG',   false);          // Set true hanya saat dev
define('APP_KEY',     '{$appKey}');

// ── Database ─────────────────────────────────────────────────────
define('DB_HOST',     '{$dbHost}');
define('DB_PORT',     '{$dbPort}');
define('DB_NAME',     '{$dbName}');
define('DB_USER',     '{$dbUser}');
define('DB_PASS',     '{$dbPass}');
define('DB_CHARSET',  'utf8mb4');

// ── Session ──────────────────────────────────────────────────────
define('SESSION_NAME',    'pmb_session');
define('SESSION_LIFETIME', 7200);   // 2 jam (detik)
define('SESSION_REGEN',    1800);   // Regenerate tiap 30 mnt

// ── Upload ───────────────────────────────────────────────────────
define('MAX_UPLOAD_SIZE',   5 * 1024 * 1024);   // 5 MB
define('ALLOWED_UPLOAD_MIME', ['application/pdf','image/jpeg','image/jpg','image/png']);
define('ALLOWED_UPLOAD_EXT',  ['pdf','jpg','jpeg','png']);

// ── Security ─────────────────────────────────────────────────────
define('LOGIN_MAX_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_MINS', 15);
define('RATE_LIMIT_REGISTER', 3);   // Max pendaftaran per IP per jam
define('BCRYPT_COST', 12);

// ── Error Reporting ──────────────────────────────────────────────
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// ── Timezone ─────────────────────────────────────────────────────
date_default_timezone_set('Asia/Jakarta');

// ── Security Headers ─────────────────────────────────────────────
if (!defined('SKIP_HEADERS')) {
    header("Content-Security-Policy: default-src 'self' https:; script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com https://fonts.gstatic.com; img-src 'self' data: https:; font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com;");
    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('X-XSS-Protection: 1; mode=block');
    if (isset(\$_SERVER['HTTPS']) && \$_SERVER['HTTPS'] === 'on') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}
PHP;

$configPath = ROOT_PATH . '/config/config.php';
$backup = null;
if (file_exists($configPath)) {
    $backup = $configPath . '.bak.' . date('YmdHis');
    copy($configPath, $backup);
    info("Config lama dibackup ke: $backup");
}

file_put_contents($configPath, $configContent);
ok("config/config.php berhasil ditulis.");

// ── Ganti password admin default ─────────────────────────────────
head('PASSWORD ADMIN');
echo "Akun superadmin default: username = \033[33madmin\033[0m\n";
$changePass = ask('Ganti password admin sekarang? (y/n)', 'y');
if (strtolower($changePass) === 'y') {
    $newPass = askSecret('Password baru (min 8 karakter)');
    if (strlen($newPass) < 8) {
        warn('Password terlalu pendek, menggunakan password default: admin123 (WAJIB GANTI!)');
        $newPass = 'admin123';
    }
    $hash = password_hash($newPass, PASSWORD_BCRYPT, ['cost' => 12]);
    $stmt = $pdo->prepare("UPDATE `users` SET `password_hash` = ? WHERE `username` = 'admin' AND `role` = 'superadmin'");
    $stmt->execute([$hash]);
    if ($stmt->rowCount() > 0) {
        ok("Password admin berhasil diubah.");
    } else {
        warn("User admin tidak ditemukan di database. Gunakan password default: admin123");
    }
}

// ── Verifikasi akhir ─────────────────────────────────────────────
head('VERIFIKASI INSTALASI');

$checks = [
    'config/config.php ada'     => file_exists(ROOT_PATH . '/config/config.php'),
    'storage/ ada'              => is_dir(ROOT_PATH . '/storage'),
    'storage/uploads/ ada'      => is_dir(ROOT_PATH . '/storage/uploads'),
    'storage/logs/ ada'         => is_dir(ROOT_PATH . '/storage/logs'),
    'public/.htaccess ada'      => file_exists(ROOT_PATH . '/public/.htaccess'),
    'database.sql ada'          => file_exists(ROOT_PATH . '/database.sql'),
    'storage writable'          => is_writable(ROOT_PATH . '/storage'),
];

$allPassed = true;
foreach ($checks as $label => $passed) {
    if ($passed) { ok($label); }
    else { error($label); $allPassed = false; }
}

// ── Ringkasan ────────────────────────────────────────────────────
head('INSTALASI SELESAI');
if ($allPassed) {
    echo <<<EOT

\033[32m╔══════════════════════════════════════════════════════════════╗
║         PMB Ma'had Aly Ibnu Abbas — Instalasi Berhasil!      ║
╚══════════════════════════════════════════════════════════════╝\033[0m

  URL Aplikasi : \033[36m{$baseUrl}\033[0m
  URL Admin    : \033[36m{$baseUrl}/admin\033[0m
  Login Admin  : username=admin | password=(yang baru diset)

\033[33m  PENTING: Hapus file setup.php setelah instalasi!\033[0m
  rm setup.php

  Jika ada masalah, cek log di: storage/logs/

EOT;
    // Sarankan hapus setup.php
    $delSetup = ask('Hapus setup.php sekarang? (y/n)', 'y');
    if (strtolower($delSetup) === 'y') {
        unlink(__FILE__);
        ok('setup.php dihapus.');
    } else {
        warn('Ingat untuk menghapus setup.php sebelum go-live!');
    }
} else {
    error('Beberapa pemeriksaan gagal. Periksa log di atas dan perbaiki sebelum menggunakan aplikasi.');
    exit(1);
}
