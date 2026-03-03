<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Utrustning</h1>
        <a href="/equipment/create" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ny utrustning
        </a>
    </div>

    <?php if (!empty($success)): ?>
        <div class="rounded-lg bg-green-50 dark:bg-green-900/30 p-4 text-green-800 dark:text-green-200"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="rounded-lg bg-red-50 dark:bg-red-900/30 p-4 text-red-800 dark:text-red-200"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Nummer</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Namn</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Kategori</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Plats</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Status</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Kritikalitet</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Avdelning</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Åtgärder</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($equipment as $e): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3"><a href="/equipment/<?= (int)$e['id'] ?>" class="text-blue-600 hover:underline font-mono text-xs"><?= htmlspecialchars($e['equipment_number'], ENT_QUOTES, 'UTF-8') ?></a></td>
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($e['name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars(eqCategoryLabel($e['category'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($e['location'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3"><?= eqStatusBadge($e['status'] ?? '') ?></td>
                    <td class="px-4 py-3"><?= eqCriticalityBadge($e['criticality'] ?? '') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($e['department_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <a href="/equipment/<?= (int)$e['id'] ?>" class="text-blue-600 hover:underline text-xs">Visa</a>
                            <a href="/equipment/<?= (int)$e['id'] ?>/edit" class="text-gray-600 dark:text-gray-400 hover:underline text-xs">Redigera</a>
                            <form method="POST" action="/equipment/<?= (int)$e['id'] ?>/delete" class="inline" onsubmit="return confirm('Är du säker på att du vill ta bort denna utrustning?')">
                                <?= \App\Core\Csrf::field() ?>
                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs">Ta bort</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($equipment)): ?>
                <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">Ingen utrustning registrerad ännu. <a href="/equipment/create" class="text-blue-600 hover:underline">Lägg till den första →</a></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
function eqStatusBadge(string $s): string {
    $m = [
        'operational'    => ['Driftsatt',      'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'],
        'maintenance'    => ['Underhåll',       'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'],
        'out_of_service' => ['Ur drift',        'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'],
        'decommissioned' => ['Avvecklad',       'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
    ];
    return '<span class="inline-flex px-2 py-1 rounded-full text-xs font-medium '.($m[$s][1] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300').'">'.htmlspecialchars($m[$s][0] ?? $s, ENT_QUOTES, 'UTF-8').'</span>';
}
function eqCriticalityBadge(string $c): string {
    $m = [
        'low'      => ['Låg',      'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
        'medium'   => ['Medel',    'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'],
        'high'     => ['Hög',      'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300'],
        'critical' => ['Kritisk',  'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'],
    ];
    return '<span class="inline-flex px-2 py-1 rounded-full text-xs font-medium '.($m[$c][1] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300').'">'.htmlspecialchars($m[$c][0] ?? $c, ENT_QUOTES, 'UTF-8').'</span>';
}
function eqCategoryLabel(string $c): string {
    $m = [
        'production' => 'Produktion',
        'facility'   => 'Fastighet',
        'utility'    => 'Försörjning',
        'safety'     => 'Säkerhet',
        'transport'  => 'Transport',
        'it'         => 'IT',
        'other'      => 'Övrigt',
    ];
    return $m[$c] ?? $c;
}
?>
