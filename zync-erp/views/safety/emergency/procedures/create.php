<div class="mx-auto max-w-2xl">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Ny nödprocedur</h1>
        <a href="/safety/emergency/procedures" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">&larr; Tillbaka</a>
    </div>
    <div class="rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-md">
        <form method="POST" action="/safety/emergency/procedures" class="space-y-5">
            <input type="hidden" name="_token" value="<?= \App\Core\Csrf::token() ?>">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Titel <span class="text-red-500">*</span></label>
                <input id="title" name="title" type="text" required value="<?= htmlspecialchars($old['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="mt-1 block w-full rounded-lg border <?= isset($errors['title']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                <?php if (isset($errors['title'])): ?><p class="mt-1 text-xs text-red-600 dark:text-red-400"><?= htmlspecialchars($errors['title'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            </div>
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori</label>
                <select id="category" name="category" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <?php foreach (['fire'=>'Brand','evacuation'=>'Utrymning','first_aid'=>'Första hjälpen','chemical_spill'=>'Kemikaliespill','electrical'=>'Elektrisk','natural_disaster'=>'Naturkatastrof','bomb_threat'=>'Bombhot','medical'=>'Medicinsk','other'=>'Annat'] as $v=>$l): ?>
                        <option value="<?= $v ?>" <?= ($old['category'] ?? 'other') === $v ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Beskrivning</label>
                <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"><?= htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <div>
                <label for="steps" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Steg <span class="text-red-500">*</span></label>
                <textarea id="steps" name="steps" rows="6" required
                          class="mt-1 block w-full rounded-lg border <?= isset($errors['steps']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"><?= htmlspecialchars($old['steps'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                <?php if (isset($errors['steps'])): ?><p class="mt-1 text-xs text-red-600 dark:text-red-400"><?= htmlspecialchars($errors['steps'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            </div>
            <div>
                <label for="responsible" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ansvarig</label>
                <input id="responsible" name="responsible" type="text" value="<?= htmlspecialchars($old['responsible'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>
            <div>
                <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Plats</label>
                <input id="location" name="location" type="text" value="<?= htmlspecialchars($old['location'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label for="last_reviewed" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Senast granskad</label>
                    <input id="last_reviewed" name="last_reviewed" type="date" value="<?= htmlspecialchars($old['last_reviewed'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="review_interval" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Granskningsintervall (dagar)</label>
                    <input id="review_interval" name="review_interval" type="number" min="1" value="<?= htmlspecialchars((string) ($old['review_interval'] ?? '365'), ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <input id="is_active" name="is_active" type="checkbox" value="1" <?= (($old['is_active'] ?? '1') === '1') ? 'checked' : '' ?>
                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <label for="is_active" class="text-sm font-medium text-gray-700 dark:text-gray-300">Aktiv</label>
            </div>
            <div class="flex items-center justify-end space-x-3 pt-2">
                <a href="/safety/emergency/procedures" class="rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Avbryt</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">Skapa procedur</button>
            </div>
        </form>
    </div>
</div>
