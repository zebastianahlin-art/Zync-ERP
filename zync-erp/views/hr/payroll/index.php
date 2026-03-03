<?php
/** @var string $title */
/** @var array $stats */
/** @var array $periods */
/** @var array $years */
/** @var array $filter */
$months = ['','Januari','Februari','Mars','April','Maj','Juni','Juli','Augusti','September','Oktober','November','December'];
$statusBadge = ['draft'=>'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300','processing'=>'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400','approved'=>'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400','paid'=>'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400','locked'=>'bg-gray-800 text-white dark:bg-gray-600'];
$statusLabel = ['draft'=>'Utkast','processing'=>'Bearbetas','approved'=>'Godkänd','paid'=>'Utbetald','locked'=>'Låst'];
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Lönehantering</h1>
        <div class="flex gap-2">
            <a href="/hr" class="rounded-lg bg-gray-100 dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">← HR</a>
            <a href="/hr/payroll/create" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">+ Ny löneperiod</a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-5 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">Totalt perioder</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $stats['total_periods'] ?></p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-5 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">Utkast</p>
            <p class="text-2xl font-bold text-amber-600 dark:text-amber-400"><?= $stats['draft_periods'] ?></p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-5 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">Anställda med lön</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $stats['employees_with_payroll'] ?></p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-5 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">Totalt utbetalt</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400"><?= number_format($stats['total_paid'], 0, ',', ' ') ?> kr</p>
        </div>
    </div>

    <!-- Filter -->
    <form method="get" class="flex items-center gap-3">
        <select name="year" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm px-3 py-2 text-gray-700 dark:text-gray-300">
            <option value="">Alla år</option>
            <?php foreach ($years as $y): ?>
                <option value="<?= $y ?>" <?= ($filter['year'] ?? '') == $y ? 'selected' : '' ?>><?= $y ?></option>
            <?php endforeach ?>
        </select>
        <button class="rounded-lg bg-indigo-100 dark:bg-indigo-900/30 px-4 py-2 text-sm font-medium text-indigo-700 dark:text-indigo-400 hover:bg-indigo-200 transition-colors">Filtrera</button>
        <?php if (!empty($filter['year'])): ?>
            <a href="/hr/payroll" class="text-sm text-gray-500 hover:text-gray-700">Rensa</a>
        <?php endif ?>
    </form>

    <!-- Periods Table -->
    <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-md">
        <?php if (empty($periods)): ?>
            <p class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">Inga löneperioder skapade ännu. <a href="/hr/payroll/create" class="text-indigo-600 dark:text-indigo-400 hover:underline">Skapa den första.</a></p>
        <?php else: ?>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Period</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Datum</th>
                        <th class="px-6 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Anställda</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Total netto</th>
                        <th class="px-6 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Status</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php foreach ($periods as $p): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="px-6 py-4"><a href="/hr/payroll/<?= $p['id'] ?>" class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline"><?= $months[$p['month']] ?> <?= $p['year'] ?></a></td>
                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400"><?= $p['start_date'] ?> – <?= $p['end_date'] ?></td>
                        <td class="px-6 py-4 text-center text-gray-700 dark:text-gray-300"><?= $p['record_count'] ?></td>
                        <td class="px-6 py-4 text-right text-gray-700 dark:text-gray-300"><?= number_format((float)($p['total_net'] ?? 0), 0, ',', ' ') ?> kr</td>
                        <td class="px-6 py-4 text-center"><span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-medium <?= $statusBadge[$p['status']] ?? $statusBadge['draft'] ?>"><?= $statusLabel[$p['status']] ?? $p['status'] ?></span></td>
                        <td class="px-6 py-4 text-right"><a href="/hr/payroll/<?= $p['id'] ?>" class="text-indigo-600 dark:text-indigo-400 hover:underline">Visa</a></td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        <?php endif ?>
    </div>
</div>
