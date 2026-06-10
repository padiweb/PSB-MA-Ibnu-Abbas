<?php
/**
 * Model Base Class
 * Semua Model extend class ini
 */
abstract class Model
{
    protected PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ── Generic CRUD ────────────────────────────────────────────

    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findAll(array $conditions = [], string $orderBy = '', int $limit = 0, int $offset = 0): array
    {
        [$where, $params] = $this->buildWhere($conditions);
        $sql = "SELECT * FROM `{$this->table}`" . $where;
        if ($orderBy) $sql .= " ORDER BY {$orderBy}";
        if ($limit)   $sql .= " LIMIT {$limit}";
        if ($offset)  $sql .= " OFFSET {$offset}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function count(array $conditions = []): int
    {
        [$where, $params] = $this->buildWhere($conditions);
        $sql  = "SELECT COUNT(*) FROM `{$this->table}`" . $where;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function insert(array $data): int
    {
        $cols   = implode('`,`', array_keys($data));
        $marks  = implode(',', array_fill(0, count($data), '?'));
        $sql    = "INSERT INTO `{$this->table}` (`{$cols}`) VALUES ({$marks})";
        $stmt   = $this->db->prepare($sql);
        $stmt->execute(array_values($data));
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sets  = implode(',', array_map(fn($k) => "`{$k}` = ?", array_keys($data)));
        $sql   = "UPDATE `{$this->table}` SET {$sets} WHERE `{$this->primaryKey}` = ?";
        $stmt  = $this->db->prepare($sql);
        $vals  = array_values($data);
        $vals[]= $id;
        return $stmt->execute($vals);
    }

    public function delete(int $id): bool
    {
        $sql  = "DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    // ── Helpers ─────────────────────────────────────────────────

    protected function buildWhere(array $conditions): array
    {
        if (empty($conditions)) return ['', []];
        $parts  = [];
        $params = [];
        foreach ($conditions as $col => $val) {
            if (is_null($val)) {
                $parts[] = "`{$col}` IS NULL";
            } else {
                $parts[] = "`{$col}` = ?";
                $params[] = $val;
            }
        }
        return [' WHERE ' . implode(' AND ', $parts), $params];
    }

    protected function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    protected function paginate(string $sql, array $params, int $page, int $perPage): array
    {
        $countSql = "SELECT COUNT(*) FROM ({$sql}) AS t";
        $stmt = $this->db->prepare($countSql);
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();

        $offset = ($page - 1) * $perPage;
        $stmt   = $this->db->prepare($sql . " LIMIT {$perPage} OFFSET {$offset}");
        $stmt->execute($params);
        $data   = $stmt->fetchAll();

        return [
            'data'         => $data,
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => (int) ceil($total / $perPage),
        ];
    }

    public function findBy(string $column, mixed $value): ?array
    {
        $allowed = preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $column);
        if (!$allowed) return null;
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE `{$column}` = ? LIMIT 1");
        $stmt->execute([$value]);
        return $stmt->fetch() ?: null;
    }
}

/**
 * ===== MODEL-MODEL KONKRET =====
 */

class PendaftarModel extends Model
{
    protected string $table = 'pendaftar';

    /**
     * Generate nomor pendaftaran otomatis: PMB-2026-000001
     */
    public function generateNomor(string $tahunKode): string
    {
        $tahun = explode('/', $tahunKode)[0];
        $stmt  = $this->db->prepare(
            "SELECT COUNT(*) FROM `pendaftar` p
             JOIN `tahun_akademik` ta ON ta.id = p.tahun_akademik_id
             WHERE ta.kode LIKE ?"
        );
        $stmt->execute([$tahun . '%']);
        $count = (int) $stmt->fetchColumn();
        return sprintf('PMB-%s-%06d', $tahun, $count + 1);
    }

    public function getWithDetails(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, u.email, u.username,
                    ta.kode AS ta_kode, ta.nama AS ta_nama,
                    ps.nama_prodi, ps.jenjang, ps.gelar, ps.fakultas,
                    u2.nama AS verifikator_nama
             FROM pendaftar p
             JOIN users u ON u.id = p.user_id
             JOIN tahun_akademik ta ON ta.id = p.tahun_akademik_id
             JOIN program_studi ps ON ps.id = p.program_studi_id
             LEFT JOIN users u2 ON u2.id = p.diverifikasi_oleh
             WHERE p.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /** Verifikasi identitas: nomor pendaftaran + tanggal lahir */
    public function findByNomorAndTanggalLahir(string $nomor, string $tanggal): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, u.id AS user_id, u.password_hash
             FROM pendaftar p
             JOIN users u ON u.id = p.user_id
             WHERE p.nomor_pendaftaran = ? AND p.tanggal_lahir = ?
             LIMIT 1"
        );
        $stmt->execute([strtoupper(trim($nomor)), $tanggal]);
        return $stmt->fetch() ?: null;
    }

    public function getByUserId(int $userId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, ta.kode AS ta_kode, ta.nama AS ta_nama,
                    ps.nama_prodi, ps.jenjang, ps.gelar, ps.fakultas,
                    u.email, u.username
             FROM pendaftar p
             JOIN tahun_akademik ta ON ta.id = p.tahun_akademik_id
             JOIN program_studi ps ON ps.id = p.program_studi_id
             JOIN users u ON u.id = p.user_id
             WHERE p.user_id = ?
             ORDER BY p.created_at DESC
             LIMIT 1"
        );
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: null;
    }

    public function getByNomor(string $nomor): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, ta.kode AS ta_kode, ps.nama_prodi,
                    ps.jenjang, ps.gelar, ps.fakultas
             FROM pendaftar p
             JOIN tahun_akademik ta ON ta.id = p.tahun_akademik_id
             JOIN program_studi ps ON ps.id = p.program_studi_id
             WHERE p.nomor_pendaftaran = ?"
        );
        $stmt->execute([$nomor]);
        return $stmt->fetch() ?: null;
    }

    public function getForExport(int $taId = 0): array
    {
        $sql = "SELECT p.*, 
                    u.email, u.username,
                    ta.nama AS ta_nama, ta.kode AS ta_kode,
                    ps.nama_prodi, ps.jenjang, ps.gelar, ps.fakultas,
                    u2.nama AS verifikator_nama
                FROM pendaftar p
                JOIN users u ON u.id = p.user_id
                JOIN tahun_akademik ta ON ta.id = p.tahun_akademik_id
                JOIN program_studi ps ON ps.id = p.program_studi_id
                LEFT JOIN users u2 ON u2.id = p.diverifikasi_oleh";
        $params = [];
        if ($taId > 0) {
            $sql   .= " WHERE p.tahun_akademik_id = ?";
            $params[] = $taId;
        }
        $sql .= " ORDER BY p.created_at ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function searchAdmin(array $filters, int $page = 1, int $perPage = 20): array
    {
        $params = [];
        $where  = ['1=1'];

        if (!empty($filters['tahun_akademik_id'])) {
            $where[] = 'p.tahun_akademik_id = ?';
            $params[] = (int) $filters['tahun_akademik_id'];
        }
        if (!empty($filters['program_studi_id'])) {
            $where[] = 'p.program_studi_id = ?';
            $params[] = (int) $filters['program_studi_id'];
        }
        if (!empty($filters['status'])) {
            $where[] = 'p.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['q'])) {
            $where[] = '(p.nama_lengkap LIKE ? OR p.nomor_pendaftaran LIKE ? OR p.nomor_hp LIKE ?)';
            $q = '%' . $filters['q'] . '%';
            $params[] = $q; $params[] = $q; $params[] = $q;
        }

        $sql = "SELECT p.*, ta.kode AS ta_kode, ps.nama_prodi, ps.jenjang
                FROM pendaftar p
                JOIN tahun_akademik ta ON ta.id = p.tahun_akademik_id
                JOIN program_studi ps ON ps.id = p.program_studi_id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY p.created_at DESC";

        return $this->paginate($sql, $params, $page, $perPage);
    }

    public function getStatistik(int $tahunAkademikId): array
    {
        $stmt = $this->db->prepare(
            "SELECT status, COUNT(*) AS total
             FROM pendaftar WHERE tahun_akademik_id = ?
             GROUP BY status"
        );
        $stmt->execute([$tahunAkademikId]);
        $rows = $stmt->fetchAll();

        $stat = ['total'=>0,'draft'=>0,'menunggu'=>0,'diterima'=>0,'revisi'=>0,'ditolak'=>0];
        foreach ($rows as $r) {
            $stat[$r['status']] = (int) $r['total'];
            $stat['total'] += (int) $r['total'];
        }
        return $stat;
    }

    public function getPerProdi(int $tahunAkademikId): array
    {
        $stmt = $this->db->prepare(
            "SELECT ps.nama_prodi, ps.jenjang, ps.fakultas AS nama_fakultas,
                    COUNT(p.id) AS total,
                    SUM(p.status = 'diterima') AS diterima,
                    SUM(p.status = 'menunggu') AS menunggu
             FROM pendaftar p
             JOIN program_studi ps ON ps.id = p.program_studi_id
             WHERE p.tahun_akademik_id = ?
             GROUP BY p.program_studi_id
             ORDER BY total DESC"
        );
        $stmt->execute([$tahunAkademikId]);
        return $stmt->fetchAll();
    }
}

/**
 * UserModel
 */
class UserModel extends Model
{
    protected string $table = 'users';

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM `users` WHERE `username` = ? LIMIT 1");
        $stmt->execute([$username]);
        return $stmt->fetch() ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM `users` WHERE `email` = ? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function findByUsernameOrEmail(string $credential): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM `users` WHERE `username` = ? OR `email` = ? LIMIT 1");
        $stmt->execute([$credential, $credential]);
        return $stmt->fetch() ?: null;
    }

    public function updateLastLogin(int $id, string $ip): void
    {
        $this->db->prepare("UPDATE `users` SET `last_login`=NOW(), `last_ip`=?, `login_attempts`=0 WHERE `id`=?")
                 ->execute([$ip, $id]);
    }

    public function isLocked(array $user): bool
    {
        if (!$user['locked_until']) return false;
        return strtotime($user['locked_until']) > time();
    }

    public function getAdmins(): array
    {
        $stmt = $this->db->prepare(
            "SELECT id, username, nama, email, role, is_aktif, last_login, created_at
             FROM `users` WHERE role != 'pendaftar' ORDER BY role, nama"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Alias untuk kompatibilitas pemanggilan di controller */
    public function getAdminUsers(): array
    {
        return $this->getAdmins();
    }
}

/**
 * DokumenModel
 */
class DokumenModel extends Model
{
    protected string $table = 'dokumen';

    public function getByPendaftar(int $pendaftarId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM `dokumen` WHERE `pendaftar_id` = ? ORDER BY `jenis_dokumen`");
        $stmt->execute([$pendaftarId]);
        return $stmt->fetchAll();
    }

    public function getDokumenTypes(): array
    {
        return [
            'ijazah_sma'    => 'Ijazah SMA/Sederajat',
            'transkrip_sma' => 'Transkrip Nilai SMA',
            'ijazah_s1'     => 'Ijazah S1',
            'transkrip_s1'  => 'Transkrip Nilai S1',
            'ktp'           => 'KTP',
            'kk'            => 'Kartu Keluarga (KK)',
            'akte_kelahiran'=> 'Akte Kelahiran',
            'foto'          => 'Foto Resmi',
        ];
    }

    public function deleteByPendaftarAndType(int $pendaftarId, string $jenis): bool
    {
        $stmt = $this->db->prepare("DELETE FROM `dokumen` WHERE `pendaftar_id`=? AND `jenis_dokumen`=?");
        return $stmt->execute([$pendaftarId, $jenis]);
    }
}

/**
 * TahunAkademikModel
 */
class TahunAkademikModel extends Model
{
    protected string $table = 'tahun_akademik';

    public function getAktif(): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM `tahun_akademik` WHERE `aktif`=1 LIMIT 1");
        $stmt->execute();
        return $stmt->fetch() ?: null;
    }

    public function setAktif(int $id): void
    {
        $this->db->prepare("UPDATE `tahun_akademik` SET `aktif`=0")->execute();
        $this->db->prepare("UPDATE `tahun_akademik` SET `aktif`=1 WHERE `id`=?")->execute([$id]);
    }
}

/**
 * ProdiModel
 */
class ProdiModel extends Model
{
    protected string $table = 'program_studi';

    public function getAktif(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM `program_studi` WHERE `is_aktif`=1 ORDER BY `urutan`,`jenjang`");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Ambil prodi aktif + biaya dari TA aktif, untuk form pendaftaran */
    public function getAktifWithBiaya(int $tahunAkademikId): array
    {
        $stmt = $this->db->prepare(
            "SELECT ps.*, ps.fakultas AS nama_fakultas,
                    COALESCE(b.biaya_pendaftaran, 0) AS biaya_pendaftaran,
                    COALESCE(b.biaya_spp, 0) AS biaya_spp,
                    COALESCE(b.biaya_pendidikan, 0) AS biaya_pendidikan,
                    COALESCE(b.keterangan, '') AS keterangan_biaya
             FROM `program_studi` ps
             LEFT JOIN `biaya` b ON b.program_studi_id = ps.id AND b.tahun_akademik_id = ?
             WHERE ps.`is_aktif` = 1
             ORDER BY ps.`urutan`, ps.`jenjang`"
        );
        $stmt->execute([$tahunAkademikId]);
        return $stmt->fetchAll();
    }

    public function getGrouped(): array
    {
        $rows   = $this->getAktif();
        $groups = [];
        foreach ($rows as $row) {
            $key = $row['jenjang'] . '_' . $row['fakultas'];
            $groups[$key]['jenjang']  = $row['jenjang'];
            $groups[$key]['fakultas'] = $row['fakultas'];
            $groups[$key]['prodi'][]  = $row;
        }
        return array_values($groups);
    }
}

/**
 * BiayaModel
 */
class BiayaModel extends Model
{
    protected string $table = 'biaya';

    public function getByTA(int $tahunAkademikId): array
    {
        $stmt = $this->db->prepare(
            "SELECT b.*, ps.nama_prodi, ps.jenjang, ps.fakultas AS nama_fakultas
             FROM `biaya` b
             LEFT JOIN `program_studi` ps ON ps.id = b.program_studi_id
             WHERE b.tahun_akademik_id = ?
             ORDER BY ps.jenjang, ps.nama_prodi"
        );
        $stmt->execute([$tahunAkademikId]);
        return $stmt->fetchAll();
    }

    /** Alias — dipanggil dari BiayaController */
    public function getByTahun(int $tahunAkademikId): array
    {
        return $this->getByTA($tahunAkademikId);
    }

    /** Ambil semua biaya untuk satu prodi dalam satu tahun akademik */
    public function getByTahunProdi(int $taId, int $prodiId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM `biaya`
             WHERE tahun_akademik_id = ?
               AND (program_studi_id = ? OR program_studi_id IS NULL)
             ORDER BY jenis"
        );
        $stmt->execute([$taId, $prodiId]);
        return $stmt->fetchAll();
    }
}

/**
 * CmsModel
 */
class CmsModel extends Model
{
    protected string $table = 'cms_settings';

    public function getAll(): array
    {
        $stmt = $this->db->prepare("SELECT `key_name`, `value` FROM `cms_settings`");
        $stmt->execute();
        $rows = $stmt->fetchAll();
        $result = [];
        foreach ($rows as $r) $result[$r['key_name']] = $r['value'];
        return $result;
    }

    public function set(string $key, string $value): void
    {
        $this->db->prepare(
            "INSERT INTO `cms_settings` (`key_name`,`value`) VALUES (?,?)
             ON DUPLICATE KEY UPDATE `value`=?"
        )->execute([$key, $value, $value]);
    }

    public function getGrouped(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM `cms_settings` ORDER BY `group_name`, `id`");
        $stmt->execute();
        $rows = $stmt->fetchAll();
        $groups = [];
        foreach ($rows as $r) $groups[$r['group_name']][] = $r;
        return $groups;
    }
}