<div class="mx-auto max-w-2xl">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Redigera utbildning</h1>
        <a href="/hr/training/courses/<?= (int)$course['id'] ?>" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600">&larr; Tillbaka</a>
    </div>

    <div class="rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-md">
        <form method="POST" action="/hr/training/courses/<?= (int)$course['id'] ?>" class="space-y-5">
            <?= \App\Core\Csrf::field() ?>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Namn <span class="text-red-500">*</span></label>
                <input id="name" name="name" type="text" required
                       value="<?= htmlspecialchars($course['name'], ENT_QUOTES, 'UTF-8') ?>"
                       class="mt-1 block w-full rounded-lg border <?= isset($errors['name']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori</label>
                    <input id="category" name="category" type="text"
                           value="<?= htmlspecialchars($course['category'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="duration_h" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Varaktighet (h)</label>
                    <input id="duration_h" name="duration_h" type="number" step="0.5" min="0"
                           value="<?= htmlspecialchars((string)($course['duration_h'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label for="provider" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Leverantör</label>
                <input id="provider" name="provider" type="text"
                       value="<?= htmlspecialchars($course['provider'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Beskrivning</label>
                <textarea id="description" name="description" rows="3"
                          class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"><?= htmlspecialchars($course['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="flex items-center gap-2">
                <input id="is_mandatory" name="is_mandatory" type="checkbox" value="1" <?= $course['is_mandatory'] ? 'checked' : '' ?>
                       class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                <label for="is_mandatory" class="text-sm text-gray-700 dark:text-gray-300">Obligatorisk utbildning</label>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <a href="/hr/training/courses/<?= (int)$course['id'] ?>" class="rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Avbryt</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">Spara ändringar</button>
            </div>
        </form>
    </div>
</div>
