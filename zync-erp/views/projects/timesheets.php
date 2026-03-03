<?php $csrf = \App\Core\Csrf::token(); ?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <h1 class="text-2xl font-bold">⏱ Tidrapporter — Alla projekt</h1>
    <a href="/projects" class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">← Tillbaka till projekt</a>
</div>

<!-- Filter -->
<form method="get" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700 mb-6">
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <select name="user_id" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
            <option value="">Alla personer</option>
            <?php foreach ($users as $u): ?>
            <option value="<?= $u['id'] ?>" <?= ($filters['user_id'] ?? '') == $u['id'] ? 'selected' : '' ?>><?= htmlspecialchars($u['full_name']) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="date" name="from" value="<?= htmlspecialchars($filters['from'] ?? '') ?>" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
        <input type="date" name="to" value="<?= htmlspecialchars($filters['to'] ?? '') ?>" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
        <select name="approved" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
            <option value="">Alla</option>
            <option value="0" <?= ($filters['approved'] ?? '') === '0' ? 'selected' : '' ?>>Ej godkända</option>
            <option value="1" <?= ($filters['approved'] ?? '') === '1' ? 'selected' : '' ?>>Godkända</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">Filtrera</button>
    </div>
</form>

<!-- Summering -->
<div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-xl p-4 mb-6">
    <span class="text-sm font-medium">Totalt: <strong><?= number_format($totalHours, 1) ?> timmar</strong> (<?= count($entries) ?> poster)</span>
</div>

<!-- Tabell -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
        <thead class="bg-gray-50 dark:bg-gray-900/50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Datum</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Person</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Projekt</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Uppgift</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Timmar</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Beskrivning</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            <?php if (empty($entries)): ?>
            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">Inga tidrapporter hittades.</td></tr>
            <?php endif; ?>
            <?php foreach ($entries as $e): ?>
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <td class="px-4 py-3"><?= $e['work_date'] ?></td>
                <td class="px-4 py-3"><?= htmlspecialchars($e['user_name']) ?></td>
                <td class="px-4 py-3"><a href="/projects/<?= $e['project_id'] ?>" class="text-indigo-600 hover:underline"><?= htmlspecialchars($e['project_number']) ?></a><br><span class="text-xs text-gray-500"><?= htmlspecialchars($e['project_name']) ?></span></td>
                <td class="px-4 py-3"><?= htmlspecialchars($e['task_title'] ?? '—') ?></td>
                <td class="px-4 py-3 font-medium"><?= $e['hours'] ?>h</td>
                <td class="px-4 py-3 text-gray-500 max-w-xs truncate"><?= htmlspecialchars($e['description'] ?? '') ?></td>
                <td class="px-4 py-3">
                    <?php if ($e['approved']): ?>
                        <span class="text-green-600 text-xs font-medium">✅ Godkänd</span>
                    <?php else: ?>
                        <form method="post" action="/projects/timesheets/<?= $e['id'] ?>/approve" class="inline">
                            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>">
                            <button class="text-xs px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700">Godkänn</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
