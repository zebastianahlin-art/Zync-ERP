<?php
$invoice = $invoice ?? [];
$lines = $lines ?? [];
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Skapa kreditnota</h1>
            <p class="text-sm text-gray-500">Originalfaktura: <?= htmlspecialchars($invoice['invoice_number'] ?? '') ?></p>
        </div>
        <a href="/finance/invoices-out/<?= $invoice['id'] ?>" class="text-sm text-gray-500 hover:text-indigo-600">← Tillbaka</a>
    </div>

    <!-- Originalfaktura info -->
    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-xl p-4 text-sm text-yellow-800 dark:text-yellow-400">
        <strong>Kreditnota skapas för faktura <?= htmlspecialchars($invoice['invoice_number'] ?? '') ?></strong><br>
        Kund: <?= htmlspecialchars($invoice['customer_name'] ?? '') ?> &nbsp;|&nbsp;
        Datum: <?= $invoice['invoice_date'] ?? '' ?> &nbsp;|&nbsp;
        Totalt: <?= number_format((float)($invoice['total_amount'] ?? 0), 2, ',', ' ') ?> <?= htmlspecialchars($invoice['currency'] ?? 'SEK') ?>
    </div>

    <!-- Originalrader -->
    <?php if (!empty($lines)): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Originalfakturans rader</h2>
        </div>
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-500">Beskrivning</th>
                    <th class="px-4 py-3 text-right text-gray-500">Antal</th>
                    <th class="px-4 py-3 text-right text-gray-500">à-pris</th>
                    <th class="px-4 py-3 text-right text-gray-500">Moms %</th>
                    <th class="px-4 py-3 text-right text-gray-500">Summa</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($lines as $line): ?>
                <tr>
                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300"><?= htmlspecialchars($line['description'] ?? '') ?></td>
                    <td class="px-4 py-3 text-right font-mono"><?= (float)$line['quantity'] ?></td>
                    <td class="px-4 py-3 text-right font-mono"><?= number_format((float)$line['unit_price'], 2, ',', ' ') ?></td>
                    <td class="px-4 py-3 text-right"><?= (float)$line['vat_rate'] ?>%</td>
                    <td class="px-4 py-3 text-right font-mono"><?= number_format((float)$line['line_total'], 2, ',', ' ') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- Bekräftelseformulär -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Kreditnotainformation</h2>
        <form method="POST" action="/finance/invoices-out/<?= $invoice['id'] ?>/credit-note" class="space-y-4">
            <?= \App\Core\Csrf::field() ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Anteckning</label>
                <textarea name="notes" rows="3" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm" placeholder="Orsak till kreditnota…"></textarea>
            </div>
            <p class="text-sm text-gray-500">Alla rader från originalfakturan krediteras med negativa belopp. Originalfakturan markeras som krediterad.</p>
            <div class="flex gap-3">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded text-sm transition" onclick="return confirm('Skapa kreditnota för hela fakturan?')">Skapa kreditnota</button>
                <a href="/finance/invoices-out/<?= $invoice['id'] ?>" class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 px-5 py-2 rounded text-sm transition text-gray-700 dark:text-gray-300">Avbryt</a>
            </div>
        </form>
    </div>
</div>
