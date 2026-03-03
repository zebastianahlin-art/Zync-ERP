<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
        <a href="/maintenance/inspections" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Nytt besiktningsobjekt</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <form method="POST" action="/maintenance/inspections" class="space-y-4">
            <?= \App\Core\Csrf::field() ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Namn *</label>
                    <input type="text" name="name" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Typ</label>
                    <select name="type" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="crane">Kran</option>
                        <option value="hoist">Lyftredskap</option>
                        <option value="lifting_gear">Lyftutrustning</option>
                        <option value="fall_protection">Fallskydd</option>
                        <option value="pressure_vessel">Tryckkärl</option>
                        <option value="forklift">Truck</option>
                        <option value="elevator">Hiss</option>
                        <option value="fire_equipment">Brandskydd</option>
                        <option value="electrical">Elinstallation</option>
                        <option value="other" selected>Övrigt</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kopplad utrustning</label>
                    <select name="equipment_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="">—</option>
                        <?php foreach ($equipment as $e): ?>
                        <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['name'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Serienummer</label>
                    <input type="text" name="serial_number" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tillverkare</label>
                    <input type="text" name="manufacturer" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Plats</label>
                    <input type="text" name="location" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Besiktningsorgan</label>
                    <input type="text" name="certification_body" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Besiktningsintervall (månader)</label>
                    <input type="number" name="inspection_interval_months" value="12" min="1" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Max last (kg)</label>
                    <input type="number" name="max_load_kg" step="0.01" min="0" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Senaste besiktningsdatum</label>
                    <input type="date" name="last_inspection_date" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nästa besiktningsdatum</label>
                    <input type="date" name="next_inspection_date" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Anteckningar</label>
                    <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <a href="/maintenance/inspections" class="px-4 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">Avbryt</a>
                <button type="submit" class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">Spara</button>
            </div>
        </form>
    </div>
</div>
