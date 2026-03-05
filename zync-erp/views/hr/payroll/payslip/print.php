<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Lönespecifikation – <?= htmlspecialchars(($payslip['first_name'] ?? '') . ' ' . ($payslip['last_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #333; padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #6366f1; }
        .company { font-size: 22px; font-weight: bold; color: #6366f1; }
        .doc-title { font-size: 16px; font-weight: bold; text-align: right; }
        .doc-subtitle { color: #666; font-size: 12px; text-align: right; margin-top: 4px; }
        .section { margin-bottom: 24px; }
        .section-title { font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.05em; color: #6366f1; margin-bottom: 10px; padding-bottom: 4px; border-bottom: 1px solid #e5e7eb; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 6px 0; vertical-align: top; }
        td:first-child { color: #666; width: 50%; }
        td:last-child { font-weight: 500; text-align: right; }
        .total-row td { font-size: 16px; font-weight: bold; color: #6366f1; padding-top: 12px; border-top: 2px solid #e5e7eb; }
        .deduction { color: #dc2626; }
        .footer { margin-top: 40px; padding-top: 16px; border-top: 1px solid #e5e7eb; color: #999; font-size: 10px; text-align: center; }
        @media print {
            body { padding: 20px; }
            @page { margin: 1cm; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="company">ZYNC ERP</div>
            <div style="color:#666; margin-top:4px; font-size:11px;">Lönesystem</div>
        </div>
        <div>
            <div class="doc-title">LÖNESPECIFIKATION</div>
            <div class="doc-subtitle"><?= htmlspecialchars($payslip['period_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Anst&#228;lld</div>
        <table>
            <tr>
                <td>Namn</td>
                <td><?= htmlspecialchars(($payslip['first_name'] ?? '') . ' ' . ($payslip['last_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
            <tr>
                <td>Period</td>
                <td><?= htmlspecialchars($payslip['period_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
            <tr>
                <td>Utbetalt</td>
                <td><?= htmlspecialchars(date('Y-m-d'), ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">L&#246;neunderlag</div>
        <table>
            <tr>
                <td>Grundl&#246;n</td>
                <td><?= number_format((float)($payslip['base_pay'] ?? 0), 2, ',', ' ') ?> kr</td>
            </tr>
            <tr>
                <td>OB-till&#228;gg</td>
                <td><?= number_format((float)($payslip['ob_amount'] ?? 0), 2, ',', ' ') ?> kr</td>
            </tr>
            <tr>
                <td>&#214;vertid</td>
                <td><?= number_format((float)($payslip['overtime_amount'] ?? 0), 2, ',', ' ') ?> kr</td>
            </tr>
            <tr>
                <td>&#214;vriga till&#228;gg</td>
                <td><?= number_format((float)($payslip['other_additions'] ?? 0), 2, ',', ' ') ?> kr</td>
            </tr>
            <tr style="border-top: 1px solid #e5e7eb;">
                <td><strong>Bruttol&#246;n</strong></td>
                <td><strong><?= number_format((float)($payslip['gross_pay'] ?? 0), 2, ',', ' ') ?> kr</strong></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Avdrag</div>
        <table>
            <tr>
                <td>Prelimin&#228;rskatt</td>
                <td class="deduction">&minus; <?= number_format((float)($payslip['tax_amount'] ?? 0), 2, ',', ' ') ?> kr</td>
            </tr>
            <tr>
                <td>Sociala avgifter (arbetsgivaravgift)</td>
                <td class="deduction">&minus; <?= number_format((float)($payslip['social_security_amount'] ?? 0), 2, ',', ' ') ?> kr</td>
            </tr>
            <tr>
                <td>&#214;vriga avdrag</td>
                <td class="deduction">&minus; <?= number_format((float)($payslip['deductions'] ?? 0) + (float)($payslip['other_deductions'] ?? 0), 2, ',', ' ') ?> kr</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <table>
            <tr class="total-row">
                <td>Nettol&#246;n att utbetala</td>
                <td><?= number_format((float)($payslip['net_pay'] ?? 0), 2, ',', ' ') ?> kr</td>
            </tr>
        </table>
    </div>

    <?php if (!empty($payslip['notes'])): ?>
    <div class="section">
        <div class="section-title">Anteckningar</div>
        <p><?= htmlspecialchars($payslip['notes'], ENT_QUOTES, 'UTF-8') ?></p>
    </div>
    <?php endif; ?>

    <div class="footer">
        Genererad <?= date('Y-m-d H:i') ?> | ZYNC ERP Lönesystem
    </div>

    <script>window.print();</script>
</body>
</html>
