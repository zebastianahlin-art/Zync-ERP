<div class="space-y-6 max-w-2xl">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Ny övringsmall</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Skapa en återanvändbar mall för nödlägesövningar</p>
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

    <form method="POST" action="/safety/emergency/drills/templates" class="bg-white dark:bg-gray-800 rounded-2xl shadow-md p-6 space-y-5">
        <?= \App\Core\Csrf::field() ?>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Namn <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="<?= htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Typ</label>
                <select name="drill_type" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <?php foreach (['fire' => 'Brand', 'evacuation' => 'Utrymning', 'chemical' => 'Kemikalieolycka', 'earthquake' => 'Jordbävning', 'lockdown' => 'Låsning', 'medical' => 'Medicinsk nödsituation', 'other' => 'Övrigt'] as $val => $label): ?>
                        <option value="<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8') ?>" <?= ($old['drill_type'] ?? 'fire') === $val ? 'selected' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Uppskattad tid (min)</label>
                <input type="number" name="duration_estimate" value="<?= htmlspecialchars($old['duration_estimate'] ?? '', ENT_QUOTES, 'UTF-8') ?>" min="0"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Beskrivning</label>
            <textarea name="description" rows="3" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"><?= htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Checklista</label>
            <textarea name="checklist" rows="5" placeholder="En punkt per rad..."
                      class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono"><?= htmlspecialchars($old['checklist'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nödvändiga resurser</label>
            <textarea name="required_resources" rows="3" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"><?= htmlspecialchars($old['required_resources'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" id="is_active" value="1" <?= !isset($old['is_active']) || $old['is_active'] ? 'checked' : '' ?> class="rounded border-gray-300 dark:border-gray-600 text-indigo-600">
            <label for="is_active" class="text-sm text-gray-700 dark:text-gray-300">Aktiv mall</label>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="/safety/emergency/drills/templates" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:underline">Avbryt</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">Spara</button>
        </div>
    </form>
</div>
