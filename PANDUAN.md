# Panduan Teknis — PMB Ma'had Aly Ibnu Abbas Karanganyar

> **Versi:** 1.0 | **Terakhir diperbarui:** 2026 | **Platform:** PHP 8.1+ · MySQL 8 · cPanel Shared Hosting

---

## Daftar Isi

1. [Instalasi di cPanel Shared Hosting](#1-instalasi-di-cpanel-shared-hosting)
2. [Struktur Folder](#2-struktur-folder)
3. [Panduan Backup & Restore](#3-panduan-backup--restore)
4. [Checklist Hardening Server](#4-checklist-hardening-server)
5. [OWASP Top 10 — Implementasi](#5-owasp-top-10--implementasi)
6. [Troubleshooting Umum](#6-troubleshooting-umum)

---

## 1. Instalasi di cPanel Shared Hosting

### 1.1 Kebutuhan Sistem

| Komponen | Minimum | Rekomendasi |
|----------|---------|-------------|
| PHP | 8.1 | 8.2+ |
| MySQL | 5.7 | 8.0+ |
| Ekstensi PHP | pdo_mysql, mbstring, json, fileinfo, openssl | + intl, opcache |
| Disk | 500 MB | 2 GB |
| RAM | 256 MB | 512 MB+ |

### 1.2 Langkah Instalasi

#### Langkah 1 — Upload File

1. Login ke cPanel → **File Manager**
2. Buat struktur folder seperti ini:

```
/home/username/               ← LUAR public_html
    pmb/                      ← Folder aplikasi
        app/
        config/
        core/
        database.sql
        setup.php
        storage/              ← Dibuat otomatis oleh setup.php
/home/username/public_html/   ← Isi dari folder /public/
    index.php
    .htaccess
    assets/
```

> **PENTING:** Folder `app/`, `config/`, `core/`, `storage/` harus berada **di luar** `public_html` agar tidak dapat diakses langsung melalui browser.

3. Upload semua file dari folder `public/` ke `public_html/`
4. Upload semua file lainnya ke `/home/username/pmb/`

#### Langkah 2 — Sesuaikan `ROOT_PATH`

Edit file `public_html/index.php`, sesuaikan baris pertama:

```php
// Sebelum:
define('ROOT_PATH', dirname(__DIR__));

// Setelah (sesuaikan dengan path server Anda):
define('ROOT_PATH', '/home/username/pmb');
```

#### Langkah 3 — Buat Database

1. cPanel → **MySQL Databases**
2. Buat database baru: `username_pmb`
3. Buat user MySQL baru: `username_pmbuser`
4. Set password yang kuat
5. Tambahkan user ke database dengan **ALL PRIVILEGES**

#### Langkah 4 — Import Database

1. cPanel → **phpMyAdmin**
2. Pilih database yang baru dibuat
3. Klik tab **Import**
4. Upload file `database.sql`
5. Klik **Go**

Alternatif via terminal/SSH:
```bash
mysql -u username_pmbuser -p username_pmb < /home/username/pmb/database.sql
```

#### Langkah 5 — Konfigurasi

Edit file `/home/username/pmb/config/config.php`:

```php
// Sesuaikan nilai-nilai berikut:
define('BASE_URL',  'https://pmb.ibnuabbass.com');  // URL domain Anda
define('DB_HOST',   '127.0.0.1');
define('DB_PORT',   '3306');
define('DB_NAME',   'username_pmb');
define('DB_USER',   'username_pmbuser');
define('DB_PASS',   'password_anda');
define('APP_KEY',   'ganti_dengan_32_karakter_acak');  // php -r "echo bin2hex(random_bytes(32));"
define('APP_DEBUG', false);  // false untuk production!
```

#### Langkah 6 — Permission Folder

Via cPanel File Manager atau SSH:
```bash
# Folder storage harus writable
chmod 755 /home/username/pmb/storage
chmod 755 /home/username/pmb/storage/uploads
chmod 755 /home/username/pmb/storage/logs

# File PHP tidak boleh writable
find /home/username/pmb -name "*.php" -exec chmod 644 {} \;

# .htaccess
chmod 644 /home/username/public_html/.htaccess
```

#### Langkah 7 — Jalankan Setup (Opsional)

Jika menggunakan SSH:
```bash
cd /home/username/pmb
php setup.php
```

Script ini akan:
- Memverifikasi koneksi database
- Mengimport database.sql
- Membuat direktori storage
- Memandu konfigurasi
- Menawarkan ganti password admin

**Setelah selesai, hapus `setup.php`!**
```bash
rm /home/username/pmb/setup.php
```

#### Langkah 8 — Verifikasi

Buka URL aplikasi di browser:
- **Landing page:** `https://pmb.ibnuabbass.com`
- **Login admin:** `https://pmb.ibnuabbass.com/login`
  - Username: `admin`
  - Password: `admin123` *(ganti segera!)*

### 1.3 Konfigurasi Domain/Subdomain

Jika menggunakan subdomain `pmb.ibnuabbass.com`:

1. cPanel → **Subdomains**
2. Buat subdomain: `pmb`
3. Set **Document Root** ke: `/home/username/public_html`
4. Atau buat subdomain terpisah dengan root `public_html/pmb/`

### 1.4 SSL/HTTPS

1. cPanel → **SSL/TLS** → **Let's Encrypt SSL**
2. Issue certificate untuk domain Anda
3. Aktifkan **Force HTTPS Redirect**

Atau tambahkan ke `.htaccess`:
```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

## 2. Struktur Folder

```
pmb/                            ← Root project (di luar public_html)
├── app/
│   ├── controllers/
│   │   ├── BaseController.php  ← Base + Home + Auth + Error controllers
│   │   ├── PendaftaranController.php
│   │   ├── AdminController.php
│   │   └── ExtraControllers.php ← Biaya, Persyaratan, User, Pendaftar
│   ├── models/
│   │   └── Models.php          ← Semua model (Pendaftar, User, Dokumen, dll)
│   └── views/
│       ├── layouts/
│       │   ├── main.php        ← Layout publik
│       │   ├── admin.php       ← Layout admin
│       │   └── print.php       ← Layout cetak
│       ├── public/
│       │   ├── home.php        ← Landing page
│       │   ├── daftar.php      ← Form pendaftaran multi-step
│       │   ├── daftar-sukses.php
│       │   └── error.php
│       ├── auth/
│       │   └── login.php
│       ├── admin/
│       │   ├── dashboard.php
│       │   ├── pendaftar.php
│       │   ├── detail.php
│       │   ├── tahun-akademik.php
│       │   ├── prodi.php
│       │   ├── biaya.php
│       │   ├── persyaratan.php
│       │   ├── pengaturan.php
│       │   └── users.php
│       └── pendaftar/
│           ├── dashboard.php
│           ├── berkas.php      ← Upload/kelola berkas
│           └── cetak.php       ← Cetak bukti
├── config/
│   └── config.php              ← Konfigurasi (jangan di-commit!)
├── core/
│   ├── Database.php
│   ├── Model.php
│   ├── Helpers.php
│   ├── Security.php
│   └── Router.php
├── storage/                    ← Dibuat saat setup
│   ├── uploads/
│   │   └── dokumen/
│   │       └── {pendaftar_id}/  ← File dokumen per pendaftar
│   └── logs/
│       └── app.log
├── database.sql
└── setup.php                   ← Hapus setelah instalasi!

public_html/                    ← Document root web server
├── index.php                   ← Front controller
├── .htaccess
└── assets/
    ├── css/
    │   └── app.css
    └── js/
        └── app.js
```

---

## 3. Panduan Backup & Restore

### 3.1 Strategi Backup

| Tipe | Frekuensi | Retensi | Metode |
|------|-----------|---------|--------|
| Database full | Harian | 30 hari | mysqldump |
| File upload | Mingguan | 90 hari | tar.gz |
| Config | Setiap perubahan | Permanent | Manual |
| Full backup | Mingguan | 4 minggu | cPanel backup |

### 3.2 Backup Database via cPanel

**Manual:**
1. cPanel → **phpMyAdmin**
2. Pilih database → Tab **Export**
3. Format: **SQL** → **Go**
4. Simpan file `.sql`

**Via Script (cron job):**

Buat file `/home/username/scripts/backup-db.sh`:
```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/home/username/backups/database"
DB_USER="username_pmbuser"
DB_PASS="password_anda"
DB_NAME="username_pmb"

mkdir -p "$BACKUP_DIR"

# Backup dengan kompresi
mysqldump --user="$DB_USER" --password="$DB_PASS" \
    --single-transaction --routines --triggers \
    "$DB_NAME" | gzip > "$BACKUP_DIR/db_${DATE}.sql.gz"

# Hapus backup lebih dari 30 hari
find "$BACKUP_DIR" -name "*.sql.gz" -mtime +30 -delete

echo "Backup selesai: db_${DATE}.sql.gz"
```

Set cron job di cPanel → **Cron Jobs**:
```
0 2 * * * /bin/bash /home/username/scripts/backup-db.sh >> /home/username/logs/backup.log 2>&1
```
*(Jalankan setiap hari jam 02:00)*

### 3.3 Backup File Upload

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/home/username/backups/uploads"
UPLOAD_DIR="/home/username/pmb/storage/uploads"

mkdir -p "$BACKUP_DIR"

tar -czf "$BACKUP_DIR/uploads_${DATE}.tar.gz" -C "$UPLOAD_DIR" .

# Hapus backup lebih dari 90 hari
find "$BACKUP_DIR" -name "*.tar.gz" -mtime +90 -delete
```

### 3.4 Restore Database

**Via phpMyAdmin:**
1. cPanel → **phpMyAdmin** → Pilih database
2. Drop semua tabel (atau buat database baru)
3. Import file `.sql` backup

**Via Terminal:**
```bash
# Jika file terkompresi:
gunzip -c /home/username/backups/database/db_20260601_020000.sql.gz | \
    mysql -u username_pmbuser -p username_pmb

# Jika file biasa:
mysql -u username_pmbuser -p username_pmb < backup.sql
```

### 3.5 Restore File Upload

```bash
tar -xzf uploads_20260601.tar.gz -C /home/username/pmb/storage/uploads/
# Perbaiki permission
chmod -R 755 /home/username/pmb/storage/uploads/
```

### 3.6 Backup Otomatis via cPanel

1. cPanel → **Backup Wizard** atau **Backup**
2. **Full Backup** → simpan ke remote FTP/home directory
3. Jadwalkan backup mingguan

---

## 4. Checklist Hardening Server

### 4.1 PHP Configuration (`php.ini`)

```ini
; ── Matikan tampilan error di production ──
display_errors = Off
display_startup_errors = Off
log_errors = On
error_log = /home/username/logs/php-errors.log

; ── Batasi eksekusi ──
max_execution_time = 30
max_input_time = 60
memory_limit = 256M

; ── Upload ──
upload_max_filesize = 6M       ; Sedikit di atas limit aplikasi (5MB)
post_max_size = 10M
file_uploads = On

; ── Session ──
session.cookie_httponly = 1
session.cookie_secure = 1      ; Hanya jika HTTPS
session.use_strict_mode = 1
session.cookie_samesite = Strict

; ── Keamanan ──
expose_php = Off               ; Sembunyikan versi PHP
allow_url_fopen = Off
allow_url_include = Off
```

Jika tidak bisa edit `php.ini`, buat file `.user.ini` di `public_html/`:
```ini
display_errors = Off
expose_php = Off
session.cookie_httponly = 1
session.cookie_secure = 1
session.cookie_samesite = Strict
```

### 4.2 .htaccess Hardening

File `public_html/.htaccess` sudah mencakup:

```apache
# ── Blokir akses ke file sensitif ──
<FilesMatch "\.(sql|log|env|bak|sh|ini|conf)$">
    Order Allow,Deny
    Deny from all
</FilesMatch>

# ── Blokir dot files ──
<FilesMatch "^\.">
    Order Allow,Deny
    Deny from all
</FilesMatch>

# ── Paksa HTTPS ──
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# ── Matikan listing direktori ──
Options -Indexes

# ── Blokir akses langsung ke PHP di luar public ──
# (File PHP di luar public_html sudah tidak bisa diakses)
```

### 4.3 Checklist File & Direktori

| Item | Target | Status |
|------|--------|--------|
| `config/config.php` tidak dapat diakses web | 644, di luar public | ✅ |
| `storage/` tidak dapat diakses web | Di luar public / .htaccess | ✅ |
| `setup.php` dihapus setelah deploy | File tidak ada | Wajib cek |
| File PHP permission | 644 | Perlu set manual |
| Direktori storage permission | 755 | Set saat setup |
| `database.sql` tidak bisa diakses web | Di luar public | ✅ |

### 4.4 MySQL Hardening

```sql
-- Cabut privilege yang tidak perlu dari user aplikasi
REVOKE FILE, PROCESS, SUPER ON *.* FROM 'username_pmbuser'@'localhost';

-- Pastikan user hanya bisa akses database sendiri
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER
    ON username_pmb.* TO 'username_pmbuser'@'localhost';

FLUSH PRIVILEGES;
```

### 4.5 Monitoring & Logging

- Log PHP error aktif (`error_log = /home/username/logs/php-errors.log`)
- Log aplikasi di `storage/logs/app.log` (error, warning, info)
- Audit log di tabel `audit_log` (semua aksi admin tercatat)
- Rate limit di tabel `rate_limit` (deteksi brute force)
- Monitor cPanel → **Error Log** untuk 500 errors

### 4.6 Update & Pemeliharaan

```bash
# Cek versi PHP yang tersedia di cPanel
# Update PHP melalui: cPanel → PHP Version Manager (MultiPHP)

# Bersihkan log lama (tambahkan ke cron job mingguan)
find /home/username/pmb/storage/logs -name "*.log" -mtime +90 -delete

# Bersihkan session kadaluarsa (jalankan mingguan)
# DELETE FROM sessions WHERE last_activity < DATE_SUB(NOW(), INTERVAL 7 DAY);
```

---

## 5. OWASP Top 10 — Implementasi

### A01:2021 — Broken Access Control ✅

**Implementasi:**
- Setiap controller memanggil `Auth::requireRole(['role1','role2'])` di constructor
- Pendaftar hanya bisa akses data milik sendiri (filter `user_id = Auth::id()`)
- Admin tidak bisa delete akun sendiri (cek di `UserController`)
- Route admin semua diproteksi, tidak ada akses anonim ke data sensitif

**Contoh kode:**
```php
// Di BaseController — setiap halaman admin wajib login
class AdminController extends Controller {
    public function __construct() {
        parent::__construct();
        Auth::requireRole(['superadmin', 'admin', 'verifikator']);
    }
}

// Pendaftar hanya lihat data sendiri
$pendaftar = $this->pendaftarModel->getByUserId(Auth::id());
if (!$pendaftar || $pendaftar['id'] != (int)$id) {
    $this->redirect('/pendaftar'); // Tolak akses
}
```

### A02:2021 — Cryptographic Failures ✅

**Implementasi:**
- Password di-hash dengan `password_hash($pass, PASSWORD_BCRYPT, ['cost'=>12])`
- Tidak ada password plain-text di database
- Session cookie `Secure` + `HttpOnly` + `SameSite=Strict`
- HTTPS diwajibkan (HSTS header + redirect)
- `APP_KEY` di config digunakan untuk signing token

### A03:2021 — Injection ✅

**Implementasi:**
- **Semua query** menggunakan PDO Prepared Statements
- Tidak ada query string concatenation
- Input di-sanitasi melalui `Security::cleanRaw()`, `Security::cleanInt()`, `Security::cleanEmail()`
- Tabel dan kolom hardcoded, tidak pernah dari input user

**Contoh kode:**
```php
// Model.php — semua query menggunakan prepared statements
public function findBy(string $field, mixed $value): ?array {
    // $field divalidasi dari whitelist kolom, bukan langsung dari user
    $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE `{$field}` = ?");
    $stmt->execute([$value]);
    return $stmt->fetch() ?: null;
}
```

### A04:2021 — Insecure Design ✅

**Implementasi:**
- Arsitektur MVC dengan separation of concerns
- File upload disimpan di luar `public_html` dengan nama UUID
- Nomor pendaftaran tidak prediktif (berbasis counter + padding)
- Promo S2 divalidasi di server, tidak hanya frontend
- Rate limiting untuk registrasi dan login

### A05:2021 — Security Misconfiguration ✅

**Implementasi:**
- `APP_DEBUG = false` di production (matikan error display)
- Security headers dipasang di setiap response: CSP, X-Frame-Options, X-Content-Type-Options, HSTS, Referrer-Policy
- Directory listing dimatikan (`Options -Indexes`)
- Akses langsung ke `.php` di luar public diblokir oleh struktur folder
- Versi PHP disembunyikan (`expose_php = Off`)

**Headers yang dikirim:**
```
Content-Security-Policy: default-src 'self' https:; ...
X-Frame-Options: DENY
X-Content-Type-Options: nosniff
Referrer-Policy: strict-origin-when-cross-origin
Strict-Transport-Security: max-age=31536000; includeSubDomains
```

### A06:2021 — Vulnerable and Outdated Components ✅

**Implementasi:**
- CDN library dengan versi tertentu (Bootstrap 5.3.3, BI 1.11.3)
- Tidak menggunakan framework — minim surface attack
- Library dari CDN Cloudflare yang terpercaya dengan Subresource Integrity (SRI) sebaiknya ditambahkan
- PHP 8.1+ minimum requirement

**Rekomendasi tambahan:**
```html
<!-- Tambahkan integrity hash untuk keamanan CDN -->
<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css"
      integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtm7no9GFiZyLHGgXJFw=="
      crossorigin="anonymous">
```

### A07:2021 — Identification and Authentication Failures ✅

**Implementasi:**
- Max 5 percobaan login → lockout 15 menit (tabel `rate_limit`)
- Session timeout 2 jam (`SESSION_LIFETIME = 7200`)
- Session ID diregenerasi setiap 30 menit (`SESSION_REGEN = 1800`)
- Session ID diregenerasi setelah login berhasil
- Password minimum 8 karakter, bcrypt cost 12
- Akun dapat dinonaktifkan oleh superadmin

**Contoh kode:**
```php
// AuthController — deteksi brute force
$attempts = RateLimit::count('login', $ip, 15);
if ($attempts >= LOGIN_MAX_ATTEMPTS) {
    Session::flash('error', 'Terlalu banyak percobaan. Coba lagi dalam 15 menit.');
    $this->redirect('/login');
}
```

### A08:2021 — Software and Data Integrity Failures ✅

**Implementasi:**
- CSRF token di semua form POST (`Security::generateCsrf()` + `verifyCsrf()`)
- File upload divalidasi MIME type (via `finfo`) + ekstensi + ukuran
- File diubah namanya dengan UUID (tidak ada nama asli yang disimpan di filesystem)
- Audit log untuk semua perubahan data

### A09:2021 — Security Logging and Monitoring Failures ✅

**Implementasi:**
- Tabel `audit_log`: mencatat semua CREATE/UPDATE/DELETE/LOGIN/LOGOUT + IP + user agent
- Tabel `rate_limit`: deteksi pola brute force
- Log error PHP ke file
- Login berhasil/gagal tercatat di audit_log
- Verifikasi berkas tercatat di tabel `verifikasi_log`

### A10:2021 — Server-Side Request Forgery (SSRF) ✅

**Implementasi:**
- Aplikasi tidak melakukan request HTTP ke URL eksternal berdasarkan input user
- Tidak ada fitur fetch/proxy URL
- Upload file hanya dari form browser (multipart), bukan dari URL

---

## 6. Troubleshooting Umum

### "500 Internal Server Error"

1. Aktifkan sementara `display_errors = On` di `config.php`
2. Cek log: cPanel → **Error Logs**
3. Cek permission file (seharusnya 644 untuk PHP, 755 untuk folder)
4. Pastikan `ROOT_PATH` di `public/index.php` benar

### "Database Connection Error"

1. Verifikasi kredensial di `config/config.php`
2. Di cPanel, pastikan user MySQL sudah punya akses ke database
3. Host: gunakan `127.0.0.1` bukan `localhost` jika ada masalah socket

### Upload Dokumen Gagal

1. Cek permission `storage/uploads/` → harus `755`
2. Cek `php.ini`: `upload_max_filesize` dan `post_max_size` ≥ 6M
3. Pastikan folder ada: `storage/uploads/dokumen/`
4. Cek `STORAGE_PATH` di config menunjuk ke path yang benar

### URL tidak bekerja (404)

1. Pastikan `mod_rewrite` aktif di Apache
2. Cek `.htaccess` ada di `public_html/`
3. Pastikan `AllowOverride All` aktif (biasanya default di cPanel)
4. Cek `BASE_URL` di config sesuai domain aktual

### Login Terkunci (Rate Limit)

```sql
-- Reset rate limit untuk IP tertentu
DELETE FROM rate_limit WHERE ip_address = '1.2.3.4';

-- Reset lockout akun tertentu
UPDATE users SET login_attempts = 0, locked_until = NULL WHERE username = 'admin';
```

### Reset Password Admin via Database

```sql
-- Ganti 'password_baru' dengan password yang diinginkan
-- Gunakan PHP untuk hash dulu:
-- php -r "echo password_hash('passwordbaru', PASSWORD_BCRYPT, ['cost'=>12]);"
UPDATE users
SET password_hash = '$2y$12$...(hasil hash)...'
WHERE username = 'admin' AND role = 'superadmin';
```

---

*Dokumen ini bersifat teknis dan rahasia. Jangan dibagikan kepada pihak yang tidak berkepentingan.*
