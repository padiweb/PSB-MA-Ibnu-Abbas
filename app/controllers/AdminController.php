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
            'pendaftar'    => $result['data'] ?? [],
            'pagination'   => [
                'total'       => $result['total'] ?? 0,
                'per_page'    => $result['per_page'] ?? 20,
                'page'        => $result['current_page'] ?? 1,
                'total_pages' => $result['last_page'] ?? 1,
            ],
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
            'verifikasi_log' => $verLog,
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

        $taId = Security::cleanInt($_GET['ta'] ?? 0);
        $pm   = new PendaftarModel();
        $data = $pm->getForExport($taId);

        // Nama file
        $taModel  = new TahunAkademikModel();
        $taLabel  = '';
        if ($taId) {
            $ta = $taModel->findById($taId);
            $taLabel = $ta ? '_' . preg_replace('/[^a-zA-Z0-9]/', '', $ta['kode']) : '';
        }
        $filename = 'data_pendaftar_pmb' . $taLabel . '_' . date('Ymd_His') . '.xls';

        // Header Excel (format XML Spreadsheet - dibuka Excel & LibreOffice tanpa library)
        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM

        // Style
        $styleHeader = 'background:#1a3a6b;color:#ffffff;font-weight:bold;font-size:11pt;border:1px solid #cccccc;';
        $styleCell   = 'font-size:10pt;border:1px solid #dddddd;vertical-align:top;';
        $styleAlt    = 'font-size:10pt;border:1px solid #dddddd;background:#f8fafc;vertical-align:top;';

        $headers = [
            'No', 'Nomor Pendaftaran', 'Nama Lengkap', 'Jenis Kelamin',
            'Tempat Lahir', 'Tanggal Lahir', 'Email', 'Nomor HP', 'Alamat',
            'Nama Ibu Kandung', 'Program Studi', 'Jenjang', 'Fakultas', 'Gelar',
            'Tahun Akademik', 'Status', 'Tanggal Daftar', 'Tanggal Submit',
            'Catatan Verifikasi', 'Verifikator',
            'Asal Universitas (S2)', 'Tahun Lulus S1 (S2)', 'IPK S1 (S2)',
        ];

        $statusLabel = [
            'draft'    => 'Draft',
            'menunggu' => 'Menunggu Verifikasi',
            'diterima' => 'Diterima',
            'revisi'   => 'Perlu Revisi',
            'ditolak'  => 'Ditolak',
        ];

        ob_start();
        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        echo '<head><meta charset="UTF-8"><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>';
        echo '<x:Name>Data Pendaftar PMB</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions>';
        echo '</x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body>';
        echo '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse;font-family:Arial,sans-serif;">';

        // Title row
        echo '<tr><td colspan="' . count($headers) . '" style="background:#0f2754;color:#ffffff;font-size:14pt;font-weight:bold;text-align:center;padding:12px;">';
        echo 'DATA PENDAFTAR PMB MA\'HAD ALY IBNU ABBAS KARANGANYAR';
        if ($taId && isset($ta)) echo ' - ' . htmlspecialchars($ta['nama'] ?? '');
        echo '</td></tr>';

        // Subtitle
        echo '<tr><td colspan="' . count($headers) . '" style="background:#1a3a6b;color:#c9a227;font-size:10pt;text-align:center;padding:5px;">';
        echo 'Dicetak: ' . date('d F Y, H:i') . ' WIB | Total Pendaftar: ' . count($data);
        echo '</td></tr>';

        // Empty row
        echo '<tr><td colspan="' . count($headers) . '">&nbsp;</td></tr>';

        // Header kolom
        echo '<tr>';
        foreach ($headers as $h) {
            echo '<th style="' . $styleHeader . 'text-align:center;padding:8px;">' . htmlspecialchars($h) . '</th>';
        }
        echo '</tr>';

        // Data rows
        foreach ($data as $i => $row) {
            $style = ($i % 2 === 0) ? $styleCell : $styleAlt;
            echo '<tr>';
            echo '<td style="' . $style . 'text-align:center;">' . ($i + 1) . '</td>';
            echo '<td style="' . $style . '">' . htmlspecialchars($row['nomor_pendaftaran'] ?? '') . '</td>';
            echo '<td style="' . $style . '">' . htmlspecialchars($row['nama_lengkap'] ?? '') . '</td>';
            echo '<td style="' . $style . 'text-align:center;">' . ($row['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan') . '</td>';
            echo '<td style="' . $style . '">' . htmlspecialchars($row['tempat_lahir'] ?? '') . '</td>';
            echo '<td style="' . $style . 'text-align:center;">' . ($row['tanggal_lahir'] ? date('d/m/Y', strtotime($row['tanggal_lahir'])) : '-') . '</td>';
            echo '<td style="' . $style . '">' . htmlspecialchars($row['email'] ?? '-') . '</td>';
            echo '<td style="' . $style . '">' . htmlspecialchars($row['nomor_hp'] ?? '') . '</td>';
            echo '<td style="' . $style . '">' . htmlspecialchars($row['alamat'] ?? '') . '</td>';
            echo '<td style="' . $style . '">' . htmlspecialchars($row['nama_ibu_kandung'] ?? '') . '</td>';
            echo '<td style="' . $style . 'font-weight:bold;">' . htmlspecialchars($row['nama_prodi'] ?? '') . '</td>';
            echo '<td style="' . $style . 'text-align:center;">' . htmlspecialchars($row['jenjang'] ?? '') . '</td>';
            echo '<td style="' . $style . '">' . htmlspecialchars($row['fakultas'] ?? '') . '</td>';
            echo '<td style="' . $style . 'text-align:center;">' . htmlspecialchars($row['gelar'] ?? '') . '</td>';
            echo '<td style="' . $style . '">' . htmlspecialchars($row['ta_nama'] ?? $row['ta_kode'] ?? '') . '</td>';
            // Status dengan warna
            $statusColors = ['diterima'=>'#d4edda;color:#155724','menunggu'=>'#fff3cd;color:#856404','revisi'=>'#cce5ff;color:#004085','ditolak'=>'#f8d7da;color:#721c24','draft'=>'#e2e3e5;color:#383d41'];
            $sc = $statusColors[$row['status'] ?? 'draft'] ?? '#e2e3e5;color:#383d41';
            echo '<td style="' . $style . 'background:' . $sc . ';text-align:center;font-weight:bold;">' . htmlspecialchars($statusLabel[$row['status'] ?? 'draft'] ?? ucfirst($row['status'] ?? '')) . '</td>';
            echo '<td style="' . $style . 'text-align:center;">' . ($row['created_at'] ? date('d/m/Y H:i', strtotime($row['created_at'])) : '-') . '</td>';
            echo '<td style="' . $style . 'text-align:center;">' . ($row['tanggal_submit'] ? date('d/m/Y H:i', strtotime($row['tanggal_submit'])) : '-') . '</td>';
            echo '<td style="' . $style . '">' . nl2br(htmlspecialchars($row['catatan_verifikasi'] ?? '-')) . '</td>';
            echo '<td style="' . $style . '">' . htmlspecialchars($row['verifikator_nama'] ?? '-') . '</td>';
            echo '<td style="' . $style . '">' . htmlspecialchars($row['asal_universitas'] ?? '-') . '</td>';
            echo '<td style="' . $style . 'text-align:center;">' . htmlspecialchars($row['tahun_lulus_s1'] ?? '-') . '</td>';
            echo '<td style="' . $style . 'text-align:center;">' . ($row['ipk_s1'] ? number_format($row['ipk_s1'], 2) : '-') . '</td>';
            echo '</tr>';
        }

        echo '</table></body></html>';
        echo ob_get_clean();
        exit;
    }

    /** GET /admin/pendaftar/{id}/edit */
    public function editPendaftarForm(string $id): void
    {
        Auth::requireRole(['superadmin','admin']);
        $pid      = Security::cleanInt($id);
        $pm       = new PendaftarModel();
        $pendaftar= $pm->getWithDetails($pid);
        if (!$pendaftar) {
            Session::flash('error', 'Data tidak ditemukan.');
            $this->redirect('/admin/pendaftar');
        }
        $this->view('admin/edit-pendaftar', [
            'layout'     => 'layouts/admin',
            'page_title' => 'Edit Data Pendaftar',
            'pendaftar'  => $pendaftar,
            'prodi_list' => (new ProdiModel())->getAktif(),
            'csrf'       => Security::generateCsrf(),
        ]);
    }

    /** POST /admin/pendaftar/{id}/edit */
    public function editPendaftarSave(string $id): void
    {
        Auth::requireRole(['superadmin','admin']);
        $this->verifyCsrf();
        $pid      = Security::cleanInt($id);
        $pm       = new PendaftarModel();
        $pendaftar= $pm->findById($pid);
        if (!$pendaftar) {
            Session::flash('error', 'Data tidak ditemukan.');
            $this->redirect('/admin/pendaftar');
        }

        $data = [
            'nama_lengkap'     => Security::cleanRaw($_POST['nama_lengkap']     ?? ''),
            'tempat_lahir'     => Security::cleanRaw($_POST['tempat_lahir']     ?? ''),
            'tanggal_lahir'    => Security::cleanRaw($_POST['tanggal_lahir']    ?? ''),
            'jenis_kelamin'    => Security::cleanRaw($_POST['jenis_kelamin']    ?? ''),
            'nomor_hp'         => preg_replace('/[^0-9+]/', '', $_POST['nomor_hp'] ?? ''),
            'alamat'           => Security::cleanRaw($_POST['alamat']           ?? ''),
            'nama_ibu_kandung' => Security::cleanRaw($_POST['nama_ibu_kandung'] ?? ''),
            'program_studi_id' => Security::cleanInt($_POST['program_studi_id'] ?? 0),
            'status'           => Security::cleanRaw($_POST['status']           ?? ''),
        ];

        // Update email jika diisi
        $newEmail = trim(strtolower(Security::cleanRaw($_POST['email'] ?? '')));
        if ($newEmail) {
            (new UserModel())->update($pendaftar['user_id'], ['email' => $newEmail]);
        }

        $pm->update($pid, $data);
        AuditLog::log('ADMIN_EDIT', 'pendaftar', $pid);
        Session::flash('success', 'Data pendaftar berhasil diperbarui.');
        $this->redirect('/admin/pendaftar/' . $pid);
    }

    /** POST /admin/pendaftar/{id}/hapus */
    public function hapusPendaftar(string $id): void
    {
        Auth::requireRole(['superadmin','admin']);
        $this->verifyCsrf();
        $pid = Security::cleanInt($id);
        $pm  = new PendaftarModel();
        $p   = $pm->findById($pid);
        if (!$p) {
            Session::flash('error', 'Data pendaftar tidak ditemukan.');
            $this->redirect('/admin/pendaftar');
        }
        // Hapus dokumen terkait dari storage
        $docs = (new DokumenModel())->getByPendaftar($pid);
        foreach ($docs as $doc) {
            $path = STORAGE_PATH . '/uploads/' . $doc['path_file'];
            if (file_exists($path)) @unlink($path);
        }
        // Hapus pendaftar (cascade ke dokumen & log via FK)
        $pm->delete($pid);
        AuditLog::log('HAPUS', 'pendaftar', $pid);
        Session::flash('success', 'Data pendaftar berhasil dihapus.');
        $this->redirect('/admin/pendaftar');
    }

    /** POST /admin/pendaftar/{id}/reset-pw */
    public function resetPasswordPendaftar(string $id): void
    {
        Auth::requireRole(['superadmin','admin']);
        $this->verifyCsrf();
        $pid      = Security::cleanInt($id);
        $pm       = new PendaftarModel();
        $pendaftar= $pm->findById($pid);
        if (!$pendaftar) {
            $this->json(['success'=>false,'message'=>'Data tidak ditemukan.']);
        }
        $newPw    = Security::cleanRaw($_POST['new_password'] ?? '');
        if (strlen($newPw) < 6) {
            Session::flash('error', 'Password minimal 6 karakter.');
            $this->redirect('/admin/pendaftar/' . $pid);
        }
        (new UserModel())->update($pendaftar['user_id'], [
            'password_hash' => Security::hashPassword($newPw)
        ]);
        AuditLog::log('RESET_PASSWORD', 'pendaftar', $pid);
        Session::flash('success', 'Password berhasil direset.');
        $this->redirect('/admin/pendaftar/' . $pid);
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

        if (empty($kode)) {
            Session::flash('error', 'Kode tahun akademik wajib diisi.');
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
            'nama_prodi'=> Security::cleanRaw($_POST['nama_prodi'] ?? $_POST['nama'] ?? ''),
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
            'nama_prodi'=> Security::cleanRaw($_POST['nama_prodi'] ?? $_POST['nama'] ?? ''),
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
            $key = Security::cleanRaw((string)$k);
            // Skip jika nilai berupa array (misal checkbox group, FAQ array)
            if (is_array($v)) {
                // Gabungkan array menjadi JSON atau string dipisah newline
                $value = implode("\n", array_map('strip_tags', $v));
            } else {
                $value = Security::cleanRaw((string)$v);
            }
            $cms->set($key, $value);
        }

        if (!empty($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $file     = $_FILES['logo'];
            $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed  = ['png','jpg','jpeg','gif','svg','webp'];
            if (in_array($ext, $allowed)) {
                // Simpan langsung ke public/assets/images/logo.ext
                $destDir  = PUBLIC_PATH . '/assets/images/';
                if (!is_dir($destDir)) mkdir($destDir, 0755, true);
                // Hapus logo lama
                foreach (['png','jpg','jpeg','gif','svg','webp'] as $e) {
                    $old = $destDir . 'logo.' . $e;
                    if (file_exists($old)) @unlink($old);
                }
                $destFile = $destDir . 'logo.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $destFile)) {
                    $logoPath = '/assets/images/logo.' . $ext;
                    $cms->set('logo_path', $logoPath);
                }
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