<?php $s = $report['summary'] ?? []; $data = $report['data'] ?? []; $p = $report['period'] ?? []; ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Ekonomirapport</h1>
        <a href="/reports" class="text-sm text-gray-500 hover:text-indigo-600 dark:text-gray-400">← Tillbaka</a>
    </div>
    <form class="flex flex-wrap gap-3 items-end bg-white dark:bg-gray-800 rounded-xl shadow p-4">
        <div><label class="block text-xs text-gray-500 mb-1">Från</label><input type="date" name="from" value="<?= htmlspecialchars($filters['from'] ?? $p['from'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm px-3 py-1.5"></div>
        <div><label class="block text-xs text-gray-500 mb-1">Till</label><input type="date" name="to" value="<?= htmlspecialchars($filters['to'] ?? $p['to'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm px-3 py-1.5"></div>
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm transition">Filtrera</button>
    </form>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center"><div class="text-3xl font-bold text-green-500"><?= number_format((float)($s['revenue'] ?? 0), 0, ',', ' ') ?> kr</div><div class="text-sm text-gray-500 mt-1">Intäkter</div></div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center"><div class="text-3xl font-bold text-red-500"><?= number_format((float)($s['costs'] ?? 0), 0, ',', ' ') ?> kr</div><div class="text-sm text-gray-500 mt-1">Kostnader</div></div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center"><div class="text-3xl font-bold <?= ($s['result'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' ?>"><?= number_format((float)($s['result'] ?? 0), 0, ',', ' ') ?> kr</div><div class="text-sm text-gray-500 mt-1">Resultat</div></div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center"><div class="text-3xl font-bold text-orange-500"><?= (int)($s['unpaid_invoices'] ?? 0) ?></div><div class="text-sm text-gray-500 mt-1">Obetalda fakturor</div></div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700"><tr>
                <th class="px-4 py-3 text-left text-gray-500">Månad</th>
                <th class="px-4 py-3 text-right text-gray-500">Intäkter</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                <?php foreach ($data as $row): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-4 py-3 text-gray-800 dark:text-gray-200"><?= htmlspecialchars($row['month'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-right font-mono font-semibold text-green-600 dark:text-green-400"><?= number_format((float)($row['revenue'] ?? 0), 2, ',', ' ') ?> kr</td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($data)): ?><tr><td colspan="2" class="px-4 py-6 text-center text-gray-400">Inga fakturor för vald period.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
