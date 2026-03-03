<?php
$statusLabels = ['planning'=>'Planering','active'=>'Aktiv','on_hold'=>'Pausad','completed'=>'Avslutad','cancelled'=>'Avbruten'];
$statusColors = ['planning'=>'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
    'active'=>'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    'on_hold'=>'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    'completed'=>'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    'cancelled'=>'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'];
$priorityIcons = ['low'=>'🟢','normal'=>'🔵','high'=>'🟠','urgent'=>'🔴'];
$catLabels = ['internal'=>'Internt','customer'=>'Kund','maintenance'=>'Underhåll','development'=>'Utveckling','other'=>'Övrigt'];
$csrf = \App\Core\Csrf::token();
?>

<!-- KPI-kort -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Aktiva</p>
        <p class="text-2xl font-bold text-green-600"><?= $stats['active'] ?? 0 ?></p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Planering</p>
        <p class="text-2xl font-bold text-blue-600"><?= $stats['planning'] ?? 0 ?></p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Pausade</p>
        <p class="text-2xl font-bold text-yellow-600"><?= $stats['on_hold'] ?? 0 ?></p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Avslutade</p>
        <p class="text-2xl font-bold text-gray-600"><?= $stats['completed'] ?? 0 ?></p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Tot. budget</p>
        <p class="text-2xl font-bold"><?= number_format($stats['total_budget'] ?? 0, 0, ',', ' ') ?> kr</p>
    </div>
</div>

<!-- Header + filter -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <h1 class="text-2xl font-bold">📋 Projekt</h1>
    <div class="flex gap-2">
        <a href="/projects/archive" class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">📁 Arkiv</a>
        <a href="/projects/timesheets" class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">⏱ Tidrapporter</a>
        <a href="/projects/create" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg">+ Nytt projekt</a>
    </div>
</div>

<!-- Filter -->
<form method="get" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700 mb-6">
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <input type="text" name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" placeholder="Sök projekt..." class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
        <select name="status" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
            <option value="">Alla statusar</option>
            <?php foreach ($statusLabels as $k => $v): ?>
            <option value="<?= $k ?>" <?= ($filters['status'] ?? '') === $k ? 'selected' : '' ?>><?= $v ?></option>
            <?php endforeach; ?>
        </select>
        <select name="category" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
            <option value="">Alla kategorier</option>
            <?php foreach ($catLabels as $k => $v): ?>
            <option value="<?= $k ?>" <?= ($filters['category'] ?? '') === $k ? 'selected' : '' ?>><?= $v ?></option>
            <?php endforeach; ?>
        </select>
        <select name="manager_id" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
            <option value="">Alla projektledare</option>
            <?php foreach ($managers as $m): ?>
            <option value="<?= $m['id'] ?>" <?= ($filters['manager_id'] ?? '') == $m['id'] ? 'selected' : '' ?>><?= htmlspecialchars($m['full_name']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">Filtrera</button>
    </div>
</form>

<!-- Projektlista -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-900/50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Projekt</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Kategori</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Kund</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Projektledare</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tid</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Färdig</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Slutdatum</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            <?php if (empty($projects)): ?>
            <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">Inga projekt hittades.</td></tr>
            <?php endif; ?>
            <?php foreach ($projects as $p): ?>
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <td class="px-4 py-3">
                    <a href="/projects/<?= $p['id'] ?>" class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline"><?= htmlspecialchars($p['project_number']) ?></a>
                    <p class="text-sm text-gray-500"><?= htmlspecialchars($p['name']) ?></p>
                </td>
                <td class="px-4 py-3 text-sm"><?= $catLabels[$p['category']] ?? $p['category'] ?></td>
                <td class="px-4 py-3 text-sm"><?= htmlspecialchars($p['customer_name'] ?? '—') ?></td>
                <td class="px-4 py-3 text-sm"><?= htmlspecialchars($p['manager_name'] ?? '—') ?></td>
                <td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs font-medium <?= $statusColors[$p['status']] ?? '' ?>"><?= $statusLabels[$p['status']] ?? $p['status'] ?></span></td>
                <td class="px-4 py-3 text-sm"><?= ($priorityIcons[$p['priority']] ?? '') . ' ' . number_format($p['actual_hours'], 1) ?> / <?= number_format($p['budget_hours'], 0) ?>h</td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <div class="w-16 bg-gray-200 dark:bg-gray-600 rounded-full h-2"><div class="h-2 rounded-full bg-indigo-600" style="width:<?= $p['completion_pct'] ?>%"></div></div>
                        <span class="text-xs"><?= $p['completion_pct'] ?>%</span>
                    </div>
                </td>
                <td class="px-4 py-3 text-sm <?= ($p['end_date'] && $p['end_date'] < date('Y-m-d') && $p['status'] !== 'completed') ? 'text-red-600 font-semibold' : '' ?>"><?= $p['end_date'] ? date('Y-m-d', strtotime($p['end_date'])) : '—' ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
