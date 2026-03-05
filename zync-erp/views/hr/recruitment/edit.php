<div class="mx-auto max-w-2xl">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Redigera tjänst</h1>
        <a href="/hr/recruitment/positions/<?= (int)$position['id'] ?>" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600">&larr; Tillbaka</a>
    </div>

    <div class="rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-md">
        <form method="POST" action="/hr/recruitment/positions/<?= (int)$position['id'] ?>" class="space-y-5">
            <?= \App\Core\Csrf::field() ?>

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Titel <span class="text-red-500">*</span></label>
                <input id="title" name="title" type="text" required
                       value="<?= htmlspecialchars($position['title'], ENT_QUOTES, 'UTF-8') ?>"
                       class="mt-1 block w-full rounded-lg border <?= isset($errors['title']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>

            <div>
                <label for="department_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Avdelning</label>
                <select id="department_id" name="department_id"
                        class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">— Välj avdelning —</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= (int)$d['id'] ?>" <?= ($position['department_id'] ?? '') == $d['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($d['name'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="num_openings" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Antal tjänster</label>
                    <input id="num_openings" name="num_openings" type="number" min="1"
                           value="<?= (int)($position['num_openings'] ?? 1) ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select id="status" name="status"
                            class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <option value="draft" <?= ($position['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Utkast</option>
                        <option value="open" <?= ($position['status'] ?? '') === 'open' ? 'selected' : '' ?>>Öppen</option>
                        <option value="on_hold" <?= ($position['status'] ?? '') === 'on_hold' ? 'selected' : '' ?>>Pausad</option>
                        <option value="closed" <?= ($position['status'] ?? '') === 'closed' ? 'selected' : '' ?>>Stängd</option>
                        <option value="filled" <?= ($position['status'] ?? '') === 'filled' ? 'selected' : '' ?>>Tillsatt</option>
                    </select>
                </div>
                <div>
                    <label for="posted_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Publicerad</label>
                    <input id="posted_at" name="posted_at" type="date"
                           value="<?= htmlspecialchars($position['posted_at'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="closes_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stängs</label>
                    <input id="closes_at" name="closes_at" type="date"
                           value="<?= htmlspecialchars($position['closes_at'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Beskrivning</label>
                <textarea id="description" name="description" rows="3"
                          class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"><?= htmlspecialchars($position['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div>
                <label for="requirements" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Krav</label>
                <textarea id="requirements" name="requirements" rows="3"
                          class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"><?= htmlspecialchars($position['requirements'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <a href="/hr/recruitment/positions/<?= (int)$position['id'] ?>" class="rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Avbryt</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">Spara ändringar</button>
            </div>
        </form>
    </div>
</div>
