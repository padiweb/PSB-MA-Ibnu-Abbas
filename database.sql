SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+07:00";
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `pmb_mahadaly`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `pmb_mahadaly`;

-- ============================================================
-- TABLE: tahun_akademik
-- ============================================================
DROP TABLE IF EXISTS `tahun_akademik`;
CREATE TABLE `tahun_akademik` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode`          VARCHAR(9)  NOT NULL COMMENT 'misal: 2026/2027',
  `nama`          VARCHAR(50) NOT NULL,
  `aktif`         TINYINT(1)  NOT NULL DEFAULT 0,
  `tanggal_buka`  DATE        NULL,
  `tanggal_tutup` DATE        NULL,
  `created_by`    INT UNSIGNED NULL,
  `created_at`    TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_kode` (`kode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: program_studi
-- ============================================================
DROP TABLE IF EXISTS `program_studi`;
CREATE TABLE `program_studi` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_prodi` VARCHAR(100) NOT NULL,
  `singkatan`  VARCHAR(20)  NOT NULL,
  `jenjang`    ENUM('S1','S2','S3','D3','D4') NOT NULL DEFAULT 'S1',
  `fakultas`   VARCHAR(100) NOT NULL,
  `gelar`      VARCHAR(20)  NOT NULL,
  `deskripsi`  TEXT         NULL,
  `is_aktif`   TINYINT(1)   NOT NULL DEFAULT 1,
  `urutan`     SMALLINT     NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: biaya
-- ============================================================
DROP TABLE IF EXISTS `biaya`;
CREATE TABLE `biaya` (
  `id`                  INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `tahun_akademik_id`   INT UNSIGNED  NOT NULL,
  `program_studi_id`    INT UNSIGNED  NOT NULL,
  `biaya_pendaftaran`   BIGINT        NOT NULL DEFAULT 0 COMMENT 'Biaya daftar ulang',
  `biaya_spp`           BIGINT        NOT NULL DEFAULT 0 COMMENT 'SPP per bulan',
  `biaya_pendidikan`    BIGINT        NOT NULL DEFAULT 0 COMMENT 'Biaya pendidikan sampai lulus (S2)',
  `keterangan`          VARCHAR(255)  NULL,
  `created_at`          TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`          TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_biaya_ta_prodi` (`tahun_akademik_id`, `program_studi_id`),
  KEY `fk_biaya_ta`    (`tahun_akademik_id`),
  KEY `fk_biaya_prodi` (`program_studi_id`),
  CONSTRAINT `fk_biaya_ta`    FOREIGN KEY (`tahun_akademik_id`) REFERENCES `tahun_akademik` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_biaya_prodi` FOREIGN KEY (`program_studi_id`)  REFERENCES `program_studi` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: promo
-- ============================================================
DROP TABLE IF EXISTS `promo`;
CREATE TABLE `promo` (
  `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tahun_akademik_id` INT UNSIGNED NOT NULL,
  `program_studi_id`  INT UNSIGNED NULL,
  `judul`             VARCHAR(100) NOT NULL,
  `keterangan`        TEXT         NULL,
  `kuota`             INT          NOT NULL DEFAULT 0 COMMENT '0 = tidak terbatas',
  `terpakai`          INT          NOT NULL DEFAULT 0,
  `berlaku_sampai`    TIMESTAMP    NULL,
  `aktif`             TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_promo_ta`    (`tahun_akademik_id`),
  KEY `fk_promo_prodi` (`program_studi_id`),
  CONSTRAINT `fk_promo_ta`    FOREIGN KEY (`tahun_akademik_id`) REFERENCES `tahun_akademik` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_promo_prodi` FOREIGN KEY (`program_studi_id`)  REFERENCES `program_studi` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: persyaratan
-- ============================================================
DROP TABLE IF EXISTS `persyaratan`;
CREATE TABLE `persyaratan` (
  `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tahun_akademik_id` INT UNSIGNED NOT NULL,
  `jenjang`           ENUM('S1','S2','semua') NOT NULL DEFAULT 'semua',
  `nama`              VARCHAR(150) NOT NULL,
  `keterangan`        TEXT         NULL,
  `wajib`             TINYINT(1)   NOT NULL DEFAULT 1,
  `urutan`            SMALLINT     NOT NULL DEFAULT 0,
  `created_at`        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_persyaratan_ta` (`tahun_akademik_id`),
  CONSTRAINT `fk_persyaratan_ta` FOREIGN KEY (`tahun_akademik_id`) REFERENCES `tahun_akademik` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: users
-- ============================================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id`             INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `email`          VARCHAR(150)  NULL COMMENT 'untuk admin; NULL untuk pendaftar yg pakai nomor pendaftaran',
  `username`       VARCHAR(50)   NULL COMMENT 'nomor_pendaftaran untuk pendaftar',
  `password_hash`  VARCHAR(255)  NOT NULL,
  `role`           ENUM('superadmin','admin','verifikator','pendaftar') NOT NULL DEFAULT 'pendaftar',
  `nama`           VARCHAR(100)  NOT NULL,
  `is_aktif`       TINYINT(1)    NOT NULL DEFAULT 1,
  `login_attempts` TINYINT       NOT NULL DEFAULT 0,
  `locked_until`   TIMESTAMP     NULL,
  `last_login`     TIMESTAMP     NULL,
  `last_ip`        VARCHAR(45)   NULL,
  `created_at`     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_email`    (`email`),
  UNIQUE KEY `uk_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: pendaftar
-- ============================================================
DROP TABLE IF EXISTS `pendaftar`;
CREATE TABLE `pendaftar` (
  `id`                    INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `user_id`               INT UNSIGNED  NOT NULL,
  `tahun_akademik_id`     INT UNSIGNED  NOT NULL,
  `program_studi_id`      INT UNSIGNED  NOT NULL,
  `nomor_pendaftaran`     VARCHAR(20)   NOT NULL,
  `nama_lengkap`          VARCHAR(150)  NOT NULL,
  `tempat_lahir`          VARCHAR(100)  NOT NULL,
  `tanggal_lahir`         DATE          NOT NULL,
  `jenis_kelamin`         ENUM('L','P') NOT NULL,
  `nomor_hp`              VARCHAR(20)   NOT NULL,
  `alamat`                TEXT          NOT NULL,
  `nama_ibu_kandung`      VARCHAR(150)  NOT NULL,
  -- Khusus S2
  `asal_universitas`      VARCHAR(200)  NULL,
  `tahun_lulus_s1`        YEAR          NULL,
  `ipk_s1`                DECIMAL(4,2)  NULL,
  -- Status
  `status`                ENUM('draft','menunggu','diterima','revisi','ditolak') NOT NULL DEFAULT 'draft',
  `catatan_verifikasi`    TEXT          NULL,
  `tanggal_submit`        TIMESTAMP     NULL,
  `tanggal_verifikasi`    TIMESTAMP     NULL,
  `diverifikasi_oleh`     INT UNSIGNED  NULL,
  `promo_id`              INT UNSIGNED  NULL,
  `created_at`            TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`            TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_nomor_pendaftaran` (`nomor_pendaftaran`),
  KEY `fk_pendaftar_user`  (`user_id`),
  KEY `fk_pendaftar_ta`    (`tahun_akademik_id`),
  KEY `fk_pendaftar_prodi` (`program_studi_id`),
  KEY `idx_status`         (`status`),
  CONSTRAINT `fk_pendaftar_user`  FOREIGN KEY (`user_id`)             REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pendaftar_ta`    FOREIGN KEY (`tahun_akademik_id`)   REFERENCES `tahun_akademik` (`id`),
  CONSTRAINT `fk_pendaftar_prodi` FOREIGN KEY (`program_studi_id`)    REFERENCES `program_studi` (`id`),
  CONSTRAINT `fk_pendaftar_promo` FOREIGN KEY (`promo_id`)            REFERENCES `promo` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_pendaftar_verif` FOREIGN KEY (`diverifikasi_oleh`)   REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: dokumen
-- ============================================================
DROP TABLE IF EXISTS `dokumen`;
CREATE TABLE `dokumen` (
  `id`            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `pendaftar_id`  INT UNSIGNED  NOT NULL,
  `jenis_dokumen` ENUM(
    'ijazah_sma','transkrip_sma',
    'ijazah_s1','transkrip_s1',
    'ktp','kk','akte_kelahiran',
    'foto','lainnya'
  ) NOT NULL,
  `nama_file_asli` VARCHAR(255) NOT NULL,
  `nama_file`      VARCHAR(100) NOT NULL COMMENT 'UUID-based filename',
  `path_file`      VARCHAR(500) NOT NULL,
  `mime_type`      VARCHAR(100) NOT NULL,
  `ukuran_file`    INT UNSIGNED NOT NULL,
  `status`         ENUM('menunggu','diterima','revisi','ditolak') NOT NULL DEFAULT 'menunggu',
  `catatan`        TEXT         NULL,
  `uploaded_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_dokumen_pendaftar` (`pendaftar_id`),
  KEY `idx_jenis`            (`jenis_dokumen`),
  CONSTRAINT `fk_dokumen_pendaftar` FOREIGN KEY (`pendaftar_id`) REFERENCES `pendaftar` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: verifikasi_log
-- ============================================================
DROP TABLE IF EXISTS `verifikasi_log`;
CREATE TABLE `verifikasi_log` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `pendaftar_id`    INT UNSIGNED NOT NULL,
  `admin_id`        INT UNSIGNED NOT NULL,
  `status_sebelum`  VARCHAR(20)  NOT NULL,
  `status_sesudah`  VARCHAR(20)  NOT NULL,
  `catatan`         TEXT         NULL,
  `created_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_vlog_pendaftar` (`pendaftar_id`),
  KEY `fk_vlog_admin`     (`admin_id`),
  CONSTRAINT `fk_vlog_pendaftar` FOREIGN KEY (`pendaftar_id`) REFERENCES `pendaftar` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_vlog_admin`     FOREIGN KEY (`admin_id`)     REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: cms_settings
-- ============================================================
DROP TABLE IF EXISTS `cms_settings`;
CREATE TABLE `cms_settings` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `key_name`   VARCHAR(100) NOT NULL,
  `value`      LONGTEXT     NULL,
  `group_name` VARCHAR(50)  NOT NULL DEFAULT 'umum',
  `label`      VARCHAR(100) NOT NULL,
  `type`       ENUM('text','textarea','image','color','url','email','number') NOT NULL DEFAULT 'text',
  `updated_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_key` (`key_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: audit_log
-- ============================================================
DROP TABLE IF EXISTS `audit_log`;
CREATE TABLE `audit_log` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     INT UNSIGNED    NULL,
  `action`      VARCHAR(50)     NOT NULL,
  `module`      VARCHAR(50)     NOT NULL,
  `record_id`   INT UNSIGNED    NULL,
  `data_before` JSON            NULL,
  `data_after`  JSON            NULL,
  `ip_address`  VARCHAR(45)     NOT NULL,
  `user_agent`  VARCHAR(500)    NULL,
  `created_at`  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_audit_user`  (`user_id`),
  KEY `idx_action`     (`action`),
  KEY `idx_module`     (`module`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: rate_limit
-- ============================================================
DROP TABLE IF EXISTS `rate_limit`;
CREATE TABLE `rate_limit` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `key_val`    VARCHAR(150)    NOT NULL,
  `hits`       INT             NOT NULL DEFAULT 1,
  `window_end` TIMESTAMP       NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_key` (`key_val`),
  KEY `idx_window` (`window_end`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: csrf_tokens (server-side double-submit)
-- ============================================================
DROP TABLE IF EXISTS `csrf_tokens`;
CREATE TABLE `csrf_tokens` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `session_id` VARCHAR(128)    NOT NULL,
  `token`      VARCHAR(64)     NOT NULL,
  `expires_at` TIMESTAMP       NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_session` (`session_id`),
  KEY `idx_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SEEDER: program_studi
-- ============================================================
INSERT INTO `program_studi` (`nama_prodi`,`singkatan`,`jenjang`,`fakultas`,`gelar`,`urutan`) VALUES
('Pendidikan Agama Islam',              'PAI',  'S1','Fakultas Tarbiyah','S.Pd.',1),
('Pendidikan Guru Madrasah Ibtidaiyyah','PGMI', 'S1','Fakultas Tarbiyah','S.Pd.',2),
('Pendidikan Islam Anak Usia Dini',     'PIAUD','S1','Fakultas Tarbiyah','S.Pd.',3),
('Manajemen Pendidikan Islam',          'MPI',  'S1','Fakultas Tarbiyah','S.Pd.',4),
('Manajemen Bisnis',                    'MB',   'S1','Fakultas Ekonomi', 'S.E.',5),
('Hukum Ekonomi Syariah',               'HES',  'S1','Fakultas Hukum',   'S.H.',6),
('Pendidikan Agama Islam',              'PAI',  'S2','Pascasarjana',     'M.Pd.',7);

-- ============================================================
-- SEEDER: tahun_akademik
-- ============================================================
INSERT INTO `tahun_akademik` (`kode`,`nama`,`aktif`,`tanggal_buka`,`tanggal_tutup`) VALUES
('2026/2027','Tahun Akademik 2026/2027',1,'2026-01-01','2026-08-31');

-- ============================================================
-- SEEDER: biaya (untuk TA 2026/2027)
-- ============================================================
-- S1 (berlaku untuk semua 6 prodi S1, prodi_id 1-6)
INSERT INTO `biaya` (`tahun_akademik_id`,`program_studi_id`,`biaya_pendaftaran`,`biaya_spp`,`keterangan`) VALUES
(1,1,300000,250000,'S1 PAI - Fakultas Tarbiyah'),
(1,2,300000,250000,'S1 PGMI - Fakultas Tarbiyah'),
(1,3,300000,250000,'S1 PIAUD - Fakultas Tarbiyah'),
(1,4,300000,250000,'S1 MPI - Fakultas Tarbiyah'),
(1,5,300000,250000,'S1 Manajemen Bisnis - Fakultas Ekonomi'),
(1,6,300000,250000,'S1 HES - Fakultas Hukum'),
-- S2 PAI (prodi_id 7)
(1,7,500000,0,'S2 PAI - Gratis bagi 20 pendaftar pertama. Biaya pendidikan sampai lulus Rp8 juta');

-- ============================================================
-- SEEDER: promo
-- ============================================================
INSERT INTO `promo` (`tahun_akademik_id`,`program_studi_id`,`judul`,`keterangan`,`kuota`,`berlaku_sampai`) VALUES
(1,7,'Gratis Biaya Pendaftaran S2','20 Pendaftar Pertama Program Magister S2 Gratis Biaya Pendaftaran',20,'2026-08-31 23:59:59');

-- ============================================================
-- SEEDER: persyaratan
-- ============================================================
INSERT INTO `persyaratan` (`tahun_akademik_id`,`jenjang`,`nama`,`wajib`,`urutan`) VALUES
(1,'semua','Mengisi Formulir Pendaftaran Online',1,1),
(1,'semua','Scan Asli KTP',1,2),
(1,'semua','Scan Asli Kartu Keluarga (KK)',1,3),
(1,'semua','Scan Asli Akte Kelahiran',1,4),
(1,'S1','Scan Asli Ijazah SMA/Sederajat',1,5),
(1,'S1','Scan Asli Transkrip Nilai SMA/Sederajat',1,6),
(1,'S2','Scan Asli Ijazah S1',1,5),
(1,'S2','Scan Asli Transkrip Nilai S1 (boleh menyusul)',0,6),
(1,'semua','Foto Resmi Berjas Warna Hitam Background Biru Muda',1,7);

-- ============================================================
-- SEEDER: cms_settings
-- ============================================================
INSERT INTO `cms_settings` (`key_name`,`value`,`group_name`,`label`,`type`) VALUES
('site_name',        'Ma''had Aly Ibnu Abbas Karanganyar', 'umum',    'Nama Institusi',         'text'),
('site_tagline',     'Mencetak Generasi Rabbani, Unggul Dalam Ilmu, Berakhlak Mulia dan Berkemajuan','umum','Tagline','textarea'),
('site_kerjasama',   'Bekerjasama dengan Institut Muhammadiyah Ngawi','umum','Kerjasama','text'),
('site_phone',       '0856-1464-905',      'kontak',  'Nomor Telepon/WA',       'text'),
('site_website',     'www.ibnuabbass.com', 'kontak',  'Website',                'url'),
('site_email',       'info@ibnuabbass.com','kontak',  'Email',                  'email'),
('site_alamat',      'Karanganyar, Jawa Tengah', 'kontak','Alamat',             'textarea'),
('hero_title',       'Penerimaan Mahasiswa Baru','landing','Judul Hero',         'text'),
('hero_subtitle',    'Wujudkan Impianmu Bersama Kami','landing','Subtitle Hero', 'text'),
('tentang_kampus',   'Ma''had Aly Ibnu Abbas Karanganyar adalah perguruan tinggi Islam yang berdedikasi dalam mencetak generasi Rabbani...','landing','Tentang Kampus','textarea'),
('color_primary',    '#1a3a6b',            'tampilan', 'Warna Utama',           'color'),
('color_accent',     '#c9a227',            'tampilan', 'Warna Aksen',           'color'),
('logo_path',        '/assets/images/logo.png','tampilan','Path Logo',          'text');

-- ============================================================
-- SEEDER: users (Super Admin)
-- ============================================================
-- Password default: admin123 (WAJIB GANTI setelah login pertama!)
-- Hash bcrypt cost 12 dari: password_hash('admin123', PASSWORD_BCRYPT, ['cost'=>12])
INSERT INTO `users` (`email`,`username`,`password_hash`,`role`,`nama`,`is_aktif`) VALUES
('admin@mahadaly.ac.id','admin','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','superadmin','Super Administrator',1);

-- ============================================================
-- AKTIFKAN EVENT SCHEDULER (jalankan sekali sebagai root)
-- SET GLOBAL event_scheduler = ON;
-- ============================================================

-- ============================================================
-- CLEANUP EVENT: hapus rate_limit & csrf kadaluarsa (opsional)
-- CATATAN: phpMyAdmin tidak support DELIMITER + BEGIN...END
-- Solusi: pisah menjadi 2 event single-statement (tanpa BEGIN...END)
-- ============================================================

DROP EVENT IF EXISTS `cleanup_expired`;
DROP EVENT IF EXISTS `cleanup_expired_csrf`;

CREATE EVENT IF NOT EXISTS `cleanup_expired`
ON SCHEDULE EVERY 1 HOUR
DO DELETE FROM `rate_limit` WHERE `window_end` < NOW();

CREATE EVENT IF NOT EXISTS `cleanup_expired_csrf`
ON SCHEDULE EVERY 1 HOUR
DO DELETE FROM `csrf_tokens` WHERE `expires_at` < NOW();

SET FOREIGN_KEY_CHECKS = 1;