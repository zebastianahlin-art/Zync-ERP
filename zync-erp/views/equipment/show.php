<?php
function eqStatusBadgeShow(string $s): string {
    $m = [
        'operational'   => ['Operativ','bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'],
        'maintenance'   => ['Underhåll','bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'],
        'breakdown'     => ['Haveri','bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'],
        'decommissioned'=> ['Avvecklad','bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
    ];
    return '<span class="inline-flex px-2 py-1 rounded-full text-xs font-medium '.($m[$s][1]??'bg-gray-100 text-gray-700').'">'.($m[$s][0]??$s).'</span>';
}
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="/equipment" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($equipment['name'], ENT_QUOTES, 'UTF-8') ?></h1>
            <?= eqStatusBadgeShow($equipment['status']) ?>
        </div>
        <div class="flex gap-2">
            <a href="/equipment/<?= $equipment['id'] ?>/edit" class="px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">Redigera</a>
            <form method="POST" action="/equipment/<?= $equipment['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort utrustning?')">
                <?= \App\Core\Csrf::field() ?>
                <button type="submit" class="px-3 py-2 text-sm bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-lg hover:bg-red-200 transition">Ta bort</button>
            </form>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
            <div><span class="text-gray-500 dark:text-gray-400">Nummer:</span> <span class="ml-1 font-mono text-indigo-600 dark:text-indigo-400"><?= htmlspecialchars($equipment['equipment_number'], ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Typ:</span> <span class="ml-1 text-gray-900 dark:text-white"><?= htmlspecialchars($equipment['type'], ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Kritikalitet:</span> <span class="ml-1 text-gray-900 dark:text-white"><?= htmlspecialchars($equipment['criticality'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Plats:</span> <span class="ml-1 text-gray-900 dark:text-white"><?= htmlspecialchars($equipment['location'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Avdelning:</span> <span class="ml-1 text-gray-900 dark:text-white"><?= htmlspecialchars($equipment['department_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Tillverkare:</span> <span class="ml-1 text-gray-900 dark:text-white"><?= htmlspecialchars($equipment['manufacturer'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Modell:</span> <span class="ml-1 text-gray-900 dark:text-white"><?= htmlspecialchars($equipment['model'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Serienummer:</span> <span class="ml-1 font-mono text-gray-900 dark:text-white"><?= htmlspecialchars($equipment['serial_number'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Installationsår:</span> <span class="ml-1 text-gray-900 dark:text-white"><?= htmlspecialchars($equipment['year_installed'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
        </div>
        <?php if (!empty($equipment['notes'])): ?>
        <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm text-gray-700 dark:text-gray-300">
            <?= nl2br(htmlspecialchars($equipment['notes'], ENT_QUOTES, 'UTF-8')) ?>
        </div>
        <?php endif; ?>
    </div>
</div>
