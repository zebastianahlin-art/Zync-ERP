<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Rapport – ZYNC ERP', ENT_QUOTES, 'UTF-8') ?></title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #111; background: #fff; padding: 20mm; }
        h1 { font-size: 20px; margin-bottom: 4px; }
        h2 { font-size: 14px; margin: 16px 0 6px; border-bottom: 1px solid #ccc; padding-bottom: 4px; color: #374151; }
        .meta { color: #6b7280; font-size: 11px; margin-bottom: 16px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 9999px; font-size: 10px; font-weight: 600; }
        .badge-blue { background: #dbeafe; color: #1e40af; }
        .badge-green { background: #dcfce7; color: #166534; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th, td { text-align: left; padding: 5px 8px; border: 1px solid #e5e7eb; font-size: 11px; }
        th { background: #f9fafb; font-weight: 600; color: #374151; }
        .text-right { text-align: right; }
        .total-row { font-weight: 700; background: #f3f4f6; }
        .progress-bar-wrap { background: #e5e7eb; height: 8px; border-radius: 4px; overflow: hidden; margin: 4px 0; }
        .progress-bar { height: 8px; }
        .bar-green { background: #22c55e; }
        .bar-yellow { background: #eab308; }
        .bar-red { background: #ef4444; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px; margin-bottom: 12px; }
        .info-item { padding: 6px 10px; border: 1px solid #e5e7eb; border-radius: 4px; }
        .info-label { font-size: 10px; color: #6b7280; margin-bottom: 2px; }
        .info-value { font-size: 12px; font-weight: 600; }
        .footer { margin-top: 24px; text-align: right; font-size: 10px; color: #9ca3af; }
        @media print {
            body { padding: 10mm; }
            @page { margin: 10mm; }
        }
    </style>
</head>
<body>
<?= $content ?>
<div class="footer">Genererad <?= date('Y-m-d H:i') ?> &nbsp;&bull;&nbsp; ZYNC ERP</div>
<div style="margin-top:16px; text-align:center;">
    <button onclick="window.print()" style="padding:8px 20px; background:#4f46e5; color:#fff; border:none; border-radius:6px; font-size:13px; cursor:pointer;">&#128196; Skriv ut / Spara PDF</button>
    <button onclick="window.close()" style="padding:8px 16px; background:#6b7280; color:#fff; border:none; border-radius:6px; font-size:13px; cursor:pointer; margin-left:8px;">Stäng</button>
</div>
</body>
</html>
