<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="/maintenance/supervisor" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mitt team</h1>
    </div>

    <?php if (!empty($success)): ?>
        <div class="rounded-lg bg-green-50 dark:bg-green-900/30 p-4 text-green-800 dark:text-green-200"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="rounded-lg bg-red-50 dark:bg-red-900/30 p-4 text-red-800 dark:text-red-200"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <?php
    // Group work orders by assigned_to_name
    $grouped = [];
    foreach ($workOrders ?? [] as $wo) {
        $key = !empty($wo['assigned_to_name']) ? $wo['assigned_to_name'] : '__unassigned__';
        $grouped[$key][] = $wo;
    }
    ksort($grouped);
    // Move unassigned to end
    if (isset($grouped['__unassigned__'])) {
        $unassignedGroup = $grouped['__unassigned__'];
        unset($grouped['__unassigned__']);
        $grouped['__unassigned__'] = $unassignedGroup;
    }
    ?>

    <?php if (empty($grouped)): ?>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-8 text-center text-gray-400 dark:text-gray-500">Inga arbetsordrar hittades</div>
    <?php endif; ?>

    <?php foreach ($grouped as $userName => $orders): ?>
    <?php $isUnassigned = $userName === '__unassigned__'; ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <?php if ($isUnassigned): ?>
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </span>
                <h2 class="text-base font-semibold text-gray-500 dark:text-gray-400 italic">Otilldelade</h2>
            <?php else: ?>
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-semibold text-sm">
                    <?= htmlspecialchars(mb_substr($userName, 0, 1), ENT_QUOTES, 'UTF-8') ?>
                </span>
                <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100"><?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?></h2>
            <?php endif; ?>
            <span class="ml-auto inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400"><?= count($orders) ?> ordrar</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Nummer</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Titel</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Typ</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Status</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Prioritet</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Uppdaterad</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($orders as $wo): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3">
                            <a href="/maintenance/work-orders/<?= (int)$wo['id'] ?>" class="text-blue-600 hover:underline font-mono text-xs"><?= htmlspecialchars($wo['order_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></a>
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                            <a href="/maintenance/work-orders/<?= (int)$wo['id'] ?>" class="hover:underline"><?= htmlspecialchars($wo['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></a>
                        </td>
                        <td class="px-4 py-3"><?= myTeamWorkTypeBadge($wo['work_type'] ?? '') ?></td>
                        <td class="px-4 py-3"><?= myTeamStatusBadge($wo['status'] ?? 'reported') ?></td>
                        <td class="px-4 py-3"><?= myTeamPriorityBadge($wo['priority'] ?? 'normal') ?></td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400"><?= htmlspecialchars(!empty($wo['updated_at']) ? date('Y-m-d H:i', strtotime($wo['updated_at'])) : '—', ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php
function myTeamWorkTypeBadge(string $t): string {
    $m = [
        'corrective'  => ['Korrigerande', 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'],
        'preventive'  => ['Förebyggande', 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'],
        'predictive'  => ['Prediktiv',    'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'],
        'emergency'   => ['Akut',         'bg-red-200 text-red-900 dark:bg-red-900/60 dark:text-red-200 font-bold'],
        'improvement' => ['Förbättring',  'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300'],
        'inspection'  => ['Inspektion',   'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'],
    ];
    $label = $m[$t][0] ?? $t;
    $class = $m[$t][1] ?? 'bg-gray-100 text-gray-700';
    return "<span class=\"inline-flex px-2 py-1 rounded-full text-xs font-medium {$class}\">" . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . "</span>";
}

function myTeamStatusBadge(string $s): string {
    $m = [
        'reported'         => ['Rapporterad',       'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'],
        'assigned'         => ['Tilldelad',          'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300'],
        'in_progress'      => ['Pågår',              'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300'],
        'work_completed'   => ['Arbete utfört',      'bg-teal-100 text-teal-700 dark:bg-teal-900/30 dark:text-teal-300'],
        'pending_approval' => ['Väntar attestering', 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'],
        'approved'         => ['Attesterad',         'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'],
        'rejected'         => ['Avvisad',            'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'],
        'closed'           => ['Avslutad',           'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
    ];
    $label = $m[$s][0] ?? $s;
    $class = $m[$s][1] ?? 'bg-gray-100 text-gray-700';
    return "<span class=\"inline-flex px-2 py-1 rounded-full text-xs font-medium {$class}\">" . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . "</span>";
}

function myTeamPriorityBadge(string $p): string {
    $m = [
        'low'      => ['Låg',        'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
        'normal'   => ['Normal',     'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'],
        'high'     => ['Hög',        'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300'],
        'urgent'   => ['Brådskande', 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'],
        'critical' => ['Kritisk',    'bg-red-200 text-red-900 dark:bg-red-900/50 dark:text-red-200 font-bold'],
    ];
    $label = $m[$p][0] ?? $p;
    $class = $m[$p][1] ?? 'bg-gray-100 text-gray-700';
    return "<span class=\"inline-flex px-2 py-1 rounded-full text-xs font-medium {$class}\">" . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . "</span>";
}
?>
