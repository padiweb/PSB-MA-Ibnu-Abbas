<?php
/**
 * PMB Ma'had Aly Ibnu Abbas Karanganyar
 * Front Controller — Query String Routing
 * URL: index.php?page=login  atau  index.php?page=admin/pendaftar/5
 */

define('ROOT_PATH', dirname(__DIR__));

require_once ROOT_PATH . '/config/config.php';
require_once ROOT_PATH . '/core/Database.php';
require_once ROOT_PATH . '/core/Helpers.php';
require_once ROOT_PATH . '/core/Security.php';
require_once ROOT_PATH . '/app/models/Models.php';
require_once ROOT_PATH . '/core/Router.php';

Session::start();

$router = new Router();

// ── Public ───────────────────────────────────────────────────────
$router->get('/',                                'HomeController',          'index');
$router->get('/daftar',                          'PendaftaranController',   'form');
$router->post('/daftar',                         'PendaftaranController',   'store');
$router->post('/daftar/submit',                  'PendaftaranController',   'store');
$router->post('/daftar/upload',                  'PendaftaranController',   'upload');
$router->get('/daftar/sukses/{nomor}',           'PendaftaranController',   'success');

// ── Auth ─────────────────────────────────────────────────────────
$router->get('/login',                           'AuthController',          'loginForm');
$router->post('/login',                          'AuthController',          'login');
$router->get('/logout',                          'AuthController',          'logout');

// ── Pendaftar ─────────────────────────────────────────────────────
$router->get('/pendaftar',                       'PendaftarController',     'dashboard');
$router->get('/pendaftar/berkas',                'PendaftarController',     'berkas');
$router->post('/pendaftar/upload',               'PendaftarController',     'uploadDokumen');
$router->get('/pendaftar/cetak/{id}',            'PendaftarController',     'cetak');

// ── Admin ─────────────────────────────────────────────────────────
$router->get('/admin',                           'AdminController',         'dashboard');
$router->get('/admin/pendaftar',                 'AdminController',         'pendaftar');
$router->get('/admin/pendaftar/{id}',            'AdminController',         'detail');
$router->post('/admin/verifikasi/{id}',          'AdminController',         'verifikasi');
$router->get('/admin/export',                    'AdminController',         'export');
$router->get('/admin/pendaftar/{id}/cetak',      'AdminController',         'cetakPendaftar');
$router->get('/admin/dokumen/{id}/download',     'AdminController',         'downloadDokumen');
$router->get('/admin/api/statistik',             'AdminController',         'apiStatistik');
$router->get('/admin/api/pendaftar',             'AdminController',         'apiPendaftar');

// Admin: Tahun Akademik
$router->get('/admin/tahun-akademik',                    'TahunAkademikController', 'index');
$router->post('/admin/tahun-akademik',                   'TahunAkademikController', 'store');
$router->post('/admin/tahun-akademik/aktif',             'TahunAkademikController', 'setAktif');
$router->post('/admin/tahun-akademik/tutup',             'TahunAkademikController', 'tutup');
$router->post('/admin/tahun-akademik/{id}/aktifkan',     'TahunAkademikController', 'aktifkan');
$router->post('/admin/tahun-akademik/{id}/hapus',        'TahunAkademikController', 'hapus');
$router->post('/admin/tahun-akademik/{id}/update',       'TahunAkademikController', 'update');

// Admin: Program Studi
$router->get('/admin/prodi',                     'ProdiController',         'index');
$router->post('/admin/prodi',                    'ProdiController',         'store');
$router->post('/admin/prodi/{id}/update',        'ProdiController',         'update');
$router->post('/admin/prodi/{id}/hapus',         'ProdiController',         'delete');
$router->post('/admin/prodi/{id}/toggle',        'ProdiController',         'toggle');

// Admin: Biaya
$router->get('/admin/biaya',                     'BiayaController',         'index');
$router->post('/admin/biaya',                    'BiayaController',         'store');
$router->post('/admin/biaya/{id}/hapus',         'BiayaController',         'delete');
$router->get('/admin/biaya/api',                 'BiayaController',         'apiGet');

// Admin: Persyaratan
$router->get('/admin/persyaratan',               'PersyaratanController',   'index');
$router->post('/admin/persyaratan',              'PersyaratanController',   'store');
$router->post('/admin/persyaratan/{id}',         'PersyaratanController',   'update');
$router->post('/admin/persyaratan/{id}/hapus',   'PersyaratanController',   'delete');

// Admin: CMS
$router->get('/admin/pengaturan',                'CmsController',           'index');
$router->post('/admin/pengaturan',               'CmsController',           'save');
$router->post('/admin/pengaturan/logo',          'CmsController',           'uploadLogo');

// Admin: Users
$router->get('/admin/users',                         'UserController',      'index');
$router->post('/admin/users',                        'UserController',      'store');
$router->post('/admin/users/{id}/hapus',             'UserController',      'delete');
$router->post('/admin/users/{id}/toggle',            'UserController',      'toggle');
$router->post('/admin/users/{id}/reset-password',    'UserController',      'resetPassword');

// Error
$router->get('/error/403',                       'ErrorController',         'forbidden');
$router->get('/error/404',                       'ErrorController',         'notFound');

$router->dispatch();
