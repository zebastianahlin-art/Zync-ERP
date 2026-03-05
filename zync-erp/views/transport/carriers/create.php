<div class="max-w-xl space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Ny transportör</h1>

    <form method="POST" action="/transport/carriers" class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-4">
        <?= \App\Core\Csrf::field() ?>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Namn <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="<?= htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <?php if (!empty($errors['name'])): ?><p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kod</label>
                <input type="text" name="code" value="<?= htmlspecialchars($old['code'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Typ</label>
            <select name="type" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="external" <?= ($old['type'] ?? 'external') === 'external' ? 'selected' : '' ?>>Extern</option>
                <option value="internal" <?= ($old['type'] ?? '') === 'internal' ? 'selected' : '' ?>>Intern</option>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kontaktperson</label>
                <input type="text" name="contact_person" value="<?= htmlspecialchars($old['contact_person'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Telefon</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($old['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">E-post</label>
            <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Avtalsnummer</label>
                <input type="text" name="contract_number" value="<?= htmlspecialchars($old['contract_number'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Avtal giltigt t.o.m.</label>
                <input type="date" name="contract_valid_until" value="<?= htmlspecialchars($old['contract_valid_until'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div>
            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                <input type="checkbox" name="is_active" value="1" <?= (!isset($old['is_active']) || $old['is_active']) ? 'checked' : '' ?>
                    class="rounded border-gray-300 dark:border-gray-600 text-indigo-600">
                Aktiv
            </label>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Anteckningar</label>
            <textarea name="notes" rows="2" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"><?= htmlspecialchars($old['notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <!-- B6: Synkronisera med leverantörsregistret -->
        <div class="border border-indigo-200 dark:border-indigo-800 rounded-lg p-4 space-y-3 bg-indigo-50 dark:bg-indigo-900/20" x-data="{ sync: false }">
            <label class="flex items-center gap-2 text-sm font-medium text-indigo-700 dark:text-indigo-300">
                <input type="checkbox" name="sync_supplier" value="1" x-model="sync" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600">
                Synkronisera med leverantörsregistret
            </label>
            <div x-show="sync" class="space-y-2">
                <p class="text-xs text-gray-500 dark:text-gray-400">Välj befintlig leverantör eller lämna tomt för att skapa ny automatiskt.</p>
                <select name="existing_supplier_id" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">— Skapa ny leverantör automatiskt —</option>
                    <?php foreach ($suppliers ?? [] as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Spara</button>
            <a href="/transport/carriers" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 text-sm font-medium rounded-lg transition">Avbryt</a>
        </div>
    </form>
</div>
