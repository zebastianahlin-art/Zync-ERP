<?php
$prioColors = [
    'low'      => 'bg-gray-100 border-gray-300 dark:bg-gray-700 dark:border-gray-600',
    'normal'   => 'bg-blue-50 border-blue-300 dark:bg-blue-900/20 dark:border-blue-700',
    'high'     => 'bg-orange-50 border-orange-300 dark:bg-orange-900/20 dark:border-orange-700',
    'critical' => 'bg-red-50 border-red-300 dark:bg-red-900/20 dark:border-red-700',
];
$intervalLabels = ['daily'=>'Daglig','weekly'=>'Veckovis','monthly'=>'Månadsvis','yearly'=>'Årsvis','hours'=>'Timmar'];
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="/maintenance/preventive" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">FU-kalender (90 dagar)</h1>
        </div>
        <a href="/maintenance/preventive/create" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">+ Nytt schema</a>
    </div>

    <?php if (empty($upcoming)): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-8 text-center">
        <p class="text-gray-500 dark:text-gray-400">Inga kommande FU-scheman de närmaste 90 dagarna.</p>
        <a href="/maintenance/preventive/create" class="mt-4 inline-block px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-lg transition">Skapa schema</a>
    </div>
    <?php else: ?>
    <!-- Group by month -->
    <?php
    $byMonth = [];
    foreach ($upcoming as $s) {
        $monthKey = date('Y-m', strtotime($s['next_due_at']));
        $byMonth[$monthKey][] = $s;
    }
    ?>
    <?php foreach ($byMonth as $month => $items): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="px-5 py-3 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
            <h2 class="font-semibold text-gray-900 dark:text-white">
                <?php
                $monthNames = [
                    1=>'Januari',2=>'Februari',3=>'Mars',4=>'April',5=>'Maj',6=>'Juni',
                    7=>'Juli',8=>'Augusti',9=>'September',10=>'Oktober',11=>'November',12=>'December'
                ];
                $dt = new \DateTime($month . '-01');
                echo htmlspecialchars($monthNames[(int)$dt->format('n')] . ' ' . $dt->format('Y'), ENT_QUOTES, 'UTF-8');
                ?>
            </h2>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            <?php foreach ($items as $s): ?>
            <?php $isOverdue = $s['next_due_at'] < date('Y-m-d H:i:s'); ?>
            <div class="flex items-center gap-4 px-5 py-3 <?= $isOverdue ? 'bg-red-50 dark:bg-red-900/10' : '' ?>">
                <div class="w-12 text-center flex-shrink-0">
                    <span class="block text-lg font-bold <?= $isOverdue ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' ?>"><?= date('d', strtotime($s['next_due_at'])) ?></span>
                    <span class="block text-xs text-gray-500 dark:text-gray-400"><?= date('D', strtotime($s['next_due_at'])) ?></span>
                </div>
                <div class="flex-1 min-w-0">
                    <a href="/maintenance/preventive/<?= $s['id'] ?>" class="text-sm font-medium text-gray-900 dark:text-white hover:underline">
                        <?= htmlspecialchars($s['title'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                        <?= htmlspecialchars($s['machine_name'] ?? $s['equipment_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                        <?php if ($s['assigned_to_name']): ?> · <?= htmlspecialchars($s['assigned_to_name'], ENT_QUOTES, 'UTF-8') ?><?php endif; ?>
                    </p>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars($intervalLabels[$s['interval_type']] ?? $s['interval_type'], ENT_QUOTES, 'UTF-8') ?></span>
                    <?php if ($isOverdue): ?>
                    <span class="px-2 py-0.5 rounded-full text-xs bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 font-semibold">Förfallen</span>
                    <?php endif; ?>
                    <form method="POST" action="/maintenance/preventive/<?= $s['id'] ?>/generate">
                        <?= \App\Core\Csrf::field() ?>
                        <button type="submit" class="px-2 py-1 text-xs bg-indigo-100 hover:bg-indigo-200 dark:bg-indigo-900/30 dark:hover:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 rounded transition">Generera AO</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>
