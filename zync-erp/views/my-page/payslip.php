<?php
$statusMap = [
    'draft'    => ['bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400', 'Utkast'],
    'approved' => ['bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400', 'Godkänd'],
    'paid'     => ['bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400', 'Utbetald'],
];
$s = $statusMap[$payslip['status']] ?? ['bg-gray-100 text-gray-600', $payslip['status']];
?>
<div class="mx-auto max-w-2xl space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Lönespeca</h1>
        <a href="/my-page/payslips" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">&larr; Mina lönespecar</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-6">

        <!-- Header info -->
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white"><?= htmlspecialchars($payslip['period_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    <?= htmlspecialchars($payslip['period_from'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                    – <?= htmlspecialchars($payslip['period_to'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                </p>
            </div>
            <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium <?= $s[0] ?>"><?= $s[1] ?></span>
        </div>

        <hr class="border-gray-200 dark:border-gray-700">

        <!-- Employee info -->
        <?php if (($payslip['first_name'] ?? null) || ($payslip['last_name'] ?? null)): ?>
        <div>
            <dt class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Anställd</dt>
            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                <?= htmlspecialchars(trim(($payslip['first_name'] ?? '') . ' ' . ($payslip['last_name'] ?? '')), ENT_QUOTES, 'UTF-8') ?>
            </dd>
        </div>
        <hr class="border-gray-200 dark:border-gray-700">
        <?php endif; ?>

        <!-- Pay details -->
        <dl class="space-y-3">
            <div class="flex justify-between text-sm">
                <dt class="text-gray-600 dark:text-gray-400">Grundlön</dt>
                <dd class="font-medium text-gray-900 dark:text-white"><?= number_format((float)($payslip['base_pay'] ?? 0), 2, ',', ' ') ?> kr</dd>
            </div>
            <?php if (($payslip['ob_amount'] ?? 0) > 0): ?>
            <div class="flex justify-between text-sm">
                <dt class="text-gray-600 dark:text-gray-400">OB-tillägg</dt>
                <dd class="font-medium text-gray-900 dark:text-white"><?= number_format((float)($payslip['ob_amount'] ?? 0), 2, ',', ' ') ?> kr</dd>
            </div>
            <?php endif; ?>
            <?php if (($payslip['overtime_amount'] ?? 0) > 0): ?>
            <div class="flex justify-between text-sm">
                <dt class="text-gray-600 dark:text-gray-400">Övertid</dt>
                <dd class="font-medium text-gray-900 dark:text-white"><?= number_format((float)($payslip['overtime_amount'] ?? 0), 2, ',', ' ') ?> kr</dd>
            </div>
            <?php endif; ?>
            <div class="flex justify-between text-sm border-t border-gray-200 dark:border-gray-700 pt-3">
                <dt class="font-semibold text-gray-700 dark:text-gray-300">Bruttolön</dt>
                <dd class="font-semibold text-gray-900 dark:text-white"><?= number_format((float)($payslip['gross_pay'] ?? 0), 2, ',', ' ') ?> kr</dd>
            </div>
            <?php if (($payslip['tax_amount'] ?? 0) > 0): ?>
            <div class="flex justify-between text-sm">
                <dt class="text-gray-600 dark:text-gray-400">Skatt</dt>
                <dd class="text-red-600 dark:text-red-400">- <?= number_format((float)($payslip['tax_amount'] ?? 0), 2, ',', ' ') ?> kr</dd>
            </div>
            <?php endif; ?>
            <?php if (($payslip['deductions'] ?? 0) > 0): ?>
            <div class="flex justify-between text-sm">
                <dt class="text-gray-600 dark:text-gray-400">Avdrag</dt>
                <dd class="text-red-600 dark:text-red-400">- <?= number_format((float)($payslip['deductions'] ?? 0), 2, ',', ' ') ?> kr</dd>
            </div>
            <?php endif; ?>
            <div class="flex justify-between border-t border-gray-300 dark:border-gray-600 pt-3">
                <dt class="text-base font-bold text-gray-900 dark:text-white">Nettolön att utbetala</dt>
                <dd class="text-base font-bold text-indigo-600 dark:text-indigo-400"><?= number_format((float)($payslip['net_pay'] ?? 0), 2, ',', ' ') ?> kr</dd>
            </div>
        </dl>

        <?php if ($payslip['notes'] ?? null): ?>
        <hr class="border-gray-200 dark:border-gray-700">
        <div>
            <dt class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 mb-1">Notering</dt>
            <p class="text-sm text-gray-600 dark:text-gray-400"><?= htmlspecialchars($payslip['notes'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>
