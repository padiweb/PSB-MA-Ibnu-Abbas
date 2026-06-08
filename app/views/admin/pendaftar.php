<?php
// views/admin/pendaftar.php
$pendaftar  = $pendaftar ?? [];
$pagination = $pagination ?? [];
$filters    = $filters ?? [];
$tahunList  = $tahun_list ?? [];
$prodiList  = $prodi_list ?? [];

$statusLabels = [
    'menunggu' => ['label'=>'Menunggu','class'=>'bg-warning text-dark'],
    'diterima' => ['label'=>'Diterima','class'=>'bg-success'],
    'revisi'   => ['label'=>'Revisi',  'class'=>'bg-info text-dark'],
    'ditolak'  => ['label'=>'Ditolak', 'class'=>'bg-danger'],
];
?>
<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1><i class="bi bi-people me-2"></i>Data Pendaftar</h1>
    </div>
    <a href="<?= BASE_URL ?>/admin/export" class="btn btn-sm" style="background:var(--primary);color:#fff;">
        <i class="bi bi-download me-1"></i> Export Data
    </a>
</div>

<!-- FILTER -->
<div class="card border-0 rounded-3 mb-4" style="box-shadow:0 2px 12px rgba(0,0,0,.06);">
    <div class="card-body p-3">
        <form method="GET" action="<?= BASE_URL ?>/admin/pendaftar" id="filterForm">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label mb-1" style="font-size:.75rem;font-weight:600;color:#64748b;">CARI</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="q" class="form-control" placeholder="Nama / Nomor Pendaftaran..."
                               value="<?= htmlspecialchars($filters['q'] ?? '') ?>" id="searchInput">
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label mb-1" style="font-size:.75rem;font-weight:600;color:#64748b;">TAHUN</label>
                    <select name="ta" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Semua</option>
                        <?php foreach ($tahunList as $ta): ?>
                        <option value="<?= $ta['id'] ?>" <?= ($filters['tahun_akademik_id'] ?? '') == $ta['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ta['nama']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label mb-1" style="font-size:.75rem;font-weight:600;color:#64748b;">PRODI</label>
                    <select name="prodi" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Semua Prodi</option>
                        <?php foreach ($prodiList as $pr): ?>
                        <option value="<?= $pr['id'] ?>" <?= ($filters['program_studi_id'] ?? '') == $pr['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($pr['nama_prodi']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label mb-1" style="font-size:.75rem;font-weight:600;color:#64748b;">STATUS</label>
                    <select name="status" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Semua</option>
                        <?php foreach ($statusLabels as $val => $s): ?>
                        <option value="<?= $val ?>" <?= ($filters['status'] ?? '') === $val ? 'selected' : '' ?>>
                            <?= $s['label'] ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6 col-md-1">
                    <a href="<?= BASE_URL ?>/admin/pendaftar" class="btn btn-sm btn-outline-secondary w-100">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- TABLE -->
<div class="admin-table">
    <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
        <span style="font-size:.82rem;color:#64748b;">
            Menampilkan <strong><?= count($pendaftar) ?></strong> dari <strong><?= $pagination['total'] ?? 0 ?></strong> pendaftar
        </span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>No. Pendaftaran</th>
                    <th>Nama Lengkap</th>
                    <th>Program Studi</th>
                    <th>Jenjang</th>
                    <th>Tanggal Daftar</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pendaftar)): ?>
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size:2.5rem;color:#cbd5e1;"></i>
                        <div class="mt-2 text-muted">Tidak ada data pendaftar</div>
                    </td>
                </tr>
                <?php else: ?>
                <?php $no = (($pagination['page'] ?? 1) - 1) * ($pagination['per_page'] ?? 20) + 1; ?>
                <?php foreach ($pendaftar as $p): ?>
                <tr>
                    <td class="text-muted" style="font-size:.78rem;"><?= $no++ ?></td>
                    <td>
                        <code style="font-size:.8rem;background:#f1f5f9;padding:3px 8px;border-radius:4px;color:var(--primary);">
                            <?= htmlspecialchars($p['nomor_pendaftaran']) ?>
                        </code>
                    </td>
                    <td>
                        <div class="fw-600"><?= htmlspecialchars($p['nama_lengkap']) ?></div>
                        <div style="font-size:.72rem;color:#64748b;"><?= htmlspecialchars($p['nomor_hp']) ?></div>
                    </td>
                    <td style="font-size:.83rem;"><?= htmlspecialchars($p['nama_prodi'] ?? '-') ?></td>
                    <td>
                        <span class="badge <?= $p['jenjang'] === 'S2' ? 'bg-warning text-dark' : 'bg-primary' ?>" style="font-size:.68rem;">
                            <?= htmlspecialchars($p['jenjang'] ?? '-') ?>
                        </span>
                    </td>
                    <td style="font-size:.8rem;color:#64748b;">
                        <?= date('d M Y', strtotime($p['created_at'])) ?>
                    </td>
                    <td>
                        <?php $s = $statusLabels[$p['status_verifikasi']] ?? ['label'=>ucfirst($p['status_verifikasi']),'class'=>'bg-secondary']; ?>
                        <span class="badge <?= $s['class'] ?>" style="font-size:.72rem;"><?= $s['label'] ?></span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="<?= BASE_URL ?>/admin/pendaftar/<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary" title="Detail" style="padding:3px 8px;">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="<?= BASE_URL ?>/admin/pendaftar/<?= $p['id'] ?>/cetak" class="btn btn-sm btn-outline-secondary" title="Cetak" style="padding:3px 8px;" target="_blank">
                                <i class="bi bi-printer"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- PAGINATION -->
    <?php if (($pagination['total_pages'] ?? 1) > 1): ?>
    <div class="d-flex justify-content-center p-3">
        <nav>
            <ul class="pagination pagination-sm mb-0">
                <?php
                $currentPage = $pagination['page'] ?? 1;
                $totalPages  = $pagination['total_pages'] ?? 1;
                $params = array_filter($filters) + ['page' => 1];
                $baseUrl = '/admin/pendaftar?' . http_build_query(array_filter($filters));
                ?>
                <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= $baseUrl ?>&page=<?= $currentPage-1 ?>">‹</a>
                </li>
                <?php for ($i = max(1, $currentPage-2); $i <= min($totalPages, $currentPage+2); $i++): ?>
                <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                    <a class="page-link" href="<?= $baseUrl ?>&page=<?= $i ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
                <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= $baseUrl ?>&page=<?= $currentPage+1 ?>">›</a>
                </li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<script>
// Debounce search
let searchTimer;
document.getElementById('searchInput').addEventListener('input', function() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => document.getElementById('filterForm').submit(), 500);
});
</script>
