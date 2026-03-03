<?php
/** @var string $title */
/** @var array $period */
/** @var array $records */
$months = ['','Januari','Februari','Mars','April','Maj','Juni','Juli','Augusti','September','Oktober','November','December'];
$statusBadge = ['draft'=>'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300','processing'=>'bg-blue-100 text-blue-700','approved'=>'bg-indigo-100 text-indigo-700','paid'=>'bg-green-100 text-green-700','locked'=>'bg-gray-800 text-white'];
$statusLabel = ['draft'=>'Utkast','processing'=>'Bearbetas','approved'=>'Godkänd','paid'=>'Utbetald','locked'=>'Låst'];
$periodName = $months[$period['month']] . ' ' . $period['year'];

$totalGross = array_sum(array_column($records, 'base_salary'));
$totalOT    = array_sum(array_column($records, 'overtime_amount'));
$totalBonus = array_sum(array_column($records, 'bonus'));
$totalDed   = array_sum(array_column($records, 'deductions'));
$totalTax   = array_sum(array_column($records, 'tax'));
$totalNet   = array_sum(array_column($records, 'net_salary'));
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"><?= $periodName ?></h1>
            <span class="inline-block mt-1 rounded-full px-2.5 py-0.5 text-xs font-medium <?= $statusBadge[$period['status']] ?? '' ?>"><?= $statusLabel[$period['status']] ?? $period['status'] ?></span>
        </div>
        <div class="flex gap-2">
            <a href="/hr/payroll" class="rounded-lg bg-gray-100 dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 transition-colors">← Tillbaka</a>
            <?php if ($period['status'] === 'draft'): ?>
                <form method="post" action="/hr/payroll/<?= $period['id'] ?>/approve" class="inline" onsubmit="return confirm('Godkänn löneperioden?')">
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(\App\Core\Csrf::token(), ENT_QUOTES) ?>">
                    <button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">Godkänn</button>
                </form>
            <?php elseif ($period['status'] === 'approved'): ?>
                <form method="post" action="/hr/payroll/<?= $period['id'] ?>/mark-paid" class="inline" onsubmit="return confirm('Markera som utbetald?')">
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(\App\Core\Csrf::token(), ENT_QUOTES) ?>">
                    <button class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-green-700 transition-colors">Markera utbetald</button>
                </form>
            <?php endif ?>
            <?php if ($period['status'] === 'draft'): ?>
                <form method="post" action="/hr/payroll/<?= $period['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort perioden?')">
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(\App\Core\Csrf::token(), ENT_QUOTES) ?>">
                    <button class="rounded-lg bg-red-50 dark:bg-red-900/20 px-4 py-2 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-100 transition-colors">Ta bort</button>
                </form>
            <?php endif ?>
        </div>
    </div>

    <!-- Summary -->
    <div class="grid grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="rounded-xl bg-white dark:bg-gray-800 shadow p-4 text-center"><p class="text-xs text-gray-500 dark:text-gray-400">Grundlön</p><p class="text-lg font-bold text-gray-900 dark:text-white"><?= number_format($totalGross, 0, ',', ' ') ?></p></div>
        <div class="rounded-xl bg-white dark:bg-gray-800 shadow p-4 text-center"><p class="text-xs text-gray-500 dark:text-gray-400">Övertid</p><p class="text-lg font-bold text-gray-900 dark:text-white"><?= number_format($totalOT, 0, ',', ' ') ?></p></div>
        <div class="rounded-xl bg-white dark:bg-gray-800 shadow p-4 text-center"><p class="text-xs text-gray-500 dark:text-gray-400">Bonus</p><p class="text-lg font-bold text-gray-900 dark:text-white"><?= number_format($totalBonus, 0, ',', ' ') ?></p></div>
        <div class="rounded-xl bg-white dark:bg-gray-800 shadow p-4 text-center"><p class="text-xs text-gray-500 dark:text-gray-400">Avdrag</p><p class="text-lg font-bold text-red-600 dark:text-red-400"><?= number_format($totalDed, 0, ',', ' ') ?></p></div>
        <div class="rounded-xl bg-white dark:bg-gray-800 shadow p-4 text-center"><p class="text-xs text-gray-500 dark:text-gray-400">Skatt</p><p class="text-lg font-bold text-red-600 dark:text-red-400"><?= number_format($totalTax, 0, ',', ' ') ?></p></div>
        <div class="rounded-xl bg-white dark:bg-gray-800 shadow p-4 text-center"><p class="text-xs text-gray-500 dark:text-gray-400">Netto</p><p class="text-lg font-bold text-green-600 dark:text-green-400"><?= number_format($totalNet, 0, ',', ' ') ?></p></div>
    </div>

    <!-- Records -->
    <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-md">
        <?php if (empty($records)): ?>
            <p class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">Inga löneposter i denna period.</p>
        <?php else: ?>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Anställd</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Avdelning</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Grundlön</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Övertid</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Bonus</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Avdrag</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Skatt</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Netto</th>
                        <?php if ($period['status'] === 'draft'): ?><th class="px-6 py-3"></th><?php endif ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php foreach ($records as $r): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="px-6 py-4"><span class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?></span><br><span class="text-xs text-gray-500"><?= htmlspecialchars($r['employee_number']) ?></span></td>
                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400"><?= htmlspecialchars($r['department_name'] ?? '–') ?></td>
                        <td class="px-6 py-4 text-right text-gray-700 dark:text-gray-300"><?= number_format((float)$r['base_salary'], 0, ',', ' ') ?></td>
                        <td class="px-6 py-4 text-right text-gray-700 dark:text-gray-300"><?= number_format((float)$r['overtime_amount'], 0, ',', ' ') ?></td>
                        <td class="px-6 py-4 text-right text-gray-700 dark:text-gray-300"><?= number_format((float)$r['bonus'], 0, ',', ' ') ?></td>
                        <td class="px-6 py-4 text-right text-gray-700 dark:text-gray-300"><?= number_format((float)$r['deductions'], 0, ',', ' ') ?></td>
                        <td class="px-6 py-4 text-right text-gray-700 dark:text-gray-300"><?= number_format((float)$r['tax'], 0, ',', ' ') ?></td>
                        <td class="px-6 py-4 text-right font-semibold text-gray-900 dark:text-white"><?= number_format((float)$r['net_salary'], 0, ',', ' ') ?></td>
                        <?php if ($period['status'] === 'draft'): ?>
                            <td class="px-6 py-4 text-right"><a href="/hr/payroll/<?= $period['id'] ?>/records/<?= $r['id'] ?>/edit" class="text-indigo-600 dark:text-indigo-400 hover:underline">Redigera</a></td>
                        <?php endif ?>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        <?php endif ?>
    </div>
</div>
