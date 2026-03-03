<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="/maintenance/inspections/<?= (int)($inspection['id'] ?? 0) ?>" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Redigera besiktning</h1>
    </div>

    <?php if (!empty($success)): ?>
        <div class="rounded-lg bg-green-50 dark:bg-green-900/30 p-4 text-green-800 dark:text-green-200"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="rounded-lg bg-red-50 dark:bg-red-900/30 p-4 text-red-800 dark:text-red-200"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <form method="POST" action="/maintenance/inspections/<?= (int)($inspection['id'] ?? 0) ?>/edit" class="space-y-5">
            <?= \App\Core\Csrf::field() ?>

            <!-- Besiktningsnummer -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Besiktningsnummer</label>
                <input type="text" name="inspection_number"
                    value="<?= htmlspecialchars($inspection['inspection_number'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Utrustning och Maskin -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Utrustning <span class="text-gray-400 font-normal">(valfritt)</span></label>
                    <select name="equipment_id"
                        class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— Ingen utrustning —</option>
                        <?php foreach ($equipment ?? [] as $eq): ?>
                        <option value="<?= (int)$eq['id'] ?>"
                            <?= (string)($inspection['equipment_id'] ?? '') === (string)$eq['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($eq['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Maskin <span class="text-gray-400 font-normal">(valfritt)</span></label>
                    <select name="machine_id"
                        class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— Ingen maskin —</option>
                        <?php foreach ($machines ?? [] as $machine): ?>
                        <option value="<?= (int)$machine['id'] ?>"
                            <?= (string)($inspection['machine_id'] ?? '') === (string)$machine['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($machine['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </option>
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
                        <?php foreach (['safety' => 'Säkerhet', 'regulatory' => 'Regulatorisk', 'routine' => 'Rutinmässig', 'preventive' => 'Förebyggande'] as $val => $label): ?>
                        <option value="<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8') ?>"
                            <?= ($inspection['inspection_type'] ?? '') === $val ? 'selected' : '' ?>>
                            <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Planerat datum <span class="text-red-500">*</span></label>
                    <input type="date" name="scheduled_date" required
                        value="<?= htmlspecialchars(!empty($inspection['scheduled_date']) ? date('Y-m-d', strtotime($inspection['scheduled_date'])) : '', ENT_QUOTES, 'UTF-8') ?>"
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
                        <option value="<?= (int)$user['id'] ?>"
                            <?= (string)($inspection['inspector_id'] ?? '') === (string)$user['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Avdelning</label>
                    <select name="department_id"
                        class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— Välj avdelning —</option>
                        <?php foreach ($departments ?? [] as $dept): ?>
                        <option value="<?= (int)$dept['id'] ?>"
                            <?= (string)($inspection['department_id'] ?? '') === (string)$dept['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($dept['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Anteckningar -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Anteckningar</label>
                <textarea name="notes" rows="4"
                    class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-y"><?= htmlspecialchars($inspection['notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <!-- Knappar -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="/maintenance/inspections/<?= (int)($inspection['id'] ?? 0) ?>" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition">Avbryt</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
                    Spara ändringar
                </button>
            </div>
        </form>
    </div>
</div>
