<?php $s = $report['summary'] ?? []; $data = $report['data'] ?? []; $p = $report['period'] ?? []; ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Lagerrapport</h1>
        <a href="/reports" class="text-sm text-gray-500 hover:text-indigo-600 dark:text-gray-400">← Tillbaka</a>
    </div>
    <form class="flex flex-wrap gap-3 items-end bg-white dark:bg-gray-800 rounded-xl shadow p-4">
        <div><label class="block text-xs text-gray-500 mb-1">Plats</label><input type="text" name="location" value="<?= htmlspecialchars($filters['location'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Alla platser" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm px-3 py-1.5"></div>
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm transition">Filtrera</button>
    </form>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center"><div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400"><?= (int)($s['total_articles'] ?? 0) ?></div><div class="text-sm text-gray-500 mt-1">Totalt artiklar</div></div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center"><div class="text-3xl font-bold text-red-500"><?= (int)($s['below_minimum'] ?? 0) ?></div><div class="text-sm text-gray-500 mt-1">Under minimum</div></div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center col-span-2"><div class="text-3xl font-bold text-green-500"><?= number_format((float)($s['total_value'] ?? 0), 2, ',', ' ') ?> kr</div><div class="text-sm text-gray-500 mt-1">Totalt lagervärde</div></div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700"><tr>
                <th class="px-4 py-3 text-left text-gray-500">Art.nr</th>
                <th class="px-4 py-3 text-left text-gray-500">Namn</th>
                <th class="px-4 py-3 text-right text-gray-500">Lagersaldo</th>
                <th class="px-4 py-3 text-right text-gray-500">Min.nivå</th>
                <th class="px-4 py-3 text-right text-gray-500">Inköpspris</th>
                <th class="px-4 py-3 text-right text-gray-500">Värde</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                <?php foreach ($data as $row): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 <?= ($row['min_stock_level'] !== null && $row['stock_quantity'] < $row['min_stock_level']) ? 'bg-red-50 dark:bg-red-900/10' : '' ?>">
                    <td class="px-4 py-3 font-mono text-xs text-gray-600 dark:text-gray-400"><?= htmlspecialchars($row['article_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-800 dark:text-gray-200"><?= htmlspecialchars($row['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-right font-mono"><?= number_format((float)($row['stock_quantity'] ?? 0), 2, ',', ' ') ?></td>
                    <td class="px-4 py-3 text-right font-mono text-gray-500"><?= $row['min_stock_level'] !== null ? number_format((float)$row['min_stock_level'], 2, ',', ' ') : '–' ?></td>
                    <td class="px-4 py-3 text-right font-mono text-gray-500"><?= $row['purchase_price'] !== null ? number_format((float)$row['purchase_price'], 2, ',', ' ') . ' kr' : '–' ?></td>
                    <td class="px-4 py-3 text-right font-mono font-semibold"><?= number_format((float)($row['value'] ?? 0), 2, ',', ' ') ?> kr</td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($data)): ?><tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">Inga artiklar hittades.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
