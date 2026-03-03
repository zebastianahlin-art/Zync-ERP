<div class="space-y-6">

    <!-- Sidhuvud -->
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3 flex-wrap">
            <a href="/maintenance/work-orders/archive" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($workOrder['order_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
            <span class="text-lg font-medium text-gray-600 dark:text-gray-300"><?= htmlspecialchars($workOrder['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
            <?= woArchiveShowWorkType($workOrder['work_type'] ?? '') ?>
            <?= woArchiveShowPriority($workOrder['priority'] ?? 'normal') ?>
            <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium bg-gray-200 text-gray-500 dark:bg-gray-800 dark:text-gray-500">Arkiverad</span>
        </div>
        <a href="/maintenance/work-orders/archive" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
            ← Tillbaka till arkiv
        </a>
    </div>

    <!-- Informationskort (2-kolumns grid) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <!-- Arbetsorderinfo -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Arbetsorderinfo</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Ordernummer</dt><dd class="text-gray-900 dark:text-white font-mono"><?= htmlspecialchars($workOrder['order_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></dd></div>
                <div class="flex justify-between items-center"><dt class="text-gray-500 dark:text-gray-400">Arbetstyp</dt><dd><?= woArchiveShowWorkType($workOrder['work_type'] ?? '') ?></dd></div>
                <div class="flex justify-between items-center"><dt class="text-gray-500 dark:text-gray-400">Prioritet</dt><dd><?= woArchiveShowPriority($workOrder['priority'] ?? 'normal') ?></dd></div>
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
                <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Total tid</dt><dd class="text-gray-900 dark:text-white font-medium"><?= !empty($workOrder['total_hours']) ? htmlspecialchars(number_format((float)$workOrder['total_hours'], 1, ',', ' '), ENT_QUOTES, 'UTF-8') . ' tim' : '—' ?></dd></div>
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
                <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Attesterad av</dt><dd class="text-gray-900 dark:text-white"><?= htmlspecialchars($workOrder['approved_by_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd></div>
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

    <!-- Slutnoteringar -->
    <?php if (!empty($workOrder['completion_notes'])): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3">Slutnoteringar</h2>
        <div class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap"><?= htmlspecialchars($workOrder['completion_notes'], ENT_QUOTES, 'UTF-8') ?></div>
    </div>
    <?php endif; ?>

    <!-- Tidrapportering (skrivskyddad) -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
        <div class="p-5 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Tidrapportering</h2>
            <span class="text-xs text-gray-500 dark:text-gray-400">Totalt: <?= !empty($workOrder['total_hours']) ? htmlspecialchars(number_format((float)$workOrder['total_hours'], 1, ',', ' '), ENT_QUOTES, 'UTF-8') : '0' ?> tim</span>
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
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($timeEntries as $te): ?>
                    <tr>
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
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($timeEntries)): ?>
                    <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">Inga tidposter registrerade</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Material (skrivskyddad) -->
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
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($parts as $pt): ?>
                    <tr>
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
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($parts)): ?>
                    <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">Inget material registrerat</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php
function woArchiveShowWorkType(string $t): string {
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

function woArchiveShowPriority(string $p): string {
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
