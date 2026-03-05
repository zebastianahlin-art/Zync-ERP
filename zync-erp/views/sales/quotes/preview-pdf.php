<?php
$total = 0;
foreach ($lines as $l) {
    $total += (float)$l['quantity'] * (float)$l['unit_price'] * (1 - (float)$l['discount'] / 100);
}
$statusLabels = ['draft' => 'Utkast', 'sent' => 'Skickad', 'accepted' => 'Accepterad', 'rejected' => 'Avvisad', 'expired' => 'Utgången'];
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offert <?= htmlspecialchars($quote['quote_number'], ENT_QUOTES, 'UTF-8') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
        }
    </style>
</head>
<body class="bg-gray-50 p-8">

<div class="no-print mb-6 flex gap-3">
    <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
        Skriv ut / Spara som PDF
    </button>
    <a href="/sales/quotes/<?= (int)$quote['id'] ?>" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg">
        &larr; Tillbaka
    </a>
</div>

<div class="max-w-3xl mx-auto bg-white shadow-lg p-8 space-y-6">
    <!-- Huvud -->
    <div class="flex justify-between items-start border-b border-gray-200 pb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">OFFERT</h1>
            <p class="text-xl font-semibold text-indigo-600 mt-1"><?= htmlspecialchars($quote['quote_number'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div class="text-right text-sm text-gray-600 space-y-1">
            <p class="font-semibold text-gray-900">ZYNC ERP</p>
            <p>Datum: <?= htmlspecialchars(date('Y-m-d', strtotime($quote['created_at'])), ENT_QUOTES, 'UTF-8') ?></p>
            <?php if (!empty($quote['valid_until'])): ?>
            <p>Giltig till: <?= htmlspecialchars($quote['valid_until'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
            <p>Status: <?= htmlspecialchars($statusLabels[$quote['status']] ?? $quote['status'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
    </div>

    <!-- Kundinfo -->
    <?php if (!empty($quote['customer_name'])): ?>
    <div>
        <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Kund</h2>
        <p class="text-lg font-semibold text-gray-900"><?= htmlspecialchars($quote['customer_name'], ENT_QUOTES, 'UTF-8') ?></p>
    </div>
    <?php endif; ?>

    <!-- Villkor -->
    <?php if (!empty($quote['delivery_terms']) || !empty($quote['payment_terms'])): ?>
    <div class="grid grid-cols-2 gap-4 text-sm">
        <?php if (!empty($quote['delivery_terms'])): ?>
        <div>
            <span class="text-gray-500 block text-xs uppercase tracking-wider mb-1">Leveransvillkor</span>
            <span class="font-medium text-gray-900"><?= htmlspecialchars($quote['delivery_terms'], ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <?php endif; ?>
        <?php if (!empty($quote['payment_terms'])): ?>
        <div>
            <span class="text-gray-500 block text-xs uppercase tracking-wider mb-1">Betalningsvillkor</span>
            <span class="font-medium text-gray-900"><?= htmlspecialchars($quote['payment_terms'], ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Offertrader -->
    <div>
        <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Offertrader</h2>
        <table class="w-full text-sm border border-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase border-b border-gray-200">Artikel</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase border-b border-gray-200">Beskrivning</th>
                    <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600 uppercase border-b border-gray-200">Antal</th>
                    <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600 uppercase border-b border-gray-200">À-pris</th>
                    <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600 uppercase border-b border-gray-200">Rab %</th>
                    <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600 uppercase border-b border-gray-200">Summa</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (!empty($lines)): ?>
                <?php foreach ($lines as $line):
                    $lineTotal = (float)$line['quantity'] * (float)$line['unit_price'] * (1 - (float)$line['discount'] / 100);
                ?>
                <tr>
                    <td class="px-4 py-2 text-gray-500 text-xs"><?= htmlspecialchars($line['article_number'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-2 text-gray-900"><?= htmlspecialchars($line['description'] ?? ($line['article_name'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-2 text-right text-gray-900"><?= number_format((float)$line['quantity'], 2, ',', ' ') ?></td>
                    <td class="px-4 py-2 text-right text-gray-900"><?= number_format((float)$line['unit_price'], 2, ',', ' ') ?> kr</td>
                    <td class="px-4 py-2 text-right text-gray-900"><?= number_format((float)$line['discount'], 1, ',', '') ?> %</td>
                    <td class="px-4 py-2 text-right font-medium text-gray-900"><?= number_format($lineTotal, 2, ',', ' ') ?> kr</td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="6" class="px-4 py-3 text-center text-gray-400 text-sm">Inga offertrader</td>
                </tr>
                <?php endif; ?>
            </tbody>
            <tfoot class="border-t-2 border-gray-300 bg-gray-50">
                <tr>
                    <td colspan="5" class="px-4 py-3 text-right font-semibold text-gray-700">Totalsumma (exkl. moms):</td>
                    <td class="px-4 py-3 text-right font-bold text-gray-900 text-base"><?= number_format($total, 2, ',', ' ') ?> kr</td>
                </tr>
                <tr>
                    <td colspan="5" class="px-4 py-1 text-right text-sm text-gray-500">Moms 25%:</td>
                    <td class="px-4 py-1 text-right text-sm text-gray-700"><?= number_format($total * 0.25, 2, ',', ' ') ?> kr</td>
                </tr>
                <tr>
                    <td colspan="5" class="px-4 py-2 text-right font-bold text-gray-900">Totalt inkl. moms:</td>
                    <td class="px-4 py-2 text-right font-bold text-indigo-600 text-lg"><?= number_format($total * 1.25, 2, ',', ' ') ?> kr</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Anteckningar -->
    <?php if (!empty($quote['notes'])): ?>
    <div class="border-t border-gray-200 pt-4">
        <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Anteckningar</h2>
        <p class="text-sm text-gray-700"><?= nl2br(htmlspecialchars($quote['notes'], ENT_QUOTES, 'UTF-8')) ?></p>
    </div>
    <?php endif; ?>

    <!-- Sidfot -->
    <div class="border-t border-gray-200 pt-4 text-xs text-gray-400 text-center">
        <p>Offert skapad av ZYNC ERP &mdash; <?= htmlspecialchars(date('Y-m-d', strtotime($quote['created_at'])), ENT_QUOTES, 'UTF-8') ?></p>
    </div>
</div>

</body>
</html>
