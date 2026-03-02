<?php
$priorityLabel = ['low' => 'Låg', 'medium' => 'Medel', 'high' => 'Hög', 'critical' => 'Kritisk'];
$priorityBadge = ['low' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400', 'medium' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400', 'high' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400', 'critical' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'];
$statusLabel = ['reported' => 'Rapporterad', 'acknowledged' => 'Bekräftad', 'in_progress' => 'Pågår', 'resolved' => 'Åtgärdad', 'closed' => 'Stängd'];
$statusBadge = ['reported' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400', 'acknowledged' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400', 'in_progress' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400', 'resolved' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400', 'closed' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'];
$faultTypeLabel = ['mechanical' => 'Mekanisk', 'electrical' => 'Elektrisk', 'hydraulic' => 'Hydraulik', 'pneumatic' => 'Pneumatik', 'software' => 'Mjukvara', 'other' => 'Övrigt'];
?>
<div class="space-y-6">
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-5">
        <?php
        $cards = [
            ['label' => 'Totalt',       'value' => $stats['total'],       'color' => 'bg-indigo-500'],
            ['label' => 'Rapporterade', 'value' => $stats['reported'],    'color' => 'bg-yellow-500'],
            ['label' => 'Pågår',        'value' => $stats['in_progress'], 'color' => 'bg-blue-500'],
            ['label' => 'Åtgärdade',    'value' => $stats['resolved'],    'color' => 'bg-green-500'],
            ['label' => 'Kritiska',     'value' => $stats['critical'],    'color' => 'bg-red-500'],
        ];
        foreach ($cards as $card): ?>
            <div class="rounded-xl bg-white dark:bg-gray-800 p-4 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
                <div class="flex items-center gap-3"><span class="h-3 w-3 rounded-full <?= $card['color'] ?>"></span><span class="text-xs font-medium text-gray-500 dark:text-gray-400"><?= $card['label'] ?></span></div>
                <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white"><?= $card['value'] ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">🔴 Felanmälan</h1>
        <div class="flex flex-wrap items-center gap-3">
            <form method="GET" action="/maintenance/faults" class="flex flex-wrap items-center gap-2">
                <select name="status" onchange="this.form.submit()" class="rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                    <option value="">Alla statusar</option>
                    <?php foreach ($statusLabel as $val => $lbl): ?><option value="<?= $val ?>" <?= ($filter['status'] ?? '') === $val ? 'selected' : '' ?>><?= $lbl ?></option><?php endforeach; ?>
                </select>
                <select name="priority" onchange="this.form.submit()" class="rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                    <option value="">Alla prioriteter</option>
                    <?php foreach ($priorityLabel as $val => $lbl): ?><option value="<?= $val ?>" <?= ($filter['priority'] ?? '') === $val ? 'selected' : '' ?>><?= $lbl ?></option><?php endforeach; ?>
                </select>
                <select name="equipment" onchange="this.form.submit()" class="rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                    <option value="">All utrustning</option>
                    <?php foreach ($equipment as $eq): ?><option value="<?= (int) $eq['id'] ?>" <?= ($filter['equipment'] ?? '') == $eq['id'] ? 'selected' : '' ?>><?= htmlspecialchars($eq['name'], ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?>
                </select>
            </form>
            <a href="/maintenance/faults/create" class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-red-700 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Ny felanmälan
            </a>
        </div>
    </div>

    <?php if (empty($reports)): ?>
        <div class="rounded-xl bg-white dark:bg-gray-800 p-10 text-center shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Inga felanmälan hittades.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Nr</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Rubrik</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Utrustning</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 hidden md:table-cell">Feltyp</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Prioritet</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Status</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 hidden lg:table-cell">Rapporterad</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php foreach ($reports as $r): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors <?= $r['priority'] === 'critical' ? 'bg-red-50/50 dark:bg-red-900/10' : '' ?>">
                            <td class="px-4 py-3 font-mono text-xs text-gray-500"><?= htmlspecialchars($r['report_number'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3"><a href="/maintenance/faults/<?= (int) $r['id'] ?>" class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline"><?= htmlspecialchars($r['title'], ENT_QUOTES, 'UTF-8') ?></a></td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($r['equipment_name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 hidden md:table-cell"><?= $faultTypeLabel[$r['fault_type']] ?? $r['fault_type'] ?></td>
                            <td class="px-4 py-3"><span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-medium <?= $priorityBadge[$r['priority']] ?? '' ?>"><?= $priorityLabel[$r['priority']] ?? $r['priority'] ?></span></td>
                            <td class="px-4 py-3"><span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-medium <?= $statusBadge[$r['status']] ?? '' ?>"><?= $statusLabel[$r['status']] ?? $r['status'] ?></span></td>
                            <td class="px-4 py-3 text-gray-500 text-xs hidden lg:table-cell"><?= date('Y-m-d H:i', strtotime($r['created_at'])) ?></td>
                            <td class="px-4 py-3 text-right"><a href="/maintenance/faults/<?= (int) $r['id'] ?>/edit" class="text-indigo-600 dark:text-indigo-400 hover:underline text-xs">Redigera</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
