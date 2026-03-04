<div class="max-w-md space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Ny roll</h1>

    <form method="POST" action="/admin/roles" class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-4">
        <?= \App\Core\Csrf::field() ?>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Namn <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="<?= htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <?php if (!empty($errors['name'])): ?><p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Slug <span class="text-red-500">*</span></label>
            <input type="text" name="slug" value="<?= htmlspecialchars($old['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                placeholder="t.ex. operator"
                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <?php if (!empty($errors['slug'])): ?><p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['slug'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nivå (1–10) <span class="text-red-500">*</span></label>
            <input type="number" name="level" value="<?= (int) ($old['level'] ?? 1) ?>" min="1" max="10"
                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <?php if (!empty($errors['level'])): ?><p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['level'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <p class="text-xs text-gray-400 mt-1">Nivå 7+ ger admin-åtkomst.</p>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Spara</button>
            <a href="/admin/roles" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-sm font-medium rounded-lg transition">Avbryt</a>
        </div>
    </form>
</div>
