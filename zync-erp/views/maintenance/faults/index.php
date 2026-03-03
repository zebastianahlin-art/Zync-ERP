<?php
function faultStatusBadge(string $s): string {
    $m = [
        'reported'     => ['Rapporterad','bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'],
        'acknowledged' => ['Bekräftad','bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'],
        'assigned'     => ['Tilldelad','bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300'],
        'in_progress'  => ['Pågående','bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'],
        'resolved'     => ['Löst','bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'],
        'closed'       => ['Stängd','bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
    ];
    return '<span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium '.($m[$s][1]??'bg-gray-100 text-gray-700').'">'.($m[$s][0]??$s).'</span>';
}
function faultPrioBadge(string $p): string {
    $m = ['low'=>['Låg','text-gray-500'],'normal'=>['Normal','text-blue-600'],'high'=>['Hög','text-orange-600'],'urgent'=>['Brådskande','text-red-600'],'critical'=>['Kritisk','text-red-700 font-bold']];
    return '<span class="text-xs '.($m[$p][1]??'text-gray-500').'">'.($m[$p][0]??$p).'</span>';
}
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Felanmälningar</h1>
        <a href="/maintenance/faults/create" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">+ Ny felanmälan</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Nummer</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Titel</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Maskin/Utrustning</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Prioritet</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Status</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Tilldelad</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Skapad</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($faults as $f): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-mono text-xs text-indigo-600 dark:text-indigo-400">
                            <a href="/maintenance/faults/<?= $f['id'] ?>"><?= htmlspecialchars($f['fault_number'], ENT_QUOTES, 'UTF-8') ?></a>
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                            <a href="/maintenance/faults/<?= $f['id'] ?>" class="hover:underline"><?= htmlspecialchars($f['title'], ENT_QUOTES, 'UTF-8') ?></a>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($f['machine_name'] ?? $f['equipment_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3"><?= faultPrioBadge($f['priority']) ?></td>
                        <td class="px-4 py-3"><?= faultStatusBadge($f['status']) ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($f['assigned_to_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs"><?= htmlspecialchars(substr($f['created_at'], 0, 10), ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-right">
                            <a href="/maintenance/faults/<?= $f['id'] ?>/edit" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline mr-3">Redigera</a>
                            <form method="POST" action="/maintenance/faults/<?= $f['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort felanmälan?')">
                                <?= \App\Core\Csrf::field() ?>
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700">Ta bort</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($faults)): ?>
                    <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">Inga felanmälningar registrerade</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
