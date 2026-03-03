<?php
function inspResultBadge(string $r): string {
    $m = [
        'passed'      => ['Godkänd','bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'],
        'failed'      => ['Underkänd','bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'],
        'conditional' => ['Villkorlig','bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'],
    ];
    return '<span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium '.($m[$r][1]??'bg-gray-100 text-gray-700').'">'.($m[$r][0]??htmlspecialchars($r, ENT_QUOTES, 'UTF-8')).'</span>';
}
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Besiktningar</h1>
        <div class="flex gap-2">
            <?php if ($overdueCount > 0): ?>
            <a href="/maintenance/inspections/overdue" class="px-3 py-2 text-sm bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-lg hover:bg-red-200 transition">⚠ <?= (int) $overdueCount ?> förfallna</a>
            <?php endif; ?>
            <a href="/maintenance/inspections/create" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">+ Nytt objekt</a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Namn</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Typ</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Plats</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Senaste besiktning</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Nästa besiktning</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Resultat</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($inspections as $insp): ?>
                    <?php $overdue = !empty($insp['next_inspection_date']) && strtotime($insp['next_inspection_date']) < time(); ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 <?= $overdue ? 'bg-red-50 dark:bg-red-900/10' : '' ?>">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                            <a href="/maintenance/inspections/<?= $insp['id'] ?>" class="hover:underline"><?= htmlspecialchars($insp['name'], ENT_QUOTES, 'UTF-8') ?></a>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($insp['type'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($insp['location'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($insp['last_inspection_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 <?= $overdue ? 'text-red-600 dark:text-red-400 font-medium' : 'text-gray-600 dark:text-gray-400' ?>">
                            <?= htmlspecialchars($insp['next_inspection_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                            <?php if ($overdue): ?><span class="ml-1 text-xs">⚠</span><?php endif; ?>
                        </td>
                        <td class="px-4 py-3">
                            <?php if (!empty($insp['last_inspection_result'])): ?>
                            <?= inspResultBadge($insp['last_inspection_result']) ?>
                            <?php else: ?>
                            <span class="text-gray-400 text-xs">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="/maintenance/inspections/<?= $insp['id'] ?>/edit" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline mr-3">Redigera</a>
                            <form method="POST" action="/maintenance/inspections/<?= $insp['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort besiktningsobjekt?')">
                                <?= \App\Core\Csrf::field() ?>
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700">Ta bort</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($inspections)): ?>
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Inga besiktningsobjekt registrerade</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
