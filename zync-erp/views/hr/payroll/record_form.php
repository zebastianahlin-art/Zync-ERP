<?php
/** @var string $title */
/** @var array $record */
?>
<div class="mx-auto max-w-xl space-y-6">
    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Redigera lönepost – <?= htmlspecialchars($record['first_name'] . ' ' . $record['last_name']) ?></h1>

    <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-6">
        <form method="post" action="/hr/payroll/<?= $record['period_id'] ?>/records/<?= $record['id'] ?>" class="space-y-4">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars(\App\Core\Csrf::token(), ENT_QUOTES) ?>">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Grundlön</label>
                    <input type="number" name="base_salary" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white" value="<?= $record['base_salary'] ?>" step="0.01">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Övertidstimmar</label>
                    <input type="number" name="overtime_hours" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white" value="<?= $record['overtime_hours'] ?>" step="0.5">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Övertidsbelopp</label>
                    <input type="number" name="overtime_amount" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white" value="<?= $record['overtime_amount'] ?>" step="0.01">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bonus</label>
                    <input type="number" name="bonus" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white" value="<?= $record['bonus'] ?>" step="0.01">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Avdrag</label>
                    <input type="number" name="deductions" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white" value="<?= $record['deductions'] ?>" step="0.01">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Skatt</label>
                    <input type="number" name="tax" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white" value="<?= $record['tax'] ?>" step="0.01">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nettolön</label>
                    <input type="number" name="net_salary" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white" value="<?= $record['net_salary'] ?>" step="0.01">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Anteckningar</label>
                <textarea name="notes" rows="2" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($record['notes'] ?? '') ?></textarea>
            </div>
            <div class="flex justify-between pt-2">
                <a href="/hr/payroll/<?= $record['period_id'] ?>" class="rounded-lg bg-gray-100 dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 transition-colors">Avbryt</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">Spara</button>
            </div>
        </form>
    </div>
</div>
