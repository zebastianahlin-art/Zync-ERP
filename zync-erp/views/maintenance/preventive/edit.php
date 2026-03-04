<?php
$checklistItems = [];
if (!empty($schedule['checklist'])) {
    $raw = $schedule['checklist'];
    if (is_string($raw)) {
        $decoded = json_decode($raw, true);
        $checklistItems = is_array($decoded) ? $decoded : [];
    } elseif (is_array($raw)) {
        $checklistItems = $raw;
    }
}
?>
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
        <a href="/maintenance/preventive/<?= $schedule['id'] ?>" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Redigera FU-schema</h1>
    </div>

    <form method="POST" action="/maintenance/preventive/<?= $schedule['id'] ?>" class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-5">
        <?= \App\Core\Csrf::field() ?>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Titel *</label>
            <input type="text" name="title" required value="<?= htmlspecialchars($schedule['title'], ENT_QUOTES, 'UTF-8') ?>"
                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Beskrivning</label>
            <textarea name="description" rows="3"
                      class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none"><?= htmlspecialchars($schedule['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Maskin</label>
                <select name="machine_id" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm">
                    <option value="">— Välj maskin —</option>
                    <?php foreach ($machines as $m): ?>
                    <option value="<?= $m['id'] ?>" <?= ($schedule['machine_id'] == $m['id']) ? 'selected' : '' ?>><?= htmlspecialchars($m['name'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Utrustning</label>
                <select name="equipment_id" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm">
                    <option value="">— Välj utrustning —</option>
                    <?php foreach ($equipment as $e): ?>
                    <option value="<?= $e['id'] ?>" <?= ($schedule['equipment_id'] == $e['id']) ? 'selected' : '' ?>><?= htmlspecialchars($e['name'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Intervalltyp</label>
                <select name="interval_type" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm">
                    <?php foreach (['daily' => 'Daglig', 'weekly' => 'Veckovis', 'monthly' => 'Månadsvis', 'yearly' => 'Årsvis', 'hours' => 'Timmar'] as $val => $lbl): ?>
                    <option value="<?= $val ?>" <?= $schedule['interval_type'] === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Intervallvärde</label>
                <input type="number" name="interval_value" min="1" value="<?= (int) $schedule['interval_value'] ?>"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Senast utfört</label>
                <input type="datetime-local" name="last_performed_at"
                       value="<?= $schedule['last_performed_at'] ? htmlspecialchars(date('Y-m-d\TH:i', strtotime($schedule['last_performed_at'])), ENT_QUOTES, 'UTF-8') : '' ?>"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nästa tillfälle</label>
                <input type="datetime-local" name="next_due_at"
                       value="<?= $schedule['next_due_at'] ? htmlspecialchars(date('Y-m-d\TH:i', strtotime($schedule['next_due_at'])), ENT_QUOTES, 'UTF-8') : '' ?>"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prioritet</label>
                <select name="priority" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm">
                    <?php foreach (['low' => 'Låg', 'normal' => 'Normal', 'high' => 'Hög', 'critical' => 'Kritisk'] as $val => $lbl): ?>
                    <option value="<?= $val ?>" <?= $schedule['priority'] === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select name="status" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm">
                    <?php foreach (['active' => 'Aktiv', 'paused' => 'Pausad', 'completed' => 'Klar'] as $val => $lbl): ?>
                    <option value="<?= $val ?>" <?= $schedule['status'] === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tilldelad till</label>
            <select name="assigned_to" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm">
                <option value="">— Välj person —</option>
                <?php foreach ($users as $u): ?>
                <option value="<?= $u['id'] ?>" <?= ($schedule['assigned_to'] == $u['id']) ? 'selected' : '' ?>><?= htmlspecialchars($u['full_name'], ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Checklista (en punkt per rad)</label>
            <textarea name="checklist_items" rows="5"
                      class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none font-mono"><?= htmlspecialchars(implode("\n", $checklistItems), ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="flex justify-between items-center pt-2">
            <form method="POST" action="/maintenance/preventive/<?= $schedule['id'] ?>/delete" onsubmit="return confirm('Ta bort detta schema?')">
                <?= \App\Core\Csrf::field() ?>
                <button type="submit" class="px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition">Ta bort</button>
            </form>
            <div class="flex gap-3">
                <a href="/maintenance/preventive/<?= $schedule['id'] ?>" class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition">Avbryt</a>
                <button type="submit" class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">Spara ändringar</button>
            </div>
        </div>
    </form>
</div>
