<?php $csrf = \App\Core\Csrf::token(); $p = $project; ?>

<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">✏️ Redigera: <?= htmlspecialchars($p['name']) ?></h1>

    <form method="post" action="/projects/<?= $p['id'] ?>" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-6">
        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Projektnummer</label>
                <input type="text" value="<?= htmlspecialchars($p['project_number']) ?>" disabled
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm bg-gray-100 dark:bg-gray-600">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-1">Projektnamn *</label>
                <input type="text" name="name" value="<?= htmlspecialchars($p['name']) ?>" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Beskrivning</label>
            <textarea name="description" rows="3"
                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm"><?= htmlspecialchars($p['description'] ?? '') ?></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Kategori</label>
                <select name="category" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                    <?php foreach (['customer'=>'Kundprojekt','internal'=>'Internt','maintenance'=>'Underhåll','development'=>'Utveckling','other'=>'Övrigt'] as $k=>$v): ?>
                    <option value="<?= $k ?>" <?= $p['category'] === $k ? 'selected' : '' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Status</label>
                <select name="status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                    <?php foreach (['planning'=>'Planering','active'=>'Aktiv','on_hold'=>'Pausad','completed'=>'Avslutad','cancelled'=>'Avbruten'] as $k=>$v): ?>
                    <option value="<?= $k ?>" <?= $p['status'] === $k ? 'selected' : '' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Prioritet</label>
                <select name="priority" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                    <?php foreach (['low'=>'🟢 Låg','normal'=>'🔵 Normal','high'=>'🟠 Hög','urgent'=>'🔴 Brådskande'] as $k=>$v): ?>
                    <option value="<?= $k ?>" <?= $p['priority'] === $k ? 'selected' : '' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Kund</label>
                <select name="customer_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                    <option value="">— Ingen —</option>
                    <?php foreach ($customers as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($p['customer_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Avdelning</label>
                <select name="department_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                    <option value="">— Ingen —</option>
                    <?php foreach ($departments as $d): ?>
                    <option value="<?= $d['id'] ?>" <?= ($p['department_id'] ?? '') == $d['id'] ? 'selected' : '' ?>><?= htmlspecialchars($d['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Projektledare</label>
                <select name="manager_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                    <option value="">— Välj —</option>
                    <?php foreach ($users as $u): ?>
                    <option value="<?= $u['id'] ?>" <?= ($p['manager_id'] ?? '') == $u['id'] ? 'selected' : '' ?>><?= htmlspecialchars($u['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Startdatum</label>
                <input type="date" name="start_date" value="<?= $p['start_date'] ?? '' ?>"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Slutdatum</label>
                <input type="date" name="end_date" value="<?= $p['end_date'] ?? '' ?>"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Färdigt (%)</label>
                <input type="number" name="completion_pct" min="0" max="100" value="<?= $p['completion_pct'] ?? 0 ?>"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
            </div>
        </div>

        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
            <h3 class="text-sm font-semibold mb-3">💰 Budget</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Timbudget (h)</label>
                    <input type="number" name="budget_hours" step="0.5" value="<?= $p['budget_hours'] ?>"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Timpris (kr/h)</label>
                    <input type="number" name="hourly_rate" step="1" value="<?= $p['hourly_rate'] ?>"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Materialbudget (kr)</label>
                    <input type="number" name="budget_material" step="1" value="<?= $p['budget_material'] ?>"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Totalbudget (kr)</label>
                    <input type="number" name="budget_total" step="1" value="<?= $p['budget_total'] ?>"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                </div>
            </div>
        </div>

        <div class="flex justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
            <form method="post" action="/projects/<?= $p['id'] ?>/delete" onsubmit="return confirm('Vill du ta bort detta projekt?')">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>">
                <button type="submit" class="px-4 py-2 text-sm text-red-600 hover:text-red-800 border border-red-300 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20">Ta bort</button>
            </form>
            <div class="flex gap-3">
                <a href="/projects/<?= $p['id'] ?>" class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">Avbryt</a>
                <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg">Spara</button>
            </div>
        </div>
    </form>
</div>
