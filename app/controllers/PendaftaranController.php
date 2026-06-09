<?php
require_once APP_PATH . '/controllers/BaseController.php';

/**
 * PendaftaranController
 * Handle proses pendaftaran mahasiswa baru
 */
class PendaftaranController extends Controller
{
    private TahunAkademikModel $taModel;
    private ProdiModel         $prodiModel;
    private PendaftarModel     $pendaftarModel;
    private UserModel          $userModel;
    private BiayaModel         $biayaModel;

    public function __construct()
    {
        parent::__construct();
        $this->taModel       = new TahunAkademikModel();
        $this->prodiModel    = new ProdiModel();
        $this->pendaftarModel= new PendaftarModel();
        $this->userModel     = new UserModel();
        $this->biayaModel    = new BiayaModel();
    }

    public function form(): void
    {
        $tahunAktif = $this->taModel->getAktif();
        if (!$tahunAktif) {
            Session::flash('info', 'Pendaftaran saat ini belum dibuka. Pantau informasi terbaru kami.');
            $this->redirect('/');
        }

        $prodiList  = $this->prodiModel->getAktifWithBiaya($tahunAktif['id']);
        $biayaList  = $this->biayaModel->getByTA($tahunAktif['id']);

        $this->view('public/daftar', [
            'page_title'  => 'Formulir Pendaftaran',
            'tahun_aktif' => $tahunAktif,
            'prodi_list'  => $prodiList,
            'biaya_list'  => $biayaList,
            'csrf'        => Security::generateCsrf(),
        ]);
    }

    public function store(): void
    {
        $this->verifyCsrf();

        // Rate limit pendaftaran
        $ip = Security::getClientIp();
        if (!Security::checkRateLimit('register', $ip, RATE_REGISTER_MAX, RATE_REGISTER_WINDOW)) {
            $this->json(['success'=>false,'message'=>'Terlalu banyak percobaan. Coba lagi nanti.'], 429);
        }

        $tahunAktif = $this->taModel->getAktif();
        if (!$tahunAktif) {
            $this->json(['success'=>false,'message'=>'Pendaftaran belum dibuka.'], 400);
        }

        $input = $this->sanitizeInput($_POST);

        // Validasi
        // Normalisasi nomor HP: hapus spasi, tanda hubung, kurung
        if (!empty($input['nomor_hp'])) {
            $input['nomor_hp'] = preg_replace('/[^0-9+]/', '', $input['nomor_hp']);
        }

        $errors = $this->validate($input, [
            'nama_lengkap'    => 'required|min:3|max:150',
            'tempat_lahir'    => 'required|min:2|max:100',
            'tanggal_lahir'   => 'required',
            'jenis_kelamin'   => 'required',
            'nomor_hp'        => 'required|min:8|max:16',
            'alamat'          => 'required|min:5',
            'nama_ibu_kandung'=> 'required|min:3|max:150',
            'program_studi_id'=> 'required|numeric',
            'password'        => 'required|min:8',
        ]);

        if (!empty($errors)) {
            $this->json(['success'=>false,'errors'=>$errors], 422);
        }

        // Validasi prodi
        $prodi = $this->prodiModel->findById((int)$input['program_studi_id']);
        if (!$prodi || !$prodi['is_aktif']) {
            $this->json(['success'=>false,'message'=>'Program studi tidak valid.'], 400);
        }

        // Konfirmasi password
        if ($input['password'] !== ($_POST['password_confirm'] ?? $_POST['password_confirmation'] ?? '')) {
            $this->json(['success'=>false,'errors'=>['password_confirmation'=>'Password tidak cocok.']], 422);
        }

        try {
            $db = Database::getInstance();
            $db->beginTransaction();

            // Generate nomor pendaftaran
            $nomorPendaftaran = $this->pendaftarModel->generateNomor($tahunAktif['kode']);

            // Buat user
            $userId = $this->userModel->insert([
                'username'      => $nomorPendaftaran,
                'password_hash' => Security::hashPassword($input['password']),
                'role'          => 'pendaftar',
                'nama'          => $input['nama_lengkap'],
                'is_aktif'      => 1,
            ]);

            // Buat data pendaftar
            $pendaftarData = [
                'user_id'           => $userId,
                'tahun_akademik_id' => $tahunAktif['id'],
                'program_studi_id'  => (int) $input['program_studi_id'],
                'nomor_pendaftaran' => $nomorPendaftaran,
                'nama_lengkap'      => $input['nama_lengkap'],
                'tempat_lahir'      => $input['tempat_lahir'],
                'tanggal_lahir'     => $input['tanggal_lahir'],
                'jenis_kelamin'     => $input['jenis_kelamin'],
                'nomor_hp'          => $input['nomor_hp'],
                'alamat'            => $input['alamat'],
                'nama_ibu_kandung'  => $input['nama_ibu_kandung'],
                'status'            => 'draft',
            ];

            // Data tambahan S2
            if ($prodi['jenjang'] === 'S2') {
                $pendaftarData['asal_universitas'] = $input['asal_universitas'] ?? null;
                $pendaftarData['tahun_lulus_s1']   = !empty($input['tahun_lulus_s1']) ? (int)$input['tahun_lulus_s1'] : null;
                $pendaftarData['ipk_s1']            = !empty($input['ipk_s1']) ? (float)$input['ipk_s1'] : null;
            }

            // Cek promo S2
            if ($prodi['jenjang'] === 'S2') {
                $stmtPromo = $db->prepare(
                    "SELECT id FROM promo
                     WHERE tahun_akademik_id=? AND (program_studi_id=? OR program_studi_id IS NULL)
                     AND aktif=1 AND (berlaku_sampai IS NULL OR berlaku_sampai > NOW())
                     AND (kuota=0 OR terpakai < kuota)
                     LIMIT 1"
                );
                $stmtPromo->execute([$tahunAktif['id'], $prodi['id']]);
                $promo = $stmtPromo->fetch();
                if ($promo) {
                    $pendaftarData['promo_id'] = $promo['id'];
                    $db->prepare("UPDATE promo SET terpakai=terpakai+1 WHERE id=?")->execute([$promo['id']]);
                }
            }

            $pendaftarId = $this->pendaftarModel->insert($pendaftarData);

            $db->commit();

            // Login otomatis
            $user = $this->userModel->findById($userId);
            Auth::login($user);
            AuditLog::log('REGISTER', 'pendaftaran', $pendaftarId, null, $pendaftarData);

            $this->json([
                'success'         => true,
                'nomor_pendaftaran'=> $nomorPendaftaran,
                'redirect'        => BASE_URL . '/daftar/sukses/' . urlencode($nomorPendaftaran),
            ]);
        } catch (Exception $e) {
            $db->rollBack();
            Logger::error('Pendaftaran gagal: ' . $e->getMessage());
            $this->json(['success'=>false,'message'=>'Terjadi kesalahan. Silakan coba lagi.'], 500);
        }
    }

    public function upload(): void
    {
        Auth::requireRole(['pendaftar'], '/login');
        $this->verifyCsrf();

        // Rate limit upload
        $ip = Security::getClientIp();
        if (!Security::checkRateLimit('upload', $ip, RATE_UPLOAD_MAX, RATE_UPLOAD_WINDOW)) {
            $this->json(['success'=>false,'message'=>'Terlalu banyak upload. Coba lagi nanti.'], 429);
        }

        $jenis = Security::cleanRaw($_POST['jenis_dokumen'] ?? '');
        $dokModel = new DokumenModel();
        $allowedJenis = array_keys($dokModel->getDokumenTypes());

        if (!in_array($jenis, $allowedJenis, true)) {
            $this->json(['success'=>false,'message'=>'Jenis dokumen tidak valid.'], 400);
        }

        if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $this->json(['success'=>false,'message'=>'File tidak ditemukan atau terjadi kesalahan upload.'], 400);
        }

        // Validasi file
        $uploadErrors = Security::validateUpload($_FILES['file']);
        if (!empty($uploadErrors)) {
            $this->json(['success'=>false,'message'=>implode(' ', $uploadErrors)], 422);
        }

        $pendaftarModel = new PendaftarModel();
        $pendaftar = $pendaftarModel->getByUserId(Auth::id());
        if (!$pendaftar) {
            $this->json(['success'=>false,'message'=>'Data pendaftar tidak ditemukan.'], 404);
        }

        try {
            // Hapus dokumen lama jika ada
            $dokModel->deleteByPendaftarAndType($pendaftar['id'], $jenis);

            // Simpan file
            $relPath = Security::saveUpload($_FILES['file'], 'dokumen/' . $pendaftar['id']);

            // Simpan ke DB
            $dokModel->insert([
                'pendaftar_id'   => $pendaftar['id'],
                'jenis_dokumen'  => $jenis,
                'nama_file_asli' => Security::cleanRaw($_FILES['file']['name']),
                'nama_file'      => basename($relPath),
                'path_file'      => $relPath,
                'mime_type'      => $_FILES['file']['type'],
                'ukuran_file'    => $_FILES['file']['size'],
                'status'         => 'menunggu',
            ]);

            AuditLog::log('UPLOAD_DOKUMEN', 'dokumen', $pendaftar['id'], null, ['jenis'=>$jenis]);

            $this->json(['success'=>true,'message'=>'Dokumen berhasil diunggah.']);
        } catch (Exception $e) {
            Logger::error('Upload gagal: ' . $e->getMessage());
            $this->json(['success'=>false,'message'=>'Gagal menyimpan file.'], 500);
        }
    }

    public function success(string $nomor): void
    {
        $pendaftar = (new PendaftarModel())->getByNomor(Security::cleanRaw($nomor));
        if (!$pendaftar) {
            $this->redirect('/');
        }
        $this->view('public/daftar-sukses', [
            'page_title' => 'Pendaftaran Berhasil',
            'pendaftar'  => $pendaftar,
        ]);
    }

    private function sanitizeInput(array $post): array
    {
        $fields = ['nama_lengkap','tempat_lahir','tanggal_lahir','jenis_kelamin',
                   'nomor_hp','alamat','nama_ibu_kandung','program_studi_id',
                   'password','asal_universitas','tahun_lulus_s1','ipk_s1'];
        $out = [];
        foreach ($fields as $f) {
            $out[$f] = in_array($f, ['password']) ? ($post[$f] ?? '') : Security::cleanRaw($post[$f] ?? '');
        }
        return $out;
    }
}