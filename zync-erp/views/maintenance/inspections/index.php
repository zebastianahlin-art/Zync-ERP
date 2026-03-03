<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Besiktningar</h1>
        <div class="flex items-center gap-3">
            <a href="/maintenance/inspections/overdue" class="inline-flex items-center gap-2 text-red-600 dark:text-red-400 hover:underline text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                Förfallna besiktningar
            </a>
            <a href="/maintenance/inspections/create" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Ny besiktning
            </a>
        </div>
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
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Utrustning/Maskin</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Typ</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Planerat datum</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Status</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Resultat</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Inspektör</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Åtgärder</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($inspections ?? [] as $ins): ?>
                <?php
                $isOverdue = !empty($ins['scheduled_date'])
                    && $ins['status'] !== 'completed'
                    && $ins['status'] !== 'cancelled'
                    && strtotime($ins['scheduled_date']) < time();
                ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3">
                        <a href="/maintenance/inspections/<?= (int)$ins['id'] ?>" class="text-blue-600 hover:underline font-mono text-xs"><?= htmlspecialchars($ins['inspection_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></a>
                    </td>
                    <td class="px-4 py-3 text-gray-900 dark:text-white">
                        <?= htmlspecialchars($ins['equipment_name'] ?? ($ins['machine_name'] ?? '—'), ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td class="px-4 py-3"><?= inspectionTypeBadge($ins['inspection_type'] ?? '') ?></td>
                    <td class="px-4 py-3 <?= $isOverdue ? 'text-red-600 dark:text-red-400 font-medium' : 'text-gray-600 dark:text-gray-400' ?>">
                        <?= htmlspecialchars(!empty($ins['scheduled_date']) ? date('Y-m-d', strtotime($ins['scheduled_date'])) : '—', ENT_QUOTES, 'UTF-8') ?>
                        <?php if ($isOverdue): ?>
                            <span class="ml-1 inline-flex px-1.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300">Förfallen</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3"><?= inspectionStatusBadge($ins['status'] ?? 'scheduled') ?></td>
                    <td class="px-4 py-3"><?= inspectionResultBadge($ins['result'] ?? '') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($ins['inspector_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="/maintenance/inspections/<?= (int)$ins['id'] ?>" class="text-blue-600 hover:underline text-xs">Visa</a>
                            <?php if (($ins['status'] ?? '') === 'scheduled'): ?>
                                <a href="/maintenance/inspections/<?= (int)$ins['id'] ?>/edit" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 text-xs">Redigera</a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($inspections)): ?>
                <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">Inga besiktningar hittades. <a href="/maintenance/inspections/create" class="text-blue-600 hover:underline">Skapa den första →</a></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
function inspectionTypeBadge(string $t): string {
    $m = [
        'safety'      => ['Säkerhet',       'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'],
        'regulatory'  => ['Regulatorisk',   'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300'],
        'routine'     => ['Rutinmässig',    'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'],
        'preventive'  => ['Förebyggande',   'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'],
    ];
    $label = $m[$t][0] ?? $t;
    $class = $m[$t][1] ?? 'bg-gray-100 text-gray-700';
    return "<span class=\"inline-flex px-2 py-1 rounded-full text-xs font-medium {$class}\">" . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . "</span>";
}

function inspectionStatusBadge(string $s): string {
    $m = [
        'scheduled'   => ['Planerad',       'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'],
        'in_progress' => ['Pågår',          'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300'],
        'completed'   => ['Slutförd',       'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'],
        'overdue'     => ['Förfallen',      'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'],
        'cancelled'   => ['Avbokad',        'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
    ];
    $label = $m[$s][0] ?? $s;
    $class = $m[$s][1] ?? 'bg-gray-100 text-gray-700';
    return "<span class=\"inline-flex px-2 py-1 rounded-full text-xs font-medium {$class}\">" . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . "</span>";
}

function inspectionResultBadge(string $r): string {
    if ($r === '') return '<span class="text-gray-400 dark:text-gray-500 text-xs">—</span>';
    $m = [
        'pass'        => ['Godkänd',        'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'],
        'fail'        => ['Underkänd',      'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'],
        'conditional' => ['Villkorlig',     'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300'],
        'na'          => ['Ej tillämpligt', 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
    ];
    $label = $m[$r][0] ?? $r;
    $class = $m[$r][1] ?? 'bg-gray-100 text-gray-700';
    return "<span class=\"inline-flex px-2 py-1 rounded-full text-xs font-medium {$class}\">" . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . "</span>";
}
?>
