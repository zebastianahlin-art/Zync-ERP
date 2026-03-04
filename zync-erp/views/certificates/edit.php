<div class="mx-auto max-w-2xl">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Redigera certifikat</h1>
        <a href="/certificates" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600 transition-colors">&larr; Tillbaka</a>
    </div>

    <div class="rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-md">
        <form method="POST" action="/certificates/<?= $certificate['id'] ?>" class="space-y-5">
            <?= \App\Core\Csrf::field() ?>

            <div>
                <label for="employee_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Personal <span class="text-red-500">*</span></label>
                <select id="employee_id" name="employee_id" required
                        class="mt-1 block w-full rounded-lg border <?= isset($errors['employee_id']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">— Välj anställd —</option>
                    <?php foreach ($employees as $emp): ?>
                        <option value="<?= $emp['id'] ?>" <?= $certificate['employee_id'] == $emp['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['employee_id'])): ?>
                    <p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['employee_id'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="certificate_type_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Certifikattyp <span class="text-red-500">*</span></label>
                <select id="certificate_type_id" name="certificate_type_id" required
                        class="mt-1 block w-full rounded-lg border <?= isset($errors['certificate_type_id']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">— Välj typ —</option>
                    <?php foreach ($types as $type): ?>
                        <option value="<?= $type['id'] ?>" <?= $certificate['certificate_type_id'] == $type['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($type['name'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['certificate_type_id'])): ?>
                    <p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['certificate_type_id'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="issued_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Utfärdandedatum <span class="text-red-500">*</span></label>
                    <input id="issued_date" name="issued_date" type="date" required
                           value="<?= htmlspecialchars($certificate['issued_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border <?= isset($errors['issued_date']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <?php if (isset($errors['issued_date'])): ?>
                        <p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['issued_date'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="expiry_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Utgångsdatum</label>
                    <input id="expiry_date" name="expiry_date" type="date"
                           value="<?= htmlspecialchars($certificate['expiry_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Anteckningar</label>
                <textarea id="notes" name="notes" rows="3"
                          class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"><?= htmlspecialchars($certificate['notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <a href="/certificates" class="rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Avbryt</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">
                    Spara ändringar
                </button>
            </div>
        </form>
    </div>
</div>
