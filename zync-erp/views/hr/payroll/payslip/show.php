<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="/hr/payroll/periods/<?= (int)$payslip['period_id'] ?>" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600">&larr; <?= htmlspecialchars($payslip['period_name'] ?? 'Löneperiod', ENT_QUOTES, 'UTF-8') ?></a>
            <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">Lönespecifikation</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars(($payslip['first_name'] ?? '') . ' ' . ($payslip['last_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div class="flex items-center gap-3">
            <a href="/hr/payroll/payslips/<?= (int)$payslip['id'] ?>/print" target="_blank"
               class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition">
                &#128438; Skriv ut / PDF
            </a>
            <a href="/hr/payroll/payslips/<?= (int)$payslip['id'] ?>/edit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Redigera</a>
            <form method="POST" action="/hr/payroll/payslips/<?= (int)$payslip['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort lönespecifikationen?')">
                <?= \App\Core\Csrf::field() ?>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">Ta bort</button>
            </form>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <div class="grid grid-cols-2 gap-x-8 gap-y-4">
            <div>
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Anst&#228;lld</dt>
                <dd class="mt-0.5 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars(($payslip['first_name'] ?? '') . ' ' . ($payslip['last_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Period</dt>
                <dd class="mt-0.5 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($payslip['period_name'] ?? '&#8212;', ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Grundl&#246;n</dt>
                <dd class="mt-0.5 text-sm text-gray-900 dark:text-white"><?= number_format((float)($payslip['base_pay'] ?? 0), 2, ',', ' ') ?> kr</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">OB-till&#228;gg</dt>
                <dd class="mt-0.5 text-sm text-gray-900 dark:text-white"><?= number_format((float)($payslip['ob_amount'] ?? 0), 2, ',', ' ') ?> kr</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">&#214;vertid</dt>
                <dd class="mt-0.5 text-sm text-gray-900 dark:text-white"><?= number_format((float)($payslip['overtime_amount'] ?? 0), 2, ',', ' ') ?> kr</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">&#214;vriga till&#228;gg</dt>
                <dd class="mt-0.5 text-sm text-gray-900 dark:text-white"><?= number_format((float)($payslip['other_additions'] ?? 0), 2, ',', ' ') ?> kr</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Bruttol&#246;n</dt>
                <dd class="mt-0.5 text-sm font-semibold text-gray-900 dark:text-white"><?= number_format((float)($payslip['gross_pay'] ?? 0), 2, ',', ' ') ?> kr</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Skatteavdrag</dt>
                <dd class="mt-0.5 text-sm text-red-600 dark:text-red-400">&minus; <?= number_format((float)($payslip['tax_amount'] ?? 0), 2, ',', ' ') ?> kr</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Sociala avgifter</dt>
                <dd class="mt-0.5 text-sm text-red-600 dark:text-red-400">&minus; <?= number_format((float)($payslip['social_security_amount'] ?? 0), 2, ',', ' ') ?> kr</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">&#214;vriga avdrag</dt>
                <dd class="mt-0.5 text-sm text-red-600 dark:text-red-400">&minus; <?= number_format((float)($payslip['deductions'] ?? 0) + (float)($payslip['other_deductions'] ?? 0), 2, ',', ' ') ?> kr</dd>
            </div>
            <div class="col-span-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Nettol&#246;n</dt>
                <dd class="mt-0.5 text-2xl font-bold text-indigo-600 dark:text-indigo-400"><?= number_format((float)($payslip['net_pay'] ?? 0), 2, ',', ' ') ?> kr</dd>
            </div>
            <?php if (!empty($payslip['notes'])): ?>
            <div class="col-span-2">
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Anteckningar</dt>
                <dd class="mt-0.5 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($payslip['notes'], ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
