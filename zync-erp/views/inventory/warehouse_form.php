<div class="mx-auto max-w-lg space-y-6">
    <div class="flex items-center gap-4">
        <a href="/inventory/warehouses" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"><?= $isEdit ? 'Redigera lagerplats' : 'Ny lagerplats' ?></h1>
    </div>

    <form method="POST" action="<?= $isEdit ? '/inventory/warehouses/' . $warehouse['id'] : '/inventory/warehouses' ?>"
          class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 p-6 space-y-5">

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Namn *</label>
                <input type="text" name="name" id="name" required
                       value="<?= htmlspecialchars($warehouse['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 <?= !empty($errors['name']) ? 'border-red-500' : '' ?>">
                <?php if (!empty($errors['name'])): ?><p class="mt-1 text-xs text-red-600"><?= $errors['name'] ?></p><?php endif; ?>
            </div>
            <div>
                <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kod *</label>
                <input type="text" name="code" id="code" required maxlength="20"
                       value="<?= htmlspecialchars($warehouse['code'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm font-mono uppercase dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 <?= !empty($errors['code']) ? 'border-red-500' : '' ?>">
                <?php if (!empty($errors['code'])): ?><p class="mt-1 text-xs text-red-600"><?= $errors['code'] ?></p><?php endif; ?>
            </div>
        </div>

        <div>
            <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Adress</label>
            <input type="text" name="address" id="address"
                   value="<?= htmlspecialchars($warehouse['address'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
        </div>

        <div>
            <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Stad</label>
            <input type="text" name="city" id="city"
                   value="<?= htmlspecialchars($warehouse['city'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
        </div>

        <?php if ($isEdit): ?>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" <?= $warehouse['is_active'] ? 'checked' : '' ?>
                       class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                <label for="is_active" class="text-sm text-gray-700 dark:text-gray-300">Aktiv</label>
            </div>
        <?php endif; ?>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="/inventory/warehouses" class="rounded-lg px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors">Avbryt</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-6 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors"><?= $isEdit ? 'Uppdatera' : 'Skapa' ?></button>
        </div>
    </form>
</div>
