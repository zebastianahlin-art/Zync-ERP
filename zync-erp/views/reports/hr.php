<?php $s = $report['summary'] ?? []; $data = $report['data'] ?? []; $p = $report['period'] ?? []; ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">HR-rapport</h1>
        <a href="/reports" class="text-sm text-gray-500 hover:text-indigo-600 dark:text-gray-400">← Tillbaka</a>
    </div>
    <form class="flex flex-wrap gap-3 items-end bg-white dark:bg-gray-800 rounded-xl shadow p-4">
        <div><label class="block text-xs text-gray-500 mb-1">Från</label><input type="date" name="from" value="<?= htmlspecialchars($filters['from'] ?? $p['from'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm px-3 py-1.5"></div>
        <div><label class="block text-xs text-gray-500 mb-1">Till</label><input type="date" name="to" value="<?= htmlspecialchars($filters['to'] ?? $p['to'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm px-3 py-1.5"></div>
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm transition">Filtrera</button>
    </form>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center"><div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400"><?= (int)($s['total_employees'] ?? 0) ?></div><div class="text-sm text-gray-500 mt-1">Totalt anställda</div></div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center"><div class="text-3xl font-bold text-green-500"><?= (int)($s['active_employees'] ?? 0) ?></div><div class="text-sm text-gray-500 mt-1">Aktiva</div></div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center"><div class="text-3xl font-bold text-orange-500"><?= (int)($s['absent_today'] ?? 0) ?></div><div class="text-sm text-gray-500 mt-1">Frånvaro idag</div></div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center"><div class="text-3xl font-bold text-blue-500"><?= (int)($s['pending_expenses'] ?? 0) ?></div><div class="text-sm text-gray-500 mt-1">Väntande reseräkn.</div></div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700"><tr>
                <th class="px-4 py-3 text-left text-gray-500">Anst.nr</th>
                <th class="px-4 py-3 text-left text-gray-500">Namn</th>
                <th class="px-4 py-3 text-left text-gray-500">Befattning</th>
                <th class="px-4 py-3 text-left text-gray-500">Status</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                <?php foreach ($data as $row): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-4 py-3 font-mono text-xs text-gray-600 dark:text-gray-400"><?= htmlspecialchars($row['employee_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-800 dark:text-gray-200"><?= htmlspecialchars(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($row['position'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-xs <?= ($row['status'] ?? '') === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' ?>"><?= htmlspecialchars($row['status'] ?? '', ENT_QUOTES, 'UTF-8') ?></span></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($data)): ?><tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">Inga anställda hittades.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
