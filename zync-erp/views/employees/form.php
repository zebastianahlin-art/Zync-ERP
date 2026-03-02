<?php $e = $employee; $err = $errors; ?>
<div class="mx-auto max-w-3xl">

    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
            <?= $isEdit ? 'Redigera anställd' : 'Ny anställd' ?>
        </h1>
        <a href="/employees" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors">&larr; Tillbaka</a>
    </div>

    <div class="rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-md">
        <form method="POST" action="<?= $isEdit ? '/employees/' . (int) $e['id'] : '/employees' ?>" class="space-y-6">
            <?= \App\Core\Csrf::field() ?>

            <!-- Grundinfo -->
            <fieldset>
                <legend class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Grundinformation</legend>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label for="employee_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Anst.nr <span class="text-red-500">*</span></label>
                        <input id="employee_number" name="employee_number" type="text" required
                               value="<?= htmlspecialchars($e['employee_number'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               class="mt-1 block w-full rounded-lg border <?= isset($err['employee_number']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> px-3 py-2 text-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 uppercase">
                        <?php if (isset($err['employee_number'])): ?><p class="mt-1 text-xs text-red-600"><?= $err['employee_number'] ?></p><?php endif; ?>
                    </div>
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Förnamn <span class="text-red-500">*</span></label>
                        <input id="first_name" name="first_name" type="text" required
                               value="<?= htmlspecialchars($e['first_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               class="mt-1 block w-full rounded-lg border <?= isset($err['first_name']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> px-3 py-2 text-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        <?php if (isset($err['first_name'])): ?><p class="mt-1 text-xs text-red-600"><?= $err['first_name'] ?></p><?php endif; ?>
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Efternamn <span class="text-red-500">*</span></label>
                        <input id="last_name" name="last_name" type="text" required
                               value="<?= htmlspecialchars($e['last_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               class="mt-1 block w-full rounded-lg border <?= isset($err['last_name']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> px-3 py-2 text-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        <?php if (isset($err['last_name'])): ?><p class="mt-1 text-xs text-red-600"><?= $err['last_name'] ?></p><?php endif; ?>
                    </div>
                </div>
            </fieldset>

            <!-- Kontakt -->
            <fieldset>
                <legend class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Kontakt</legend>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">E-post</label>
                        <input id="email" name="email" type="email" value="<?= htmlspecialchars($e['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Telefon</label>
                        <input id="phone" name="phone" type="tel" value="<?= htmlspecialchars($e['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    </div>
                </div>
            </fieldset>

            <!-- Organisation -->
            <fieldset>
                <legend class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Organisation</legend>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Titel</label>
                        <input id="title" name="title" type="text" value="<?= htmlspecialchars($e['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="department_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Avdelning</label>
                        <select id="department_id" name="department_id" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                            <option value="">– Ingen –</option>
                            <?php foreach ($departments as $d): ?>
                                <option value="<?= (int) $d['id'] ?>" <?= (string) ($e['department_id'] ?? '') === (string) $d['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($d['name'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="manager_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Närmaste chef</label>
                        <select id="manager_id" name="manager_id" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                            <option value="">– Ingen –</option>
                            <?php foreach ($managers as $m): ?>
                                <option value="<?= (int) $m['id'] ?>" <?= (string) ($e['manager_id'] ?? '') === (string) $m['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($m['name'] . ' (' . $m['employee_number'] . ')', ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Koppla systemanvändare</label>
                        <select id="user_id" name="user_id" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                            <option value="">– Ingen –</option>
                            <?php foreach ($users as $u): ?>
                                <option value="<?= (int) $u['id'] ?>" <?= (string) ($e['user_id'] ?? '') === (string) $u['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars(($u['full_name'] ?? $u['username']), ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </fieldset>

            <!-- Anställning -->
            <fieldset>
                <legend class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Anställning</legend>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label for="employment_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Anställningsform</label>
                        <select id="employment_type" name="employment_type" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                            <option value="full_time"  <?= ($e['employment_type'] ?? '') === 'full_time' ? 'selected' : '' ?>>Heltid</option>
                            <option value="part_time"  <?= ($e['employment_type'] ?? '') === 'part_time' ? 'selected' : '' ?>>Deltid</option>
                            <option value="consultant" <?= ($e['employment_type'] ?? '') === 'consultant' ? 'selected' : '' ?>>Konsult</option>
                            <option value="intern"     <?= ($e['employment_type'] ?? '') === 'intern' ? 'selected' : '' ?>>Praktikant</option>
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                        <select id="status" name="status" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                            <option value="active"     <?= ($e['status'] ?? '') === 'active' ? 'selected' : '' ?>>Aktiv</option>
                            <option value="on_leave"   <?= ($e['status'] ?? '') === 'on_leave' ? 'selected' : '' ?>>Tjänstledig</option>
                            <option value="terminated" <?= ($e['status'] ?? '') === 'terminated' ? 'selected' : '' ?>>Avslutad</option>
                        </select>
                    </div>
                    <div>
                        <label for="salary" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lön (SEK/mån)</label>
                        <input id="salary" name="salary" type="number" step="0.01" value="<?= htmlspecialchars($e['salary'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="hire_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Startdatum</label>
                        <input id="hire_date" name="hire_date" type="date" value="<?= htmlspecialchars($e['hire_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="birth_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Födelsedatum</label>
                        <input id="birth_date" name="birth_date" type="date" value="<?= htmlspecialchars($e['birth_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    </div>
                </div>
            </fieldset>

            <!-- Adress -->
            <fieldset>
                <legend class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Adress</legend>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="sm:col-span-3">
                        <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gatuadress</label>
                        <input id="address" name="address" type="text" value="<?= htmlspecialchars($e['address'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Postnummer</label>
                        <input id="postal_code" name="postal_code" type="text" value="<?= htmlspecialchars($e['postal_code'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ort</label>
                        <input id="city" name="city" type="text" value="<?= htmlspecialchars($e['city'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Land</label>
                        <input id="country" name="country" type="text" value="<?= htmlspecialchars($e['country'] ?? 'Sverige', ENT_QUOTES, 'UTF-8') ?>"
                               class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    </div>
                </div>
            </fieldset>

            <!-- Anteckningar -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Anteckningar</label>
                <textarea id="notes" name="notes" rows="3"
                          class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"><?= htmlspecialchars($e['notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <!-- Knappar -->
            <div class="flex items-center justify-end space-x-3 pt-2">
                <a href="/employees" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">Avbryt</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">
                    <?= $isEdit ? 'Spara ändringar' : 'Skapa anställd' ?>
                </button>
            </div>
        </form>
    </div>

    <?php if ($isEdit): ?>
    <!-- Radera -->
    <div class="mt-6 rounded-2xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 p-6">
        <h3 class="text-sm font-semibold text-red-700 dark:text-red-400">Radera anställd</h3>
        <p class="mt-1 text-xs text-red-600 dark:text-red-400">Denna åtgärd kan inte ångras.</p>
        <form method="POST" action="/employees/<?= (int) $e['id'] ?>/delete" class="mt-3"
              onsubmit="return confirm('Är du säker?');">
            <?= \App\Core\Csrf::field() ?>
            <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-red-700 transition-colors">Radera</button>
        </form>
    </div>
    <?php endif; ?>
</div>
