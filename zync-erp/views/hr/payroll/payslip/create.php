<div class="mx-auto max-w-2xl">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Ny lönespecifikation</h1>
        <a href="/hr/payroll/periods/<?= (int)$period['id'] ?>" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600">&larr; Tillbaka till <?= htmlspecialchars($period['name'], ENT_QUOTES, 'UTF-8') ?></a>
    </div>

    <div class="rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-md">
        <form method="POST" action="/hr/payroll/periods/<?= (int)$period['id'] ?>/payslips" class="space-y-5"
              x-data="{ basePay: <?= (float)($old['base_pay'] ?? 0) ?>, obAmount: <?= (float)($old['ob_amount'] ?? 0) ?>, otAmount: <?= (float)($old['overtime_amount'] ?? 0) ?>, deductions: <?= (float)($old['deductions'] ?? 0) ?>, taxAmount: <?= (float)($old['tax_amount'] ?? 0) ?> }"
              x-init="$watch('basePay', () => {}); $watch('obAmount', () => {}); $watch('otAmount', () => {}); $watch('deductions', () => {}); $watch('taxAmount', () => {})">
            <?= \App\Core\Csrf::field() ?>

            <div>
                <label for="employee_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Anställd <span class="text-red-500">*</span></label>
                <select id="employee_id" name="employee_id"
                        class="mt-1 block w-full rounded-lg border <?= isset($errors['employee_id']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">— Välj anställd —</option>
                    <?php foreach ($employees as $emp): ?>
                        <option value="<?= (int)$emp['id'] ?>" <?= ($old['employee_id'] ?? '') == $emp['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($emp['last_name'] . ', ' . $emp['first_name'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['employee_id'])): ?>
                    <p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['employee_id'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
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
                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Anteckningar</label>
                <textarea id="notes" name="notes" rows="2"
                          class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"><?= htmlspecialchars($old['notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <a href="/hr/payroll/periods/<?= (int)$period['id'] ?>" class="rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Avbryt</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">Skapa lönespecifikation</button>
            </div>
        </form>
    </div>
</div>
