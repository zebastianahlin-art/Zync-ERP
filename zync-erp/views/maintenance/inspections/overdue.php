<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <a href="/maintenance/inspections" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Förfallna besiktningar</h1>
        </div>
    </div>

    <!-- Varningsbanner -->
    <div class="rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 p-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        <div>
            <p class="font-semibold text-red-800 dark:text-red-200">Förfallna besiktningar kräver omedelbar uppmärksamhet</p>
            <p class="text-sm text-red-700 dark:text-red-300 mt-0.5">Nedanstående besiktningar har passerat sitt planerade datum och måste åtgärdas snarast.</p>
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
            <thead class="bg-red-50 dark:bg-red-900/20">
                <tr>
                    <th class="px-4 py-3 text-left text-red-600 dark:text-red-400">Nummer</th>
                    <th class="px-4 py-3 text-left text-red-600 dark:text-red-400">Utrustning/Maskin</th>
                    <th class="px-4 py-3 text-left text-red-600 dark:text-red-400">Typ</th>
                    <th class="px-4 py-3 text-left text-red-600 dark:text-red-400">Planerat datum</th>
                    <th class="px-4 py-3 text-left text-red-600 dark:text-red-400">Dagar försenad</th>
                    <th class="px-4 py-3 text-left text-red-600 dark:text-red-400">Inspektör</th>
                    <th class="px-4 py-3 text-left text-red-600 dark:text-red-400">Status</th>
                    <th class="px-4 py-3 text-left text-red-600 dark:text-red-400">Åtgärder</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($inspections ?? [] as $ins): ?>
                <?php
                $daysOverdue = 0;
                if (!empty($ins['scheduled_date'])) {
                    $diff = (int)floor((time() - strtotime($ins['scheduled_date'])) / 86400);
                    $daysOverdue = max(0, $diff);
                }
                ?>
                <tr class="hover:bg-red-50 dark:hover:bg-red-900/10">
                    <td class="px-4 py-3">
                        <a href="/maintenance/inspections/<?= (int)$ins['id'] ?>" class="text-blue-600 hover:underline font-mono text-xs"><?= htmlspecialchars($ins['inspection_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></a>
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                        <?= htmlspecialchars($ins['equipment_name'] ?? ($ins['machine_name'] ?? '—'), ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td class="px-4 py-3"><?= overdueTypeBadge($ins['inspection_type'] ?? '') ?></td>
                    <td class="px-4 py-3 text-red-600 dark:text-red-400 font-medium">
                        <?= htmlspecialchars(!empty($ins['scheduled_date']) ? date('Y-m-d', strtotime($ins['scheduled_date'])) : '—', ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td class="px-4 py-3">
                        <?php if ($daysOverdue > 0): ?>
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <?= (int)$daysOverdue ?> dag<?= $daysOverdue !== 1 ? 'ar' : '' ?>
                        </span>
                        <?php else: ?>
                        <span class="text-gray-400 dark:text-gray-500 text-xs">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($ins['inspector_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3"><?= overdueStatusBadge($ins['status'] ?? 'overdue') ?></td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="/maintenance/inspections/<?= (int)$ins['id'] ?>" class="text-blue-600 hover:underline text-xs font-medium">Visa</a>
                            <?php if (($ins['status'] ?? '') === 'scheduled'): ?>
                                <a href="/maintenance/inspections/<?= (int)$ins['id'] ?>/edit" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 text-xs">Redigera</a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($inspections)): ?>
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">
                        <svg class="w-8 h-8 mx-auto mb-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Inga förfallna besiktningar – bra jobbat!
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
function overdueTypeBadge(string $t): string {
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

function overdueStatusBadge(string $s): string {
    $m = [
        'scheduled'   => ['Planerad',  'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'],
        'in_progress' => ['Pågår',     'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300'],
        'completed'   => ['Slutförd',  'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'],
        'overdue'     => ['Förfallen', 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'],
        'cancelled'   => ['Avbokad',   'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
    ];
    $label = $m[$s][0] ?? $s;
    $class = $m[$s][1] ?? 'bg-gray-100 text-gray-700';
    return "<span class=\"inline-flex px-2 py-1 rounded-full text-xs font-medium {$class}\">" . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . "</span>";
}
?>
