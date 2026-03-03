<div class="mx-auto max-w-2xl">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Ny riskbedömning</h1>
        <a href="/safety/risks" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">&larr; Tillbaka</a>
    </div>
    <div class="rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-md">
        <form method="POST" action="/safety/risks" class="space-y-5">
            <input type="hidden" name="_token" value="<?= \App\Core\Csrf::token() ?>">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Titel <span class="text-red-500">*</span></label>
                <input id="title" name="title" type="text" required value="<?= htmlspecialchars($old['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="mt-1 block w-full rounded-lg border <?= isset($errors['title']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                <?php if (isset($errors['title'])): ?><p class="mt-1 text-xs text-red-600 dark:text-red-400"><?= htmlspecialchars($errors['title'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            </div>
            <div>
                <label for="risk_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Risktyp</label>
                <select id="risk_type" name="risk_type" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <?php foreach (['physical'=>'Fysisk','chemical'=>'Kemisk','biological'=>'Biologisk','ergonomic'=>'Ergonomisk','psychosocial'=>'Psykosocial','electrical'=>'Elektrisk','fire'=>'Brand','fall'=>'Fall','other'=>'Annat'] as $v=>$l): ?>
                        <option value="<?= $v ?>" <?= ($old['risk_type'] ?? 'other') === $v ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label for="probability" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sannolikhet (1–5) <span class="text-red-500">*</span></label>
                    <input id="probability" name="probability" type="number" min="1" max="5" required value="<?= htmlspecialchars($old['probability'] ?? '1', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border <?= isset($errors['probability']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <?php if (isset($errors['probability'])): ?><p class="mt-1 text-xs text-red-600 dark:text-red-400"><?= htmlspecialchars($errors['probability'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                </div>
                <div>
                    <label for="consequence" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Konsekvens (1–5) <span class="text-red-500">*</span></label>
                    <input id="consequence" name="consequence" type="number" min="1" max="5" required value="<?= htmlspecialchars($old['consequence'] ?? '1', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border <?= isset($errors['consequence']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <?php if (isset($errors['consequence'])): ?><p class="mt-1 text-xs text-red-600 dark:text-red-400"><?= htmlspecialchars($errors['consequence'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                </div>
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Beskrivning</label>
                <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"><?= htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <div>
                <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Plats</label>
                <input id="location" name="location" type="text" value="<?= htmlspecialchars($old['location'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>
            <div>
                <label for="mitigation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Åtgärder/Mitigering</label>
                <textarea id="mitigation" name="mitigation" rows="3" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"><?= htmlspecialchars($old['mitigation'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                <select id="status" name="status" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <?php foreach (['draft'=>'Utkast','active'=>'Aktiv','under_review'=>'Under granskning','closed'=>'Stängd','archived'=>'Arkiverad'] as $v=>$l): ?>
                        <option value="<?= $v ?>" <?= ($old['status'] ?? 'draft') === $v ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="valid_until" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Giltig till</label>
                <input id="valid_until" name="valid_until" type="date" value="<?= htmlspecialchars($old['valid_until'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>
            <div class="flex items-center justify-end space-x-3 pt-2">
                <a href="/safety/risks" class="rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Avbryt</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">Skapa riskbedömning</button>
            </div>
        </form>
    </div>
</div>
