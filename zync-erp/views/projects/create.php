<?php $csrf = \App\Core\Csrf::token(); ?>

<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">📋 Skapa nytt projekt</h1>

    <form method="post" action="/projects" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-6">
        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>">

        <!-- Rad 1: Nummer + Namn -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Projektnummer</label>
                <input type="text" name="project_number" value="<?= htmlspecialchars($nextNumber) ?>" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-1">Projektnamn *</label>
                <input type="text" name="name" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
            </div>
        </div>

        <!-- Beskrivning -->
        <div>
            <label class="block text-sm font-medium mb-1">Beskrivning</label>
            <textarea name="description" rows="3"
                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm"></textarea>
        </div>

        <!-- Rad 2: Kategori, Status, Prioritet -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Kategori</label>
                <select name="category" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                    <option value="customer">Kundprojekt</option>
                    <option value="internal">Internt</option>
                    <option value="maintenance">Underhåll</option>
                    <option value="development">Utveckling</option>
                    <option value="other">Övrigt</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Status</label>
                <select name="status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                    <option value="planning">Planering</option>
                    <option value="active">Aktiv</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Prioritet</label>
                <select name="priority" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                    <option value="low">🟢 Låg</option>
                    <option value="normal" selected>🔵 Normal</option>
                    <option value="high">🟠 Hög</option>
                    <option value="urgent">🔴 Brådskande</option>
                </select>
            </div>
        </div>

        <!-- Rad 3: Kund, Avdelning, Projektledare -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Kund</label>
                <select name="customer_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                    <option value="">— Ingen —</option>
                    <?php foreach ($customers as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Avdelning</label>
                <select name="department_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                    <option value="">— Ingen —</option>
                    <?php foreach ($departments as $d): ?>
                    <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Projektledare</label>
                <select name="manager_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                    <option value="">— Välj —</option>
                    <?php foreach ($users as $u): ?>
                    <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Rad 4: Datum -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Startdatum</label>
                <input type="date" name="start_date" value="<?= date('Y-m-d') ?>"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Slutdatum</label>
                <input type="date" name="end_date"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
            </div>
        </div>

        <!-- Rad 5: Budget -->
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
            <h3 class="text-sm font-semibold mb-3">💰 Budget</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Timbudget (h)</label>
                    <input type="number" name="budget_hours" step="0.5" value="0"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Timpris (kr/h)</label>
                    <input type="number" name="hourly_rate" step="1" value="850"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Materialbudget (kr)</label>
                    <input type="number" name="budget_material" step="1" value="0"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Totalbudget (kr)</label>
                    <input type="number" name="budget_total" step="1" value="0"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                </div>
            </div>
        </div>

        <!-- Knappar -->
        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <a href="/projects" class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">Avbryt</a>
            <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg">Skapa projekt</button>
        </div>
    </form>
</div>
