<div class="max-w-xl space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Nytt projekt</h1>

    <form method="POST" action="/projects" class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-4">
        <?= \App\Core\Csrf::field() ?>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Projektnummer <span class="text-red-500">*</span></label>
                <input type="text" name="project_number" value="<?= htmlspecialchars($old['project_number'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <?php if (!empty($errors['project_number'])): ?><p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['project_number'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <?php foreach (['planning' => 'Planering', 'active' => 'Aktiv', 'on_hold' => 'Pausad', 'completed' => 'Klar', 'cancelled' => 'Avbruten'] as $val => $label): ?>
                    <option value="<?= $val ?>" <?= ($old['status'] ?? 'planning') === $val ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Namn <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="<?= htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <?php if (!empty($errors['name'])): ?><p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kund</label>
            <select name="customer_id" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">— Välj kund —</option>
                <?php foreach ($customers as $c): ?>
                <option value="<?= $c['id'] ?>" <?= ($old['customer_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Projektledare</label>
            <select name="manager_id" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">— Välj användare —</option>
                <?php foreach ($users as $u): ?>
                <option value="<?= $u['id'] ?>" <?= ($old['manager_id'] ?? '') == $u['id'] ? 'selected' : '' ?>><?= htmlspecialchars($u['full_name'] ?? $u['username'], ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Startdatum</label>
                <input type="date" name="start_date" value="<?= htmlspecialchars($old['start_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Slutdatum</label>
                <input type="date" name="end_date" value="<?= htmlspecialchars($old['end_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Budget (kr)</label>
            <input type="number" name="budget" value="<?= htmlspecialchars($old['budget'] ?? '0', ENT_QUOTES, 'UTF-8') ?>" step="0.01"
                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Beskrivning</label>
            <textarea name="description" rows="3" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"><?= htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Spara</button>
            <a href="/projects" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-sm font-medium rounded-lg transition">Avbryt</a>
        </div>
    </form>
</div>
