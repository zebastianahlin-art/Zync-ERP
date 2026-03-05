<div class="mx-auto max-w-2xl">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Redigera lönespecifikation</h1>
        <a href="/hr/payroll/payslips/<?= (int)$payslip['id'] ?>" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600">&larr; Tillbaka</a>
    </div>

    <div class="rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-md">
        <form method="POST" action="/hr/payroll/payslips/<?= (int)$payslip['id'] ?>" class="space-y-5"
              x-data="{ basePay: <?= (float)($payslip['base_pay'] ?? 0) ?>, obAmount: <?= (float)($payslip['ob_amount'] ?? 0) ?>, otAmount: <?= (float)($payslip['overtime_amount'] ?? 0) ?>, deductions: <?= (float)($payslip['deductions'] ?? 0) ?>, taxAmount: <?= (float)($payslip['tax_amount'] ?? 0) ?> }">
            <?= \App\Core\Csrf::field() ?>

            <div class="rounded-lg bg-gray-50 dark:bg-gray-700/50 p-3 text-sm text-gray-600 dark:text-gray-400">
                <strong class="text-gray-900 dark:text-white"><?= htmlspecialchars(($payslip['first_name'] ?? '') . ' ' . ($payslip['last_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></strong>
                &bull; <?= htmlspecialchars($payslip['period_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="base_pay" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Grundlön</label>
                    <input id="base_pay" name="base_pay" type="number" step="0.01" min="0" x-model="basePay"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="ob_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">OB-tillägg</label>
                    <input id="ob_amount" name="ob_amount" type="number" step="0.01" min="0" x-model="obAmount"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="overtime_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Övertid</label>
                    <input id="overtime_amount" name="overtime_amount" type="number" step="0.01" min="0" x-model="otAmount"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bruttolön</label>
                    <div class="mt-1 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 px-3 py-2 text-sm font-medium text-gray-900 dark:text-white">
                        <span x-text="(parseFloat(basePay||0) + parseFloat(obAmount||0) + parseFloat(otAmount||0)).toFixed(2)"></span> kr
                    </div>
                </div>
                <div>
                    <label for="tax_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Skatt</label>
                    <input id="tax_amount" name="tax_amount" type="number" step="0.01" min="0" x-model="taxAmount"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="deductions" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Övriga avdrag</label>
                    <input id="deductions" name="deductions" type="number" step="0.01" min="0" x-model="deductions"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nettolön</label>
                    <div class="mt-1 rounded-lg border border-indigo-200 dark:border-indigo-600 bg-indigo-50 dark:bg-indigo-900/20 px-3 py-2 text-sm font-bold text-indigo-700 dark:text-indigo-300">
                        <span x-text="(parseFloat(basePay||0) + parseFloat(obAmount||0) + parseFloat(otAmount||0) - parseFloat(deductions||0) - parseFloat(taxAmount||0)).toFixed(2)"></span> kr
                    </div>
                </div>
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                <select id="status" name="status"
                        class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="draft" <?= ($payslip['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Utkast</option>
                    <option value="approved" <?= ($payslip['status'] ?? '') === 'approved' ? 'selected' : '' ?>>Godkänd</option>
                    <option value="paid" <?= ($payslip['status'] ?? '') === 'paid' ? 'selected' : '' ?>>Utbetald</option>
                </select>
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Anteckningar</label>
                <textarea id="notes" name="notes" rows="2"
                          class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"><?= htmlspecialchars($payslip['notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <a href="/hr/payroll/payslips/<?= (int)$payslip['id'] ?>" class="rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Avbryt</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">Spara ändringar</button>
            </div>
        </form>
    </div>
</div>
