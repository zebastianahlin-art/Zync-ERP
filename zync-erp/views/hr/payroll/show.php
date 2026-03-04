<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($period['name'], ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5"><?= htmlspecialchars($period['period_from'], ENT_QUOTES, 'UTF-8') ?> – <?= htmlspecialchars($period['period_to'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400">
            <?= htmlspecialchars($period['status'], ENT_QUOTES, 'UTF-8') ?>
        </span>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 font-semibold text-gray-900 dark:text-white">Lönespecifikationer</div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Anställd</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400 uppercase text-xs">Bruttolön</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400 uppercase text-xs">Avdrag</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400 uppercase text-xs">Nettolön</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($payslips as $slip): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars(($slip['first_name'] ?? '') . ' ' . ($slip['last_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-right"><?= number_format((float) $slip['gross_pay'], 2, ',', ' ') ?></td>
                        <td class="px-4 py-3 text-right"><?= number_format((float) $slip['deductions'], 2, ',', ' ') ?></td>
                        <td class="px-4 py-3 text-right font-medium"><?= number_format((float) $slip['net_pay'], 2, ',', ' ') ?></td>
                        <td class="px-4 py-3"><?= htmlspecialchars($slip['status'], ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($payslips)): ?>
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Inga lönespecifikationer ännu</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <a href="/hr/payroll" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">&larr; Tillbaka</a>
    </div>
</div>
