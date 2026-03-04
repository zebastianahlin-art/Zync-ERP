<div class="max-w-lg space-y-6">
    <div class="flex items-center gap-3">
        <a href="/hr/attendance" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700">← Tillbaka</a>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Registrera frånvaro</h1>
    </div>
    <form method="post" action="/hr/attendance" class="space-y-5 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Anställd <span class="text-red-500">*</span></label>
            <select name="employee_id" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none">
                <option value="">– Välj anställd –</option>
                <?php foreach ($employees as $e): ?><option value="<?= $e['id'] ?>" <?= ($old['employee_id'] ?? '') == $e['id'] ? 'selected' : '' ?>><?= htmlspecialchars($e['full_name']) ?></option><?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Frånvarotyp</label>
            <select name="record_type" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none">
                <option value="vacation">Semester</option>
                <option value="sick_leave">Sjukdom</option>
                <option value="parental_leave">Föräldraledighet</option>
                <option value="comp_time">Kompledighet</option>
                <option value="other">Övrigt</option>
            </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Startdatum <span class="text-red-500">*</span></label>
                <input type="date" name="start_date" value="<?= htmlspecialchars($old['start_date'] ?? '') ?>" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Slutdatum <span class="text-red-500">*</span></label>
                <input type="date" name="end_date" value="<?= htmlspecialchars($old['end_date'] ?? '') ?>" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Anteckningar</label>
            <textarea name="notes" rows="2" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none"><?= htmlspecialchars($old['notes'] ?? '') ?></textarea>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Spara</button>
            <a href="/hr/attendance" class="rounded-lg border border-gray-300 dark:border-gray-600 px-5 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Avbryt</a>
        </div>
    </form>
</div>
