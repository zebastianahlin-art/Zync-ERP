<?php
$typeLabel = ['corrective' => 'Avhjälpande', 'preventive' => 'Förebyggande', 'inspection' => 'Inspektion', 'improvement' => 'Förbättring'];
$priorityLabel = ['low' => 'Låg', 'medium' => 'Medel', 'high' => 'Hög', 'critical' => 'Kritisk'];
$priorityBadge = ['low' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400', 'medium' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400', 'high' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400', 'critical' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'];
$statusLabel = ['draft' => 'Utkast', 'planned' => 'Planerad', 'assigned' => 'Tilldelad', 'in_progress' => 'Pågår', 'on_hold' => 'Pausad', 'completed' => 'Slutförd', 'closed' => 'Stängd', 'cancelled' => 'Avbruten'];
$statusBadge = ['draft' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400', 'planned' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400', 'assigned' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400', 'in_progress' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400', 'on_hold' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400', 'completed' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400', 'closed' => 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400', 'cancelled' => 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400'];
?>
<div class="space-y-6">
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-5">
        <?php
        $cards = [
            ['label' => 'Totalt',    'value' => $stats['total'],       'color' => 'bg-indigo-500'],
            ['label' => 'Öppna',     'value' => $stats['open'],        'color' => 'bg-blue-500'],
            ['label' => 'Pågår',     'value' => $stats['in_progress'], 'color' => 'bg-yellow-500'],
            ['label' => 'Slutförda', 'value' => $stats['completed'],   'color' => 'bg-green-500'],
            ['label' => 'Försenade', 'value' => $stats['overdue'],     'color' => 'bg-red-500'],
        ];
        foreach ($cards as $card): ?>
            <div class="rounded-xl bg-white dark:bg-gray-800 p-4 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
                <div class="flex items-center gap-3"><span class="h-3 w-3 rounded-full <?= $card['color'] ?>"></span><span class="text-xs font-medium text-gray-500 dark:text-gray-400"><?= $card['label'] ?></span></div>
                <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white"><?= $card['value'] ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">🔧 Arbetsordrar</h1>
        <div class="flex flex-wrap items-center gap-3">
            <form method="GET" action="/maintenance/work-orders" class="flex flex-wrap items-center gap-2">
                <select name="status" onchange="this.form.submit()" class="rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                    <option value="">Alla statusar</option>
                    <?php foreach ($statusLabel as $val => $lbl): ?><option value="<?= $val ?>" <?= ($filter['status'] ?? '') === $val ? 'selected' : '' ?>><?= $lbl ?></option><?php endforeach; ?>
                </select>
                <select name="type" onchange="this.form.submit()" class="rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                    <option value="">Alla typer</option>
                    <?php foreach ($typeLabel as $val => $lbl): ?><option value="<?= $val ?>" <?= ($filter['type'] ?? '') === $val ? 'selected' : '' ?>><?= $lbl ?></option><?php endforeach; ?>
                </select>
                <select name="assigned" onchange="this.form.submit()" class="rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                    <option value="">Alla tekniker</option>
                    <?php foreach ($users as $u): ?><option value="<?= (int) $u['id'] ?>" <?= ($filter['assigned'] ?? '') == $u['id'] ? 'selected' : '' ?>><?= htmlspecialchars($u['full_name'], ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?>
                </select>
            </form>
            <a href="/maintenance/work-orders/create" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Ny arbetsorder
            </a>
        </div>
    </div>

    <?php if (empty($orders)): ?>
        <div class="rounded-xl bg-white dark:bg-gray-800 p-10 text-center shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Inga arbetsordrar hittades.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Nr</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Rubrik</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Utrustning</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 hidden md:table-cell">Typ</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Prio</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Status</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 hidden lg:table-cell">Tilldelad</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 hidden lg:table-cell">Planerat slut</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php foreach ($orders as $wo):
                        $overdue = $wo['planned_end'] && $wo['planned_end'] < date('Y-m-d H:i:s') && !in_array($wo['status'], ['completed','closed','cancelled']);
                    ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors <?= $overdue ? 'bg-red-50/50 dark:bg-red-900/10' : '' ?>">
                            <td class="px-4 py-3 font-mono text-xs text-gray-500"><?= htmlspecialchars($wo['wo_number'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3"><a href="/maintenance/work-orders/<?= (int) $wo['id'] ?>" class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline"><?= htmlspecialchars($wo['title'], ENT_QUOTES, 'UTF-8') ?></a></td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($wo['equipment_name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 hidden md:table-cell"><?= $typeLabel[$wo['type']] ?? $wo['type'] ?></td>
                            <td class="px-4 py-3"><span class="rounded-full px-2.5 py-0.5 text-xs font-medium <?= $priorityBadge[$wo['priority']] ?? '' ?>"><?= $priorityLabel[$wo['priority']] ?? '' ?></span></td>
                            <td class="px-4 py-3"><span class="rounded-full px-2.5 py-0.5 text-xs font-medium <?= $statusBadge[$wo['status']] ?? '' ?>"><?= $statusLabel[$wo['status']] ?? '' ?></span></td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 hidden lg:table-cell"><?= $wo['assigned_name'] ?? '—' ?></td>
                            <td class="px-4 py-3 text-xs hidden lg:table-cell <?= $overdue ? 'text-red-600 font-semibold' : 'text-gray-500' ?>"><?= $wo['planned_end'] ? date('Y-m-d', strtotime($wo['planned_end'])) : '—' ?></td>
                            <td class="px-4 py-3 text-right"><a href="/maintenance/work-orders/<?= (int) $wo['id'] ?>/edit" class="text-indigo-600 dark:text-indigo-400 hover:underline text-xs">Redigera</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
