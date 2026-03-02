<?php
$w = $wo;
$typeLabel = ['corrective' => 'Avhjälpande', 'preventive' => 'Förebyggande', 'inspection' => 'Inspektion', 'improvement' => 'Förbättring'];
$priorityLabel = ['low' => 'Låg', 'medium' => 'Medel', 'high' => 'Hög', 'critical' => 'Kritisk'];
$priorityBadge = ['low' => 'bg-gray-100 text-gray-600', 'medium' => 'bg-blue-100 text-blue-700', 'high' => 'bg-orange-100 text-orange-700', 'critical' => 'bg-red-100 text-red-700'];
$statusLabel = ['draft' => 'Utkast', 'planned' => 'Planerad', 'assigned' => 'Tilldelad', 'in_progress' => 'Pågår', 'on_hold' => 'Pausad', 'completed' => 'Slutförd', 'closed' => 'Stängd', 'cancelled' => 'Avbruten'];
$statusBadge = ['draft' => 'bg-gray-100 text-gray-600', 'planned' => 'bg-blue-100 text-blue-700', 'assigned' => 'bg-purple-100 text-purple-700', 'in_progress' => 'bg-indigo-100 text-indigo-700', 'on_hold' => 'bg-yellow-100 text-yellow-700', 'completed' => 'bg-green-100 text-green-700', 'closed' => 'bg-gray-100 text-gray-500', 'cancelled' => 'bg-red-100 text-red-600'];
$nextStatus = ['draft' => 'planned', 'planned' => 'assigned', 'assigned' => 'in_progress', 'in_progress' => 'completed', 'completed' => 'closed'];
$totalTime = array_sum(array_column($time, 'hours'));
?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"><?= htmlspecialchars($w['title'], ENT_QUOTES, 'UTF-8') ?></h1>
                <span class="rounded-full px-2.5 py-0.5 text-xs font-medium <?= $priorityBadge[$w['priority']] ?? '' ?>"><?= $priorityLabel[$w['priority']] ?? '' ?></span>
                <span class="rounded-full px-2.5 py-0.5 text-xs font-medium <?= $statusBadge[$w['status']] ?? '' ?>"><?= $statusLabel[$w['status']] ?? '' ?></span>
            </div>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400"><?= $w['wo_number'] ?> · <?= $typeLabel[$w['type']] ?? '' ?><?= $w['fault_report_number'] ? ' · Felanmälan: ' . $w['fault_report_number'] : '' ?></p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="/maintenance/work-orders" class="text-sm text-gray-500 hover:text-indigo-600">&larr; Alla</a>
            <?php if (isset($nextStatus[$w['status']])): ?>
                <form method="POST" action="/maintenance/work-orders/<?= (int) $w['id'] ?>/status" class="inline">
                    <?= \App\Core\Csrf::field() ?>
                    <input type="hidden" name="status" value="<?= $nextStatus[$w['status']] ?>">
                    <button type="submit" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-green-700">→ <?= $statusLabel[$nextStatus[$w['status']]] ?></button>
                </form>
            <?php endif; ?>
            <a href="/maintenance/work-orders/<?= (int) $w['id'] ?>/edit" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300">Redigera</a>
        </div>
    </div>

    <!-- Info -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
            <h2 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">Detaljer</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">Utrustning</dt><dd class="font-medium"><a href="/equipment/<?= (int) $w['equipment_id'] ?>" class="text-indigo-600 hover:underline"><?= htmlspecialchars($w['equipment_name'], ENT_QUOTES, 'UTF-8') ?></a></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Tilldelad</dt><dd class="text-gray-900 dark:text-white"><?= $w['assigned_name'] ?? 'Ej tilldelad' ?></dd></div>
                <?php if ($w['planned_start']): ?><div class="flex justify-between"><dt class="text-gray-500">Planerad start</dt><dd><?= date('Y-m-d H:i', strtotime($w['planned_start'])) ?></dd></div><?php endif; ?>
                <?php if ($w['planned_end']): ?><div class="flex justify-between"><dt class="text-gray-500">Planerat slut</dt><dd><?= date('Y-m-d H:i', strtotime($w['planned_end'])) ?></dd></div><?php endif; ?>
                <?php if ($w['actual_start']): ?><div class="flex justify-between"><dt class="text-gray-500">Faktisk start</dt><dd><?= date('Y-m-d H:i', strtotime($w['actual_start'])) ?></dd></div><?php endif; ?>
                <?php if ($w['actual_end']): ?><div class="flex justify-between"><dt class="text-gray-500">Faktiskt slut</dt><dd><?= date('Y-m-d H:i', strtotime($w['actual_end'])) ?></dd></div><?php endif; ?>
                <div class="flex justify-between"><dt class="text-gray-500">Tid</dt><dd class="font-medium"><?= $totalTime ?>h / <?= $w['estimated_hours'] ?? '—' ?>h beräknat</dd></div>
            </dl>
            <?php if ($w['description']): ?><div class="mt-4 rounded-lg bg-gray-50 dark:bg-gray-700/30 p-3 text-sm text-gray-600 dark:text-gray-400"><?= nl2br(htmlspecialchars($w['description'], ENT_QUOTES, 'UTF-8')) ?></div><?php endif; ?>
            <?php if ($w['root_cause']): ?><div class="mt-3"><span class="text-xs font-semibold text-gray-500">Grundorsak:</span><p class="text-sm text-gray-700 dark:text-gray-300"><?= nl2br(htmlspecialchars($w['root_cause'], ENT_QUOTES, 'UTF-8')) ?></p></div><?php endif; ?>
            <?php if ($w['action_taken']): ?><div class="mt-3"><span class="text-xs font-semibold text-gray-500">Utförd åtgärd:</span><p class="text-sm text-gray-700 dark:text-gray-300"><?= nl2br(htmlspecialchars($w['action_taken'], ENT_QUOTES, 'UTF-8')) ?></p></div><?php endif; ?>
        </div>

        <!-- Tid -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
            <h2 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">Tidrapportering (<?= $totalTime ?>h)</h2>
            <?php if (!empty($time)): ?>
                <div class="mb-3 space-y-2">
                    <?php foreach ($time as $t): ?>
                        <div class="flex justify-between rounded-lg bg-gray-50 dark:bg-gray-700/30 px-3 py-2 text-sm">
                            <div><span class="font-medium"><?= htmlspecialchars($t['user_name'], ENT_QUOTES, 'UTF-8') ?></span> · <?= $t['date'] ?></div>
                            <div class="font-semibold"><?= $t['hours'] ?>h</div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="/maintenance/work-orders/<?= (int) $w['id'] ?>/time" class="flex flex-wrap items-end gap-3 rounded-lg bg-gray-50 dark:bg-gray-700/30 p-4">
                <?= \App\Core\Csrf::field() ?>
                <div><label class="block text-xs font-medium text-gray-600">Datum</label><input name="date" type="date" value="<?= date('Y-m-d') ?>" required class="mt-1 rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white"></div>
                <div><label class="block text-xs font-medium text-gray-600">Timmar</label><input name="hours" type="number" step="0.5" min="0.5" required class="mt-1 w-20 rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white"></div>
                <div><label class="block text-xs font-medium text-gray-600">Notering</label><input name="description" type="text" class="mt-1 rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white"></div>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">Rapportera</button>
            </form>
        </div>
    </div>

    <!-- Material -->
    <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
        <h2 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">Materialuttag (<?= count($materials) ?>)</h2>
        <?php if (!empty($materials)): ?>
            <div class="mb-3 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead><tr class="border-b dark:border-gray-700">
                        <th class="px-3 py-2 text-left text-gray-600">Artikelnr</th>
                        <th class="px-3 py-2 text-left text-gray-600">Namn</th>
                        <th class="px-3 py-2 text-left text-gray-600">Antal</th>
                        <th class="px-3 py-2 text-left text-gray-600">Uttagen av</th>
                    </tr></thead>
                    <tbody>
                        <?php foreach ($materials as $m): ?>
                            <tr class="border-b dark:border-gray-700">
                                <td class="px-3 py-2 font-mono text-xs text-gray-500"><?= htmlspecialchars($m['article_number'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-3 py-2"><?= htmlspecialchars($m['article_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-3 py-2"><?= $m['quantity'] ?> <?= $m['unit'] ?></td>
                                <td class="px-3 py-2 text-gray-500"><?= $m['withdrawn_name'] ?? '—' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        <form method="POST" action="/maintenance/work-orders/<?= (int) $w['id'] ?>/materials" class="flex flex-wrap items-end gap-3 rounded-lg bg-gray-50 dark:bg-gray-700/30 p-4">
            <?= \App\Core\Csrf::field() ?>
            <div><label class="block text-xs font-medium text-gray-600">Artikel-ID</label><input name="article_id" type="number" min="1" required class="mt-1 w-24 rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white"></div>
            <div><label class="block text-xs font-medium text-gray-600">Antal</label><input name="quantity" type="number" step="0.01" min="0.01" required class="mt-1 w-20 rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white"></div>
            <div><label class="block text-xs font-medium text-gray-600">Notering</label><input name="notes" type="text" class="mt-1 rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white"></div>
            <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">Uttag</button>
        </form>
    </div>

    <!-- Kommentarer -->
    <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
        <h2 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">Historik & kommentarer (<?= count($comments) ?>)</h2>
        <?php if (!empty($comments)): ?>
            <div class="mb-4 space-y-3">
                <?php foreach ($comments as $c):
                    $isSystem = $c['type'] !== 'comment';
                ?>
                    <div class="rounded-lg <?= $isSystem ? 'bg-gray-50 dark:bg-gray-700/30' : 'bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700' ?> px-4 py-3">
                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span class="font-medium"><?= htmlspecialchars($c['user_name'], ENT_QUOTES, 'UTF-8') ?></span>
                            <span><?= date('Y-m-d H:i', strtotime($c['created_at'])) ?></span>
                        </div>
                        <p class="mt-1 text-sm text-gray-700 dark:text-gray-300 <?= $isSystem ? 'italic' : '' ?>"><?= nl2br(htmlspecialchars($c['comment'], ENT_QUOTES, 'UTF-8')) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="/maintenance/work-orders/<?= (int) $w['id'] ?>/comments" class="flex items-end gap-3">
            <?= \App\Core\Csrf::field() ?>
            <div class="flex-1">
                <textarea name="comment" rows="2" required placeholder="Skriv en kommentar..." class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white"></textarea>
            </div>
            <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">Skicka</button>
        </form>
    </div>
</div>
