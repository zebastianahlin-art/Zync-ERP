<?php
$woId     = (int)$workOrder['id'];
$woStatus = $workOrder['status'] ?? 'reported';
$isClosed = in_array($woStatus, ['closed', 'archived']);
?>
<div class="space-y-6">

    <!-- Sidhuvud -->
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3 flex-wrap">
            <a href="/maintenance/work-orders" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($workOrder['order_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
            <span class="text-lg font-medium text-gray-600 dark:text-gray-300"><?= htmlspecialchars($workOrder['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
            <?= woShowWorkType($workOrder['work_type'] ?? '') ?>
            <?= woShowPriority($workOrder['priority'] ?? 'normal') ?>
            <?= woShowStatus($woStatus) ?>
        </div>

        <!-- Åtgärdsknappar beroende på status -->
        <div class="flex flex-wrap gap-2">

            <?php if (in_array($woStatus, ['reported', 'assigned'])): ?>
                <a href="/maintenance/work-orders/<?= $woId ?>/edit"
                   class="px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                    Redigera
                </a>
            <?php endif; ?>

            <?php if ($woStatus === 'reported'): ?>
                <form method="POST" action="/maintenance/work-orders/<?= $woId ?>/delete" class="inline"
                      onsubmit="return confirm('Är du säker på att du vill ta bort denna arbetsorder?');">
                    <?= \App\Core\Csrf::field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="px-3 py-2 text-sm text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition">
                        Ta bort
                    </button>
                </form>
            <?php endif; ?>

            <?php if ($woStatus === 'assigned'): ?>
                <form method="POST" action="/maintenance/work-orders/<?= $woId ?>/start" class="inline">
                    <?= \App\Core\Csrf::field() ?>
                    <button type="submit" class="px-3 py-2 text-sm bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition">
                        Starta arbete
                    </button>
                </form>
            <?php endif; ?>

            <?php if ($woStatus === 'work_completed'): ?>
                <form method="POST" action="/maintenance/work-orders/<?= $woId ?>/submit-approval" class="inline">
                    <?= \App\Core\Csrf::field() ?>
                    <button type="submit" class="px-3 py-2 text-sm bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition">
                        Skicka för attestering
                    </button>
                </form>
            <?php endif; ?>

            <?php if ($woStatus === 'approved'): ?>
                <form method="POST" action="/maintenance/work-orders/<?= $woId ?>/close" class="inline">
                    <?= \App\Core\Csrf::field() ?>
                    <button type="submit" class="px-3 py-2 text-sm bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                        Stäng arbetsorder
                    </button>
                </form>
            <?php endif; ?>

            <?php if ($woStatus === 'closed'): ?>
                <form method="POST" action="/maintenance/work-orders/<?= $woId ?>/archive" class="inline">
                    <?= \App\Core\Csrf::field() ?>
                    <button type="submit" class="px-3 py-2 text-sm bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                        Arkivera
                    </button>
                </form>
            <?php endif; ?>

        </div>
    </div>

    <?php if (!empty($success)): ?>
        <div class="rounded-lg bg-green-50 dark:bg-green-900/30 p-4 text-green-800 dark:text-green-200"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="rounded-lg bg-red-50 dark:bg-red-900/30 p-4 text-red-800 dark:text-red-200"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <!-- Tilldela (vid status reported eller rejected) -->
    <?php if (in_array($woStatus, ['reported', 'rejected'])): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">
            <?= $woStatus === 'rejected' ? 'Tilldela igen' : 'Tilldela arbetsorder' ?>
        </h2>
        <form method="POST" action="/maintenance/work-orders/<?= $woId ?>/assign" class="flex flex-wrap items-end gap-3">
            <?= \App\Core\Csrf::field() ?>
            <div class="flex-1 min-w-48">
                <label for="assign_user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tekniker <span class="text-red-500">*</span></label>
                <select id="assign_user_id" name="assigned_to" required
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">— Välj tekniker —</option>
                    <?php foreach ($users as $u): ?>
                    <option value="<?= (int)$u['id'] ?>"><?= htmlspecialchars($u['name'] ?? ($u['full_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                Tilldela
            </button>
        </form>
    </div>
    <?php endif; ?>

    <!-- Tilldela om (vid status assigned) -->
    <?php if ($woStatus === 'assigned'): ?>
    <details class="bg-white dark:bg-gray-800 rounded-xl shadow">
        <summary class="px-6 py-4 cursor-pointer text-sm font-medium text-gray-700 dark:text-gray-300 select-none hover:text-gray-900 dark:hover:text-white">
            Tilldela om till annan tekniker
        </summary>
        <div class="px-6 pb-5">
            <form method="POST" action="/maintenance/work-orders/<?= $woId ?>/assign" class="flex flex-wrap items-end gap-3 mt-3">
                <?= \App\Core\Csrf::field() ?>
                <div class="flex-1 min-w-48">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ny tekniker <span class="text-red-500">*</span></label>
                    <select name="assigned_to" required
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">— Välj tekniker —</option>
                        <?php foreach ($users as $u): ?>
                        <option value="<?= (int)$u['id'] ?>" <?= (($workOrder['assigned_to'] ?? '') == $u['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($u['name'] ?? ($u['full_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                    Tilldela om
                </button>
            </form>
        </div>
    </details>
    <?php endif; ?>

    <!-- Markera arbete utfört (vid status in_progress) -->
    <?php if ($woStatus === 'in_progress'): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Markera arbete utfört</h2>
        <form method="POST" action="/maintenance/work-orders/<?= $woId ?>/complete" class="space-y-3">
            <?= \App\Core\Csrf::field() ?>
            <div>
                <label for="completion_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Slutnoteringar</label>
                <textarea id="completion_notes" name="completion_notes" rows="3"
                          class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                          placeholder="Beskriv vad som utfördes..."></textarea>
            </div>
            <div>
                <label for="downtime_hours" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Driftstopp (tim)</label>
                <input type="number" id="downtime_hours" name="downtime_hours" step="0.5" min="0" value="0"
                       class="w-40 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
            </div>
            <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-sm font-medium transition">
                Markera arbete utfört
            </button>
        </form>
    </div>
    <?php endif; ?>

    <!-- Attestering (vid status pending_approval) -->
    <?php if ($woStatus === 'pending_approval'): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-4">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Attestering</h2>
        <div class="flex flex-wrap gap-3">
            <!-- Attestera -->
            <form method="POST" action="/maintenance/work-orders/<?= $woId ?>/approve" class="flex-1 min-w-64 space-y-3">
                <?= \App\Core\Csrf::field() ?>
                <div>
                    <label for="approval_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Attesteringsnotering (valfritt)</label>
                    <textarea id="approval_notes" name="approval_notes" rows="2"
                              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                              placeholder="Eventuell kommentar..."></textarea>
                </div>
                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition">
                    ✓ Attestera
                </button>
            </form>
            <!-- Avvisa -->
            <form method="POST" action="/maintenance/work-orders/<?= $woId ?>/reject" class="flex-1 min-w-64 space-y-3">
                <?= \App\Core\Csrf::field() ?>
                <div>
                    <label for="rejected_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Anledning till avvisning <span class="text-red-500">*</span></label>
                    <textarea id="rejected_reason" name="rejected_reason" rows="2" required
                              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                              placeholder="Ange anledning..."></textarea>
                </div>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition">
                    ✗ Avvisa
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Avvisningsorsak (vid status rejected) -->
    <?php if ($woStatus === 'rejected' && !empty($workOrder['rejected_reason'])): ?>
    <div class="rounded-lg bg-red-50 dark:bg-red-900/20 p-4 text-red-800 dark:text-red-200 text-sm">
        <strong>Avvisad<?= !empty($workOrder['approved_by_name']) ? ' av ' . htmlspecialchars($workOrder['approved_by_name'], ENT_QUOTES, 'UTF-8') : '' ?>:</strong>
        <?= htmlspecialchars($workOrder['rejected_reason'], ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php endif; ?>

    <!-- Informationskort (2-kolumns grid) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <!-- Arbetsorderinfo -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Arbetsorderinfo</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Ordernummer</dt><dd class="text-gray-900 dark:text-white font-mono"><?= htmlspecialchars($workOrder['order_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></dd></div>
                <div class="flex justify-between items-center"><dt class="text-gray-500 dark:text-gray-400">Arbetstyp</dt><dd><?= woShowWorkType($workOrder['work_type'] ?? '') ?></dd></div>
                <div class="flex justify-between items-center"><dt class="text-gray-500 dark:text-gray-400">Prioritet</dt><dd><?= woShowPriority($workOrder['priority'] ?? 'normal') ?></dd></div>
                <div class="flex justify-between items-center"><dt class="text-gray-500 dark:text-gray-400">Status</dt><dd><?= woShowStatus($woStatus) ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Maskin</dt><dd class="text-gray-900 dark:text-white"><?= htmlspecialchars($workOrder['machine_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Utrustning</dt><dd class="text-gray-900 dark:text-white"><?= htmlspecialchars($workOrder['equipment_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Plats</dt><dd class="text-gray-900 dark:text-white"><?= htmlspecialchars($workOrder['location'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Avdelning</dt><dd class="text-gray-900 dark:text-white"><?= htmlspecialchars($workOrder['department_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd></div>
            </dl>
        </div>

        <!-- Planering & Tider -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Planering &amp; Tider</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Planerad start</dt><dd class="text-gray-900 dark:text-white"><?= !empty($workOrder['planned_start']) ? htmlspecialchars(date('Y-m-d H:i', strtotime($workOrder['planned_start'])), ENT_QUOTES, 'UTF-8') : '—' ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Planerat slut</dt><dd class="text-gray-900 dark:text-white"><?= !empty($workOrder['planned_end']) ? htmlspecialchars(date('Y-m-d H:i', strtotime($workOrder['planned_end'])), ENT_QUOTES, 'UTF-8') : '—' ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Beräknad tid</dt><dd class="text-gray-900 dark:text-white"><?= !empty($workOrder['estimated_hours']) ? htmlspecialchars(number_format((float)$workOrder['estimated_hours'], 1, ',', ' '), ENT_QUOTES, 'UTF-8') . ' tim' : '—' ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Total tid (rapporterad)</dt><dd class="text-gray-900 dark:text-white font-medium"><?= !empty($workOrder['total_hours']) ? htmlspecialchars(number_format((float)$workOrder['total_hours'], 1, ',', ' '), ENT_QUOTES, 'UTF-8') . ' tim' : '—' ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Startad</dt><dd class="text-gray-900 dark:text-white"><?= !empty($workOrder['started_at']) ? htmlspecialchars(date('Y-m-d H:i', strtotime($workOrder['started_at'])), ENT_QUOTES, 'UTF-8') : '—' ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Utfört</dt><dd class="text-gray-900 dark:text-white"><?= !empty($workOrder['completed_at']) ? htmlspecialchars(date('Y-m-d H:i', strtotime($workOrder['completed_at'])), ENT_QUOTES, 'UTF-8') : '—' ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Driftstopp</dt><dd class="text-gray-900 dark:text-white"><?= !empty($workOrder['downtime_hours']) ? htmlspecialchars(number_format((float)$workOrder['downtime_hours'], 1, ',', ' '), ENT_QUOTES, 'UTF-8') . ' tim' : '—' ?></dd></div>
            </dl>
        </div>

        <!-- Tilldelning -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Tilldelning</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Tilldelad till</dt><dd class="text-gray-900 dark:text-white"><?= htmlspecialchars($workOrder['assigned_to_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Tilldelad av</dt><dd class="text-gray-900 dark:text-white"><?= htmlspecialchars($workOrder['assigned_by_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Tilldelad</dt><dd class="text-gray-900 dark:text-white"><?= !empty($workOrder['assigned_at']) ? htmlspecialchars(date('Y-m-d H:i', strtotime($workOrder['assigned_at'])), ENT_QUOTES, 'UTF-8') : '—' ?></dd></div>
            </dl>
        </div>

        <!-- Kostnader -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Kostnader</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Materialkostnad</dt><dd class="text-gray-900 dark:text-white font-mono"><?= !empty($workOrder['total_material_cost']) ? htmlspecialchars(number_format((float)$workOrder['total_material_cost'], 2, ',', ' '), ENT_QUOTES, 'UTF-8') . ' kr' : '—' ?></dd></div>
                <div class="flex justify-between border-t border-gray-100 dark:border-gray-700 pt-2"><dt class="font-semibold text-gray-700 dark:text-gray-300">Total kostnad</dt><dd class="font-bold text-gray-900 dark:text-white font-mono"><?= !empty($workOrder['total_cost']) ? htmlspecialchars(number_format((float)$workOrder['total_cost'], 2, ',', ' '), ENT_QUOTES, 'UTF-8') . ' kr' : '—' ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Kostnadsställe</dt><dd class="text-gray-900 dark:text-white"><?= htmlspecialchars(!empty($workOrder['cost_center_code']) ? $workOrder['cost_center_code'] . ' ' . ($workOrder['cost_center_name'] ?? '') : ($workOrder['cost_center_name'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></dd></div>
            </dl>
        </div>
    </div>

    <!-- Beskrivning -->
    <?php if (!empty($workOrder['description'])): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3">Beskrivning</h2>
        <div class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap"><?= htmlspecialchars($workOrder['description'], ENT_QUOTES, 'UTF-8') ?></div>
    </div>
    <?php endif; ?>

    <!-- Slutnoteringar (om ifyllda) -->
    <?php if (!empty($workOrder['completion_notes'])): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3">Slutnoteringar</h2>
        <div class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap"><?= htmlspecialchars($workOrder['completion_notes'], ENT_QUOTES, 'UTF-8') ?></div>
    </div>
    <?php endif; ?>

    <!-- Tidrapportering -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
        <div class="p-5 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Tidrapportering</h2>
            <?php if (!$isClosed): ?>
            <span class="text-xs text-gray-500 dark:text-gray-400">Totalt: <?= !empty($workOrder['total_hours']) ? htmlspecialchars(number_format((float)$workOrder['total_hours'], 1, ',', ' '), ENT_QUOTES, 'UTF-8') : '0' ?> tim</span>
            <?php endif; ?>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Datum</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Anst.</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400">Tim.</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Övertid</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Beskrivning</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Status</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Åtgärder</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($timeEntries as $te): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars(!empty($te['work_date']) ? date('Y-m-d', strtotime($te['work_date'])) : '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-900 dark:text-white"><?= htmlspecialchars($te['user_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-right font-mono text-gray-900 dark:text-white"><?= htmlspecialchars(number_format((float)($te['hours'] ?? 0), 1, ',', ' '), ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3">
                            <?php if (!empty($te['is_overtime'])): ?>
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300">Övertid</span>
                            <?php else: ?>
                                <span class="text-gray-400 text-xs">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400 max-w-xs truncate"><?= htmlspecialchars($te['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3">
                            <?php if (!empty($te['is_approved'])): ?>
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">Attesterad</span>
                            <?php else: ?>
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300">Ej attesterad</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <?php if (empty($te['is_approved']) && !$isClosed): ?>
                                <form method="POST" action="/maintenance/work-orders/<?= $woId ?>/time/<?= (int)$te['id'] ?>/approve" class="inline">
                                    <?= \App\Core\Csrf::field() ?>
                                    <button type="submit" class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 text-xs">Attestera</button>
                                </form>
                                <?php endif; ?>
                                <?php if (!$isClosed): ?>
                                <form method="POST" action="/maintenance/work-orders/<?= $woId ?>/time/<?= (int)$te['id'] ?>/delete" class="inline">
                                    <?= \App\Core\Csrf::field() ?>
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs"
                                            onclick="return confirm('Ta bort tidposten?')">Ta bort</button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($timeEntries)): ?>
                    <tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">Inga tidposter registrerade</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Lägg till tid -->
        <?php if (!$isClosed): ?>
        <div class="p-5 border-t border-gray-200 dark:border-gray-700">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Lägg till tid</h3>
            <form method="POST" action="/maintenance/work-orders/<?= $woId ?>/time" class="grid grid-cols-1 sm:grid-cols-6 gap-3 items-end">
                <?= \App\Core\Csrf::field() ?>
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Tekniker</label>
                    <select name="user_id" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="">— Välj —</option>
                        <?php foreach ($users as $u): ?>
                        <option value="<?= (int)$u['id'] ?>"><?= htmlspecialchars($u['name'] ?? ($u['full_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Datum</label>
                    <input type="date" name="work_date" value="<?= date('Y-m-d') ?>" required
                           class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Timmar</label>
                    <input type="number" name="hours" step="0.5" min="0.5" value="1" required
                           class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Beskrivning</label>
                    <input type="text" name="description" placeholder="Vad utfördes..."
                           class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div class="flex items-center gap-2 pt-4">
                    <input type="checkbox" id="is_overtime" name="is_overtime" value="1" class="rounded border-gray-300 dark:border-gray-600">
                    <label for="is_overtime" class="text-xs text-gray-600 dark:text-gray-400">Övertid</label>
                </div>
                <div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 rounded text-sm font-medium transition">Lägg till</button>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <!-- Material -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
        <div class="p-5 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Material</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Artikel</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400">Antal</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400">À-pris</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400">Total</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Notering</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Status</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Åtgärder</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($parts as $pt): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 text-gray-900 dark:text-white"><?= htmlspecialchars($pt['article_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-right text-gray-900 dark:text-white font-mono"><?= htmlspecialchars(number_format((float)($pt['quantity'] ?? 0), 2, ',', ' '), ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-right text-gray-900 dark:text-white font-mono"><?= htmlspecialchars(number_format((float)($pt['unit_price'] ?? 0), 2, ',', ' '), ENT_QUOTES, 'UTF-8') ?> kr</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white font-mono"><?= htmlspecialchars(number_format((float)($pt['total_price'] ?? 0), 2, ',', ' '), ENT_QUOTES, 'UTF-8') ?> kr</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400 max-w-xs truncate"><?= htmlspecialchars($pt['notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3">
                            <?php if (!empty($pt['is_approved'])): ?>
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">Attesterad</span>
                            <?php else: ?>
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300">Ej attesterad</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <?php if (empty($pt['is_approved']) && !$isClosed): ?>
                                <form method="POST" action="/maintenance/work-orders/<?= $woId ?>/parts/<?= (int)$pt['id'] ?>/approve" class="inline">
                                    <?= \App\Core\Csrf::field() ?>
                                    <button type="submit" class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 text-xs">Attestera</button>
                                </form>
                                <?php endif; ?>
                                <?php if (!$isClosed): ?>
                                <form method="POST" action="/maintenance/work-orders/<?= $woId ?>/parts/<?= (int)$pt['id'] ?>/delete" class="inline">
                                    <?= \App\Core\Csrf::field() ?>
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs"
                                            onclick="return confirm('Ta bort material?')">Ta bort</button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($parts)): ?>
                    <tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">Inget material registrerat</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Lägg till material -->
        <?php if (!$isClosed): ?>
        <div class="p-5 border-t border-gray-200 dark:border-gray-700">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Lägg till material</h3>
            <form method="POST" action="/maintenance/work-orders/<?= $woId ?>/parts" class="grid grid-cols-1 sm:grid-cols-5 gap-3 items-end">
                <?= \App\Core\Csrf::field() ?>
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Artikel</label>
                    <select name="article_id" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm" onchange="woFillArticle(this)">
                        <option value="">— Välj artikel —</option>
                        <?php foreach ($articles as $a): ?>
                        <option value="<?= (int)$a['id'] ?>"
                                data-price="<?= htmlspecialchars($a['purchase_price'] ?? $a['price'] ?? '0', ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars(($a['article_number'] ?? '') . ' – ' . ($a['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Antal</label>
                    <input type="number" name="quantity" value="1" step="0.01" min="0.01" required
                           class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">À-pris (kr)</label>
                    <input type="number" name="unit_price" id="wo-part-price" value="0" step="0.01" min="0"
                           class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Notering</label>
                    <input type="text" name="notes" placeholder="Valfri notering"
                           class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 rounded text-sm font-medium transition">Lägg till</button>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>

</div>

<script>
function woFillArticle(sel) {
    const opt = sel.options[sel.selectedIndex];
    const priceEl = document.getElementById('wo-part-price');
    if (opt.value && priceEl) {
        priceEl.value = opt.dataset.price || '0';
    }
}
</script>

<?php
function woShowWorkType(string $t): string {
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

function woShowPriority(string $p): string {
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

function woShowStatus(string $s): string {
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
