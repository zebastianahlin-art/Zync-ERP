<?php
$checklistItems = [];
if (!empty($schedule['checklist'])) {
    $raw = $schedule['checklist'];
    if (is_string($raw)) {
        $decoded = json_decode($raw, true);
        $checklistItems = is_array($decoded) ? $decoded : [];
    } elseif (is_array($raw)) {
        $checklistItems = $raw;
    }
}
$intervalLabels = ['daily'=>'Daglig','weekly'=>'Veckovis','monthly'=>'Månadsvis','yearly'=>'Årsvis','hours'=>'Timmar'];
$prioLabels = ['low'=>'Låg','normal'=>'Normal','high'=>'Hög','critical'=>'Kritisk'];
$statusLabels = ['active'=>'Aktiv','paused'=>'Pausad','completed'=>'Klar'];
$isOverdue = $schedule['next_due_at'] && $schedule['next_due_at'] < date('Y-m-d H:i:s') && $schedule['status'] === 'active';
?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <a href="/maintenance/preventive" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($schedule['title'], ENT_QUOTES, 'UTF-8') ?></h1>
            <?php if ($isOverdue): ?><span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300">FÖRFALLEN</span><?php endif; ?>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="/maintenance/preventive/<?= $schedule['id'] ?>/edit" class="px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition">Redigera</a>
            <form method="POST" action="/maintenance/preventive/<?= $schedule['id'] ?>/generate" class="inline">
                <?= \App\Core\Csrf::field() ?>
                <button type="submit" class="px-3 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">📋 Generera arbetsorder</button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h2 class="font-semibold text-gray-900 dark:text-white mb-4">Detaljer</h2>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Maskin/Utrustning</dt>
                        <dd class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($schedule['machine_name'] ?? $schedule['equipment_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Intervall</dt>
                        <dd class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars(($intervalLabels[$schedule['interval_type']] ?? $schedule['interval_type']) . ' (var ' . $schedule['interval_value'] . ')', ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Prioritet</dt>
                        <dd class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($prioLabels[$schedule['priority']] ?? $schedule['priority'], ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Status</dt>
                        <dd class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($statusLabels[$schedule['status']] ?? $schedule['status'], ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Senast utfört</dt>
                        <dd class="font-medium text-gray-900 dark:text-white"><?= $schedule['last_performed_at'] ? htmlspecialchars(date('Y-m-d H:i', strtotime($schedule['last_performed_at'])), ENT_QUOTES, 'UTF-8') : '—' ?></dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Nästa tillfälle</dt>
                        <dd class="font-medium <?= $isOverdue ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' ?>"><?= $schedule['next_due_at'] ? htmlspecialchars(date('Y-m-d H:i', strtotime($schedule['next_due_at'])), ENT_QUOTES, 'UTF-8') : '—' ?></dd>
                    </div>
                    <div class="col-span-2">
                        <dt class="text-gray-500 dark:text-gray-400">Tilldelad till</dt>
                        <dd class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($schedule['assigned_to_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <?php if ($schedule['description']): ?>
                    <div class="col-span-2">
                        <dt class="text-gray-500 dark:text-gray-400">Beskrivning</dt>
                        <dd class="text-gray-900 dark:text-white mt-1"><?= nl2br(htmlspecialchars($schedule['description'], ENT_QUOTES, 'UTF-8')) ?></dd>
                    </div>
                    <?php endif; ?>
                </dl>
            </div>

            <!-- Checklist -->
            <?php if (!empty($checklistItems)): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h2 class="font-semibold text-gray-900 dark:text-white mb-4">Checklista</h2>
                <ul class="space-y-2">
                    <?php foreach ($checklistItems as $item): ?>
                    <li class="flex items-center gap-3 text-sm text-gray-700 dark:text-gray-300">
                        <span class="w-4 h-4 rounded border border-gray-300 dark:border-gray-500 flex-shrink-0"></span>
                        <?= htmlspecialchars($item, ENT_QUOTES, 'UTF-8') ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>

        <!-- Logs -->
        <div class="space-y-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
                <h2 class="font-semibold text-gray-900 dark:text-white mb-4">Utförandehistorik</h2>
                <?php if (empty($logs)): ?>
                <p class="text-sm text-gray-500 dark:text-gray-400">Inga registrerade utföranden.</p>
                <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($logs as $log): ?>
                    <div class="border-l-2 border-green-400 pl-3">
                        <p class="text-xs font-medium text-gray-900 dark:text-white"><?= htmlspecialchars(date('Y-m-d H:i', strtotime($log['performed_at'])), ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars($log['performed_by_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
                        <?php if ($log['notes']): ?>
                        <p class="text-xs text-gray-600 dark:text-gray-300 mt-1"><?= htmlspecialchars($log['notes'], ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endif; ?>
                        <?php if ($log['wo_number']): ?>
                        <a href="/maintenance/work-orders/<?= $log['work_order_id'] ?>" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline"><?= htmlspecialchars($log['wo_number'], ENT_QUOTES, 'UTF-8') ?></a>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
