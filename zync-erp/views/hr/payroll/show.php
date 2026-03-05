<?php if (!empty($success)): ?>
<div class="mb-4 rounded-lg bg-green-50 dark:bg-green-900/20 p-4 text-green-800 dark:text-green-300 text-sm">
    <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
</div>
<?php endif; ?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="/hr/payroll" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600">&larr; Lönehantering</a>
            <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($period['name'], ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                <?= htmlspecialchars($period['period_from'] ?? '—', ENT_QUOTES, 'UTF-8') ?> – <?= htmlspecialchars($period['period_to'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
            </p>
        </div>
        <div class="flex items-center gap-3">
            <?php
            $statusLabels = ['open'=>'Öppen','locked'=>'Låst','closed'=>'Stängd'];
            $statusClasses = [
                'open'   => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
                'locked' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300',
                'closed' => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300',
            ];
            $st = $period['status'] ?? 'open';
            $stLabel = $statusLabels[$st] ?? $st;
            $stClass = $statusClasses[$st] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300';
            ?>
            <span class="px-3 py-1 rounded-full text-sm font-medium <?= $stClass ?>"><?= htmlspecialchars($stLabel, ENT_QUOTES, 'UTF-8') ?></span>

            <?php if ($st === 'open'): ?>
            <form method="POST" action="/hr/payroll/periods/<?= (int)$period['id'] ?>/generate">
                <?= \App\Core\Csrf::field() ?>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition">Generera lönespec.</button>
            </form>
            <form method="POST" action="/hr/payroll/periods/<?= (int)$period['id'] ?>/close" onsubmit="return confirm('Lås löneperioden?')">
                <?= \App\Core\Csrf::field() ?>
                <button type="submit" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition">Lås period</button>
            </form>
            <?php endif; ?>

            <a href="/hr/payroll/periods/<?= (int)$period['id'] ?>/payslips/create" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">+ Ny lönespec.</a>
            <a href="/hr/payroll/periods/<?= (int)$period['id'] ?>/edit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition">Redigera</a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <span class="font-semibold text-gray-900 dark:text-white">Lönespecifikationer</span>
            <span class="text-sm text-gray-500 dark:text-gray-400"><?= (int)$payslipCount ?> st</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Anställd</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400 uppercase text-xs">Grundlön</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400 uppercase text-xs">Bruttolön</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400 uppercase text-xs">Avdrag</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400 uppercase text-xs">Nettolön</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($payslips as $slip): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                            <?= htmlspecialchars(($slip['first_name'] ?? '') . ' ' . ($slip['last_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400"><?= number_format((float)($slip['base_pay'] ?? 0), 2, ',', ' ') ?></td>
                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400"><?= number_format((float)($slip['gross_pay'] ?? 0), 2, ',', ' ') ?></td>
                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400"><?= number_format((float)($slip['deductions'] ?? 0), 2, ',', ' ') ?></td>
                        <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white"><?= number_format((float)($slip['net_pay'] ?? 0), 2, ',', ' ') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($slip['status'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="/hr/payroll/payslips/<?= (int)$slip['id'] ?>" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline mr-2">Visa</a>
                            <a href="/hr/payroll/payslips/<?= (int)$slip['id'] ?>/edit" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Redigera</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($payslips)): ?>
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">Inga lönespecifikationer ännu</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
