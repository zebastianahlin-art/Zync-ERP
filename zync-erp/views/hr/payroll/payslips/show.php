<div class="max-w-2xl space-y-6">
    <div class="flex items-center gap-3">
        <a href="/hr/payroll/payslips" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700">← Tillbaka</a>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Lönebesked</h1>
    </div>
    <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 shadow-sm space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Anställd</p><p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($payslip['employee_name'] ?? '–') ?></p></div>
            <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Period</p><p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($payslip['period_name'] ?? '–') ?></p></div>
            <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Bruttolön</p><p class="mt-1 text-sm text-gray-900 dark:text-white"><?= number_format((float)$payslip['gross_salary'], 2) ?> SEK</p></div>
            <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Skatteavdrag</p><p class="mt-1 text-sm text-gray-900 dark:text-white"><?= number_format((float)$payslip['tax_deduction'], 2) ?> SEK</p></div>
            <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nettolön</p><p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white"><?= number_format((float)$payslip['net_salary'], 2) ?> SEK</p></div>
            <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Övertidstimmar</p><p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($payslip['overtime_hours']) ?> h</p></div>
        </div>
        <?php if ($payslip['notes']): ?><div class="pt-2 border-t border-gray-100 dark:border-gray-700"><p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Anteckningar</p><p class="mt-1 text-sm text-gray-900 dark:text-white"><?= nl2br(htmlspecialchars($payslip['notes'])) ?></p></div><?php endif; ?>
    </div>
</div>
