<?php
require_once APP_PATH . '/controllers/BaseController.php';

/**
 * AdminController - Dashboard Admin PMB
 */
class AdminController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Auth::requireRole(['superadmin','admin','verifikator']);
    }

    public function dashboard(): void
    {
        $taModel   = new TahunAkademikModel();
        $tahunAktif= $taModel->getAktif();

        $pendaftarModel = new PendaftarModel();
        $stat    = $tahunAktif ? $pendaftarModel->getStatistik($tahunAktif['id']) : [];
        $perProdi= $tahunAktif ? $pendaftarModel->getPerProdi($tahunAktif['id']) : [];

        $this->view('admin/dashboard', [
            'layout'      => 'layouts/admin',
            'page_title'  => 'Dashboard',
            'tahun_aktif' => $tahunAktif,
            'stat'        => $stat,
            'per_prodi'   => $perProdi,
        ]);
    }

    public function pendaftar(): void
    {
        $taModel   = new TahunAkademikModel();
        $prodiModel= new ProdiModel();
        $pendaftarModel = new PendaftarModel();

        $filters = [
            'tahun_akademik_id' => Security::cleanInt($_GET['ta'] ?? 0) ?: null,
            'program_studi_id'  => Security::cleanInt($_GET['prodi'] ?? 0) ?: null,
            'status'            => Security::cleanRaw($_GET['status'] ?? ''),
            'q'                 => Security::cleanRaw($_GET['q'] ?? ''),
        ];
        $page   = max(1, Security::cleanInt($_GET['page'] ?? 1));
        $result = $pendaftarModel->searchAdmin($filters, $page, 20);

        $this->view('admin/pendaftar', [
            'layout'       => 'layouts/admin',
            'page_title'   => 'Data Pendaftar',
            'result'       => $result,
            'filters'      => $filters,
            'tahun_list'   => $taModel->findAll([], 'created_at DESC'),
            'prodi_list'   => $prodiModel->getAktif(),
        ]);
    }

    public function detail(string $id): void
    {
        $pendaftarModel = new PendaftarModel();
        $dokModel       = new DokumenModel();
        $verModel       = new VerifikasiLogModel();

        $pendaftar = $pendaftarModel->getWithDetails(Security::cleanInt($id));
        if (!$pendaftar) {
            Session::flash('error', 'Data tidak ditemukan.');
            $this->redirect('/admin/pendaftar');
        }

        $dokumen    = $dokModel->getByPendaftar($pendaftar['id']);
        $verLog     = $verModel->getByPendaftar($pendaftar['id']);

        $this->view('admin/detail', [
            'layout'    => 'layouts/admin',
            'page_title'=> 'Detail Pendaftar',
            'pendaftar' => $pendaftar,
            'dokumen'   => $dokumen,
            'ver_log'   => $verLog,
            'csrf'      => Security::generateCsrf(),
            'dok_types' => (new DokumenModel())->getDokumenTypes(),
        ]);
    }

    public function verifikasi(string $id): void
    {
        Auth::requireRole(['superadmin','admin','verifikator']);
        $this->verifyCsrf();

        $pendaftarId = Security::cleanInt($id);
        $status      = Security::cleanRaw($_POST['status'] ?? '');
        $catatan     = Security::cleanRaw($_POST['catatan'] ?? '');

        $allowedStatus = ['menunggu','diterima','revisi','ditolak'];
        if (!in_array($status, $allowedStatus, true)) {
            Session::flash('error', 'Status tidak valid.');
            $this->redirect('/admin/pendaftar/' . $pendaftarId);
        }

        $pendaftarModel = new PendaftarModel();
        $pendaftar      = $pendaftarModel->findById($pendaftarId);
        if (!$pendaftar) {
            Session::flash('error', 'Data tidak ditemukan.');
            $this->redirect('/admin/pendaftar');
        }

        $sebelum = $pendaftar['status'];
        $pendaftarModel->update($pendaftarId, [
            'status'              => $status,
            'catatan_verifikasi'  => $catatan,
            'tanggal_verifikasi'  => date('Y-m-d H:i:s'),
            'diverifikasi_oleh'   => Auth::id(),
        ]);

        (new VerifikasiLogModel())->insert([
            'pendaftar_id'  => $pendaftarId,
            'admin_id'      => Auth::id(),
            'status_sebelum'=> $sebelum,
            'status_sesudah'=> $status,
            'catatan'       => $catatan,
        ]);

        AuditLog::log('VERIFIKASI', 'pendaftar', $pendaftarId, ['status'=>$sebelum], ['status'=>$status,'catatan'=>$catatan]);
        Session::flash('success', 'Status pendaftar berhasil diperbarui.');
        $this->redirect('/admin/pendaftar/' . $pendaftarId);
    }

    public function export(): void
    {
        Auth::requireRole(['superadmin','admin']);

        $taId   = Security::cleanInt($_GET['ta'] ?? 0);
        $format = Security::cleanRaw($_GET['format'] ?? 'csv');
        $pendaftarModel = new PendaftarModel();

        $filters = $taId ? ['tahun_akademik_id' => $taId] : [];
        $data = $pendaftarModel->findAll($filters, 'created_at DESC', 5000);

        if ($format === 'csv') {
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="pendaftar_pmb_' . date('Ymd') . '.csv"');
            echo "\xEF\xBB\xBF";

            $out = fopen('php://output', 'w');
            fputcsv($out, ['No','Nomor Pendaftaran','Nama Lengkap','Jenis Kelamin','Tempat Lahir','Tanggal Lahir','Nomor HP','Alamat','Nama Ibu','Status','Tanggal Submit']);
            foreach ($data as $i => $row) {
                fputcsv($out, [
                    $i+1,
                    $row['nomor_pendaftaran'],
                    $row['nama_lengkap'],
                    $row['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan',
                    $row['tempat_lahir'],
                    $row['tanggal_lahir'],
                    $row['nomor_hp'],
                    $row['alamat'],
                    $row['nama_ibu_kandung'],
                    ucfirst($row['status']),
                    $row['tanggal_submit'] ?? '-',
                ]);
            }
            fclose($out);
            exit;
        }

        $this->redirect('/admin/pendaftar');
    }

    public function cetakPendaftar(string $id): void
    {
        $pendaftar = (new PendaftarModel())->getWithDetails(Security::cleanInt($id));
        if (!$pendaftar) {
            Session::flash('error', 'Data tidak ditemukan.');
            $this->redirect('/admin/pendaftar');
        }
        $dokumen = (new DokumenModel())->getByPendaftar($pendaftar['id']);
        $this->view('pendaftar/cetak', [
            'layout'     => 'layouts/print',
            'page_title' => 'Cetak - ' . $pendaftar['nomor_pendaftaran'],
            'pendaftar'  => $pendaftar,
            'dokumen'    => $dokumen,
        ]);
    }

    public function downloadDokumen(string $id): void
    {
        Auth::requireRole(['superadmin','admin','verifikator']);
        $dokModel = new DokumenModel();
        $dok = $dokModel->findById(Security::cleanInt($id));
        if (!$dok) { http_response_code(404); exit('File tidak ditemukan.'); }

        $filePath = STORAGE_PATH . '/uploads/' . $dok['path_file'];
        if (!file_exists($filePath)) {
            http_response_code(404);
            exit('File tidak ditemukan di server.');
        }

        $realPath    = realpath($filePath);
        $realStorage = realpath(STORAGE_PATH);
        if (!$realPath || strpos($realPath, $realStorage) !== 0) {
            http_response_code(403); exit('Akses ditolak.');
        }

        AuditLog::log('DOWNLOAD', 'dokumen', (int)$id);
        header('Content-Type: ' . $dok['mime_type']);
        header('Content-Disposition: inline; filename="' . basename($dok['nama_file_asli']) . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: private, max-age=0');
        readfile($filePath);
        exit;
    }

    public function apiStatistik(): void
    {
        Auth::requireRole(['superadmin','admin','verifikator']);
        $taId = Security::cleanInt($_GET['ta'] ?? 0);
        if (!$taId) {
            $ta   = (new TahunAkademikModel())->getAktif();
            $taId = $ta ? $ta['id'] : 0;
        }
        $model = new PendaftarModel();
        $this->json([
            'stat'      => $model->getStatistik($taId),
            'per_prodi' => $model->getPerProdi($taId),
        ]);
    }

    public function apiPendaftar(): void
    {
        Auth::requireRole(['superadmin','admin','verifikator']);
        $filters = [
            'tahun_akademik_id' => Security::cleanInt($_GET['ta'] ?? 0) ?: null,
            'status'            => Security::cleanRaw($_GET['status'] ?? ''),
            'q'                 => Security::cleanRaw($_GET['q'] ?? ''),
        ];
        $page   = max(1, Security::cleanInt($_GET['page'] ?? 1));
        $result = (new PendaftarModel())->searchAdmin($filters, $page, 20);
        $this->json($result);
    }
}

/**
 * VerifikasiLogModel
 */
class VerifikasiLogModel extends Model
{
    protected string $table = 'verifikasi_log';

    public function getByPendaftar(int $pendaftarId): array
    {
        $stmt = $this->db->prepare(
            "SELECT vl.*, u.nama AS admin_nama
             FROM verifikasi_log vl
             JOIN users u ON u.id = vl.admin_id
             WHERE vl.pendaftar_id = ?
             ORDER BY vl.created_at DESC"
        );
        $stmt->execute([$pendaftarId]);
        return $stmt->fetchAll();
    }
}

/**
 * TahunAkademikController
 */
class TahunAkademikController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Auth::requireRole(['superadmin','admin']);
    }

    public function index(): void
    {
        $taModel = new TahunAkademikModel();
        $list    = $taModel->findAll([], 'id DESC');
        $this->view('admin/tahun-akademik', [
            'layout'     => 'layouts/admin',
            'page_title' => 'Tahun Akademik',
            'list'       => $list,
            'csrf'       => Security::generateCsrf(),
        ]);
    }

    public function store(): void
    {
        $this->verifyCsrf();
        $kode = Security::cleanRaw($_POST['kode'] ?? '');
        $nama = Security::cleanRaw($_POST['nama'] ?? '');
        $buka = Security::cleanRaw($_POST['tanggal_buka'] ?? '') ?: null;
        $tutup= Security::cleanRaw($_POST['tanggal_tutup'] ?? '') ?: null;

        if (!preg_match('/^\d{4}\/\d{4}$/', $kode)) {
            Session::flash('error', 'Format kode salah. Gunakan: 2026/2027');
            $this->redirect('/admin/tahun-akademik');
        }

        (new TahunAkademikModel())->insert([
            'kode'          => $kode,
            'nama'          => $nama ?: 'Tahun Akademik ' . $kode,
            'aktif'         => 0,
            'tanggal_buka'  => $buka,
            'tanggal_tutup' => $tutup,
            'created_by'    => Auth::id(),
        ]);

        AuditLog::log('CREATE', 'tahun_akademik');
        Session::flash('success', 'Tahun akademik berhasil ditambahkan.');
        $this->redirect('/admin/tahun-akademik');
    }

    public function setAktif(): void
    {
        $this->verifyCsrf();
        $id = Security::cleanInt($_POST['id'] ?? 0);
        (new TahunAkademikModel())->setAktif($id);
        AuditLog::log('SET_AKTIF', 'tahun_akademik', $id);
        Session::flash('success', 'Tahun akademik aktif diperbarui.');
        $this->redirect('/admin/tahun-akademik');
    }

    public function tutup(): void
    {
        $this->verifyCsrf();
        $id = Security::cleanInt($_POST['id'] ?? 0);
        (new TahunAkademikModel())->update($id, ['aktif'=>0]);
        AuditLog::log('TUTUP', 'tahun_akademik', $id);
        Session::flash('success', 'PMB ditutup.');
        $this->redirect('/admin/tahun-akademik');
    }

    public function aktifkan(string $id): void
    {
        $this->verifyCsrf();
        (new TahunAkademikModel())->setAktif((int)$id);
        AuditLog::log('SET_AKTIF', 'tahun_akademik', (int)$id);
        Session::flash('success', 'Tahun akademik berhasil diaktifkan.');
        $this->redirect('/admin/tahun-akademik');
    }

    public function hapus(string $id): void
    {
        $this->verifyCsrf();
        (new TahunAkademikModel())->update((int)$id, ['aktif' => 0]);
        AuditLog::log('TUTUP', 'tahun_akademik', (int)$id);
        Session::flash('success', 'Tahun akademik ditutup.');
        $this->redirect('/admin/tahun-akademik');
    }

    public function update(string $id): void
    {
        $this->verifyCsrf();
        $kode = Security::cleanRaw($_POST['kode'] ?? '');
        $nama = Security::cleanRaw($_POST['nama'] ?? '');
        $buka = Security::cleanRaw($_POST['tanggal_buka'] ?? '') ?: null;
        $tutup= Security::cleanRaw($_POST['tanggal_tutup'] ?? '') ?: null;

        $model  = new TahunAkademikModel();
        $before = $model->findById((int)$id);
        $model->update((int)$id, [
            'kode'          => $kode,
            'nama'          => $nama,
            'tanggal_buka'  => $buka,
            'tanggal_tutup' => $tutup,
        ]);
        AuditLog::log('UPDATE', 'tahun_akademik', (int)$id, $before, ['kode'=>$kode,'nama'=>$nama]);
        Session::flash('success', 'Tahun akademik diperbarui.');
        $this->redirect('/admin/tahun-akademik');
    }
}

/**
 * ProdiController
 */
class ProdiController extends Controller
{
    public function __construct() { parent::__construct(); Auth::requireRole(['superadmin','admin']); }

    public function index(): void
    {
        $list = (new ProdiModel())->findAll([], 'urutan,jenjang,nama_prodi');
        $this->view('admin/prodi', [
            'layout'     => 'layouts/admin',
            'page_title' => 'Program Studi',
            'list'       => $list,
            'csrf'       => Security::generateCsrf(),
        ]);
    }

    public function store(): void
    {
        $this->verifyCsrf();
        $data = [
            'nama_prodi'=> Security::cleanRaw($_POST['nama'] ?? ''),
            'singkatan' => strtoupper(Security::cleanRaw($_POST['singkatan'] ?? '')),
            'jenjang'   => Security::cleanRaw($_POST['jenjang'] ?? 'S1'),
            'fakultas'  => Security::cleanRaw($_POST['fakultas'] ?? ''),
            'gelar'     => Security::cleanRaw($_POST['gelar'] ?? ''),
            'is_aktif'  => 1,
            'urutan'    => Security::cleanInt($_POST['urutan'] ?? 0),
        ];
        (new ProdiModel())->insert($data);
        Session::flash('success', 'Program studi ditambahkan.');
        $this->redirect('/admin/prodi');
    }

    public function update(string $id): void
    {
        $this->verifyCsrf();
        $model = new ProdiModel();
        $data  = [
            'nama_prodi'=> Security::cleanRaw($_POST['nama'] ?? ''),
            'singkatan' => strtoupper(Security::cleanRaw($_POST['singkatan'] ?? '')),
            'jenjang'   => Security::cleanRaw($_POST['jenjang'] ?? 'S1'),
            'fakultas'  => Security::cleanRaw($_POST['fakultas'] ?? ''),
            'gelar'     => Security::cleanRaw($_POST['gelar'] ?? ''),
            'is_aktif'  => isset($_POST['aktif']) ? 1 : 0,
            'urutan'    => Security::cleanInt($_POST['urutan'] ?? 0),
        ];
        $before = $model->findById((int)$id);
        $model->update((int)$id, $data);
        AuditLog::log('UPDATE', 'prodi', (int)$id, $before, $data);
        Session::flash('success', 'Program studi diperbarui.');
        $this->redirect('/admin/prodi');
    }

    public function delete(string $id): void
    {
        $this->verifyCsrf();
        (new ProdiModel())->update((int)$id, ['is_aktif'=>0]);
        Session::flash('success', 'Program studi dinonaktifkan.');
        $this->redirect('/admin/prodi');
    }

    public function toggle(string $id): void
    {
        $this->verifyCsrf();
        $model = new ProdiModel();
        $prodi = $model->findById((int)$id);
        if (!$prodi) {
            Session::flash('error', 'Program studi tidak ditemukan.');
            $this->redirect('/admin/prodi');
        }
        $newStatus = $prodi['is_aktif'] ? 0 : 1;
        $model->update((int)$id, ['is_aktif' => $newStatus]);
        AuditLog::log($newStatus ? 'ACTIVATE' : 'DEACTIVATE', 'prodi', (int)$id);
        Session::flash('success', 'Status program studi diperbarui.');
        $this->redirect('/admin/prodi');
    }
}

/**
 * CmsController
 */
class CmsController extends Controller
{
    public function __construct() { parent::__construct(); Auth::requireRole(['superadmin']); }

    public function index(): void
    {
        $grouped = (new CmsModel())->getGrouped();
        $this->view('admin/pengaturan', [
            'layout'     => 'layouts/admin',
            'page_title' => 'Pengaturan Website',
            'grouped'    => $grouped,
            'csrf'       => Security::generateCsrf(),
        ]);
    }

    public function save(): void
    {
        $this->verifyCsrf();
        $cms = new CmsModel();
        foreach ($_POST as $k => $v) {
            if (in_array($k, ['csrf_token', '_token'], true)) continue;
            $cms->set(Security::cleanRaw($k), Security::cleanRaw($v));
        }

        if (!empty($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $err = Security::validateUpload($_FILES['logo']);
            if (empty($err)) {
                $path = Security::saveUpload($_FILES['logo'], 'images');
                $cms->set('logo_path', '/assets/images/' . basename($path));
                $src  = UPLOAD_PATH . '/images/' . basename($path);
                $dest = PUBLIC_PATH . '/assets/images/logo.' . pathinfo($path, PATHINFO_EXTENSION);
                if (file_exists($src)) copy($src, $dest);
            }
        }

        AuditLog::log('UPDATE', 'cms_settings');
        Session::flash('success', 'Pengaturan berhasil disimpan.');
        $this->redirect('/admin/pengaturan');
    }

    public function uploadLogo(): void
    {
        $this->verifyCsrf();
        if (empty($_FILES['logo'])) {
            Session::flash('error', 'File tidak ditemukan.');
            $this->redirect('/admin/pengaturan');
        }

        $err = Security::validateUpload($_FILES['logo']);
        if (!empty($err)) {
            Session::flash('error', implode(' ', $err));
            $this->redirect('/admin/pengaturan');
        }

        $path = Security::saveUpload($_FILES['logo'], 'images');
        (new CmsModel())->set('logo_path', '/assets/images/' . basename($path));

        $src  = UPLOAD_PATH . '/images/' . basename($path);
        $dest = PUBLIC_PATH . '/assets/images/logo.' . pathinfo($path, PATHINFO_EXTENSION);
        copy($src, $dest);

        Session::flash('success', 'Logo berhasil diunggah.');
        $this->redirect('/admin/pengaturan');
    }
}