<?php
// views/admin/dashboard.php
$stat       = $stat ?? [];
$perProdi   = $per_prodi ?? [];
$tahunAktif = $tahun_aktif ?? null;
?>

<!-- Page Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="fw-700 mb-1" style="color:var(--primary)">
      <i class="bi bi-grid-1x2 me-2"></i>Dashboard
    </h4>
    <p class="text-muted mb-0" style="font-size:.82rem">
      <?= $tahunAktif ? 'Ringkasan PMB ' . htmlspecialchars($tahunAktif['nama']) : 'Belum ada tahun akademik aktif' ?>
    </p>
  </div>
  <?php if ($tahunAktif): ?>
  <span class="badge rounded-pill px-3 py-2" style="background:linear-gradient(135deg,var(--primary),#2563eb);font-size:.78rem">
    <i class="bi bi-calendar-check me-1"></i><?= htmlspecialchars($tahunAktif['nama']) ?> — Aktif
  </span>
  <?php else: ?>
  <a href="<?= url('/admin/tahun-akademik') ?>" class="btn btn-sm" style="background:var(--primary);color:#fff;border-radius:8px">
    <i class="bi bi-plus-circle me-1"></i> Buka PMB Baru
  </a>
  <?php endif; ?>
</div>

<!-- STAT CARDS -->
<div class="row g-3 mb-4">
  <?php
  $cards = [
    ['label'=>'Total Pendaftar',     'val'=>$stat['total']    ?? 0, 'icon'=>'bi-people-fill',      'grad'=>'linear-gradient(135deg,#0f2754,#1a3a6b)', 'sub'=>'Semua status'],
    ['label'=>'Menunggu Verifikasi', 'val'=>$stat['menunggu'] ?? 0, 'icon'=>'bi-hourglass-split',  'grad'=>'linear-gradient(135deg,#b45309,#f59e0b)', 'sub'=>'Perlu ditindak'],
    ['label'=>'Diterima',            'val'=>$stat['diterima'] ?? 0, 'icon'=>'bi-patch-check-fill', 'grad'=>'linear-gradient(135deg,#15803d,#22c55e)', 'sub'=>'Lolos verifikasi'],
    ['label'=>'Revisi / Ditolak',    'val'=>($stat['revisi'] ?? 0)+($stat['ditolak'] ?? 0), 'icon'=>'bi-x-circle-fill','grad'=>'linear-gradient(135deg,#991b1b,#ef4444)', 'sub'=>'Butuh perhatian'],
  ];
  foreach ($cards as $c):
  ?>
  <div class="col-6 col-xl-3">
    <div class="rounded-3 p-3 h-100 position-relative overflow-hidden" style="background:<?= $c['grad'] ?>;color:#fff;box-shadow:0 4px 16px rgba(0,0,0,.12)">
      <div class="d-flex align-items-start justify-content-between">
        <div>
          <div style="font-size:2rem;font-weight:800;line-height:1;letter-spacing:-.02em">
            <?= number_format($c['val']) ?>
          </div>
          <div style="font-size:.75rem;font-weight:600;opacity:.9;margin-top:.25rem"><?= $c['label'] ?></div>
          <div style="font-size:.68rem;opacity:.65;margin-top:.15rem"><?= $c['sub'] ?></div>
        </div>
        <i class="bi <?= $c['icon'] ?>" style="font-size:2rem;opacity:.25;position:absolute;right:1rem;bottom:.75rem"></i>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- CHARTS -->
<div class="row g-3 mb-4">
  <div class="col-lg-7">
    <div class="card border-0 rounded-3 h-100" style="box-shadow:0 2px 12px rgba(0,0,0,.06)">
      <div class="card-body p-4">
        <h6 class="fw-700 mb-3" style="color:var(--primary)">
          <i class="bi bi-bar-chart-fill me-2"></i>Pendaftar per Program Studi
        </h6>
        <?php if (empty($perProdi)): ?>
        <div class="text-center py-5 text-muted">
          <i class="bi bi-bar-chart" style="font-size:2.5rem;opacity:.3"></i>
          <p class="mt-2 mb-0 small">Belum ada data pendaftar</p>
        </div>
        <?php else: ?>
        <div style="position:relative;height:220px">
          <canvas id="chartProdi"></canvas>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="card border-0 rounded-3 h-100" style="box-shadow:0 2px 12px rgba(0,0,0,.06)">
      <div class="card-body p-4">
        <h6 class="fw-700 mb-3" style="color:var(--primary)">
          <i class="bi bi-pie-chart-fill me-2"></i>Distribusi Status
        </h6>
        <?php $hasStatus = array_sum([$stat['diterima']??0,$stat['menunggu']??0,$stat['revisi']??0,$stat['ditolak']??0]) > 0; ?>
        <?php if (!$hasStatus): ?>
        <div class="text-center py-5 text-muted">
          <i class="bi bi-pie-chart" style="font-size:2.5rem;opacity:.3"></i>
          <p class="mt-2 mb-0 small">Belum ada data</p>
        </div>
        <?php else: ?>
        <div style="position:relative;height:220px">
          <canvas id="chartStatus"></canvas>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- TABLE + AKSI CEPAT -->
<div class="row g-3">
  <div class="col-lg-8">
    <div class="card border-0 rounded-3" style="box-shadow:0 2px 12px rgba(0,0,0,.06)">
      <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
        <h6 class="mb-0 fw-700" style="color:var(--primary)">Detail per Program Studi</h6>
        <a href="<?= url('/admin/pendaftar') ?>" class="btn btn-sm rounded-pill px-3" style="background:var(--primary);color:#fff;font-size:.75rem">
          Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
        </a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover mb-0" style="font-size:.83rem">
          <thead style="background:#f8fafc">
            <tr>
              <th class="ps-3">Program Studi</th>
              <th class="text-center">Jenjang</th>
              <th class="text-center">Total</th>
              <th class="text-center">Diterima</th>
              <th class="text-center">Menunggu</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($perProdi)): ?>
            <tr><td colspan="5" class="text-center text-muted py-5">
              <i class="bi bi-inbox" style="font-size:1.5rem;opacity:.3"></i>
              <p class="mt-2 mb-0 small">Belum ada data pendaftar</p>
            </td></tr>
            <?php else: ?>
            <?php foreach ($perProdi as $row): ?>
            <tr>
              <td class="ps-3">
                <div class="fw-600"><?= htmlspecialchars($row['nama_prodi']) ?></div>
                <div style="font-size:.72rem;color:#94a3b8"><?= htmlspecialchars($row['nama_fakultas'] ?? '') ?></div>
              </td>
              <td class="text-center">
                <span class="badge rounded-pill <?= ($row['jenjang']??'S1') === 'S2' ? 'bg-warning text-dark' : 'bg-primary' ?>" style="font-size:.68rem">
                  <?= htmlspecialchars($row['jenjang'] ?? 'S1') ?>
                </span>
              </td>
              <td class="text-center fw-700"><?= (int)$row['total'] ?></td>
              <td class="text-center"><span class="fw-700" style="color:#16a34a"><?= (int)($row['diterima'] ?? 0) ?></span></td>
              <td class="text-center"><span class="fw-700" style="color:#d97706"><?= (int)($row['menunggu'] ?? 0) ?></span></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <!-- Aksi Cepat -->
    <div class="card border-0 rounded-3 mb-3" style="box-shadow:0 2px 12px rgba(0,0,0,.06)">
      <div class="card-body p-4">
        <h6 class="fw-700 mb-3" style="color:var(--primary)">
          <i class="bi bi-lightning-charge-fill me-2 text-gold"></i>Aksi Cepat
        </h6>
        <div class="d-grid gap-2">
          <a href="<?= url('/admin/pendaftar?status=menunggu') ?>"
             class="btn btn-sm text-start d-flex align-items-center gap-2 rounded-3"
             style="background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;font-size:.82rem;padding:.6rem .9rem">
            <i class="bi bi-shield-check"></i> Verifikasi Berkas
            <?php if (($stat['menunggu'] ?? 0) > 0): ?>
            <span class="badge bg-danger ms-auto"><?= $stat['menunggu'] ?></span>
            <?php endif; ?>
          </a>
          <a href="<?= url('/admin/pendaftar?status=revisi') ?>"
             class="btn btn-sm text-start d-flex align-items-center gap-2 rounded-3"
             style="background:#fffbeb;color:#b45309;border:1px solid #fde68a;font-size:.82rem;padding:.6rem .9rem">
            <i class="bi bi-pencil-square"></i> Pendaftar Revisi
            <?php if (($stat['revisi'] ?? 0) > 0): ?>
            <span class="badge bg-warning text-dark ms-auto"><?= $stat['revisi'] ?></span>
            <?php endif; ?>
          </a>
          <a href="<?= url('/admin/export') ?>"
             class="btn btn-sm text-start d-flex align-items-center gap-2 rounded-3"
             style="background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;font-size:.82rem;padding:.6rem .9rem">
            <i class="bi bi-file-earmark-excel"></i> Export Excel
          </a>
          <a href="<?= url('/admin/tahun-akademik') ?>"
             class="btn btn-sm text-start d-flex align-items-center gap-2 rounded-3"
             style="background:#f5f3ff;color:#6d28d9;border:1px solid #ddd6fe;font-size:.82rem;padding:.6rem .9rem">
            <i class="bi bi-calendar3"></i> Kelola Tahun Akademik
          </a>
          <a href="<?= url('/admin/pengaturan') ?>"
             class="btn btn-sm text-start d-flex align-items-center gap-2 rounded-3"
             style="background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;font-size:.82rem;padding:.6rem .9rem">
            <i class="bi bi-gear"></i> Pengaturan Landing Page
          </a>
        </div>
      </div>
    </div>

    <!-- Status PMB -->
    <div class="card border-0 rounded-3" style="box-shadow:0 2px 12px rgba(0,0,0,.06)">
      <div class="card-body p-4">
        <h6 class="fw-700 mb-3" style="color:var(--primary)">
          <i class="bi bi-info-circle-fill me-2"></i>Status PMB
        </h6>
        <?php if ($tahunAktif): ?>
        <div style="font-size:.82rem">
          <div class="d-flex justify-content-between py-2 border-bottom">
            <span class="text-muted">Tahun Akademik</span>
            <strong><?= htmlspecialchars($tahunAktif['nama']) ?></strong>
          </div>
          <div class="d-flex justify-content-between py-2 border-bottom">
            <span class="text-muted">Mulai Pendaftaran</span>
            <strong><?= !empty($tahunAktif['tanggal_buka']) ? date('d M Y', strtotime($tahunAktif['tanggal_buka'])) : '-' ?></strong>
          </div>
          <div class="d-flex justify-content-between py-2">
            <span class="text-muted">Akhir Pendaftaran</span>
            <strong><?= !empty($tahunAktif['tanggal_tutup']) ? date('d M Y', strtotime($tahunAktif['tanggal_tutup'])) : 'Belum ditentukan' ?></strong>
          </div>
        </div>
        <?php else: ?>
        <p class="text-muted mb-2" style="font-size:.82rem">Tidak ada tahun akademik aktif.</p>
        <a href="<?= url('/admin/tahun-akademik') ?>" class="btn btn-sm rounded-pill px-3" style="background:var(--primary);color:#fff;font-size:.8rem">
          <i class="bi bi-plus-circle me-1"></i> Buka PMB Baru
        </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php
$prodiLabels   = array_column($perProdi, 'nama_prodi');
$prodiData     = array_map(fn($r) => (int)$r['total'], $perProdi);
$prodiDiterima = array_map(fn($r) => (int)($r['diterima'] ?? 0), $perProdi);
$statusData    = [
  $stat['diterima'] ?? 0,
  $stat['menunggu'] ?? 0,
  $stat['revisi']   ?? 0,
  $stat['ditolak']  ?? 0,
];
?>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const labels = <?= json_encode($prodiLabels) ?>;
  const total  = <?= json_encode($prodiData) ?>;
  const terima = <?= json_encode($prodiDiterima) ?>;

  if (labels.length > 0 && document.getElementById('chartProdi')) {
    new Chart(document.getElementById('chartProdi'), {
      type: 'bar',
      data: {
        labels,
        datasets: [
          { label:'Total',    data:total,  backgroundColor:'rgba(26,58,107,.75)',  borderRadius:6 },
          { label:'Diterima', data:terima, backgroundColor:'rgba(22,163,74,.65)',  borderRadius:6 }
        ]
      },
      options: {
        responsive:true, maintainAspectRatio:false,
        plugins:{ legend:{ position:'top', labels:{ font:{size:11}, padding:12 } } },
        scales:{
          x:{ ticks:{ font:{size:10}, maxRotation:30 } },
          y:{ beginAtZero:true, ticks:{ stepSize:1, font:{size:10} } }
        }
      }
    });
  }

  const sd = <?= json_encode($statusData) ?>;
  if (sd.some(v=>v>0) && document.getElementById('chartStatus')) {
    new Chart(document.getElementById('chartStatus'), {
      type:'doughnut',
      data:{
        labels:['Diterima','Menunggu','Revisi','Ditolak'],
        datasets:[{
          data:sd,
          backgroundColor:['#22c55e','#f59e0b','#3b82f6','#ef4444'],
          hoverOffset:8, borderWidth:2, borderColor:'#fff'
        }]
      },
      options:{
        responsive:true, maintainAspectRatio:false,
        plugins:{
          legend:{ position:'bottom', labels:{ padding:14, font:{size:11} } }
        },
        cutout:'65%'
      }
    });
  }
});
</script>