<div class="max-w-xl space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Ny offertmall</h1>

    <form method="POST" action="/sales/quotes/templates" class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-4">
        <?= \App\Core\Csrf::field() ?>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Namn <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="<?= htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <?php if (!empty($errors['name'])): ?>
                <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Beskrivning</label>
            <textarea name="description" rows="3"
                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"><?= htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Standardgiltighetsdagar</label>
            <input type="number" name="default_valid_days" min="1" value="<?= (int) ($old['default_valid_days'] ?? 30) ?>"
                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" id="is_active" value="1"
                <?= !empty($old['is_active']) ? 'checked' : '' ?>
                class="h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded">
            <label for="is_active" class="text-sm font-medium text-gray-700 dark:text-gray-300">Aktiv</label>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Spara</button>
            <a href="/sales/quotes/templates" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white text-sm font-medium rounded-lg transition">Avbryt</a>
        </div>
    </form>
</div>
