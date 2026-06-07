<?php
/**
 * PMB Ma'had Aly Ibnu Abbas Karanganyar
 * Bootstrap / Front Controller
 */

define('ROOT_PATH', dirname(__DIR__));

// Load config
require_once ROOT_PATH . '/config/config.php';

// Load core files
require_once ROOT_PATH . '/core/Router.php';
require_once ROOT_PATH . '/core/Database.php';
require_once ROOT_PATH . '/core/Model.php';
require_once ROOT_PATH . '/core/Helpers.php';
require_once ROOT_PATH . '/core/Security.php';

// Load Models (diperlukan oleh semua controller)
require_once ROOT_PATH . '/app/models/Models.php';

// Start session
Session::start();

// Routing
$router = new Router();

// ── Public / Landing ────────────────────────────────────────────
$router->get('/',                          'HomeController',          'index');
$router->get('/daftar',                    'PendaftaranController',   'form');
$router->post('/daftar',                   'PendaftaranController',   'store');
$router->post('/daftar/step',              'PendaftaranController',   'step');
$router->post('/daftar/upload',            'PendaftaranController',   'upload');
$router->get('/daftar/sukses/{nomor}',     'PendaftaranController',   'success');

// ── Auth ────────────────────────────────────────────────────────
$router->get('/login',                     'AuthController',          'loginForm');
$router->post('/login',                    'AuthController',          'login');
$router->get('/logout',                    'AuthController',          'logout');

// ── Pendaftar Dashboard ─────────────────────────────────────────
$router->get('/pendaftar',                 'PendaftarController',     'dashboard');
$router->get('/pendaftar/berkas',          'PendaftarController',     'berkas');
$router->post('/pendaftar/upload',         'PendaftarController',     'uploadDokumen');
$router->get('/pendaftar/cetak/{id}',      'PendaftarController',     'cetak');

// ── Admin ───────────────────────────────────────────────────────
$router->get('/admin',                     'AdminController',         'dashboard');
$router->get('/admin/pendaftar',           'AdminController',         'pendaftar');
$router->get('/admin/pendaftar/{id}',      'AdminController',         'detail');
$router->post('/admin/verifikasi/{id}',    'AdminController',         'verifikasi');
$router->get('/admin/export',              'AdminController',         'export');

// Admin: Tahun Akademik
$router->get('/admin/tahun-akademik',      'TahunAkademikController', 'index');
$router->post('/admin/tahun-akademik',     'TahunAkademikController', 'store');
$router->post('/admin/tahun-akademik/aktif', 'TahunAkademikController', 'setAktif');
$router->post('/admin/tahun-akademik/tutup', 'TahunAkademikController', 'tutup');

// Admin: Program Studi
$router->get('/admin/prodi',               'ProdiController',         'index');
$router->post('/admin/prodi',              'ProdiController',         'store');
$router->post('/admin/prodi/{id}',         'ProdiController',         'update');
$router->post('/admin/prodi/{id}/hapus',   'ProdiController',         'delete');

// Admin: Biaya
$router->get('/admin/biaya',               'BiayaController',         'index');
$router->post('/admin/biaya',              'BiayaController',         'store');
$router->post('/admin/biaya/{id}/hapus',   'BiayaController',         'delete');

// Admin: Persyaratan
$router->get('/admin/persyaratan',         'PersyaratanController',   'index');
$router->post('/admin/persyaratan',        'PersyaratanController',   'store');
$router->post('/admin/persyaratan/{id}',   'PersyaratanController',   'update');
$router->post('/admin/persyaratan/{id}/hapus', 'PersyaratanController','delete');

// Admin: CMS Settings
$router->get('/admin/pengaturan',          'CmsController',           'index');
$router->post('/admin/pengaturan',         'CmsController',           'save');
$router->post('/admin/pengaturan/logo',    'CmsController',           'uploadLogo');

// Admin: Users
$router->get('/admin/users',               'UserController',          'index');
$router->post('/admin/users',              'UserController',          'store');
$router->post('/admin/users/{id}/hapus',   'UserController',          'delete');

// Admin: Statistik (AJAX)
$router->get('/admin/api/statistik',       'AdminController',         'apiStatistik');
$router->get('/admin/api/pendaftar',       'AdminController',         'apiPendaftar');

// ── Error ────────────────────────────────────────────────────────
$router->get('/error/403',                 'ErrorController',         'forbidden');
$router->get('/error/404',                 'ErrorController',         'notFound');

// Dispatch
$router->dispatch();
