<div class="max-w-md space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Ny lagerpost</h1>

    <form method="POST" action="/production/stock" class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-4">
        <?= \App\Core\Csrf::field() ?>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Artikel-ID</label>
            <input type="number" name="article_id" value="<?= htmlspecialchars($old['article_id'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Plats <span class="text-red-500">*</span></label>
            <input type="text" name="location" value="<?= htmlspecialchars($old['location'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                placeholder="t.ex. A-01-3">
            <?php if (!empty($errors['location'])): ?><p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['location'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Antal <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" name="quantity" value="<?= htmlspecialchars($old['quantity'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <?php if (!empty($errors['quantity'])): ?><p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['quantity'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Enhet</label>
                <input type="text" name="unit" value="<?= htmlspecialchars($old['unit'] ?? 'st', ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Spara</button>
            <a href="/production/stock/manage" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white text-sm font-medium rounded-lg transition">Avbryt</a>
        </div>
    </form>
</div>
