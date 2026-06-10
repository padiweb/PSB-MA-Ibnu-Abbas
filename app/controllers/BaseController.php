<?php
/**
 * Base Controller
 */
class Controller
{
    protected CmsModel $cms;
    protected array    $settings = [];

    public function __construct()
    {
        $this->cms      = new CmsModel();
        $this->settings = $this->cms->getAll();
    }

    protected function view(string $viewPath, array $data = []): void
    {
        $data['settings'] = $this->settings;
        extract($data);

        $viewFile = APP_PATH . '/views/' . $viewPath . '.php';
        if (!file_exists($viewFile)) {
            http_response_code(500);
            die("View tidak ditemukan: {$viewPath}");
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        $layout = $data['layout'] ?? 'layouts/main';
        require APP_PATH . '/views/' . $layout . '.php';
    }

    protected function json(array $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function redirect(string $path, bool $absolute = false): void
    {
        if ($absolute) {
            $url = $path;
        } elseif ($path === '/' || $path === '') {
            $url = BASE_URL . '/index.php';
        } else {
            // Pisahkan path dari query string jika ada
            // Contoh: '/admin/biaya?ta=1' → page=admin/biaya&ta=1
            $parts     = explode('?', ltrim($path, '/'), 2);
            $page      = $parts[0];
            $extraQs   = $parts[1] ?? '';
            $url       = BASE_URL . '/index.php?page=' . $page;
            if ($extraQs) $url .= '&' . $extraQs;
        }
        header('Location: ' . $url);
        exit;
    }

    protected function isAjax(): bool
    {
        return ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';
    }

    protected function verifyCsrf(): void
    {
        $token = $_POST['csrf_token'] ?? ($_POST['_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ''));
        if (!Security::verifyCsrf($token)) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Token tidak valid. Muat ulang halaman.'], 403);
            }
            Session::flash('error', 'Token keamanan tidak valid. Silakan coba lagi.');
            header('Location: ' . $_SERVER['HTTP_REFERER'] ?? BASE_URL);
            exit;
        }
    }

    protected function validate(array $data, array $rules): array
    {
        $labels = [
            'nama_lengkap'     => 'Nama lengkap',
            'tempat_lahir'     => 'Tempat lahir',
            'tanggal_lahir'    => 'Tanggal lahir',
            'jenis_kelamin'    => 'Jenis kelamin',
            'nomor_hp'         => 'Nomor HP',
            'alamat'           => 'Alamat',
            'nama_ibu_kandung' => 'Nama ibu kandung',
            'program_studi_id' => 'Program studi',
            'password'         => 'Password',
            'email'            => 'Email',
        ];

        $errors = [];
        foreach ($rules as $field => $rule) {
            $parts = explode('|', $rule);
            $val   = $data[$field] ?? '';
            $label = $labels[$field] ?? $field;
            foreach ($parts as $part) {
                if ($part === 'required' && empty(trim((string)$val))) {
                    $errors[$field] = "{$label} wajib diisi.";
                    break;
                }
                if (strncmp($part, 'min:', 4) === 0 && strlen((string)$val) < (int)substr($part, 4)) {
                    $min = (int)substr($part, 4);
                    $errors[$field] = "{$label} minimal {$min} karakter.";
                    break;
                }
                if (strncmp($part, 'max:', 4) === 0 && strlen((string)$val) > (int)substr($part, 4)) {
                    $max = (int)substr($part, 4);
                    $errors[$field] = "{$label} maksimal {$max} karakter.";
                    break;
                }
                if ($part === 'email' && !filter_var($val, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = "Format {$label} tidak valid.";
                    break;
                }
                if ($part === 'numeric' && !is_numeric($val)) {
                    $errors[$field] = "{$label} harus dipilih.";
                    break;
                }
                if ($part === 'date' && !strtotime((string)$val)) {
                    $errors[$field] = "Format {$label} tidak valid.";
                    break;
                }
            }
        }
        return $errors;
    }
}

/**
 * HomeController - Landing Page
 */
class HomeController extends Controller
{
    public function index(): void
    {
        $taModel   = new TahunAkademikModel();
        $prodiModel= new ProdiModel();
        $biayaModel= new BiayaModel();

        $tahunAktif = $taModel->getAktif();
        $prodiList  = $prodiModel->getGrouped();
        $biayaList  = $tahunAktif ? $biayaModel->getByTA($tahunAktif['id']) : [];

        // FAQ, persyaratan dari DB
        $persyaratanList = [];
        if ($tahunAktif) {
            $db   = Database::getInstance();
            $stmt = $db->prepare("SELECT * FROM persyaratan WHERE tahun_akademik_id = ? ORDER BY urutan");
            $stmt->execute([$tahunAktif['id']]);
            $persyaratanList = $stmt->fetchAll();
        }

        $this->view('public/home', [
            'page_title'      => 'Beranda',
            'tahun_aktif'     => $tahunAktif,
            'prodi_grouped'   => $prodiList,
            'biaya_list'      => $biayaList,
            'persyaratan_list'=> $persyaratanList,
        ]);
    }
}

/**
 * AuthController
 */
class AuthController extends Controller
{
    public function loginForm(): void
    {
        if (Auth::check()) {
            $this->redirect(Auth::is('pendaftar') ? '/pendaftar' : '/admin');
        }
        $this->view('auth/login', ['page_title' => 'Login', 'csrf' => Security::generateCsrf()]);
    }

    public function login(): void
    {
        $this->verifyCsrf();
        $ip = Security::getClientIp();

        // Rate limit
        if (!Security::checkRateLimit('login', $ip, RATE_LOGIN_MAX, RATE_LOGIN_WINDOW)) {
            Session::flash('error', 'Terlalu banyak percobaan login. Coba lagi nanti.');
            $this->redirect('/login');
        }

        $credential = Security::cleanRaw($_POST['credential'] ?? '');
        $password   = $_POST['password'] ?? '';

        if (empty($credential) || empty($password)) {
            Session::flash('error', 'Username/nomor pendaftaran dan password wajib diisi.');
            $this->redirect('/login');
        }

        $userModel = new UserModel();
        $user      = $userModel->findByUsernameOrEmail($credential);

        if (!$user || !$user['is_aktif']) {
            Session::flash('error', 'Akun tidak ditemukan atau tidak aktif.');
            $this->redirect('/login');
        }

        if ($userModel->isLocked($user)) {
            $remaining = ceil((strtotime($user['locked_until']) - time()) / 60);
            Session::flash('error', "Akun dikunci karena terlalu banyak percobaan. Coba lagi dalam {$remaining} menit.");
            $this->redirect('/login');
        }

        if (!Security::verifyPassword($password, $user['password_hash'])) {
            Security::recordFailedLogin($user['id']);
            AuditLog::log('LOGIN_FAILED', 'auth', $user['id']);
            Session::flash('error', 'Password salah.');
            $this->redirect('/login');
        }

        Security::resetLoginAttempts($user['id']);
        $userModel->updateLastLogin($user['id'], $ip);
        Auth::login($user);
        AuditLog::log('LOGIN', 'auth', $user['id']);

        if (Auth::is('pendaftar')) {
            $this->redirect('/pendaftar');
        } else {
            $this->redirect('/admin');
        }
    }

    public function logout(): void
    {
        Auth::logout();
        Session::flash('info', 'Anda telah keluar.');
        $this->redirect('/login');
    }

    /** GET /lupa-password */
    public function lupaPasswordForm(): void
    {
        if (Auth::check()) $this->redirect('/');
        $this->view('auth/lupa-password', [
            'page_title' => 'Lupa Password',
            'csrf'       => Security::generateCsrf(),
        ]);
    }

    /** POST /lupa-password */
    public function lupaPassword(): void
    {
        $this->verifyCsrf();
        $nomor   = strtoupper(trim(Security::cleanRaw($_POST['nomor_pendaftaran'] ?? '')));
        $newPw   = Security::cleanRaw($_POST['new_password'] ?? '');
        $confirm = Security::cleanRaw($_POST['confirm_password'] ?? '');

        if (empty($nomor)) {
            Session::flash('error', 'Nomor pendaftaran wajib diisi.');
            $this->redirect('/lupa-password');
        }
        if (strlen($newPw) < 8) {
            Session::flash('error', 'Password baru minimal 8 karakter.');
            $this->redirect('/lupa-password');
        }
        if ($newPw !== $confirm) {
            Session::flash('error', 'Konfirmasi password tidak cocok.');
            $this->redirect('/lupa-password');
        }

        // Cari user via nomor pendaftaran ATAU email
        $pm        = new PendaftarModel();
        $userModel = new UserModel();
        $pendaftar = $pm->findBy('nomor_pendaftaran', $nomor);
        $user      = null;

        if ($pendaftar) {
            $user = $userModel->findById($pendaftar['user_id']);
        } else {
            // Coba cari via email
            $user = $userModel->findByUsernameOrEmail($nomor);
            if ($user) {
                $pendaftar = $pm->findBy('user_id', $user['id']);
            }
        }

        if (!$pendaftar || !$user) {
            Session::flash('error', 'Nomor pendaftaran atau email tidak ditemukan.');
            $this->redirect('/lupa-password');
        }

        $userModel->update($user['id'], [
            'password_hash' => Security::hashPassword($newPw)
        ]);

        Session::flash('success', 'Password berhasil diubah. Silakan login.');
        $this->redirect('/login');
    }
}

/**
 * ErrorController
 */
class ErrorController extends Controller
{
    public function notFound(): void
    {
        http_response_code(404);
        $this->view('public/error', ['page_title' => '404 - Halaman Tidak Ditemukan', 'code' => 404, 'message' => 'Halaman yang Anda cari tidak ditemukan.']);
    }

    public function forbidden(): void
    {
        http_response_code(403);
        $this->view('public/error', ['page_title' => '403 - Akses Ditolak', 'code' => 403, 'message' => 'Anda tidak memiliki akses ke halaman ini.']);
    }
}