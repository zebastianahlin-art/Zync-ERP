<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="/machines" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($machine['machine_number'], ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars($machine['name'], ENT_QUOTES, 'UTF-8') ?></h1>
            <?= mShowStatusBadge($machine['status'] ?? '') ?>
        </div>
        <div class="flex gap-2">
            <a href="/machines/<?= (int)$machine['id'] ?>/edit" class="px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">Redigera</a>
            <form method="POST" action="/machines/<?= (int)$machine['id'] ?>/delete" class="inline" onsubmit="return confirm('Är du säker på att du vill ta bort denna maskin?')">
                <?= \App\Core\Csrf::field() ?>
                <button type="submit" class="px-3 py-2 text-sm bg-red-600 hover:bg-red-700 text-white rounded-lg transition">Ta bort</button>
            </form>
        </div>
    </div>

    <?php if (!empty($success)): ?>
        <div class="rounded-lg bg-green-50 dark:bg-green-900/30 p-4 text-green-800 dark:text-green-200"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="rounded-lg bg-red-50 dark:bg-red-900/30 p-4 text-red-800 dark:text-red-200"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <!-- Info card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Information</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-x-6 gap-y-3 text-sm">
            <div>
                <span class="text-gray-500 dark:text-gray-400">Utrustning:</span>
                <?php if (!empty($machine['equipment_id'])): ?>
                <a href="/equipment/<?= (int)$machine['equipment_id'] ?>" class="text-blue-600 hover:underline ml-1"><?= htmlspecialchars($machine['equipment_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></a>
                <?php else: ?>
                <span class="text-gray-900 dark:text-white ml-1">—</span>
                <?php endif; ?>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Avdelning:</span>
                <span class="text-gray-900 dark:text-white ml-1"><?= htmlspecialchars($machine['department_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Kritikalitet:</span>
                <span class="ml-1"><?= mShowCriticalityBadge($machine['criticality'] ?? '') ?></span>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Plats:</span>
                <span class="text-gray-900 dark:text-white ml-1"><?= htmlspecialchars($machine['location'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Tillverkare:</span>
                <span class="text-gray-900 dark:text-white ml-1"><?= htmlspecialchars($machine['manufacturer'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Modell:</span>
                <span class="text-gray-900 dark:text-white ml-1"><?= htmlspecialchars($machine['model'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Serienummer:</span>
                <span class="text-gray-900 dark:text-white ml-1 font-mono text-xs"><?= htmlspecialchars($machine['serial_number'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Tillverkningsår:</span>
                <span class="text-gray-900 dark:text-white ml-1"><?= htmlspecialchars($machine['year_of_manufacture'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Effekt:</span>
                <span class="text-gray-900 dark:text-white ml-1"><?= !empty($machine['power_kw']) ? htmlspecialchars($machine['power_kw'], ENT_QUOTES, 'UTF-8') . ' kW' : '—' ?></span>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Underhållsintervall:</span>
                <span class="text-gray-900 dark:text-white ml-1"><?= !empty($machine['maintenance_interval_days']) ? htmlspecialchars($machine['maintenance_interval_days'], ENT_QUOTES, 'UTF-8') . ' dagar' : '—' ?></span>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Senaste underhåll:</span>
                <span class="text-gray-900 dark:text-white ml-1"><?= htmlspecialchars($machine['last_maintenance_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div>
                <?php
                    $nextMaint = $machine['next_maintenance_date'] ?? null;
                    $isPastDue = $nextMaint && strtotime($nextMaint) < time();
                ?>
                <span class="text-gray-500 dark:text-gray-400">Nästa underhåll:</span>
                <span class="ml-1 <?= $isPastDue ? 'text-red-600 dark:text-red-400 font-semibold' : 'text-gray-900 dark:text-white' ?>">
                    <?= htmlspecialchars($nextMaint ?? '—', ENT_QUOTES, 'UTF-8') ?>
                    <?php if ($isPastDue): ?><span class="text-xs">(förfallen)</span><?php endif; ?>
                </span>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Aktiv:</span>
                <span class="text-gray-900 dark:text-white ml-1"><?= !empty($machine['is_active']) ? 'Ja' : 'Nej' ?></span>
            </div>
        </div>
        <?php if (!empty($machine['notes'])): ?>
        <div class="mt-4 text-sm text-gray-700 dark:text-gray-300 border-t border-gray-200 dark:border-gray-700 pt-4">
            <p class="font-medium text-gray-500 dark:text-gray-400 mb-1">Anteckningar</p>
            <?= nl2br(htmlspecialchars($machine['notes'], ENT_QUOTES, 'UTF-8')) ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Felrapporter -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
        <div class="p-5 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Felrapporter</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Nummer</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Titel</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Prioritet</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Status</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Rapporterad</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($faults as $f): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-mono text-xs text-blue-600"><?= htmlspecialchars($f['fault_number'] ?? $f['id'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($f['title'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3"><?= mFaultPriorityBadge($f['priority'] ?? '') ?></td>
                        <td class="px-4 py-3"><?= mFaultStatusBadge($f['status'] ?? '') ?></td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400"><?= htmlspecialchars($f['reported_at'] ?? $f['created_at'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($faults)): ?>
                    <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">Inga felrapporter registrerade för denna maskin</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
function mShowStatusBadge(string $s): string {
    $m = [
        'running'        => ['I drift',    'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'],
        'idle'           => ['Stationär',  'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'],
        'maintenance'    => ['Underhåll',  'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'],
        'breakdown'      => ['Driftstopp', 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'],
        'decommissioned' => ['Avvecklad',  'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
    ];
    return '<span class="inline-flex px-2 py-1 rounded-full text-xs font-medium '.($m[$s][1] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300').'">'.htmlspecialchars($m[$s][0] ?? $s, ENT_QUOTES, 'UTF-8').'</span>';
}
function mShowCriticalityBadge(string $c): string {
    $m = [
        'low'      => ['Låg',     'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
        'medium'   => ['Medel',   'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'],
        'high'     => ['Hög',     'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300'],
        'critical' => ['Kritisk', 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'],
    ];
    return '<span class="inline-flex px-2 py-1 rounded-full text-xs font-medium '.($m[$c][1] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300').'">'.htmlspecialchars($m[$c][0] ?? $c, ENT_QUOTES, 'UTF-8').'</span>';
}
function mFaultPriorityBadge(string $p): string {
    $m = [
        'low'      => ['Låg',        'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
        'medium'   => ['Normal',     'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'],
        'high'     => ['Hög',        'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300'],
        'critical' => ['Kritisk',    'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'],
    ];
    return '<span class="inline-flex px-2 py-1 rounded-full text-xs font-medium '.($m[$p][1] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300').'">'.htmlspecialchars($m[$p][0] ?? $p, ENT_QUOTES, 'UTF-8').'</span>';
}
function mFaultStatusBadge(string $s): string {
    $m = [
        'open'        => ['Öppen',      'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'],
        'in_progress' => ['Pågående',   'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'],
        'resolved'    => ['Löst',       'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'],
        'closed'      => ['Stängd',     'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
    ];
    return '<span class="inline-flex px-2 py-1 rounded-full text-xs font-medium '.($m[$s][1] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300').'">'.htmlspecialchars($m[$s][0] ?? $s, ENT_QUOTES, 'UTF-8').'</span>';
}
?>
