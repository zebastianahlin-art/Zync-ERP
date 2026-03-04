<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Faktura <?= htmlspecialchars($invoice['invoice_number'] ?? '') ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; color: #1a1a1a; margin: 0; padding: 0; }
        .page { max-width: 800px; margin: 0 auto; padding: 40px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 32px; }
        .company { font-size: 11px; color: #666; }
        h1 { font-size: 28px; margin: 0 0 4px; color: #1a1a1a; }
        .invoice-meta { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px; }
        .meta-box { background: #f9f9f9; padding: 16px; border-radius: 6px; }
        .meta-box h3 { margin: 0 0 8px; font-size: 11px; text-transform: uppercase; color: #666; letter-spacing: 0.5px; }
        .meta-box p { margin: 2px 0; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-top: 24px; }
        th { background: #f0f0f0; text-align: left; padding: 8px 12px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.3px; color: #666; border-bottom: 2px solid #ddd; }
        th.right, td.right { text-align: right; }
        td { padding: 8px 12px; border-bottom: 1px solid #eee; }
        .totals { margin-top: 16px; display: flex; justify-content: flex-end; }
        .totals-table { width: 280px; }
        .totals-table td { padding: 4px 12px; font-size: 13px; border: none; }
        .totals-table .total-row { font-weight: bold; font-size: 16px; border-top: 2px solid #1a1a1a; }
        .footer { margin-top: 48px; padding-top: 16px; border-top: 1px solid #ddd; font-size: 11px; color: #888; text-align: center; }
        .print-btn { position: fixed; top: 20px; right: 20px; background: #4f46e5; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-size: 14px; }
        @media print { .print-btn { display: none; } body { padding: 0; } .page { padding: 20px; } }
    </style>
</head>
<body>
<button class="print-btn" onclick="window.print()">🖨 Skriv ut</button>
<div class="page">
    <div class="header">
        <div>
            <h1>FAKTURA</h1>
            <p style="font-size:20px; font-weight:bold; color:#4f46e5; margin:0"><?= htmlspecialchars($invoice['invoice_number'] ?? '') ?></p>
        </div>
        <div class="company">
            <strong>Ditt Företag AB</strong><br>
            Företagsgatan 1<br>
            123 45 Stad<br>
            org.nr: 556000-0000
        </div>
    </div>

    <div class="invoice-meta">
        <div class="meta-box">
            <h3>Kund</h3>
            <p><strong><?= htmlspecialchars($invoice['customer_name'] ?? '') ?></strong></p>
            <p><?= htmlspecialchars($invoice['customer_address'] ?? '') ?></p>
            <p><?= htmlspecialchars(($invoice['customer_postal_code'] ?? '') . ' ' . ($invoice['customer_city'] ?? '')) ?></p>
            <p><?= htmlspecialchars($invoice['customer_email'] ?? '') ?></p>
        </div>
        <div class="meta-box">
            <h3>Fakturainformation</h3>
            <p><strong>Fakturanr:</strong> <?= htmlspecialchars($invoice['invoice_number'] ?? '') ?></p>
            <p><strong>Fakturadatum:</strong> <?= $invoice['invoice_date'] ?? '' ?></p>
            <p><strong>Förfallodatum:</strong> <?= $invoice['due_date'] ?? '' ?></p>
            <p><strong>Betalningsvillkor:</strong> <?= htmlspecialchars($invoice['payment_terms'] ?? '') ?></p>
            <?php if (!empty($invoice['ocr_number'])): ?>
            <p><strong>OCR:</strong> <span style="font-family:monospace"><?= htmlspecialchars($invoice['ocr_number']) ?></span></p>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($invoice['our_reference']) || !empty($invoice['your_reference'])): ?>
    <div style="margin-bottom:16px; font-size:12px; color:#555;">
        <?php if (!empty($invoice['our_reference'])): ?><span>Vår ref: <?= htmlspecialchars($invoice['our_reference']) ?></span> &nbsp;<?php endif; ?>
        <?php if (!empty($invoice['your_reference'])): ?><span>Er ref: <?= htmlspecialchars($invoice['your_reference']) ?></span><?php endif; ?>
    </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Beskrivning</th>
                <th class="right">Antal</th>
                <th class="right">à-pris</th>
                <th class="right">Moms %</th>
                <th class="right">Rabatt</th>
                <th class="right">Summa</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lines as $line): ?>
            <tr>
                <td><?= htmlspecialchars($line['description'] ?? '') ?></td>
                <td class="right" style="font-family:monospace"><?= (float)$line['quantity'] ?></td>
                <td class="right" style="font-family:monospace"><?= number_format((float)$line['unit_price'], 2, ',', ' ') ?></td>
                <td class="right"><?= (float)$line['vat_rate'] ?>%</td>
                <td class="right"><?= (float)($line['discount'] ?? 0) ?>%</td>
                <td class="right" style="font-family:monospace"><?= number_format((float)$line['line_total'], 2, ',', ' ') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="totals">
        <table class="totals-table">
            <tr><td>Netto (exkl. moms)</td><td class="right" style="font-family:monospace"><?= number_format((float)($invoice['subtotal'] ?? 0), 2, ',', ' ') ?></td></tr>
            <tr><td>Moms</td><td class="right" style="font-family:monospace"><?= number_format((float)($invoice['vat_amount'] ?? 0), 2, ',', ' ') ?></td></tr>
            <?php if ((float)($invoice['rounding'] ?? 0) != 0): ?>
            <tr><td>Öresavrundning</td><td class="right" style="font-family:monospace"><?= number_format((float)$invoice['rounding'], 2, ',', ' ') ?></td></tr>
            <?php endif; ?>
            <tr class="total-row"><td>TOTALT <?= htmlspecialchars($invoice['currency'] ?? 'SEK') ?></td><td class="right" style="font-family:monospace"><?= number_format((float)($invoice['total_amount'] ?? 0), 2, ',', ' ') ?></td></tr>
        </table>
    </div>

    <?php if (!empty($invoice['notes'])): ?>
    <div style="margin-top:32px; font-size:12px; color:#555;">
        <strong>Anteckningar:</strong><br>
        <?= nl2br(htmlspecialchars($invoice['notes'])) ?>
    </div>
    <?php endif; ?>

    <div class="footer">
        Tack för er beställning! | Bankgiro: 123-4567 | E-post: faktura@foretag.se | Webbplats: www.foretag.se
    </div>
</div>
</body>
</html>
