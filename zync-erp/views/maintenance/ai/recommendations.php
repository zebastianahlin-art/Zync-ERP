<?php
$severityColors = [
    'critical' => 'border-red-500 bg-red-50 dark:bg-red-900/10',
    'high'     => 'border-orange-400 bg-orange-50 dark:bg-orange-900/10',
    'normal'   => 'border-blue-400 bg-blue-50 dark:bg-blue-900/10',
    'low'      => 'border-gray-300 bg-gray-50 dark:bg-gray-700/30',
];
$severityBadge = [
    'critical' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
    'high'     => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300',
    'normal'   => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
    'low'      => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
];
$severityLabel = ['critical' => 'Kritisk', 'high' => 'Hög', 'normal' => 'Normal', 'low' => 'Låg'];
$typeIcons = ['frequent_faults' => '🔴', 'no_pm_schedule' => '📅', 'overdue_pm' => '⏰'];
$typeLabels = ['frequent_faults' => 'Frekventa fel', 'no_pm_schedule' => 'Saknar FU-schema', 'overdue_pm' => 'Förfallet FU'];
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="/maintenance/ai" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">AI-rekommendationer</h1>
        </div>
        <span class="text-sm text-gray-500 dark:text-gray-400"><?= count($recommendations) ?> rekommendationer</span>
    </div>

    <?php if (empty($recommendations)): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-8 text-center">
        <span class="text-4xl block mb-3">✅</span>
        <p class="text-gray-900 dark:text-white font-medium">Inga rekommendationer</p>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Underhållet ser bra ut baserat på nuvarande data.</p>
    </div>
    <?php else: ?>
    <div class="space-y-4">
        <?php foreach ($recommendations as $rec): ?>
        <div class="border-l-4 <?= $severityColors[$rec['severity']] ?? $severityColors['normal'] ?> rounded-r-xl p-5 flex items-start gap-4">
            <span class="text-2xl flex-shrink-0"><?= $typeIcons[$rec['type']] ?? '💡' ?></span>
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1 flex-wrap">
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full <?= $severityBadge[$rec['severity']] ?? '' ?>"><?= htmlspecialchars($severityLabel[$rec['severity']] ?? $rec['severity'], ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars($typeLabels[$rec['type']] ?? $rec['type'], ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <p class="text-sm text-gray-800 dark:text-gray-200"><?= htmlspecialchars($rec['message'], ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <div class="flex flex-col gap-2 flex-shrink-0">
                <a href="/maintenance/ai/machine/<?= (int) $rec['machine_id'] ?>" class="px-3 py-1.5 text-xs bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">Hälsorapport</a>
                <?php if (in_array($rec['type'], ['frequent_faults', 'no_pm_schedule'])): ?>
                <a href="/maintenance/preventive/create" class="px-3 py-1.5 text-xs bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">Skapa FU</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
