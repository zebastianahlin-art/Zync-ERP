<?php
/** @var string $title */
/** @var array $period */
$months = ['','Januari','Februari','Mars','April','Maj','Juni','Juli','Augusti','September','Oktober','November','December'];
?>
<div class="mx-auto max-w-lg space-y-6">
    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Ny löneperiod</h1>

    <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-6">
        <form method="post" action="/hr/payroll" class="space-y-4">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars(\App\Core\Csrf::token(), ENT_QUOTES) ?>">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">År</label>
                    <input type="number" name="year" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white" value="<?= $period['year'] ?? date('Y') ?>" min="2020" max="2040" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Månad</label>
                    <select name="month" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white" required>
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?= $m ?>" <?= ($period['month'] ?? date('n')) == $m ? 'selected' : '' ?>><?= $months[$m] ?></option>
                        <?php endfor ?>
                    </select>
                </div>
            </div>
            <div class="rounded-lg bg-blue-50 dark:bg-blue-900/20 px-4 py-3 text-sm text-blue-700 dark:text-blue-400">
                Löneperioden skapas och alla aktiva anställda läggs till automatiskt med sin grundlön.
            </div>
            <div class="flex justify-between pt-2">
                <a href="/hr/payroll" class="rounded-lg bg-gray-100 dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 transition-colors">Avbryt</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">Skapa period</button>
            </div>
        </form>
    </div>
</div>
