<?php $departments = $departments ?? []; $users = $users ?? []; $costCenters = $costCenters ?? []; ?>
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Nytt kostnadsställe</h1>
        <a href="/finance/cost-centers" class="text-sm text-gray-500 hover:text-indigo-600">← Tillbaka</a>
    </div>
    <form method="POST" action="/finance/cost-centers" class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-4">
        <?= \App\Core\Csrf::field() ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium mb-1">Kod *</label><input type="text" name="code" required maxlength="20" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></div>
            <div><label class="block text-sm font-medium mb-1">Namn *</label><input type="text" name="name" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></div>
            <div><label class="block text-sm font-medium mb-1">Överordnat KS</label><select name="parent_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"><option value="">Ingen</option><?php foreach ($costCenters as $cc): ?><option value="<?= $cc['id'] ?>"><?= htmlspecialchars($cc['code'] . ' ' . $cc['name']) ?></option><?php endforeach; ?></select></div>
            <div><label class="block text-sm font-medium mb-1">Avdelning</label><select name="department_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"><option value="">Ingen</option><?php foreach ($departments as $d): ?><option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option><?php endforeach; ?></select></div>
            <div><label class="block text-sm font-medium mb-1">Ansvarig</label><select name="responsible_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"><option value="">Ingen</option><?php foreach ($users as $u): ?><option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['full_name']) ?></option><?php endforeach; ?></select></div>
            <div><label class="block text-sm font-medium mb-1">Budget (kr)</label><input type="number" name="budget" step="0.01" value="0" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></div>
        </div>
        <div><label class="block text-sm font-medium mb-1">Beskrivning</label><textarea name="description" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></textarea></div>
        <div class="flex justify-end pt-4"><button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition">Skapa</button></div>
    </form>
</div>
