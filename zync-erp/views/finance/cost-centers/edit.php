<?php $costCenter = $costCenter ?? []; $departments = $departments ?? []; $users = $users ?? []; $costCenters = $costCenters ?? []; ?>
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Redigera KS <?= htmlspecialchars($costCenter['code'] ?? '') ?></h1>
        <a href="/finance/cost-centers" class="text-sm text-gray-500 hover:text-indigo-600">← Tillbaka</a>
    </div>
    <form method="POST" action="/finance/cost-centers/<?= $costCenter['id'] ?>" class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-4">
        <?= \App\Core\Csrf::field() ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium mb-1">Kod *</label><input type="text" name="code" value="<?= htmlspecialchars($costCenter['code'] ?? '') ?>" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></div>
            <div><label class="block text-sm font-medium mb-1">Namn *</label><input type="text" name="name" value="<?= htmlspecialchars($costCenter['name'] ?? '') ?>" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></div>
            <div><label class="block text-sm font-medium mb-1">Överordnat KS</label><select name="parent_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"><option value="">Ingen</option><?php foreach ($costCenters as $cc): ?><?php if ($cc['id'] != $costCenter['id']): ?><option value="<?= $cc['id'] ?>" <?= ($cc['id'] ?? '') == ($costCenter['parent_id'] ?? '') ? 'selected' : '' ?>><?= htmlspecialchars($cc['code'] . ' ' . $cc['name']) ?></option><?php endif; ?><?php endforeach; ?></select></div>
            <div><label class="block text-sm font-medium mb-1">Avdelning</label><select name="department_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"><option value="">Ingen</option><?php foreach ($departments as $d): ?><option value="<?= $d['id'] ?>" <?= ($d['id'] ?? '') == ($costCenter['department_id'] ?? '') ? 'selected' : '' ?>><?= htmlspecialchars($d['name']) ?></option><?php endforeach; ?></select></div>
            <div><label class="block text-sm font-medium mb-1">Ansvarig</label><select name="responsible_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"><option value="">Ingen</option><?php foreach ($users as $u): ?><option value="<?= $u['id'] ?>" <?= ($u['id'] ?? '') == ($costCenter['responsible_id'] ?? '') ? 'selected' : '' ?>><?= htmlspecialchars($u['full_name']) ?></option><?php endforeach; ?></select></div>
            <div><label class="block text-sm font-medium mb-1">Budget (kr)</label><input type="number" name="budget" step="0.01" value="<?= $costCenter['budget'] ?? 0 ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></div>
            <div class="flex items-center gap-2 pt-6"><input type="checkbox" name="is_active" value="1" <?= ($costCenter['is_active'] ?? 1) ? 'checked' : '' ?> class="rounded border-gray-300"><label class="text-sm">Aktiv</label></div>
        </div>
        <div><label class="block text-sm font-medium mb-1">Beskrivning</label><textarea name="description" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"><?= htmlspecialchars($costCenter['description'] ?? '') ?></textarea></div>
        <div class="flex justify-end pt-4"><button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition">Spara</button></div>
    </form>
</div>
