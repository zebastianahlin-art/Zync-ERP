<?php
$assets = $assets ?? [];
$liabilities = $liabilities ?? [];
$from = $from ?? '';
$to = $to ?? '';

$totalAssets = array_sum(array_column($assets, 'balance'));
$totalLiabilities = array_sum(array_column($liabilities, 'balance'));
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Balansräkning</h1>
        <a href="/finance" class="text-sm text-gray-500 hover:text-indigo-600">← Ekonomi</a>
    </div>

    <form class="flex gap-3 items-end flex-wrap">
        <div><label class="block text-xs text-gray-500 mb-1">Från</label><input type="date" name="from" value="<?= $from ?>" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"></div>
        <div><label class="block text-xs text-gray-500 mb-1">Till</label><input type="date" name="to" value="<?= $to ?>" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"></div>
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm transition">Visa</button>
    </form>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Tillgångar -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
            <div class="px-6 py-4 bg-indigo-50 dark:bg-indigo-900/20 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-indigo-700 dark:text-indigo-400">Tillgångar (1xxx)</h2>
            </div>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500">Konto</th>
                        <th class="px-4 py-3 text-left text-gray-500">Namn</th>
                        <th class="px-4 py-3 text-right text-gray-500">Saldo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($assets as $row): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-2 font-mono text-xs text-gray-500"><?= htmlspecialchars($row['account_number']) ?></td>
                        <td class="px-4 py-2 text-gray-700 dark:text-gray-300"><?= htmlspecialchars($row['account_name']) ?></td>
                        <td class="px-4 py-2 text-right font-mono font-medium <?= (float)$row['balance'] < 0 ? 'text-red-600' : '' ?>"><?= number_format((float)$row['balance'], 2, ',', ' ') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="bg-indigo-50 dark:bg-indigo-900/20 font-bold">
                        <td colspan="2" class="px-4 py-3 text-indigo-700 dark:text-indigo-400">Summa tillgångar</td>
                        <td class="px-4 py-3 text-right font-mono text-indigo-700 dark:text-indigo-400"><?= number_format($totalAssets, 2, ',', ' ') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Skulder & Eget kapital -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
            <div class="px-6 py-4 bg-orange-50 dark:bg-orange-900/20 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-orange-700 dark:text-orange-400">Skulder &amp; Eget kapital (2xxx)</h2>
            </div>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500">Konto</th>
                        <th class="px-4 py-3 text-left text-gray-500">Namn</th>
                        <th class="px-4 py-3 text-right text-gray-500">Saldo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($liabilities as $row): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-2 font-mono text-xs text-gray-500"><?= htmlspecialchars($row['account_number']) ?></td>
                        <td class="px-4 py-2 text-gray-700 dark:text-gray-300"><?= htmlspecialchars($row['account_name']) ?></td>
                        <td class="px-4 py-2 text-right font-mono font-medium <?= (float)$row['balance'] < 0 ? 'text-red-600' : '' ?>"><?= number_format((float)$row['balance'], 2, ',', ' ') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="bg-orange-50 dark:bg-orange-900/20 font-bold">
                        <td colspan="2" class="px-4 py-3 text-orange-700 dark:text-orange-400">Summa skulder &amp; eget kapital</td>
                        <td class="px-4 py-3 text-right font-mono text-orange-700 dark:text-orange-400"><?= number_format($totalLiabilities, 2, ',', ' ') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Sammanfattning -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 flex justify-between items-center">
        <span class="text-lg font-semibold text-gray-700 dark:text-gray-300">Differens (tillgångar — skulder/eget kapital)</span>
        <span class="text-2xl font-bold font-mono <?= abs($totalAssets - $totalLiabilities) > 0.01 ? 'text-red-600' : 'text-green-600' ?>"><?= number_format($totalAssets - $totalLiabilities, 2, ',', ' ') ?></span>
    </div>
</div>
