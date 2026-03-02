<?php
$r = $report;
$priorityLabel = ['low' => 'Låg', 'medium' => 'Medel', 'high' => 'Hög', 'critical' => 'Kritisk'];
$priorityBadge = ['low' => 'bg-gray-100 text-gray-600', 'medium' => 'bg-blue-100 text-blue-700', 'high' => 'bg-orange-100 text-orange-700', 'critical' => 'bg-red-100 text-red-700'];
$statusLabel = ['reported' => 'Rapporterad', 'acknowledged' => 'Bekräftad', 'in_progress' => 'Pågår', 'resolved' => 'Åtgärdad', 'closed' => 'Stängd'];
$statusBadge = ['reported' => 'bg-yellow-100 text-yellow-700', 'acknowledged' => 'bg-blue-100 text-blue-700', 'in_progress' => 'bg-indigo-100 text-indigo-700', 'resolved' => 'bg-green-100 text-green-700', 'closed' => 'bg-gray-100 text-gray-600'];
$faultTypeLabel = ['mechanical' => 'Mekanisk', 'electrical' => 'Elektrisk', 'hydraulic' => 'Hydraulik', 'pneumatic' => 'Pneumatik', 'software' => 'Mjukvara', 'other' => 'Övrigt'];
$nextStatus = ['reported' => 'acknowledged', 'acknowledged' => 'in_progress', 'in_progress' => 'resolved', 'resolved' => 'closed'];
?>
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"><?= htmlspecialchars($r['title'], ENT_QUOTES, 'UTF-8') ?></h1>
                <span class="rounded-full px-2.5 py-0.5 text-xs font-medium <?= $priorityBadge[$r['priority']] ?? '' ?>"><?= $priorityLabel[$r['priority']] ?? '' ?></span>
                <span class="rounded-full px-2.5 py-0.5 text-xs font-medium <?= $statusBadge[$r['status']] ?? '' ?>"><?= $statusLabel[$r['status']] ?? '' ?></span>
            </div>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400"><?= $r['report_number'] ?> · <?= $faultTypeLabel[$r['fault_type']] ?? '' ?></p>
        </div>
        <div class="flex items-center gap-3">
            <a href="/maintenance/faults" class="text-sm text-gray-500 hover:text-indigo-600">&larr; Alla</a>
            <?php if (isset($nextStatus[$r['status']])): ?>
                <form method="POST" action="/maintenance/faults/<?= (int) $r['id'] ?>/status" class="inline">
                    <?= \App\Core\Csrf::field() ?>
                    <input type="hidden" name="status" value="<?= $nextStatus[$r['status']] ?>">
                    <button type="submit" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-green-700">→ <?= $statusLabel[$nextStatus[$r['status']]] ?></button>
                </form>
            <?php endif; ?>
            <a href="/maintenance/work-orders/create?fault=<?= (int) $r['id'] ?>&equipment=<?= (int) $r['equipment_id'] ?>" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">Skapa arbetsorder</a>
            <a href="/maintenance/faults/<?= (int) $r['id'] ?>/edit" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300">Redigera</a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
            <h2 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">Detaljer</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">Utrustning</dt><dd class="font-medium text-gray-900 dark:text-white"><a href="/equipment/<?= (int) $r['equipment_id'] ?>" class="text-indigo-600 hover:underline"><?= htmlspecialchars($r['equipment_name'], ENT_QUOTES, 'UTF-8') ?></a></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Rapporterad av</dt><dd class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($r['reporter_name'], ENT_QUOTES, 'UTF-8') ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Rapporterad</dt><dd class="text-gray-900 dark:text-white"><?= date('Y-m-d H:i', strtotime($r['created_at'])) ?></dd></div>
                <?php if ($r['acknowledged_at']): ?><div class="flex justify-between"><dt class="text-gray-500">Bekräftad</dt><dd class="text-gray-900 dark:text-white"><?= date('Y-m-d H:i', strtotime($r['acknowledged_at'])) ?></dd></div><?php endif; ?>
                <?php if ($r['resolved_at']): ?><div class="flex justify-between"><dt class="text-gray-500">Åtgärdad</dt><dd class="text-gray-900 dark:text-white"><?= date('Y-m-d H:i', strtotime($r['resolved_at'])) ?></dd></div><?php endif; ?>
            </dl>
        </div>
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
            <h2 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">Beskrivning</h2>
            <div class="text-sm text-gray-700 dark:text-gray-300"><?= nl2br(htmlspecialchars($r['description'], ENT_QUOTES, 'UTF-8')) ?></div>
            <?php if ($r['notes']): ?>
                <div class="mt-4 rounded-lg bg-gray-50 dark:bg-gray-700/30 p-3 text-sm text-gray-600 dark:text-gray-400"><?= nl2br(htmlspecialchars($r['notes'], ENT_QUOTES, 'UTF-8')) ?></div>
            <?php endif; ?>
            <?php if ($r['image_path']): ?>
                <div class="mt-4"><img src="<?= $r['image_path'] ?>" alt="Felbild" class="max-h-64 rounded-lg shadow"></div>
            <?php endif; ?>
        </div>
    </div>
</div>
