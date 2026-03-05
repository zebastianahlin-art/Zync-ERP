<div class="mx-auto max-w-2xl">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Redigera kurstillfälle</h1>
        <a href="/hr/training/sessions/<?= (int)$session['id'] ?>" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600">&larr; Tillbaka</a>
    </div>

    <div class="rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-md">
        <form method="POST" action="/hr/training/sessions/<?= (int)$session['id'] ?>" class="space-y-5">
            <?= \App\Core\Csrf::field() ?>

            <div>
                <label for="course_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kurs <span class="text-red-500">*</span></label>
                <select id="course_id" name="course_id"
                        class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <?php foreach ($courses as $c): ?>
                        <option value="<?= (int)$c['id'] ?>" <?= ($session['course_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Startdatum</label>
                    <input id="start_date" name="start_date" type="date"
                           value="<?= htmlspecialchars($session['start_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Slutdatum</label>
                    <input id="end_date" name="end_date" type="date"
                           value="<?= htmlspecialchars($session['end_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Plats</label>
                    <input id="location" name="location" type="text"
                           value="<?= htmlspecialchars($session['location'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="trainer" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tränare</label>
                    <input id="trainer" name="trainer" type="text"
                           value="<?= htmlspecialchars($session['trainer'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="max_participants" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Max deltagare</label>
                    <input id="max_participants" name="max_participants" type="number" min="1"
                           value="<?= htmlspecialchars((string)($session['max_participants'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select id="status" name="status"
                            class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <option value="planned" <?= ($session['status'] ?? '') === 'planned' ? 'selected' : '' ?>>Planerad</option>
                        <option value="ongoing" <?= ($session['status'] ?? '') === 'ongoing' ? 'selected' : '' ?>>Pågående</option>
                        <option value="completed" <?= ($session['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Avslutad</option>
                        <option value="cancelled" <?= ($session['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Inställd</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <a href="/hr/training/sessions/<?= (int)$session['id'] ?>" class="rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Avbryt</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">Spara ändringar</button>
            </div>
        </form>
    </div>
</div>
