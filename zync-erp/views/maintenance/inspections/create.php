<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="/maintenance/inspections" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Ny besiktning</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <form method="POST" action="/maintenance/inspections" class="space-y-5">
            <?= \App\Core\Csrf::field() ?>

            <!-- Besiktningsnummer -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Besiktningsnummer</label>
                <input type="text" name="inspection_number"
                    placeholder="Lämna tomt för automatisk generering"
                    class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Lämnas tomt – genereras automatiskt</p>
            </div>

            <!-- Utrustning och Maskin -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Utrustning <span class="text-gray-400 font-normal">(valfritt)</span></label>
                    <select name="equipment_id"
                        class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— Ingen utrustning —</option>
                        <?php foreach ($equipment ?? [] as $eq): ?>
                        <option value="<?= (int)$eq['id'] ?>"><?= htmlspecialchars($eq['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Maskin <span class="text-gray-400 font-normal">(valfritt)</span></label>
                    <select name="machine_id"
                        class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— Ingen maskin —</option>
                        <?php foreach ($machines ?? [] as $machine): ?>
                        <option value="<?= (int)$machine['id'] ?>"><?= htmlspecialchars($machine['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Besiktningstyp och Planerat datum -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Besiktningstyp <span class="text-red-500">*</span></label>
                    <select name="inspection_type" required
                        class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Välj typ…</option>
                        <option value="safety">Säkerhet</option>
                        <option value="regulatory">Regulatorisk</option>
                        <option value="routine">Rutinmässig</option>
                        <option value="preventive">Förebyggande</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Planerat datum <span class="text-red-500">*</span></label>
                    <input type="date" name="scheduled_date" required
                        class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Inspektör och Avdelning -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Inspektör</label>
                    <select name="inspector_id"
                        class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— Välj inspektör —</option>
                        <?php foreach ($users ?? [] as $user): ?>
                        <option value="<?= (int)$user['id'] ?>"><?= htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Avdelning</label>
                    <select name="department_id"
                        class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— Välj avdelning —</option>
                        <?php foreach ($departments ?? [] as $dept): ?>
                        <option value="<?= (int)$dept['id'] ?>"><?= htmlspecialchars($dept['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Anteckningar -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Anteckningar</label>
                <textarea name="notes" rows="4" placeholder="Ange eventuella anteckningar…"
                    class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-y"></textarea>
            </div>

            <!-- Knappar -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="/maintenance/inspections" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition">Avbryt</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
                    Skapa besiktning
                </button>
            </div>
        </form>
    </div>
</div>
