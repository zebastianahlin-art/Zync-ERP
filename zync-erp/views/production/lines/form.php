<?php
$isEdit = !empty($line['id']);
$action = $isEdit ? '/production/lines/' . $line['id'] : '/production/lines';
$old    = $old ?? $line ?? [];
?>
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= $isEdit ? 'Redigera linje' : 'Ny produktionslinje' ?></h1>
        <a href="/production/lines" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600">← Tillbaka</a>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="rounded-lg bg-red-50 dark:bg-red-900/30 px-4 py-3">
        <ul class="text-sm text-red-700 dark:text-red-400 list-disc list-inside">
            <?php foreach ($errors as $msg): ?><li><?= htmlspecialchars($msg) ?></li><?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= $action ?>" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-5">
        <?= \App\Core\Csrf::field() ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Namn <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" required
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kod <span class="text-red-500">*</span></label>
                <input type="text" name="code" value="<?= htmlspecialchars($old['code'] ?? '') ?>" required
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Avdelning</label>
                <select name="department_id" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <option value="">— Välj —</option>
                    <?php foreach ($departments as $d): ?>
                    <option value="<?= $d['id'] ?>" <?= ($old['department_id'] ?? '') == $d['id'] ? 'selected' : '' ?>><?= htmlspecialchars($d['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kopplad maskin/linje</label>
                <select name="machine_id" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <option value="">— Ingen —</option>
                    <?php foreach ($machines as $m): ?>
                    <option value="<?= $m['id'] ?>" <?= ($old['machine_id'] ?? '') == $m['id'] ? 'selected' : '' ?>><?= htmlspecialchars($m['name']) ?> (<?= $m['code'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kapacitet per timme</label>
                <input type="number" step="0.01" name="capacity_per_hour" value="<?= htmlspecialchars($old['capacity_per_hour'] ?? '') ?>"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select name="status" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <option value="active" <?= ($old['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Aktiv</option>
                    <option value="maintenance" <?= ($old['status'] ?? '') === 'maintenance' ? 'selected' : '' ?>>Underhåll</option>
                    <option value="inactive" <?= ($old['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inaktiv</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sortering</label>
                <input type="number" name="sort_order" value="<?= htmlspecialchars($old['sort_order'] ?? '0') ?>"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Beskrivning</label>
            <textarea name="description" rows="3" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <a href="/production/lines" class="rounded-lg px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Avbryt</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-6 py-2 text-sm font-medium text-white hover:bg-indigo-700"><?= $isEdit ? 'Spara' : 'Skapa' ?></button>
        </div>
    </form>
</div>
