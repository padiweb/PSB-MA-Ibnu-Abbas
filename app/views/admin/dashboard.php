<?php
// views/admin/dashboard.php
$stat     = $stat ?? [];
$perProdi = $per_prodi ?? [];
$tahunAktif = $tahun_aktif ?? null;
?>
<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1><i class="bi bi-grid-1x2 me-2"></i>Dashboard</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size:.78rem;">
                <li class="breadcrumb-item active">Ringkasan PMB<?= $tahunAktif ? ' ' . htmlspecialchars($tahunAktif['nama']) : '' ?></li>
            </ol>
        </nav>
    </div>
    <?php if ($tahunAktif): ?>
    <span class="badge" style="background:var(--accent);color:#fff;font-size:.8rem;padding:8px 14px;border-radius:20px;">
        <i class="bi bi-calendar-check me-1"></i> <?= htmlspecialchars($tahunAktif['nama']) ?> — Aktif
    </span>
    <?php else: ?>
    <a href="/admin/tahun-akademik" class="btn btn-sm" style="background:var(--primary);color:#fff;">
        <i class="bi bi-plus-circle me-1"></i> Buka PMB Baru
    </a>
    <?php endif; ?>
</div>

<!-- STAT CARDS -->
<div class="row g-3 mb-4">
    <?php
    $cards = [
        ['label'=>'Total Pendaftar', 'val'=> $stat['total'] ?? 0, 'icon'=>'bi-people-fill', 'bg'=>'linear-gradient(135deg,#1a3a6b,#2563eb)', 'color'=>'#fff'],
        ['label'=>'Menunggu Verifikasi','val'=> $stat['menunggu'] ?? 0, 'icon'=>'bi-hourglass-split','bg'=>'linear-gradient(135deg,#d97706,#f59e0b)','color'=>'#fff'],
        ['label'=>'Diterima',          'val'=> $stat['diterima'] ?? 0, 'icon'=>'bi-check-circle-fill','bg'=>'linear-gradient(135deg,#15803d,#22c55e)','color'=>'#fff'],
        ['label'=>'Revisi / Ditolak',  'val'=> ($stat['revisi'] ?? 0)+($stat['ditolak'] ?? 0), 'icon'=>'bi-x-circle-fill','bg'=>'linear-gradient(135deg,#b91c1c,#ef4444)','color'=>'#fff'],
    ];
    foreach ($cards as $c): ?>
    <div class="col-6 col-xl-3">
        <div class="stat-card card h-100" style="background:<?= $c['bg'] ?>;">
            <div class="card-body d-flex align-items-center gap-3" style="color:<?= $c['color'] ?>;">
                <div style="font-size:2.2rem;opacity:.85;"><i class="bi <?= $c['icon'] ?>"></i></div>
                <div>
                    <div style="font-size:1.9rem;font-weight:800;line-height:1;"><?= number_format($c['val']) ?></div>
                    <div style="font-size:.78rem;opacity:.85;font-weight:500;"><?= $c['label'] ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- CHARTS ROW -->
<div class="row g-3 mb-4">
    <div class="col-lg-7">
        <div class="card border-0 rounded-3 h-100" style="box-shadow:0 2px 12px rgba(0,0,0,.06);">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="mb-0 fw-700" style="color:var(--primary);">
                        <i class="bi bi-bar-chart me-2"></i>Pendaftar per Program Studi
                    </h6>
                </div>
                <canvas id="chartProdi" height="220"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card border-0 rounded-3 h-100" style="box-shadow:0 2px 12px rgba(0,0,0,.06);">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="mb-0 fw-700" style="color:var(--primary);">
                        <i class="bi bi-pie-chart me-2"></i>Distribusi Status
                    </h6>
                </div>
                <canvas id="chartStatus" height="220"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- PER PRODI TABLE + QUICK ACTIONS -->
<div class="row g-3">
    <div class="col-lg-8">
        <div class="admin-table">
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                <h6 class="mb-0 fw-600" style="color:var(--primary);">Detail per Program Studi</h6>
                <a href="/admin/pendaftar" class="btn btn-sm" style="background:var(--primary);color:#fff;font-size:.75rem;">
                    Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Program Studi</th>
                        <th>Jenjang</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Diterima</th>
                        <th class="text-center">Menunggu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($perProdi)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada data pendaftar</td></tr>
                    <?php else: ?>
                    <?php foreach ($perProdi as $row): ?>
                    <tr>
                        <td>
                            <div class="fw-600" style="font-size:.85rem;"><?= htmlspecialchars($row['nama_prodi']) ?></div>
                            <div style="font-size:.72rem;color:#64748b;"><?= htmlspecialchars($row['nama_fakultas'] ?? '') ?></div>
                        </td>
                        <td>
                            <span class="badge <?= $row['jenjang'] === 'S2' ? 'bg-warning text-dark' : 'bg-primary' ?>" style="font-size:.7rem;">
                                <?= htmlspecialchars($row['jenjang']) ?>
                            </span>
                        </td>
                        <td class="text-center fw-700"><?= $row['total'] ?></td>
                        <td class="text-center">
                            <span style="color:#16a34a;font-weight:700;"><?= $row['diterima'] ?? 0 ?></span>
                        </td>
                        <td class="text-center">
                            <span style="color:#d97706;font-weight:700;"><?= $row['menunggu'] ?? 0 ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 rounded-3" style="box-shadow:0 2px 12px rgba(0,0,0,.06);">
            <div class="card-body p-4">
                <h6 class="fw-700 mb-3" style="color:var(--primary);">
                    <i class="bi bi-lightning-charge me-2"></i>Aksi Cepat
                </h6>
                <div class="d-grid gap-2">
                    <a href="/admin/verifikasi" class="btn btn-sm text-start" style="background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;">
                        <i class="bi bi-shield-check me-2"></i> Verifikasi Berkas
                        <?php if(($stat['menunggu'] ?? 0) > 0): ?>
                        <span class="badge bg-danger ms-1"><?= $stat['menunggu'] ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="/admin/pendaftar?status=revisi" class="btn btn-sm text-start" style="background:#fffbeb;color:#b45309;border:1px solid #fde68a;">
                        <i class="bi bi-pencil-square me-2"></i> Pendaftar Revisi
                    </a>
                    <a href="/admin/export" class="btn btn-sm text-start" style="background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i> Export Excel/CSV
                    </a>
                    <a href="/admin/tahun-akademik" class="btn btn-sm text-start" style="background:#f5f3ff;color:#6d28d9;border:1px solid #ddd6fe;">
                        <i class="bi bi-calendar3 me-2"></i> Kelola Tahun Akademik
                    </a>
                    <a href="/admin/pengaturan" class="btn btn-sm text-start" style="background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;">
                        <i class="bi bi-gear me-2"></i> Pengaturan Landing Page
                    </a>
                </div>
            </div>
        </div>

        <!-- Tahun Akademik Card -->
        <div class="card border-0 rounded-3 mt-3" style="box-shadow:0 2px 12px rgba(0,0,0,.06);">
            <div class="card-body p-4">
                <h6 class="fw-700 mb-3" style="color:var(--primary);">
                    <i class="bi bi-info-circle me-2"></i>Status PMB
                </h6>
                <?php if ($tahunAktif): ?>
                <div style="font-size:.82rem;">
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Tahun Akademik</span>
                        <strong><?= htmlspecialchars($tahunAktif['nama']) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Mulai Pendaftaran</span>
                        <strong><?= $tahunAktif['tanggal_mulai'] ? date('d M Y', strtotime($tahunAktif['tanggal_mulai'])) : '-' ?></strong>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted">Akhir Pendaftaran</span>
                        <strong><?= $tahunAktif['tanggal_tutup'] ? date('d M Y', strtotime($tahunAktif['tanggal_tutup'])) : 'Belum ditentukan' ?></strong>
                    </div>
                </div>
                <?php else: ?>
                <p class="text-muted mb-0" style="font-size:.82rem;">Tidak ada tahun akademik aktif.</p>
                <a href="/admin/tahun-akademik" class="btn btn-sm mt-2" style="background:var(--primary);color:#fff;">
                    <i class="bi bi-plus-circle me-1"></i> Buka PMB Baru
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$prodiLabels = array_column($perProdi, 'nama_prodi');
$prodiData   = array_column($perProdi, 'total');
$prodiDiterima = array_map(fn($r) => $r['diterima'] ?? 0, $perProdi);

$statusData = [
    $stat['diterima'] ?? 0,
    $stat['menunggu'] ?? 0,
    $stat['revisi'] ?? 0,
    $stat['ditolak'] ?? 0,
];
?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Chart per Prodi
    const prodiLabels = <?= json_encode($prodiLabels) ?>;
    const prodiTotal  = <?= json_encode($prodiData) ?>;
    const prodiTerima = <?= json_encode($prodiDiterima) ?>;

    if (prodiLabels.length > 0) {
        new Chart(document.getElementById('chartProdi'), {
            type: 'bar',
            data: {
                labels: prodiLabels,
                datasets: [
                    { label: 'Total', data: prodiTotal, backgroundColor: 'rgba(26,58,107,.7)', borderRadius: 6 },
                    { label: 'Diterima', data: prodiTerima, backgroundColor: 'rgba(22,163,74,.6)', borderRadius: 6 }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'top', labels: { font: { size: 11 } } } },
                scales: {
                    x: { ticks: { font: { size: 10 }, maxRotation: 30 } },
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    }

    // Chart Status
    const statusData = <?= json_encode($statusData) ?>;
    if (statusData.some(v => v > 0)) {
        new Chart(document.getElementById('chartStatus'), {
            type: 'doughnut',
            data: {
                labels: ['Diterima', 'Menunggu', 'Revisi', 'Ditolak'],
                datasets: [{
                    data: statusData,
                    backgroundColor: ['#22c55e','#f59e0b','#3b82f6','#ef4444'],
                    hoverOffset: 8, borderWidth: 2
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 12, font: { size: 11 } } }
                },
                cutout: '65%'
            }
        });
    }
});
</script>
