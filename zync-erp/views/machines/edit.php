<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
        <a href="/machines/<?= $machine['id'] ?>" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Redigera maskin</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <form method="POST" action="/machines/<?= $machine['id'] ?>" class="space-y-4">
            <?= \App\Core\Csrf::field() ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Namn *</label>
                    <input type="text" name="name" required value="<?= htmlspecialchars($machine['name'], ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Utrustning (parent)</label>
                    <select name="equipment_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="">—</option>
                        <?php foreach ($equipment as $e): ?>
                        <option value="<?= $e['id'] ?>"<?= ($machine['equipment_id']==$e['id'])?' selected':'' ?>><?= htmlspecialchars($e['name'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Avdelning</label>
                    <select name="department_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="">—</option>
                        <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['id'] ?>"<?= ($machine['department_id']==$d['id'])?' selected':'' ?>><?= htmlspecialchars($d['name'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <?php foreach (['running'=>'Igång','idle'=>'Idle','maintenance'=>'Underhåll','breakdown'=>'Haveri','decommissioned'=>'Avvecklad'] as $v=>$l): ?>
                        <option value="<?= $v ?>"<?= $machine['status']===$v?' selected':'' ?>><?= $l ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kritikalitet</label>
                    <select name="criticality" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <?php foreach (['low'=>'Låg','medium'=>'Medel','high'=>'Hög','critical'=>'Kritisk'] as $v=>$l): ?>
                        <option value="<?= $v ?>"<?= $machine['criticality']===$v?' selected':'' ?>><?= $l ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Plats</label>
                    <input type="text" name="location" value="<?= htmlspecialchars($machine['location'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tillverkare</label>
                    <input type="text" name="manufacturer" value="<?= htmlspecialchars($machine['manufacturer'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Modell</label>
                    <input type="text" name="model" value="<?= htmlspecialchars($machine['model'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Serienummer</label>
                    <input type="text" name="serial_number" value="<?= htmlspecialchars($machine['serial_number'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tillverkningsår</label>
                    <input type="number" name="year_of_manufacture" value="<?= htmlspecialchars($machine['year_of_manufacture'] ?? '', ENT_QUOTES, 'UTF-8') ?>" min="1900" max="2099" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Effekt (kW)</label>
                    <input type="number" name="power_kw" step="0.01" min="0" value="<?= htmlspecialchars($machine['power_kw'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Serviceintervall (dagar)</label>
                    <input type="number" name="maintenance_interval_days" min="1" value="<?= htmlspecialchars($machine['maintenance_interval_days'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Senaste service</label>
                    <input type="date" name="last_maintenance_date" value="<?= htmlspecialchars($machine['last_maintenance_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nästa service</label>
                    <input type="date" name="next_maintenance_date" value="<?= htmlspecialchars($machine['next_maintenance_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Beskrivning</label>
                    <textarea name="description" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"><?= htmlspecialchars($machine['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Anteckningar</label>
                    <textarea name="notes" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"><?= htmlspecialchars($machine['notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <a href="/machines/<?= $machine['id'] ?>" class="px-4 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">Avbryt</a>
                <button type="submit" class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">Spara ändringar</button>
            </div>
        </form>
    </div>
</div>
