<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Lönebesked</h1>
        <a href="/hr/payroll/payslips/create" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">+ Nytt lönebesked</a>
    </div>
    <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Anställd</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Period</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Bruttolön</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Nettolön</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Åtgärder</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                <?php foreach ($payslips as $p): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($p['employee_name'] ?? '–') ?></td>
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($p['period_name'] ?? '–') ?></td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300"><?= number_format((float)$p['gross_salary'], 2) ?> SEK</td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300"><?= number_format((float)$p['net_salary'], 2) ?> SEK</td>
                    <td class="px-6 py-4 text-right text-sm"><a href="/hr/payroll/payslips/<?= $p['id'] ?>" class="text-indigo-600 dark:text-indigo-400 hover:underline">Visa</a></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($payslips)): ?><tr><td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Inga lönebesked.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
