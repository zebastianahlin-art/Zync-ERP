<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Arbetsorder</h1>
        <a href="/maintenance/work-orders/create" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ny arbetsorder
        </a>
    </div>

    <?php if (!empty($success)): ?>
        <div class="rounded-lg bg-green-50 dark:bg-green-900/30 p-4 text-green-800 dark:text-green-200"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="rounded-lg bg-red-50 dark:bg-red-900/30 p-4 text-red-800 dark:text-red-200"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <!-- Statusfilter-flikar -->
    <?php
    $tabs = [
        ''                 => 'Alla',
        'reported'         => 'Rapporterade',
        'assigned'         => 'Tilldelade',
        'in_progress'      => 'Pågående',
        'work_completed'   => 'Slutförda',
        'pending_approval' => 'Väntar attestering',
        'approved'         => 'Attesterade',
        'closed'           => 'Avslutade',
    ];
    $current = $statusFilter ?? '';
    ?>
    <div class="flex flex-wrap gap-1 border-b border-gray-200 dark:border-gray-700">
        <?php foreach ($tabs as $val => $label): ?>
        <a href="<?= $val === '' ? '/maintenance/work-orders' : '/maintenance/work-orders?status=' . htmlspecialchars($val, ENT_QUOTES, 'UTF-8') ?>"
           class="px-3 py-2 text-sm font-medium rounded-t-lg transition
               <?= $current === $val
                   ? 'text-blue-600 bg-white dark:bg-gray-800 border border-b-white dark:border-gray-700 dark:border-b-gray-800 -mb-px'
                   : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' ?>">
            <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
        </a>
        <?php endforeach; ?>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Nummer</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Titel</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Typ</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Maskin/Utrustning</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Prioritet</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Status</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Tilldelad</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Skapad</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Åtgärder</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($workOrders as $wo): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3">
                        <a href="/maintenance/work-orders/<?= (int)$wo['id'] ?>" class="text-blue-600 hover:underline font-mono text-xs"><?= htmlspecialchars($wo['order_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></a>
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($wo['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3"><?= woIndexWorkTypeBadge($wo['work_type'] ?? '') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                        <?= htmlspecialchars($wo['machine_name'] ?? ($wo['equipment_name'] ?? '—'), ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td class="px-4 py-3"><?= woIndexPriorityBadge($wo['priority'] ?? 'normal') ?></td>
                    <td class="px-4 py-3"><?= woIndexStatusBadge($wo['status'] ?? 'reported') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($wo['assigned_to_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400"><?= htmlspecialchars(!empty($wo['created_at']) ? date('Y-m-d', strtotime($wo['created_at'])) : '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="/maintenance/work-orders/<?= (int)$wo['id'] ?>" class="text-blue-600 hover:underline text-xs">Visa</a>
                            <?php if (in_array($wo['status'] ?? '', ['reported', 'assigned'])): ?>
                                <a href="/maintenance/work-orders/<?= (int)$wo['id'] ?>/edit" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 text-xs">Redigera</a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($workOrders)): ?>
                <tr><td colspan="9" class="px-4 py-8 text-center text-gray-400">Inga arbetsorder hittades. <a href="/maintenance/work-orders/create" class="text-blue-600 hover:underline">Skapa den första →</a></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="flex justify-end">
        <a href="/maintenance/work-orders/archive" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12a2 2 0 002 2h8a2 2 0 002-2L19 8"/></svg>
            Visa arkiv
        </a>
    </div>
</div>

<?php
function woIndexWorkTypeBadge(string $t): string {
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

function woIndexPriorityBadge(string $p): string {
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

function woIndexStatusBadge(string $s): string {
    $m = [
        'reported'         => ['Rapporterad',       'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'],
        'assigned'         => ['Tilldelad',          'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300'],
        'in_progress'      => ['Pågår',              'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300'],
        'work_completed'   => ['Arbete utfört',      'bg-teal-100 text-teal-700 dark:bg-teal-900/30 dark:text-teal-300'],
        'pending_approval' => ['Väntar attestering', 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'],
        'approved'         => ['Attesterad',         'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'],
        'rejected'         => ['Avvisad',            'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'],
        'closed'           => ['Avslutad',           'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
        'archived'         => ['Arkiverad',          'bg-gray-200 text-gray-500 dark:bg-gray-800 dark:text-gray-500'],
    ];
    $label = $m[$s][0] ?? $s;
    $class = $m[$s][1] ?? 'bg-gray-100 text-gray-700';
    return "<span class=\"inline-flex px-2 py-1 rounded-full text-xs font-medium {$class}\">" . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . "</span>";
}
?>
