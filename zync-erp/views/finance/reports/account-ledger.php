<?php
$account = $account ?? [];
$lines = $lines ?? [];
$from = $from ?? '';
$to = $to ?? '';
$opening_balance = $opening_balance ?? 0;
$closing_balance = $closing_balance ?? 0;
?>
<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kontoutdrag</h1>
            <p class="text-lg text-indigo-600 font-mono"><?= htmlspecialchars($account['account_number'] ?? '') ?> — <?= htmlspecialchars($account['name'] ?? '') ?></p>
        </div>
        <a href="/finance/reports/ledger" class="text-sm text-gray-500 hover:text-indigo-600">← Huvudbok</a>
    </div>

    <form class="flex gap-3 items-end flex-wrap">
        <div><label class="block text-xs text-gray-500 mb-1">Från</label><input type="date" name="from" value="<?= $from ?>" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"></div>
        <div><label class="block text-xs text-gray-500 mb-1">Till</label><input type="date" name="to" value="<?= $to ?>" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"></div>
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm transition">Visa</button>
    </form>

    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
            <p class="text-xs text-gray-500">Ingående saldo</p>
            <p class="text-xl font-mono font-bold text-gray-900 dark:text-white"><?= number_format($opening_balance, 2, ',', ' ') ?></p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
            <p class="text-xs text-gray-500">Transaktioner</p>
            <p class="text-xl font-mono font-bold text-gray-900 dark:text-white"><?= count($lines) ?></p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
            <p class="text-xs text-gray-500">Utgående saldo</p>
            <p class="text-xl font-mono font-bold <?= $closing_balance < 0 ? 'text-red-600' : 'text-indigo-600' ?>"><?= number_format($closing_balance, 2, ',', ' ') ?></p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-500">Datum</th>
                    <th class="px-4 py-3 text-left text-gray-500">Verifikation</th>
                    <th class="px-4 py-3 text-left text-gray-500">Beskrivning</th>
                    <th class="px-4 py-3 text-left text-gray-500">KS</th>
                    <th class="px-4 py-3 text-right text-gray-500">Debet</th>
                    <th class="px-4 py-3 text-right text-gray-500">Kredit</th>
                    <th class="px-4 py-3 text-right text-gray-500">Saldo</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php if (empty($lines)): ?>
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Inga transaktioner i vald period</td></tr>
                <?php else: ?>
                <?php foreach ($lines as $line): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-2 text-gray-600 dark:text-gray-400"><?= $line['entry_date'] ?></td>
                    <td class="px-4 py-2 font-mono text-indigo-600 text-xs"><?= htmlspecialchars($line['voucher_number']) ?></td>
                    <td class="px-4 py-2 text-gray-700 dark:text-gray-300"><?= htmlspecialchars($line['description'] ?? $line['entry_description'] ?? '') ?></td>
                    <td class="px-4 py-2 text-xs text-gray-500"><?= htmlspecialchars($line['cost_center_code'] ?? '') ?></td>
                    <td class="px-4 py-2 text-right font-mono <?= (float)$line['debit'] > 0 ? '' : 'text-gray-300' ?>"><?= number_format((float)$line['debit'], 2, ',', ' ') ?></td>
                    <td class="px-4 py-2 text-right font-mono <?= (float)$line['credit'] > 0 ? '' : 'text-gray-300' ?>"><?= number_format((float)$line['credit'], 2, ',', ' ') ?></td>
                    <td class="px-4 py-2 text-right font-mono font-bold <?= (float)$line['running_balance'] < 0 ? 'text-red-600' : '' ?>"><?= number_format((float)$line['running_balance'], 2, ',', ' ') ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
