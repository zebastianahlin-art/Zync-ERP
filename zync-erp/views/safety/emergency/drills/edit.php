<div class="space-y-6 max-w-2xl">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Redigera nödlägesövning</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5"><?= htmlspecialchars($drill['drill_number'], ENT_QUOTES, 'UTF-8') ?></p>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="rounded-lg bg-red-50 dark:bg-red-900/30 px-4 py-3 text-sm text-red-700 dark:text-red-400">
            <ul class="list-disc pl-4 space-y-1">
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="/safety/emergency/drills/<?= (int) $drill['id'] ?>" class="bg-white dark:bg-gray-800 rounded-2xl shadow-md p-6 space-y-5">
        <?= \App\Core\Csrf::field() ?>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Titel <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="<?= htmlspecialchars($drill['title'], ENT_QUOTES, 'UTF-8') ?>"
                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Typ</label>
                <select name="drill_type" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <?php foreach (['fire' => 'Brand', 'evacuation' => 'Utrymning', 'chemical' => 'Kemikalieolycka', 'earthquake' => 'Jordbävning', 'lockdown' => 'Låsning', 'medical' => 'Medicinsk nödsituation', 'other' => 'Övrigt'] as $val => $label): ?>
                        <option value="<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8') ?>" <?= $drill['drill_type'] === $val ? 'selected' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select name="status" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <?php foreach (['planned' => 'Planerad', 'in_progress' => 'Pågående', 'completed' => 'Slutförd', 'cancelled' => 'Avbruten'] as $val => $label): ?>
                        <option value="<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8') ?>" <?= $drill['status'] === $val ? 'selected' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <?php if (!empty($templates)): ?>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mall</label>
            <select name="template_id" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">– Ingen mall –</option>
                <?php foreach ($templates as $t): ?>
                    <option value="<?= (int) $t['id'] ?>" <?= $drill['template_id'] == $t['id'] ? 'selected' : '' ?>><?= htmlspecialchars($t['name'], ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Beskrivning</label>
            <textarea name="description" rows="3" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"><?= htmlspecialchars($drill['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Plats</label>
                <input type="text" name="location" value="<?= htmlspecialchars($drill['location'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Planerat datum <span class="text-red-500">*</span></label>
                <input type="date" name="scheduled_date" value="<?= htmlspecialchars($drill['scheduled_date'], ENT_QUOTES, 'UTF-8') ?>"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Genomfört datum</label>
                <input type="date" name="executed_date" value="<?= htmlspecialchars($drill['executed_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nästa övning</label>
                <input type="date" name="next_drill_date" value="<?= htmlspecialchars($drill['next_drill_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Varaktighet (min)</label>
                <input type="number" name="duration_minutes" value="<?= htmlspecialchars($drill['duration_minutes'] ?? '', ENT_QUOTES, 'UTF-8') ?>" min="0"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deltagare</label>
                <input type="number" name="participants" value="<?= htmlspecialchars($drill['participants'] ?? '', ENT_QUOTES, 'UTF-8') ?>" min="0"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Poäng</label>
                <input type="number" step="0.01" name="score" value="<?= htmlspecialchars($drill['score'] ?? '', ENT_QUOTES, 'UTF-8') ?>" min="0" max="100"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Utvärdering</label>
            <textarea name="evaluation" rows="3" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"><?= htmlspecialchars($drill['evaluation'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Förbättringsförslag</label>
            <textarea name="improvements" rows="3" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"><?= htmlspecialchars($drill['improvements'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="/safety/emergency/drills/<?= (int) $drill['id'] ?>" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:underline">Avbryt</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">Spara ändringar</button>
        </div>
    </form>
</div>
