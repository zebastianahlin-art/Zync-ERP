<?php $assets = $assets ?? []; $statusLabels = ['active'=>'Aktiv','disposed'=>'Avyttrad','written_off'=>'Utskriven']; $statusClasses = ['active'=>'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400','disposed'=>'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400','written_off'=>'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400']; ?>
<div class="space-y-6">
    <?php if (!empty($success)): ?>
    <div class="rounded-lg bg-green-50 dark:bg-green-900/30 px-4 py-3 text-sm text-green-700 dark:text-green-400"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Anläggningstillgångar</h1>
        <a href="/finance/assets/create" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm transition">+ Ny tillgång</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-500">Anl.nr</th>
                    <th class="px-4 py-3 text-left text-gray-500">Namn</th>
                    <th class="px-4 py-3 text-left text-gray-500">Inköpsdatum</th>
                    <th class="px-4 py-3 text-right text-gray-500">Inköpspris</th>
                    <th class="px-4 py-3 text-right text-gray-500">Bokfört värde</th>
                    <th class="px-4 py-3 text-left text-gray-500">Status</th>
                    <th class="px-4 py-3 text-left text-gray-500">Avdelning</th>
                    <th class="px-4 py-3 text-right text-gray-500">Åtgärder</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php if (empty($assets)): ?>
                <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">Inga tillgångar registrerade</td></tr>
                <?php else: ?>
                <?php foreach ($assets as $a): $sc = $statusClasses[$a['status']] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400'; ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                    <td class="px-4 py-3 font-mono text-xs text-gray-500"><?= htmlspecialchars($a['asset_number']) ?></td>
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white"><a href="/finance/assets/<?= $a['id'] ?>" class="hover:text-indigo-600"><?= htmlspecialchars($a['name']) ?></a></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= $a['purchase_date'] ?></td>
                    <td class="px-4 py-3 text-right font-mono"><?= number_format((float)$a['purchase_price'], 2, ',', ' ') ?></td>
                    <td class="px-4 py-3 text-right font-mono"><?= number_format((float)$a['current_value'], 2, ',', ' ') ?></td>
                    <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-xs font-medium <?= $sc ?>"><?= $statusLabels[$a['status']] ?? htmlspecialchars($a['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
                    <td class="px-4 py-3 text-gray-500"><?= htmlspecialchars($a['department_name'] ?? '—') ?></td>
                    <td class="px-4 py-3 text-right flex justify-end gap-2">
                        <a href="/finance/assets/<?= $a['id'] ?>/edit" class="text-indigo-600 hover:underline text-xs">Redigera</a>
                        <form method="POST" action="/finance/assets/<?= $a['id'] ?>/delete" onsubmit="return confirm('Ta bort tillgång?')">
                            <?= \App\Core\Csrf::field() ?>
                            <button class="text-red-500 hover:underline text-xs">Ta bort</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
