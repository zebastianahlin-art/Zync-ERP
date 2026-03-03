<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
        <a href="/maintenance/faults/<?= $fault['id'] ?>" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Redigera felanmälan</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <form method="POST" action="/maintenance/faults/<?= $fault['id'] ?>" class="space-y-4">
            <?= \App\Core\Csrf::field() ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Titel *</label>
                    <input type="text" name="title" required value="<?= htmlspecialchars($fault['title'], ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Maskin</label>
                    <select name="machine_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="">—</option>
                        <?php foreach ($machines as $m): ?>
                        <option value="<?= $m['id'] ?>"<?= ($fault['machine_id']==$m['id'])?' selected':'' ?>><?= htmlspecialchars($m['machine_number'] . ' — ' . $m['name'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Utrustning</label>
                    <select name="equipment_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="">—</option>
                        <?php foreach ($equipment as $e): ?>
                        <option value="<?= $e['id'] ?>"<?= ($fault['equipment_id']==$e['id'])?' selected':'' ?>><?= htmlspecialchars($e['equipment_number'] . ' — ' . $e['name'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Feltyp</label>
                    <select name="fault_type" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <?php foreach (['mechanical'=>'Mekanisk','electrical'=>'Elektrisk','hydraulic'=>'Hydraulik','pneumatic'=>'Pneumatik','software'=>'Mjukvara','structural'=>'Strukturell','safety'=>'Säkerhet','other'=>'Övrigt'] as $v=>$l): ?>
                        <option value="<?= $v ?>"<?= $fault['fault_type']===$v?' selected':'' ?>><?= $l ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prioritet</label>
                    <select name="priority" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <?php foreach (['low'=>'Låg','normal'=>'Normal','high'=>'Hög','urgent'=>'Brådskande','critical'=>'Kritisk'] as $v=>$l): ?>
                        <option value="<?= $v ?>"<?= $fault['priority']===$v?' selected':'' ?>><?= $l ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Avdelning</label>
                    <select name="department_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="">—</option>
                        <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['id'] ?>"<?= ($fault['department_id']==$d['id'])?' selected':'' ?>><?= htmlspecialchars($d['name'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Plats</label>
                    <input type="text" name="location" value="<?= htmlspecialchars($fault['location'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Beskrivning</label>
                    <textarea name="description" rows="4" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"><?= htmlspecialchars($fault['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <a href="/maintenance/faults/<?= $fault['id'] ?>" class="px-4 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">Avbryt</a>
                <button type="submit" class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">Spara ändringar</button>
            </div>
        </form>
    </div>
</div>
