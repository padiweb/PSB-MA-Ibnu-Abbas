<?php
/**
 * Security - CSRF, Input Sanitasi, Rate Limiting
 */
class Security
{
    // ── CSRF ────────────────────────────────────────────────────

    public static function generateCsrf(): string
    {
        if (empty($_SESSION['csrf_token']) || self::isCsrfExpired()) {
            $_SESSION['csrf_token']         = bin2hex(random_bytes(CSRF_TOKEN_LENGTH));
            $_SESSION['csrf_token_created'] = time();
        }
        return $_SESSION['csrf_token'];
    }

    public static function verifyCsrf(string $token): bool
    {
        if (empty($_SESSION['csrf_token'])) return false;
        if (self::isCsrfExpired()) {
            unset($_SESSION['csrf_token'], $_SESSION['csrf_token_created']);
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    private static function isCsrfExpired(): bool
    {
        return isset($_SESSION['csrf_token_created'])
            && (time() - $_SESSION['csrf_token_created']) > CSRF_EXPIRE;
    }

    // ── Input Sanitasi ──────────────────────────────────────────

    public static function clean(mixed $input): mixed
    {
        if (is_array($input)) {
            return array_map([self::class, 'clean'], $input);
        }
        return htmlspecialchars(trim((string) $input), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    public static function cleanRaw(string $input): string
    {
        return trim(strip_tags($input));
    }

    public static function cleanInt(mixed $val): int
    {
        return (int) filter_var($val, FILTER_SANITIZE_NUMBER_INT);
    }

    public static function cleanEmail(string $email): string|false
    {
        $e = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
        return filter_var($e, FILTER_VALIDATE_EMAIL) ? $e : false;
    }

    // ── Password ────────────────────────────────────────────────

    public static function hashPassword(string $plain): string
    {
        return password_hash($plain, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
    }

    public static function verifyPassword(string $plain, string $hash): bool
    {
        return password_verify($plain, $hash);
    }

    // ── Rate Limiting ───────────────────────────────────────────

    public static function checkRateLimit(string $action, string $identifier, int $maxHits, int $windowSeconds): bool
    {
        $db  = Database::getInstance();
        $key = hash('sha256', $action . ':' . $identifier);
        $now = time();

        $db->prepare("DELETE FROM `rate_limit` WHERE `key_val` = ? AND `window_end` < NOW()")
           ->execute([$key]);

        $stmt = $db->prepare("SELECT `hits`,`window_end` FROM `rate_limit` WHERE `key_val` = ?");
        $stmt->execute([$key]);
        $row = $stmt->fetch();

        if (!$row) {
            $windowEnd = date('Y-m-d H:i:s', $now + $windowSeconds);
            $db->prepare("INSERT INTO `rate_limit` (`key_val`,`hits`,`window_end`) VALUES (?,1,?)")
               ->execute([$key, $windowEnd]);
            return true;
        }

        if ($row['hits'] >= $maxHits) {
            return false;
        }

        $db->prepare("UPDATE `rate_limit` SET `hits` = `hits`+1 WHERE `key_val` = ?")
           ->execute([$key]);
        return true;
    }

    // ── Login Attempt ───────────────────────────────────────────

    public static function recordFailedLogin(int $userId): void
    {
        $db   = Database::getInstance();
        $stmt = $db->prepare("SELECT `login_attempts` FROM `users` WHERE `id` = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        if (!$user) return;

        $attempts = $user['login_attempts'] + 1;
        if ($attempts >= MAX_LOGIN_ATTEMPTS) {
            $locked = date('Y-m-d H:i:s', time() + LOCKOUT_DURATION);
            $db->prepare("UPDATE `users` SET `login_attempts`=?, `locked_until`=? WHERE `id`=?")
               ->execute([$attempts, $locked, $userId]);
        } else {
            $db->prepare("UPDATE `users` SET `login_attempts`=? WHERE `id`=?")
               ->execute([$attempts, $userId]);
        }
    }

    public static function resetLoginAttempts(int $userId): void
    {
        Database::getInstance()
            ->prepare("UPDATE `users` SET `login_attempts`=0, `locked_until`=NULL WHERE `id`=?")
            ->execute([$userId]);
    }

    // ── UUID ────────────────────────────────────────────────────

    public static function uuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    // ── IP ──────────────────────────────────────────────────────

    public static function getClientIp(): string
    {
        $keys = ['HTTP_CF_CONNECTING_IP','HTTP_X_FORWARDED_FOR','HTTP_X_REAL_IP','REMOTE_ADDR'];
        foreach ($keys as $k) {
            $ip = $_SERVER[$k] ?? '';
            if ($ip) {
                $ip = explode(',', $ip)[0];
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP)) return $ip;
            }
        }
        return '0.0.0.0';
    }

    // ── File Upload ─────────────────────────────────────────────

    public static function validateUpload(array $file): array
    {
        $errors = [];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Terjadi kesalahan saat upload file.';
            return $errors;
        }

        if ($file['size'] > UPLOAD_MAX_SIZE) {
            $errors[] = 'Ukuran file maksimum ' . (UPLOAD_MAX_SIZE / 1024 / 1024) . ' MB.';
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, UPLOAD_ALLOWED_EXT, true)) {
            $errors[] = 'Format file tidak diizinkan. Gunakan: ' . implode(', ', UPLOAD_ALLOWED_EXT);
        }

        // Validasi MIME real dari konten file
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);
        if (!in_array($mime, UPLOAD_ALLOWED_MIME, true)) {
            $errors[] = 'Tipe file tidak valid.';
        }

        // Cek magic bytes untuk file gambar
        if (in_array($mime, ['image/jpeg','image/png'], true)) {
            if (!getimagesize($file['tmp_name'])) {
                $errors[] = 'File gambar tidak valid.';
            }
        }

        return $errors;
    }

    public static function saveUpload(array $file, string $subfolder = ''): string
    {
        $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $uuid     = self::uuid();
        $filename = $uuid . '.' . $ext;
        $dir      = UPLOAD_PATH . ($subfolder ? '/' . trim($subfolder, '/') : '');

        if (!is_dir($dir)) {
            mkdir($dir, 0750, true);
        }

        $dest = $dir . '/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            throw new RuntimeException('Gagal menyimpan file upload.');
        }

        // Tambahan: hapus metadata EXIF dari gambar untuk privasi
        if (in_array($file['type'], ['image/jpeg','image/jpg'], true) && function_exists('imagejpeg')) {
            $img = imagecreatefromjpeg($dest);
            if ($img) { imagejpeg($img, $dest, 90); imagedestroy($img); }
        }

        return ($subfolder ? $subfolder . '/' : '') . $filename;
    }
}
