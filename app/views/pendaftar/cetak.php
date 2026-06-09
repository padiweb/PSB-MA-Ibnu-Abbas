<?php
// views/pendaftar/cetak.php — digunakan dengan layout print
$p    = $pendaftar ?? [];
$docs = $dokumen ?? [];
$logs = $verifikasi_log ?? [];

$statusLabels = ['menunggu'=>'Menunggu Verifikasi','diterima'=>'Diterima','revisi'=>'Perlu Revisi','ditolak'=>'Ditolak'];
$statusColors = ['menunggu'=>'#d97706','diterima'=>'#16a34a','revisi'=>'#2563eb','ditolak'=>'#dc2626'];
$st = $p['status'] ?? 'menunggu';
?>
<div class="print-container">

    <!-- KOP SURAT -->
    <div class="kop">
        <div class="kop-logo">M</div>
        <div class="kop-text">
            <div class="kop-name">Ma'had 'Aly Ibnu Abbas Karanganyar</div>
            <div class="kop-sub">Ma'had 'Aly Tahfidzul Qur'an Waddirosah Al Islamiyah</div>
            <div class="kop-sub2">Bekerjasama dengan Institut Muhammadiyah Ngawi &bull; www.ibnuabbass.com</div>
        </div>
    </div>

    <!-- JUDUL -->
    <div class="doc-title">
        <h2>Bukti Pendaftaran Mahasiswa Baru</h2>
        <div>
            <span class="nomor"><?= htmlspecialchars($p['nomor_pendaftaran'] ?? '') ?></span>
            <span class="status-badge"
                  style="background:<?= $statusColors[$st] ?? '#888' ?>;color:#fff;">
                <?= $statusLabels[$st] ?? ucfirst($st) ?>
            </span>
        </div>
    </div>

    <hr style="border-color:#e0e0e0;margin:14px 0;">

    <!-- DATA DIRI -->
    <div class="section">
        <div class="section-title">Data Diri</div>
        <div>
            <?php $dataDiri = [
                ['Nama Lengkap', $p['nama_lengkap'] ?? '-'],
                ['Tempat Lahir', $p['tempat_lahir'] ?? '-'],
                ['Tanggal Lahir', $p['tanggal_lahir'] ? date('d F Y', strtotime($p['tanggal_lahir'])) : '-'],
                ['Nomor HP', $p['nomor_hp'] ?? '-'],
                ['Nama Ibu Kandung', $p['nama_ibu_kandung'] ?? '-'],
                ['Alamat KTP', $p['alamat'] ?? '-'],
            ]; foreach ($dataDiri as [$lbl, $val]): ?>
            <div class="info-row">
                <span class="info-label"><?= $lbl ?></span>
                <span class="info-value"><?= htmlspecialchars($val) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- PROGRAM STUDI -->
    <div class="section">
        <div class="section-title">Program Studi</div>
        <?php $dataProdi = [
            ['Program Studi', $p['nama_prodi'] ?? '-'],
            ['Jenjang', $p['jenjang'] ?? '-'],
            ['Tahun Akademik', $p['ta_nama'] ?? $p['ta_kode'] ?? '-' ?? '-'],
            ['Tanggal Daftar', $p['created_at'] ? date('d F Y H:i', strtotime($p['created_at'])) : '-'],
        ]; foreach ($dataProdi as [$lbl, $val]): ?>
        <div class="info-row">
            <span class="info-label"><?= $lbl ?></span>
            <span class="info-value"><?= htmlspecialchars($val) ?></span>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- DOKUMEN -->
    <div class="section">
        <div class="section-title">Daftar Dokumen Diunggah</div>
        <table class="docs-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jenis Dokumen</th>
                    <th>Nama File</th>
                    <th>Tanggal Upload</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($docs)): ?>
                <tr><td colspan="5" style="text-align:center;color:#888;">Belum ada dokumen diunggah</td></tr>
                <?php else: ?>
                <?php foreach ($docs as $i => $doc): ?>
                <tr>
                    <td><?= $i+1 ?></td>
                    <td><?= htmlspecialchars($doc['jenis_dokumen']) ?></td>
                    <td style="font-size:8.5pt;"><?= htmlspecialchars($doc['nama_file_asli'] ?? '-') ?></td>
                    <td><?= date('d/m/Y', strtotime($doc['created_at'])) ?></td>
                    <td class="status-ok">✓ Terupload</td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- RIWAYAT VERIFIKASI -->
    <?php if (!empty($logs)): ?>
    <div class="section">
        <div class="section-title">Riwayat Verifikasi</div>
        <table class="docs-table">
            <thead>
                <tr><th>Tanggal</th><th>Status</th><th>Catatan</th></tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?></td>
                    <td><strong><?= ucfirst($log['status_sesudah']) ?></strong></td>
                    <td><?= htmlspecialchars($log['catatan'] ?? '-') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- CATATAN PENTING -->
    <div class="footer-note">
        <strong>Catatan Penting:</strong> Simpan bukti pendaftaran ini. Gunakan Nomor Pendaftaran
        <strong><?= htmlspecialchars($p['nomor_pendaftaran'] ?? '') ?></strong> beserta password untuk login ke
        sistem dan memantau status pendaftaran. Informasi: WhatsApp 0856-1464-905 | www.ibnuabbass.com
    </div>

    <!-- TANDA TANGAN -->
    <div class="ttd-area">
        <div class="ttd-box">
            <div style="font-size:9pt;color:#555;">Karanganyar, <?= date('d F Y') ?></div>
            <div class="ttd-line">
                <strong>Panitia PMB</strong><br>
                <span style="color:#555;">Ma'had 'Aly Ibnu Abbas</span>
            </div>
        </div>
        <div class="ttd-box">
            <div style="font-size:9pt;color:#555;">&nbsp;</div>
            <div class="ttd-line">
                <strong><?= htmlspecialchars($p['nama_lengkap'] ?? 'Pendaftar') ?></strong><br>
                <span style="color:#555;">Pendaftar</span>
            </div>
        </div>
    </div>

    <div style="text-align:center;margin-top:16px;font-size:7.5pt;color:#aaa;border-top:1px solid #eee;padding-top:8px;">
        Dicetak otomatis oleh sistem PMB Ma'had 'Aly Ibnu Abbas &bull; <?= date('d/m/Y H:i') ?>
    </div>

</div>