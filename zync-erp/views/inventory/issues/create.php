<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Nytt uttag</h1>
        <a href="/inventory/issues"
           class="inline-flex items-center gap-2 rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
            Tillbaka
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 max-w-2xl">
        <form method="POST" action="/inventory/issues" class="space-y-5">
            <?= \App\Core\Csrf::field() ?>

            <!-- Artikel -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Artikel <span class="text-red-500">*</span></label>
                <select name="article_id" required
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    <option value="">— Välj artikel —</option>
                    <?php foreach ($articles as $article): ?>
                    <option value="<?= htmlspecialchars((string) $article['id'], ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars(($article['article_number'] ?? '') . ' – ' . ($article['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Lagerställe -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lagerställe <span class="text-red-500">*</span></label>
                <select name="warehouse_id" required
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    <option value="">— Välj lagerställe —</option>
                    <?php foreach ($warehouses as $wh): ?>
                    <option value="<?= htmlspecialchars((string) $wh['id'], ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($wh['name'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Antal -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Antal <span class="text-red-500">*</span></label>
                <input type="number" name="quantity" required step="0.01" min="0.01"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>

            <!-- Referenstyp -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Referenstyp <span class="text-gray-400 text-xs">(valfritt)</span></label>
                <input type="text" name="reference_type"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>

            <!-- Referens-ID -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Referens-ID <span class="text-gray-400 text-xs">(valfritt)</span></label>
                <input type="number" name="reference_id"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>

            <!-- Noteringar -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Noteringar</label>
                <textarea name="notes" rows="3"
                          class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="rounded-lg bg-indigo-600 px-6 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
                    Registrera uttag
                </button>
                <a href="/inventory/issues"
                   class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Avbryt</a>
            </div>
        </form>
    </div>
</div>
