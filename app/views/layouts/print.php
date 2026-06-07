<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Bukti Pendaftaran') ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11pt; color: #222; background: #fff; }
        .print-container { max-width: 800px; margin: 0 auto; padding: 24px; }

        .kop { display: flex; align-items: center; gap: 16px; border-bottom: 3px solid #1a3a6b; padding-bottom: 12px; margin-bottom: 16px; }
        .kop-logo { width: 72px; height: 72px; display: flex; align-items: center; justify-content: center; border: 2px solid #1a3a6b; border-radius: 50%; font-size: 1.5rem; font-weight: 700; color: #1a3a6b; }
        .kop-text { flex: 1; }
        .kop-name { font-size: 15pt; font-weight: 700; color: #1a3a6b; line-height: 1.2; }
        .kop-sub { font-size: 9pt; color: #555; margin-top: 2px; }
        .kop-sub2 { font-size: 8pt; color: #888; }

        .doc-title { text-align: center; margin: 12px 0; }
        .doc-title h2 { font-size: 13pt; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #1a3a6b; }
        .doc-title .nomor { display: inline-block; background: #1a3a6b; color: #fff; padding: 4px 20px; border-radius: 20px; font-size: 13pt; font-weight: 700; margin-top: 6px; letter-spacing: .08em; }
        .doc-title .status-badge { display: inline-block; padding: 3px 14px; border-radius: 20px; font-size: 9pt; font-weight: 700; margin-top: 6px; margin-left: 8px; }

        .section { margin-bottom: 14px; }
        .section-title { background: #1a3a6b; color: #fff; padding: 5px 12px; font-size: 9pt; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; border-radius: 4px; margin-bottom: 8px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0; }
        .info-row { display: flex; padding: 5px 8px; font-size: 9.5pt; border-bottom: 1px solid #f0f0f0; }
        .info-row:nth-child(odd) { background: #f9f9f9; }
        .info-label { width: 180px; color: #555; flex-shrink: 0; }
        .info-value { font-weight: 600; color: #222; flex: 1; }

        .docs-table { width: 100%; border-collapse: collapse; font-size: 9pt; }
        .docs-table th { background: #f0f4f8; padding: 6px 10px; text-align: left; border: 1px solid #ddd; }
        .docs-table td { padding: 6px 10px; border: 1px solid #ddd; }
        .status-ok { color: #16a34a; font-weight: 700; }
        .status-pending { color: #d97706; font-weight: 700; }
        .status-reject { color: #dc2626; font-weight: 700; }

        .footer-note { margin-top: 20px; padding: 10px 14px; background: #fffbea; border: 1px solid #fde68a; border-radius: 6px; font-size: 8.5pt; color: #92400e; }

        .ttd-area { display: flex; justify-content: space-between; margin-top: 24px; }
        .ttd-box { text-align: center; width: 200px; }
        .ttd-line { border-top: 1px solid #333; margin-top: 60px; padding-top: 4px; font-size: 9pt; }

        .print-btn { position: fixed; bottom: 24px; right: 24px; background: #1a3a6b; color: #fff; border: none; border-radius: 8px; padding: 10px 20px; font-size: 13px; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,.2); display: flex; align-items: center; gap: 8px; }
        @media print {
            .print-btn { display: none; }
            body { background: #fff; }
        }
    </style>
</head>
<body>
<?= $content ?>
<button class="print-btn" onclick="window.print()">
    🖨️ Cetak / Simpan PDF
</button>
</body>
</html>
