<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Redigera lagerställe</h1>
        <a href="/inventory/warehouses"
           class="inline-flex items-center gap-2 rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
            Tillbaka
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 max-w-2xl">
        <form method="POST" action="/inventory/warehouses/<?= htmlspecialchars((string) $warehouse['id'], ENT_QUOTES, 'UTF-8') ?>" class="space-y-5">
            <?= \App\Core\Csrf::field() ?>

            <!-- Namn -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Namn <span class="text-red-500">*</span></label>
                <input type="text" name="name" required
                       value="<?= htmlspecialchars($warehouse['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>

            <!-- Kod -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kod <span class="text-red-500">*</span></label>
                <input type="text" name="code" required maxlength="50"
                       value="<?= htmlspecialchars($warehouse['code'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>

            <!-- Adress -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Adress</label>
                <textarea name="address" rows="3"
                          class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none"><?= htmlspecialchars($warehouse['address'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <!-- Ansvarig -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ansvarig</label>
                <select name="responsible_user_id"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    <option value="">— Ingen —</option>
                    <?php foreach ($users as $user): ?>
                    <option value="<?= htmlspecialchars((string) $user['id'], ENT_QUOTES, 'UTF-8') ?>"
                        <?= ((string) ($warehouse['responsible_user_id'] ?? '') === (string) $user['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Aktiv -->
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       <?= !empty($warehouse['is_active']) ? 'checked' : '' ?>
                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <label for="is_active" class="text-sm font-medium text-gray-700 dark:text-gray-300">Aktiv</label>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="rounded-lg bg-indigo-600 px-6 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
                    Spara
                </button>
                <a href="/inventory/warehouses"
                   class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Avbryt</a>
            </div>
        </form>
    </div>
</div>
