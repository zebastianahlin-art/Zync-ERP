<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <a href="/maintenance/work-orders" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Arkiv – Arbetsorder</h1>
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
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Titel</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Typ</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Maskin/Utrustning</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Prioritet</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Tilldelad</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Avslutad</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Åtgärder</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($workOrders as $wo): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3">
                        <a href="/maintenance/work-orders/archive/<?= (int)$wo['id'] ?>" class="text-blue-600 hover:underline font-mono text-xs"><?= htmlspecialchars($wo['order_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></a>
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($wo['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3"><?= woArchiveWorkTypeBadge($wo['work_type'] ?? '') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                        <?= htmlspecialchars($wo['machine_name'] ?? ($wo['equipment_name'] ?? '—'), ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td class="px-4 py-3"><?= woArchivePriorityBadge($wo['priority'] ?? 'normal') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($wo['assigned_to_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400"><?= htmlspecialchars(!empty($wo['closed_at']) ? date('Y-m-d', strtotime($wo['closed_at'])) : (!empty($wo['updated_at']) ? date('Y-m-d', strtotime($wo['updated_at'])) : '—'), ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3">
                        <a href="/maintenance/work-orders/archive/<?= (int)$wo['id'] ?>" class="text-blue-600 hover:underline text-xs">Visa</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($workOrders)): ?>
                <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">Inga arkiverade arbetsorder</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
function woArchiveWorkTypeBadge(string $t): string {
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

function woArchivePriorityBadge(string $p): string {
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
