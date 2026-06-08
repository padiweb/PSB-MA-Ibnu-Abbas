<?php
require_once APP_PATH . '/controllers/BaseController.php';

/**
 * BiayaController — Kelola biaya per prodi per tahun akademik
 */
class BiayaController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Auth::requireRole(['superadmin','admin']);
    }

    public function index(): void
    {
        $taModel    = new TahunAkademikModel();
        $prodiModel = new ProdiModel();
        $biayaModel = new BiayaModel();

        $taId   = Security::cleanInt($_GET['ta'] ?? 0);
        $taList = $taModel->findAll([], 'id DESC');
        if (!$taId && !empty($taList)) $taId = $taList[0]['id'];

        $list   = $taId ? $biayaModel->getByTahun($taId) : [];
        $prodiList = $prodiModel->getAktif();

        $this->view('admin/biaya', [
            'layout'      => 'layouts/admin',
            'page_title'  => 'Pengaturan Biaya',
            'list'        => $list,
            'ta_list'     => $taList,
            'prodi_list'  => $prodiList,
            'ta_id'       => $taId,
        ]);
    }

    public function store(): void
    {
        $this->verifyCsrf();
        $biayaModel = new BiayaModel();

        $taId   = Security::cleanInt($_POST['tahun_akademik_id'] ?? 0);
        $prodiId= Security::cleanInt($_POST['program_studi_id'] ?? 0);

        if (!$taId || !$prodiId) {
            Session::flash('error', 'Pilih tahun akademik dan program studi.');
            $this->redirect('/admin/biaya');
        }

        // Cek apakah sudah ada biaya untuk ta+prodi ini (spesifik)
        $db = Database::getInstance();
        $stmtEx = $db->prepare("SELECT * FROM `biaya` WHERE tahun_akademik_id=? AND program_studi_id=? LIMIT 1");
        $stmtEx->execute([$taId, $prodiId]);
        $existing = $stmtEx->fetch() ?: null;
        $data = [
            'tahun_akademik_id'  => $taId,
            'program_studi_id'   => $prodiId,
            'biaya_pendaftaran'  => Security::cleanInt(str_replace(['.',',',' '], '', $_POST['biaya_pendaftaran'] ?? 0)),
            'biaya_spp'          => Security::cleanInt(str_replace(['.',',',' '], '', $_POST['biaya_spp'] ?? 0)),
            'biaya_pendidikan'   => Security::cleanInt(str_replace(['.',',',' '], '', $_POST['biaya_pendidikan'] ?? 0)),
            'keterangan'         => Security::cleanRaw($_POST['keterangan'] ?? ''),
        ];

        if ($existing) {
            $biayaModel->update($existing['id'], $data);
        } else {
            $biayaModel->insert($data);
        }

        AuditLog::log('UPSERT', 'biaya', $prodiId, [], $data);
        Session::flash('success', 'Biaya berhasil disimpan.');
        $this->redirect('/admin/biaya?ta=' . $taId);
    }

    public function delete(string $id): void
    {
        $this->verifyCsrf();
        $biayaModel = new BiayaModel();
        $biaya = $biayaModel->findById((int)$id);
        $taId = $biaya['tahun_akademik_id'] ?? 0;
        $biayaModel->delete((int)$id);
        AuditLog::log('DELETE', 'biaya', (int)$id);
        Session::flash('success', 'Biaya dihapus.');
        $this->redirect('/admin/biaya?ta=' . $taId);
    }

    // AJAX: get biaya by prodi+tahun (spesifik prodi)
    public function apiGet(): void
    {
        Auth::requireRole(['superadmin','admin','verifikator','pendaftar']);
        $taId   = Security::cleanInt($_GET['ta'] ?? 0);
        $prodiId= Security::cleanInt($_GET['prodi'] ?? 0);
        $db     = Database::getInstance();
        $stmt   = $db->prepare("SELECT * FROM `biaya` WHERE tahun_akademik_id=? AND program_studi_id=? LIMIT 1");
        $stmt->execute([$taId, $prodiId]);
        $biaya  = $stmt->fetch() ?: null;
        $this->json($biaya ?: (object)[]);
    }
}

/**
 * PersyaratanController — CRUD persyaratan PMB
 */
class PersyaratanController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Auth::requireRole(['superadmin','admin']);
    }

    public function index(): void
    {
        $taModel = new TahunAkademikModel();
        $taId    = Security::cleanInt($_GET['ta'] ?? 0);
        $taList  = $taModel->findAll([], 'id DESC');
        if (!$taId && !empty($taList)) $taId = $taList[0]['id'];

        $list = $this->getList($taId);

        $this->view('admin/persyaratan', [
            'layout'     => 'layouts/admin',
            'page_title' => 'Persyaratan PMB',
            'list'       => $list,
            'ta_list'    => $taList,
            'ta_id'      => $taId,
        ]);
    }

    private function getList(int $taId): array
    {
        $db   = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM persyaratan WHERE tahun_akademik_id=? ORDER BY urutan ASC");
        $stmt->execute([$taId]);
        return $stmt->fetchAll();
    }

    public function store(): void
    {
        $this->verifyCsrf();
        $db = Database::getInstance();
        $taId  = Security::cleanInt($_POST['tahun_akademik_id'] ?? 0);
        $nama  = Security::cleanRaw($_POST['nama'] ?? '');
        $ket   = Security::cleanRaw($_POST['keterangan'] ?? '');
        $urut  = Security::cleanInt($_POST['urutan'] ?? 0);
        $wajib = isset($_POST['is_wajib']) ? 1 : 0;

        if (!$taId || !$nama) {
            Session::flash('error', 'Nama persyaratan wajib diisi.');
            $this->redirect('/admin/persyaratan');
        }

        $stmt = $db->prepare("INSERT INTO persyaratan (tahun_akademik_id,nama,keterangan,urutan,wajib) VALUES (?,?,?,?,?)");
        $stmt->execute([$taId, $nama, $ket, $urut, $wajib]);

        AuditLog::log('CREATE', 'persyaratan');
        Session::flash('success', 'Persyaratan ditambahkan.');
        $this->redirect('/admin/persyaratan?ta=' . $taId);
    }

    public function update(string $id): void
    {
        $this->verifyCsrf();
        $db = Database::getInstance();
        $nama  = Security::cleanRaw($_POST['nama'] ?? '');
        $ket   = Security::cleanRaw($_POST['keterangan'] ?? '');
        $urut  = Security::cleanInt($_POST['urutan'] ?? 0);
        $wajib = isset($_POST['is_wajib']) ? 1 : 0;
        $taId  = Security::cleanInt($_POST['tahun_akademik_id'] ?? 0);

        $stmt = $db->prepare("UPDATE persyaratan SET nama=?,keterangan=?,urutan=?,wajib=? WHERE id=?");
        $stmt->execute([$nama, $ket, $urut, $wajib, (int)$id]);

        AuditLog::log('UPDATE', 'persyaratan', (int)$id);
        Session::flash('success', 'Persyaratan diperbarui.');
        $this->redirect('/admin/persyaratan?ta=' . $taId);
    }

    public function delete(string $id): void
    {
        $this->verifyCsrf();
        $db = Database::getInstance();
        $taId = Security::cleanInt($_GET['ta'] ?? 0);
        $db->prepare("DELETE FROM persyaratan WHERE id=?")->execute([(int)$id]);
        AuditLog::log('DELETE', 'persyaratan', (int)$id);
        Session::flash('success', 'Persyaratan dihapus.');
        $this->redirect('/admin/persyaratan?ta=' . $taId);
    }
}

/**
 * UserController — Manajemen User Admin
 */
class UserController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Auth::requireRole(['superadmin']);
    }

    public function index(): void
    {
        $userModel = new UserModel();
        $list = $userModel->getAdminUsers();

        $this->view('admin/users', [
            'layout'     => 'layouts/admin',
            'page_title' => 'Manajemen User',
            'list'       => $list,
        ]);
    }

    public function store(): void
    {
        $this->verifyCsrf();
        $userModel = new UserModel();

        $username = Security::cleanRaw($_POST['username'] ?? '');
        $nama     = Security::cleanRaw($_POST['nama'] ?? '');
        $email    = Security::cleanEmail($_POST['email'] ?? '');
        $role     = Security::cleanRaw($_POST['role'] ?? 'admin');
        $password = $_POST['password'] ?? '';

        $allowedRoles = ['superadmin','admin','verifikator'];
        if (!in_array($role, $allowedRoles, true)) $role = 'admin';

        // Validasi
        $errors = [];
        if (!$username || strlen($username) < 4) $errors[] = 'Username minimal 4 karakter.';
        if (!$nama) $errors[] = 'Nama wajib diisi.';
        if (!$email) $errors[] = 'Email tidak valid.';
        if (strlen($password) < 8) $errors[] = 'Password minimal 8 karakter.';
        if ($userModel->findBy('username', $username)) $errors[] = 'Username sudah digunakan.';
        if ($email && $userModel->findBy('email', $email)) $errors[] = 'Email sudah digunakan.';

        if (!empty($errors)) {
            Session::flash('error', implode(' ', $errors));
            $this->redirect('/admin/users');
        }

        $userModel->insert([
            'username'       => $username,
            'nama'           => $nama,
            'email'          => $email,
            'password_hash'  => password_hash($password, PASSWORD_BCRYPT, ['cost'=>12]),
            'role'           => $role,
            'is_aktif'       => 1,
        ]);

        AuditLog::log('CREATE', 'users', 0, [], ['username'=>$username,'role'=>$role]);
        Session::flash('success', 'User berhasil ditambahkan.');
        $this->redirect('/admin/users');
    }

    public function resetPassword(string $id): void
    {
        $this->verifyCsrf();
        $userId   = (int)$id;
        $password = $_POST['password'] ?? '';

        if (strlen($password) < 8) {
            Session::flash('error', 'Password minimal 8 karakter.');
            $this->redirect('/admin/users');
        }

        (new UserModel())->update($userId, [
            'password_hash' => password_hash($password, PASSWORD_BCRYPT, ['cost'=>12]),
        ]);

        AuditLog::log('RESET_PASSWORD', 'users', $userId);
        Session::flash('success', 'Password berhasil direset.');
        $this->redirect('/admin/users');
    }

    public function toggle(string $id): void
    {
        $this->verifyCsrf();
        $userId = (int)$id;
        if ($userId === Auth::id()) {
            Session::flash('error', 'Tidak bisa menonaktifkan akun sendiri.');
            $this->redirect('/admin/users');
        }

        $userModel = new UserModel();
        $user = $userModel->findById($userId);
        if (!$user) { Session::flash('error', 'User tidak ditemukan.'); $this->redirect('/admin/users'); }

        $newStatus = $user['is_aktif'] ? 0 : 1;
        $userModel->update($userId, ['is_aktif' => $newStatus]);

        AuditLog::log($newStatus ? 'ACTIVATE' : 'DEACTIVATE', 'users', $userId);
        Session::flash('success', 'Status user diperbarui.');
        $this->redirect('/admin/users');
    }

    public function delete(string $id): void
    {
        $this->verifyCsrf();
        $userId = (int)$id;
        if ($userId === Auth::id()) {
            Session::flash('error', 'Tidak bisa menghapus akun sendiri.');
            $this->redirect('/admin/users');
        }

        (new UserModel())->update($userId, ['is_aktif' => 0]);
        AuditLog::log('DELETE', 'users', $userId);
        Session::flash('success', 'User berhasil dihapus.');
        $this->redirect('/admin/users');
    }
}

/**
 * PendaftarController — Dashboard & fitur untuk pendaftar yang sudah login
 */
class PendaftarController extends Controller
{
    private PendaftarModel $pendaftarModel;
    private DokumenModel   $dokumenModel;

    public function __construct()
    {
        parent::__construct();
        Auth::requireRole(['pendaftar']);
    }

    /** GET /pendaftar — Dashboard pendaftar */
    public function dashboard(): void
    {
        $this->pendaftarModel = new PendaftarModel();
        $this->dokumenModel   = new DokumenModel();

        $pendaftar = $this->pendaftarModel->getByUserId(Auth::id());
        if (!$pendaftar) {
            Session::flash('error', 'Data pendaftaran tidak ditemukan.');
            $this->redirect('/login');
        }

        $dokumen = $this->dokumenModel->getByPendaftar($pendaftar['id']);
        $riwayat = $this->getVerifikasiLog($pendaftar['id']);

        $this->view('pendaftar/dashboard', [
            'page_title' => 'Dashboard Pendaftar',
            'pendaftar'  => $pendaftar,
            'dokumen'    => $dokumen,
            'riwayat'    => $riwayat,
        ]);
    }

    /** GET /pendaftar/berkas — Halaman upload/kelola berkas */
    public function berkas(): void
    {
        $this->pendaftarModel = new PendaftarModel();
        $this->dokumenModel   = new DokumenModel();

        $pendaftar = $this->pendaftarModel->getByUserId(Auth::id());
        if (!$pendaftar) { $this->redirect('/login'); }

        // Hanya bisa upload jika status draft/revisi
        $canUpload = in_array($pendaftar['status'], ['draft', 'revisi', 'menunggu'], true);

        $dokumen     = $this->dokumenModel->getByPendaftar($pendaftar['id']);
        $dokumenTypes = $this->dokumenModel->getDokumenTypes();

        // Map dokumen yang sudah ada
        $uploaded = [];
        foreach ($dokumen as $d) {
            $uploaded[$d['jenis_dokumen']] = $d;
        }

        $this->view('pendaftar/berkas', [
            'page_title'   => 'Kelola Berkas',
            'pendaftar'    => $pendaftar,
            'dokumen'      => $dokumen,
            'uploaded'     => $uploaded,
            'dokumen_types'=> $dokumenTypes,
            'can_upload'   => $canUpload,
            'csrf'         => Security::generateCsrf(),
        ]);
    }

    /** POST /pendaftar/upload — Upload dokumen via AJAX */
    public function uploadDokumen(): void
    {
        header('Content-Type: application/json');
        $this->verifyCsrfJson();

        $this->pendaftarModel = new PendaftarModel();
        $this->dokumenModel   = new DokumenModel();

        $pendaftar = $this->pendaftarModel->getByUserId(Auth::id());
        if (!$pendaftar) {
            echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan.']);
            exit;
        }

        $jenis = Security::cleanRaw($_POST['jenis_dokumen'] ?? '');
        $allowedJenis = array_keys($this->dokumenModel->getDokumenTypes());
        if (!in_array($jenis, $allowedJenis, true)) {
            echo json_encode(['success' => false, 'message' => 'Jenis dokumen tidak valid.']);
            exit;
        }

        if (empty($_FILES['dokumen']) || $_FILES['dokumen']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'File tidak diterima.']);
            exit;
        }

        // Validasi file
        $allowedMime = ['application/pdf','image/jpeg','image/jpg','image/png'];
        $allowedExt  = ['pdf','jpg','jpeg','png'];
        $maxSize     = 5 * 1024 * 1024; // 5MB

        $file     = $_FILES['dokumen'];
        $finfo    = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($mimeType, $allowedMime, true) || !in_array($ext, $allowedExt, true)) {
            echo json_encode(['success' => false, 'message' => 'Format file tidak diizinkan. Gunakan PDF/JPG/PNG.']);
            exit;
        }
        if ($file['size'] > $maxSize) {
            echo json_encode(['success' => false, 'message' => 'Ukuran file maksimal 5MB.']);
            exit;
        }

        // Generate UUID filename
        $uuid     = sprintf('%s-%s-%s-%s-%s',
            bin2hex(random_bytes(4)), bin2hex(random_bytes(2)),
            bin2hex(random_bytes(2)), bin2hex(random_bytes(2)),
            bin2hex(random_bytes(6))
        );
        $filename = $uuid . '.' . $ext;
        $uploadDir = STORAGE_PATH . '/uploads/dokumen/' . $pendaftar['id'] . '/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $dest = $uploadDir . $filename;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan file.']);
            exit;
        }

        // Hapus file lama jika ada
        $existing = null;
        foreach ($this->dokumenModel->getByPendaftar($pendaftar['id']) as $d) {
            if ($d['jenis_dokumen'] === $jenis) { $existing = $d; break; }
        }
        if ($existing) {
            $oldPath = STORAGE_PATH . '/uploads/dokumen/' . $pendaftar['id'] . '/' . $existing['nama_file'];
            if (file_exists($oldPath)) @unlink($oldPath);
            $this->dokumenModel->deleteByPendaftarAndType($pendaftar['id'], $jenis);
        }

        // Simpan record ke DB — sesuai skema tabel dokumen
        $relPath = 'dokumen/' . $pendaftar['id'] . '/' . $filename;
        $this->dokumenModel->insert([
            'pendaftar_id'   => $pendaftar['id'],
            'jenis_dokumen'  => $jenis,
            'nama_file_asli' => $file['name'],
            'nama_file'      => $filename,
            'path_file'      => $relPath,
            'ukuran_file'    => $file['size'],
            'mime_type'      => $mimeType,
        ]);

        AuditLog::log('UPLOAD', 'dokumen', $pendaftar['id'], [], ['jenis' => $jenis]);

        echo json_encode([
            'success'  => true,
            'message'  => 'Berkas berhasil diunggah.',
            'filename' => $filename,
            'jenis'    => $jenis,
        ]);
        exit;
    }

    /** GET /pendaftar/cetak/{id} — Cetak bukti pendaftaran */
    public function cetak(string $id): void
    {
        $this->pendaftarModel = new PendaftarModel();
        $this->dokumenModel   = new DokumenModel();

        $pendaftar = $this->pendaftarModel->getByUserId(Auth::id());
        if (!$pendaftar || $pendaftar['id'] != (int)$id) {
            $this->redirect('/pendaftar');
        }

        $dokumen = $this->dokumenModel->getByPendaftar($pendaftar['id']);

        $this->view('pendaftar/cetak', [
            'layout'    => 'layouts/print',
            'page_title'=> 'Bukti Pendaftaran - ' . $pendaftar['nomor_pendaftaran'],
            'pendaftar' => $pendaftar,
            'dokumen'   => $dokumen,
        ]);
    }

    /** Helper: ambil riwayat verifikasi */
    private function getVerifikasiLog(int $pendaftarId): array
    {
        try {
            $db   = Database::getInstance();
            $stmt = $db->prepare(
                "SELECT vl.*, u.nama AS nama_verifikator
                 FROM verifikasi_log vl
                 LEFT JOIN users u ON u.id = vl.admin_id
                 WHERE vl.pendaftar_id = ?
                 ORDER BY vl.created_at DESC
                 LIMIT 20"
            );
            $stmt->execute([$pendaftarId]);
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            return [];
        }
    }

    /** Helper: CSRF check untuk endpoint JSON */
    private function verifyCsrfJson(): void
    {
        $token = $_POST['csrf_token'] ?? ($_POST['_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ''));
        if (!Security::verifyCsrf($token)) {
            echo json_encode(['success' => false, 'message' => 'Token keamanan tidak valid.']);
            exit;
        }
    }
}
