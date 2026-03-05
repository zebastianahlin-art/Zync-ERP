<?php $items = $stockData['items'] ?? []; $totalValue = $stockData['total_value'] ?? 0; ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Inventering Ekonomi</h1>
        <a href="/reports" class="text-sm text-gray-500 hover:text-indigo-600 dark:text-gray-400">← Rapporter</a>
    </div>
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 text-center">
            <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400"><?= count($items) ?></div>
            <div class="text-sm text-gray-500 mt-1">Totalt artiklar</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 text-center col-span-2 lg:col-span-2">
            <div class="text-3xl font-bold text-green-500"><?= number_format((float)$totalValue, 2, ',', ' ') ?> kr</div>
            <div class="text-sm text-gray-500 mt-1">Totalt lagervärde</div>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-500">Art.nr</th>
                    <th class="px-4 py-3 text-left text-gray-500">Namn</th>
                    <th class="px-4 py-3 text-left text-gray-500">Enhet</th>
                    <th class="px-4 py-3 text-right text-gray-500">Lagersaldo</th>
                    <th class="px-4 py-3 text-right text-gray-500">Min.nivå</th>
                    <th class="px-4 py-3 text-right text-gray-500">Värde</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                <?php foreach ($items as $item): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 <?= ($item['min_stock_level'] !== null && $item['stock_quantity'] < $item['min_stock_level']) ? 'bg-red-50 dark:bg-red-900/10' : '' ?>">
                    <td class="px-4 py-3 font-mono text-xs text-gray-600 dark:text-gray-400"><?= htmlspecialchars($item['article_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-800 dark:text-gray-200"><?= htmlspecialchars($item['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-500"><?= htmlspecialchars($item['unit'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-right font-mono"><?= number_format((float)($item['stock_quantity'] ?? 0), 2, ',', ' ') ?></td>
                    <td class="px-4 py-3 text-right font-mono text-gray-500"><?= $item['min_stock_level'] !== null ? number_format((float)$item['min_stock_level'], 2, ',', ' ') : '–' ?></td>
                    <td class="px-4 py-3 text-right font-mono font-semibold"><?= number_format((float)($item['value'] ?? 0), 2, ',', ' ') ?> kr</td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($items)): ?>
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Inga artiklar hittades.</td></tr>
                <?php endif; ?>
            </tbody>
            <?php if (!empty($items)): ?>
            <tfoot class="bg-gray-100 dark:bg-gray-700 font-bold">
                <tr>
                    <td colspan="5" class="px-4 py-3 text-right text-gray-700 dark:text-gray-300">Totalt lagervärde:</td>
                    <td class="px-4 py-3 text-right font-mono text-green-600 dark:text-green-400"><?= number_format((float)$totalValue, 2, ',', ' ') ?> kr</td>
                </tr>
            </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>
