<?php
function eqStatusBadge(string $s): string {
    $m = [
        'operational'    => ['Operativ','bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'],
        'maintenance'    => ['Underhåll','bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'],
        'out_of_service' => ['Ur drift','bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'],
        'decommissioned' => ['Avvecklad','bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
    ];
    return '<span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium '.($m[$s][1]??'bg-gray-100 text-gray-700').'">'.($m[$s][0]??$s).'</span>';
}
function eqCritBadge(string $c): string {
    $m = ['low'=>['Låg','text-gray-500'],'medium'=>['Medel','text-blue-600'],'high'=>['Hög','text-orange-600'],'critical'=>['Kritisk','text-red-600 font-bold']];
    return '<span class="text-xs '.($m[$c][1]??'text-gray-500').'">'.($m[$c][0]??$c).'</span>';
}
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Utrustning</h1>
        <a href="/equipment/create" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">+ Ny utrustning</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Nummer</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Namn</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Kategori</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Plats</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Avdelning</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Status</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Kritikalitet</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($equipment as $eq): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-mono text-xs text-indigo-600 dark:text-indigo-400">
                            <a href="/equipment/<?= $eq['id'] ?>"><?= htmlspecialchars($eq['equipment_number'], ENT_QUOTES, 'UTF-8') ?></a>
                        </td>
                        <td class="px-4 py-3 text-gray-900 dark:text-white font-medium">
                            <a href="/equipment/<?= $eq['id'] ?>" class="hover:underline"><?= htmlspecialchars($eq['name'], ENT_QUOTES, 'UTF-8') ?></a>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($eq['category'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($eq['location'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($eq['department_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3"><?= eqStatusBadge($eq['status']) ?></td>
                        <td class="px-4 py-3"><?= eqCritBadge($eq['criticality']) ?></td>
                        <td class="px-4 py-3 text-right">
                            <a href="/equipment/<?= $eq['id'] ?>/edit" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline mr-3">Redigera</a>
                            <form method="POST" action="/equipment/<?= $eq['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort utrustning?')">
                                <?= \App\Core\Csrf::field() ?>
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700">Ta bort</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($equipment)): ?>
                    <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">Ingen utrustning registrerad ännu</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
