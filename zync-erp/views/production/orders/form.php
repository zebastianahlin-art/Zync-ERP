<?php
$isEdit = !empty($order['id']);
$action = $isEdit ? '/production/orders/' . $order['id'] : '/production/orders';
$old    = $old ?? $order ?? [];
?>
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= $isEdit ? 'Redigera order' : 'Ny produktionsorder' ?></h1>
        <a href="<?= $isEdit ? '/production/orders/' . $order['id'] : '/production/orders' ?>" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600">← Tillbaka</a>
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

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ordernummer <span class="text-red-500">*</span></label>
                <input type="text" name="order_number" value="<?= htmlspecialchars($old['order_number'] ?? $nextNumber ?? '') ?>" <?= $isEdit ? 'readonly' : 'required' ?>
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 <?= $isEdit ? 'bg-gray-50 dark:bg-gray-600' : '' ?>">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prioritet</label>
                <select name="priority" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <option value="low" <?= ($old['priority'] ?? '') === 'low' ? 'selected' : '' ?>>Låg</option>
                    <option value="normal" <?= ($old['priority'] ?? 'normal') === 'normal' ? 'selected' : '' ?>>Normal</option>
                    <option value="high" <?= ($old['priority'] ?? '') === 'high' ? 'selected' : '' ?>>Hög</option>
                    <option value="urgent" <?= ($old['priority'] ?? '') === 'urgent' ? 'selected' : '' ?>>Brådskande</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select name="status" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <option value="draft" <?= ($old['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Utkast</option>
                    <option value="planned" <?= ($old['status'] ?? '') === 'planned' ? 'selected' : '' ?>>Planerad</option>
                    <option value="released" <?= ($old['status'] ?? '') === 'released' ? 'selected' : '' ?>>Frisläppt</option>
                    <option value="in_progress" <?= ($old['status'] ?? '') === 'in_progress' ? 'selected' : '' ?>>Pågår</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Artikel att producera</label>
                <select name="article_id" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <option value="">— Välj artikel —</option>
                    <?php foreach ($articles as $a): ?>
                    <option value="<?= $a['id'] ?>" <?= ($old['article_id'] ?? '') == $a['id'] ? 'selected' : '' ?>><?= htmlspecialchars($a['article_number'] . ' — ' . $a['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Produktionslinje</label>
                <select name="line_id" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <option value="">— Välj linje —</option>
                    <?php foreach ($lines as $l): ?>
                    <option value="<?= $l['id'] ?>" <?= ($old['line_id'] ?? '') == $l['id'] ? 'selected' : '' ?>><?= htmlspecialchars($l['name']) ?> (<?= $l['code'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Planerat antal <span class="text-red-500">*</span></label>
                <input type="number" step="0.001" name="quantity_planned" value="<?= htmlspecialchars($old['quantity_planned'] ?? '') ?>" required
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Enhet</label>
                <input type="text" name="unit" value="<?= htmlspecialchars($old['unit'] ?? 'st') ?>"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ansvarig</label>
                <select name="assigned_to" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <option value="">— Välj —</option>
                    <?php foreach ($employees as $e): ?>
                    <option value="<?= $e['id'] ?>" <?= ($old['assigned_to'] ?? '') == $e['id'] ? 'selected' : '' ?>><?= htmlspecialchars($e['first_name'] . ' ' . $e['last_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Planerad start</label>
                <input type="datetime-local" name="planned_start" value="<?= $old['planned_start'] ? date('Y-m-d\TH:i', strtotime($old['planned_start'])) : '' ?>"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Planerat slut</label>
                <input type="datetime-local" name="planned_end" value="<?= $old['planned_end'] ? date('Y-m-d\TH:i', strtotime($old['planned_end'])) : '' ?>"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Beskrivning</label>
            <textarea name="description" rows="2" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Anteckningar</label>
            <textarea name="notes" rows="2" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500"><?= htmlspecialchars($old['notes'] ?? '') ?></textarea>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <a href="<?= $isEdit ? '/production/orders/' . $order['id'] : '/production/orders' ?>" class="rounded-lg px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Avbryt</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-6 py-2 text-sm font-medium text-white hover:bg-indigo-700"><?= $isEdit ? 'Spara' : 'Skapa order' ?></button>
        </div>
    </form>
</div>
