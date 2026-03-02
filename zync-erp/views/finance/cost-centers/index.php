<?php $costCenters = $costCenters ?? []; ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kostnadsställen</h1>
        <div class="flex gap-3">
            <a href="/finance" class="text-sm text-gray-500 hover:text-indigo-600">← Ekonomi</a>
            <a href="/finance/cost-centers/create" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">+ Nytt KS</a>
        </div>
    </div>
    <?php if (!empty($success)): ?><div class="rounded-lg bg-green-50 dark:bg-green-900/30 px-4 py-3 text-sm text-green-700 dark:text-green-400"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Kod</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Namn</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Avdelning</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Ansvarig</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Budget</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500">Aktiv</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($costCenters as $cc): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3 font-mono text-sm font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($cc['code']) ?></td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300"><?= htmlspecialchars($cc['name']) ?></td>
                    <td class="px-4 py-3 text-sm text-gray-600"><?= htmlspecialchars($cc['department_name'] ?? '—') ?></td>
                    <td class="px-4 py-3 text-sm text-gray-600"><?= htmlspecialchars($cc['responsible_name'] ?? '—') ?></td>
                    <td class="px-4 py-3 text-sm text-right font-mono"><?= number_format((float)($cc['budget'] ?? 0), 0, ',', ' ') ?> kr</td>
                    <td class="px-4 py-3 text-center"><?= $cc['is_active'] ? '✅' : '❌' ?></td>
                    <td class="px-4 py-3 flex gap-2">
                        <a href="/finance/cost-centers/<?= $cc['id'] ?>/edit" class="text-indigo-600 hover:underline text-xs">Redigera</a>
                        <form method="POST" action="/finance/cost-centers/<?= $cc['id'] ?>/delete" class="inline"><?= \App\Core\Csrf::field() ?><button onclick="return confirm('Ta bort?')" class="text-red-500 hover:text-red-700 text-xs">Ta bort</button></form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($costCenters)): ?>
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Inga kostnadsställen</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
