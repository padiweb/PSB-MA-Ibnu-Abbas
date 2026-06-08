<?php
/**
 * FILE DIAGNOSTIK - taruh di C:/xampp/htdocs/PSB_MA_IBNU_ABBAS/public/test.php
 * Akses: http://localhost/PSB_MA_IBNU_ABBAS/public/test.php
 * HAPUS file ini setelah selesai debugging!
 */

define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/config/config.php';

echo "<h2>=== DIAGNOSTIK PSB MA IBNU ABBAS ===</h2>";
echo "<pre>";

// 1. Cek konstanta
echo "1. BASE_URL     = " . BASE_URL . "\n";
echo "   APP_ENV      = " . APP_ENV . "\n";
echo "   ROOT_PATH    = " . ROOT_PATH . "\n";
echo "   APP_PATH     = " . APP_PATH . "\n";

// 2. Cek file assets
echo "\n2. CEK FILE ASSETS:\n";
$assets = [
    PUBLIC_PATH . '/assets/css/app.css',
    PUBLIC_PATH . '/assets/js/app.js',
];
foreach ($assets as $f) {
    echo "   " . $f . " => " . (file_exists($f) ? "ADA (".filesize($f)." bytes)" : "TIDAK ADA!") . "\n";
}

// 3. Cek URL assets
echo "\n3. URL ASSETS (cek di browser):\n";
echo "   CSS: " . BASE_URL . "/assets/css/app.css\n";
echo "   JS : " . BASE_URL . "/assets/js/app.js\n";

// 4. Cek koneksi database
echo "\n4. CEK DATABASE:\n";
try {
    require_once ROOT_PATH . '/core/Database.php';
    $db = Database::getInstance();
    echo "   Koneksi DB: BERHASIL\n";

    $stmt = $db->query("SELECT COUNT(*) as total FROM program_studi");
    $row = $stmt->fetch();
    echo "   Data prodi: " . $row['total'] . " records\n";

    $stmt = $db->query("SELECT COUNT(*) as total FROM tahun_akademik WHERE aktif=1");
    $row = $stmt->fetch();
    echo "   Tahun aktif: " . $row['total'] . " records\n";

    $stmt = $db->query("SELECT COUNT(*) as total FROM cms_settings");
    $row = $stmt->fetch();
    echo "   CMS settings: " . $row['total'] . " records\n";

} catch (Exception $e) {
    echo "   ERROR: " . $e->getMessage() . "\n";
}

// 5. Cek mod_rewrite
echo "\n5. CEK SERVER:\n";
echo "   PHP Version  : " . PHP_VERSION . "\n";
echo "   SCRIPT_NAME  : " . ($_SERVER['SCRIPT_NAME'] ?? '-') . "\n";
echo "   REQUEST_URI  : " . ($_SERVER['REQUEST_URI'] ?? '-') . "\n";
echo "   HTTP_HOST    : " . ($_SERVER['HTTP_HOST'] ?? '-') . "\n";
echo "   mod_rewrite  : " . (in_array('mod_rewrite', apache_get_modules()) ? "AKTIF" : "TIDAK AKTIF!") . "\n";

echo "</pre>";
echo "<hr>";
echo "<h3>Test load CSS langsung:</h3>";
echo '<link rel="stylesheet" href="' . BASE_URL . '/assets/css/app.css">';
echo '<p class="text-gold" style="color:var(--gold-main,orange)">Jika teks ini berwarna emas/oranye, CSS berhasil dimuat.</p>';
echo '<p style="color:blue">Jika teks ini biru (bawaan browser), CSS GAGAL dimuat.</p>';