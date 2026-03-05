<div class="mx-auto max-w-3xl">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Ny anst&#228;lld</h1>
        <a href="/employees" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600 transition-colors">&larr; Tillbaka</a>
    </div>

    <div class="rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-md">
        <form method="POST" action="/employees" class="space-y-5">
            <?= \App\Core\Csrf::field() ?>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">F&#246;rnamn <span class="text-red-500">*</span></label>
                    <input id="first_name" name="first_name" type="text" required
                           value="<?= htmlspecialchars($old['first_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border <?= isset($errors['first_name']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <?php if (isset($errors['first_name'])): ?>
                        <p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['first_name'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Efternamn <span class="text-red-500">*</span></label>
                    <input id="last_name" name="last_name" type="text" required
                           value="<?= htmlspecialchars($old['last_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border <?= isset($errors['last_name']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <?php if (isset($errors['last_name'])): ?>
                        <p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['last_name'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <label for="employee_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Anst&#228;llningsnummer</label>
                <input id="employee_number" name="employee_number" type="text"
                       value="<?= htmlspecialchars($old['employee_number'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>

            <div>
                <label for="department_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Avdelning</label>
                <select id="department_id" name="department_id"
                        class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">&#8212; V&#228;lj avdelning &#8212;</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['id'] ?>" <?= ($old['department_id'] ?? '') == $d['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($d['name'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="position" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Befattning</label>
                <input id="position" name="position" type="text"
                       value="<?= htmlspecialchars($old['position'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Telefon</label>
                    <input id="phone" name="phone" type="text"
                           value="<?= htmlspecialchars($old['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">E-post</label>
                    <input id="email" name="email" type="email"
                           value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border <?= isset($errors['email']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <?php if (isset($errors['email'])): ?>
                        <p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="hire_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Anst&#228;llningsdatum</label>
                    <input id="hire_date" name="hire_date" type="date"
                           value="<?= htmlspecialchars($old['hire_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Slutdatum</label>
                    <input id="end_date" name="end_date" type="date"
                           value="<?= htmlspecialchars($old['end_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="employment_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Anst&#228;llningsform</label>
                    <select id="employment_type" name="employment_type"
                            class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <option value="">&#8212; V&#228;lj &#8212;</option>
                        <option value="full_time" <?= ($old['employment_type'] ?? '') === 'full_time' ? 'selected' : '' ?>>Heltid</option>
                        <option value="part_time" <?= ($old['employment_type'] ?? '') === 'part_time' ? 'selected' : '' ?>>Deltid</option>
                        <option value="consultant" <?= ($old['employment_type'] ?? '') === 'consultant' ? 'selected' : '' ?>>Konsult</option>
                        <option value="intern" <?= ($old['employment_type'] ?? '') === 'intern' ? 'selected' : '' ?>>Praktikant</option>
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select id="status" name="status"
                            class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <option value="active" <?= ($old['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Aktiv</option>
                        <option value="on_leave" <?= ($old['status'] ?? '') === 'on_leave' ? 'selected' : '' ?>>Tj&#228;nstledig</option>
                        <option value="terminated" <?= ($old['status'] ?? '') === 'terminated' ? 'selected' : '' ?>>Avslutad</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="salary" class="block text-sm font-medium text-gray-700 dark:text-gray-300">M&#229;nadsl&#246;n (kr)</label>
                <input id="salary" name="salary" type="number" step="0.01" min="0"
                       value="<?= htmlspecialchars($old['salary'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>

            <div>
                <label for="manager_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Chef</label>
                <select id="manager_id" name="manager_id"
                        class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">&#8212; Ingen chef &#8212;</option>
                    <?php foreach ($managers as $mgr): ?>
                        <option value="<?= $mgr['id'] ?>" <?= ($old['manager_id'] ?? '') == $mgr['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($mgr['last_name'] . ', ' . $mgr['first_name'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">N&#246;dkontakt (namn)</label>
                    <input id="emergency_contact_name" name="emergency_contact_name" type="text"
                           value="<?= htmlspecialchars($old['emergency_contact_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">N&#246;dkontakt (telefon)</label>
                    <input id="emergency_contact_phone" name="emergency_contact_phone" type="text"
                           value="<?= htmlspecialchars($old['emergency_contact_phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Anteckningar</label>
                <textarea id="notes" name="notes" rows="3"
                          class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"><?= htmlspecialchars($old['notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <a href="/employees" class="rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Avbryt</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">
                    Skapa anst&#228;lld
                </button>
            </div>
        </form>
    </div>
</div>
