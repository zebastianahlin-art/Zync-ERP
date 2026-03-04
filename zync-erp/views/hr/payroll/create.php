<div class="max-w-xl space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Ny löneperiod</h1>

    <form method="POST" action="/hr/payroll/periods" class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-4">
        <?= \App\Core\Csrf::field() ?>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Namn <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="<?= htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                placeholder="t.ex. Januari 2025"
                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <?php if (!empty($errors['name'])): ?><p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Från</label>
                <input type="date" name="period_from" value="<?= htmlspecialchars($old['period_from'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Till</label>
                <input type="date" name="period_to" value="<?= htmlspecialchars($old['period_to'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Spara</button>
            <a href="/hr/payroll" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-sm font-medium rounded-lg transition">Avbryt</a>
        </div>
    </form>
</div>
