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
}
