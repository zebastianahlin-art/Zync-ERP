<div class="mx-auto max-w-2xl">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Redigera audit</h1>
        <a href="/safety/audits/<?= (int) $audit['id'] ?>" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">&larr; Tillbaka</a>
    </div>
    <div class="rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-md">
        <form method="POST" action="/safety/audits/<?= (int) $audit['id'] ?>" class="space-y-5">
            <input type="hidden" name="_token" value="<?= \App\Core\Csrf::token() ?>">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Titel <span class="text-red-500">*</span></label>
                <input id="title" name="title" type="text" required value="<?= htmlspecialchars($audit['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="mt-1 block w-full rounded-lg border <?= isset($errors['title']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                <?php if (isset($errors['title'])): ?><p class="mt-1 text-xs text-red-600 dark:text-red-400"><?= htmlspecialchars($errors['title'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            </div>
            <div>
                <label for="template_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mall</label>
                <select id="template_id" name="template_id" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">Ingen mall</option>
                    <?php foreach ($templates as $t): ?>
                        <option value="<?= (int) $t['id'] ?>" <?= ($audit['template_id'] ?? '') === (string) $t['id'] ? 'selected' : '' ?>><?= htmlspecialchars($t['name'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Beskrivning</label>
                <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"><?= htmlspecialchars($audit['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <div>
                <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Plats</label>
                <input id="location" name="location" type="text" value="<?= htmlspecialchars($audit['location'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>
            <div>
                <label for="assigned_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ansvarig (användar-ID) <span class="text-red-500">*</span></label>
                <input id="assigned_to" name="assigned_to" type="number" min="1" required value="<?= htmlspecialchars((string) ($audit['assigned_to'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                       class="mt-1 block w-full rounded-lg border <?= isset($errors['assigned_to']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                <?php if (isset($errors['assigned_to'])): ?><p class="mt-1 text-xs text-red-600 dark:text-red-400"><?= htmlspecialchars($errors['assigned_to'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            </div>
            <div>
                <label for="scheduled_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Planerat datum <span class="text-red-500">*</span></label>
                <input id="scheduled_date" name="scheduled_date" type="date" required value="<?= htmlspecialchars($audit['scheduled_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="mt-1 block w-full rounded-lg border <?= isset($errors['scheduled_date']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                <?php if (isset($errors['scheduled_date'])): ?><p class="mt-1 text-xs text-red-600 dark:text-red-400"><?= htmlspecialchars($errors['scheduled_date'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            </div>
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Anteckningar</label>
                <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"><?= htmlspecialchars($audit['notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <div class="flex items-center justify-end space-x-3 pt-2">
                <a href="/safety/audits/<?= (int) $audit['id'] ?>" class="rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Avbryt</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">Spara ändringar</button>
            </div>
        </form>
    </div>
</div>
