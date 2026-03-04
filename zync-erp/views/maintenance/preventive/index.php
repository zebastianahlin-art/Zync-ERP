<?php
function pmStatusBadge(string $s): string {
    $m = [
        'active'    => ['Aktiv', 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'],
        'paused'    => ['Pausad', 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'],
        'completed' => ['Klar', 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
    ];
    return '<span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium '.($m[$s][1]??'bg-gray-100 text-gray-600').'">'.($m[$s][0]??$s).'</span>';
}
function pmPrioBadge(string $p): string {
    $m = ['low'=>['Låg','text-gray-500'],'normal'=>['Normal','text-blue-600'],'high'=>['Hög','text-orange-600'],'critical'=>['Kritisk','text-red-700 font-bold']];
    return '<span class="text-xs '.($m[$p][1]??'text-gray-500').'">'.($m[$p][0]??$p).'</span>';
}
function intervalLabel(string $type, int $val): string {
    $m=['daily'=>'dag','weekly'=>'vecka','monthly'=>'månad','yearly'=>'år','hours'=>'timmar'];
    return $val.' '.($m[$type]??$type);
}
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Förebyggande underhåll</h1>
        <div class="flex gap-2">
            <a href="/maintenance/preventive/calendar" class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition">📅 Kalender</a>
            <a href="/maintenance/preventive/create" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">+ Nytt schema</a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 font-medium">Titel</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 font-medium">Maskin/Utrustning</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 font-medium">Intervall</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 font-medium">Prioritet</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 font-medium">Nästa tillfälle</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 font-medium">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php if (empty($schedules)): ?>
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">Inga FU-scheman skapade ännu.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($schedules as $s): ?>
                    <?php
                    $isOverdue = $s['next_due_at'] && $s['next_due_at'] < date('Y-m-d H:i:s') && $s['status'] === 'active';
                    ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 <?= $isOverdue ? 'bg-red-50 dark:bg-red-900/10' : '' ?>">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                            <a href="/maintenance/preventive/<?= $s['id'] ?>" class="hover:underline"><?= htmlspecialchars($s['title'], ENT_QUOTES, 'UTF-8') ?></a>
                            <?php if ($isOverdue): ?><span class="ml-2 text-xs text-red-600 dark:text-red-400 font-semibold">FÖRFALLEN</span><?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            <?= htmlspecialchars($s['machine_name'] ?? $s['equipment_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars(intervalLabel($s['interval_type'], (int)$s['interval_value']), ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3"><?= pmPrioBadge($s['priority']) ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400 <?= $isOverdue ? 'text-red-600 dark:text-red-400 font-semibold' : '' ?>">
                            <?= $s['next_due_at'] ? htmlspecialchars(date('Y-m-d', strtotime($s['next_due_at'])), ENT_QUOTES, 'UTF-8') : '—' ?>
                        </td>
                        <td class="px-4 py-3"><?= pmStatusBadge($s['status']) ?></td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a href="/maintenance/preventive/<?= $s['id'] ?>/edit" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Redigera</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
