<div class="mx-auto max-w-2xl">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Redigera löneperiod</h1>
        <a href="/hr/payroll/periods/<?= (int)$period['id'] ?>" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600">&larr; Tillbaka</a>
    </div>

    <div class="rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-md">
        <form method="POST" action="/hr/payroll/periods/<?= (int)$period['id'] ?>" class="space-y-5">
            <?= \App\Core\Csrf::field() ?>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Namn <span class="text-red-500">*</span></label>
                <input id="name" name="name" type="text" required
                       value="<?= htmlspecialchars($period['name'], ENT_QUOTES, 'UTF-8') ?>"
                       class="mt-1 block w-full rounded-lg border <?= isset($errors['name']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                <?php if (isset($errors['name'])): ?>
                    <p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="period_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Periodens start</label>
                    <input id="period_from" name="period_from" type="date"
                           value="<?= htmlspecialchars($period['period_from'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="period_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Periodens slut</label>
                    <input id="period_to" name="period_to" type="date"
                           value="<?= htmlspecialchars($period['period_to'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <a href="/hr/payroll/periods/<?= (int)$period['id'] ?>" class="rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Avbryt</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">Spara ändringar</button>
            </div>
        </form>
    </div>
</div>
