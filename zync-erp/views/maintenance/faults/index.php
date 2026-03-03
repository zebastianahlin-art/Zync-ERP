<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Felanmälningar</h1>
        <a href="/maintenance/faults/create" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ny felanmälan
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
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Titel</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Maskin/Utrustning</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Prioritet</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Status</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Rapporterad av</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Datum</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Åtgärder</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($faults as $f): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3">
                        <a href="/maintenance/faults/<?= (int)$f['id'] ?>" class="text-blue-600 hover:underline font-mono text-xs"><?= htmlspecialchars($f['fault_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></a>
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($f['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                        <?php
                            $asset = $f['machine_name'] ?? ($f['equipment_name'] ?? '—');
                            echo htmlspecialchars($asset, ENT_QUOTES, 'UTF-8');
                        ?>
                    </td>
                    <td class="px-4 py-3"><?= faultIndexPriorityBadge($f['priority'] ?? 'normal') ?></td>
                    <td class="px-4 py-3"><?= faultIndexStatusBadge($f['status'] ?? 'reported') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($f['reported_by_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400"><?= htmlspecialchars(!empty($f['created_at']) ? date('Y-m-d', strtotime($f['created_at'])) : '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="/maintenance/faults/<?= (int)$f['id'] ?>" class="text-blue-600 hover:underline text-xs">Visa</a>
                            <?php if (in_array($f['status'] ?? '', ['reported', 'acknowledged'])): ?>
                                <a href="/maintenance/faults/<?= (int)$f['id'] ?>/edit" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 text-xs">Redigera</a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($faults)): ?>
                <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">Inga felanmälningar ännu. <a href="/maintenance/faults/create" class="text-blue-600 hover:underline">Skapa den första →</a></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
function faultIndexPriorityBadge(string $p): string {
    $m = [
        'low'      => ['Låg',       'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
        'normal'   => ['Normal',    'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'],
        'high'     => ['Hög',       'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300'],
        'urgent'   => ['Brådskande','bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'],
        'critical' => ['Kritisk',   'bg-red-200 text-red-900 dark:bg-red-900/50 dark:text-red-200 font-bold'],
    ];
    $label = $m[$p][0] ?? $p;
    $class = $m[$p][1] ?? 'bg-gray-100 text-gray-700';
    return "<span class=\"inline-flex px-2 py-1 rounded-full text-xs font-medium {$class}\">" . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . "</span>";
}

function faultIndexStatusBadge(string $s): string {
    $m = [
        'reported'    => ['Rapporterad', 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'],
        'acknowledged'=> ['Bekräftad',   'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'],
        'assigned'    => ['Tilldelad',   'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300'],
        'in_progress' => ['Pågår',       'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300'],
        'resolved'    => ['Löst',        'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'],
        'closed'      => ['Stängd',      'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
    ];
    $label = $m[$s][0] ?? $s;
    $class = $m[$s][1] ?? 'bg-gray-100 text-gray-700';
    return "<span class=\"inline-flex px-2 py-1 rounded-full text-xs font-medium {$class}\">" . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . "</span>";
}
?>
