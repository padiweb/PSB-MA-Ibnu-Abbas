<?php
/**
 * Session Handler
 */
class Session
{
    public static function start(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_name(SESSION_NAME);
            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'secure'   => SESSION_SECURE,
                'httponly' => true,
                'samesite' => 'Strict',
            ]);
            session_start();
        }
        // Auto-regenerate setiap 30 menit
        if (!isset($_SESSION['_created'])) {
            $_SESSION['_created'] = time();
        } elseif (time() - $_SESSION['_created'] > 1800) {
            session_regenerate_id(true);
            $_SESSION['_created'] = time();
        }
        // Session timeout
        if (isset($_SESSION['_last_activity'])) {
            if (time() - $_SESSION['_last_activity'] > SESSION_LIFETIME) {
                self::destroy();
                return;
            }
        }
        $_SESSION['_last_activity'] = time();
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function delete(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
    }

    public static function flash(string $key, string $message): void
    {
        $_SESSION['_flash'][$key] = $message;
    }

    public static function getFlash(string $key): ?string
    {
        $msg = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $msg;
    }
}

/**
 * Logger
 */
class Logger
{
    public static function write(string $level, string $message, array $context = []): void
    {
        $date    = date('Y-m-d H:i:s');
        $ip      = Security::getClientIp();
        $ctx     = $context ? ' | ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        $line    = "[{$date}] [{$level}] [{$ip}] {$message}{$ctx}\n";
        $file    = LOG_PATH . '/' . date('Y-m-d') . '.log';

        if (!is_dir(LOG_PATH)) mkdir(LOG_PATH, 0750, true);
        file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
    }

    public static function info(string $msg, array $ctx = []): void  { self::write('INFO',  $msg, $ctx); }
    public static function error(string $msg, array $ctx = []): void { self::write('ERROR', $msg, $ctx); }
    public static function warn(string $msg, array $ctx = []): void  { self::write('WARN',  $msg, $ctx); }
    public static function audit(string $msg, array $ctx = []): void { self::write('AUDIT', $msg, $ctx); }
}

/**
 * Audit Log (DB)
 */
class AuditLog
{
    public static function log(string $action, string $module, ?int $recordId = null, ?array $before = null, ?array $after = null): void
    {
        try {
            $db = Database::getInstance();
            $db->prepare("INSERT INTO `audit_log` (`user_id`,`action`,`module`,`record_id`,`data_before`,`data_after`,`ip_address`,`user_agent`) VALUES (?,?,?,?,?,?,?,?)")
               ->execute([
                   Session::get('user_id'),
                   $action,
                   $module,
                   $recordId,
                   $before ? json_encode($before, JSON_UNESCAPED_UNICODE) : null,
                   $after  ? json_encode($after,  JSON_UNESCAPED_UNICODE) : null,
                   Security::getClientIp(),
                   substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
               ]);
        } catch (Exception $e) {
            Logger::error('AuditLog failed: ' . $e->getMessage());
        }
    }
}

/**
 * Auth helper
 */
class Auth
{
    public static function check(): bool
    {
        return Session::has('user_id');
    }

    public static function user(): ?array
    {
        if (!self::check()) return null;
        return [
            'id'   => Session::get('user_id'),
            'nama' => Session::get('user_nama'),
            'role' => Session::get('user_role'),
        ];
    }

    public static function id(): ?int
    {
        return Session::get('user_id');
    }

    public static function role(): ?string
    {
        return Session::get('user_role');
    }

    public static function is(string ...$roles): bool
    {
        return in_array(Session::get('user_role'), $roles, true);
    }

    public static function requireLogin(string $redirect = '/auth/login'): void
    {
        if (!self::check()) {
            Session::flash('error', 'Silakan login terlebih dahulu.');
            header('Location: ' . BASE_URL . $redirect);
            exit;
        }
    }

    public static function requireRole(array $roles, string $redirect = '/auth/login'): void
    {
        self::requireLogin($redirect);
        if (!in_array(self::role(), $roles, true)) {
            http_response_code(403);
            header('Location: ' . BASE_URL . '/error/403');
            exit;
        }
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);
        Session::set('user_id',   $user['id']);
        Session::set('user_nama', $user['nama']);
        Session::set('user_role', $user['role']);
        Session::set('_created',  time());
    }

    public static function logout(): void
    {
        AuditLog::log('LOGOUT', 'auth');
        Session::destroy();
    }
}
