<?php
$e = $eq;
$action  = $isNew ? '/equipment' : '/equipment/' . (int) $e['id'];
$heading = $isNew ? 'Ny utrustning' : 'Redigera ' . htmlspecialchars($e['name'], ENT_QUOTES, 'UTF-8');
$typeOptions   = ['facility' => 'Anläggning', 'line' => 'Linje', 'machine' => 'Maskin', 'component' => 'Komponent', 'tool' => 'Verktyg'];
$statusOptions = ['operational' => 'I drift', 'maintenance' => 'Underhåll', 'breakdown' => 'Haveri', 'decommissioned' => 'Avvecklad'];
$critOptions   = ['A' => 'A – Kritisk', 'B' => 'B – Viktig', 'C' => 'C – Övrigt'];
?>
<div class="mx-auto max-w-3xl">

    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"><?= $heading ?></h1>
        <a href="/equipment" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors">&larr; Tillbaka</a>
    </div>

    <form method="POST" action="<?= $action ?>" class="space-y-8">
        <?= \App\Core\Csrf::field() ?>

        <!-- Grundinfo -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
            <h2 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">Grunduppgifter</h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="equipment_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Utrustningsnr <span class="text-red-500">*</span></label>
                    <input id="equipment_number" name="equipment_number" type="text" required
                           value="<?= htmlspecialchars($e['equipment_number'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border <?= isset($errors['equipment_number']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                    <?php if (isset($errors['equipment_number'])): ?><p class="mt-1 text-xs text-red-600"><?= $errors['equipment_number'] ?></p><?php endif; ?>
                </div>
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Namn <span class="text-red-500">*</span></label>
                    <input id="name" name="name" type="text" required
                           value="<?= htmlspecialchars($e['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border <?= isset($errors['name']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                    <?php if (isset($errors['name'])): ?><p class="mt-1 text-xs text-red-600"><?= $errors['name'] ?></p><?php endif; ?>
                </div>
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Typ</label>
                    <select id="type" name="type" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                        <?php foreach ($typeOptions as $val => $lbl): ?>
                            <option value="<?= $val ?>" <?= ($e['type'] ?? '') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="parent_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Överordnad utrustning</label>
                    <select id="parent_id" name="parent_id" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                        <option value="">– Ingen (toppnivå) –</option>
                        <?php foreach ($parents as $p): ?>
                            <option value="<?= (int) $p['id'] ?>" <?= (int) ($e['parent_id'] ?? 0) === (int) $p['id'] ? 'selected' : '' ?>>
                                [<?= $p['equipment_number'] ?>] <?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select id="status" name="status" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                        <?php foreach ($statusOptions as $val => $lbl): ?>
                            <option value="<?= $val ?>" <?= ($e['status'] ?? '') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="criticality" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kritikalitet</label>
                    <select id="criticality" name="criticality" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                        <?php foreach ($critOptions as $val => $lbl): ?>
                            <option value="<?= $val ?>" <?= ($e['criticality'] ?? '') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Avdelning</label>
                    <select id="department_id" name="department_id" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                        <option value="">– Ingen –</option>
                        <?php foreach ($departments as $dep): ?>
                            <option value="<?= (int) $dep['id'] ?>" <?= (int) ($e['department_id'] ?? 0) === (int) $dep['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($dep['name'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Plats</label>
                    <input id="location" name="location" type="text" value="<?= htmlspecialchars($e['location'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                </div>
            </div>
        </div>

        <!-- Teknisk info -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
            <h2 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">Teknisk information</h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="manufacturer" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tillverkare</label>
                    <input id="manufacturer" name="manufacturer" type="text" value="<?= htmlspecialchars($e['manufacturer'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label for="model" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Modell</label>
                    <input id="model" name="model" type="text" value="<?= htmlspecialchars($e['model'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label for="serial_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Serienummer</label>
                    <input id="serial_number" name="serial_number" type="text" value="<?= htmlspecialchars($e['serial_number'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label for="year_installed" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Installationsår</label>
                    <input id="year_installed" name="year_installed" type="number" min="1900" max="2099" value="<?= htmlspecialchars($e['year_installed'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                </div>
            </div>
        </div>

        <!-- Anteckningar -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Anteckningar</label>
            <textarea id="notes" name="notes" rows="3"
                      class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white"><?= htmlspecialchars($e['notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="flex items-center justify-end space-x-3">
            <a href="/equipment" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">Avbryt</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">
                <?= $isNew ? 'Skapa utrustning' : 'Spara ändringar' ?>
            </button>
        </div>
    </form>

    <?php if (!$isNew): ?>
    <div class="mt-6 rounded-2xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 p-6">
        <h3 class="text-sm font-semibold text-red-700 dark:text-red-400">Radera utrustning</h3>
        <p class="mt-1 text-xs text-red-600 dark:text-red-400">Denna åtgärd kan inte ångras.</p>
        <form method="POST" action="/equipment/<?= (int) $e['id'] ?>/delete" class="mt-3" onsubmit="return confirm('Är du säker?');">
            <?= \App\Core\Csrf::field() ?>
            <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-red-700 transition-colors">Radera</button>
        </form>
    </div>
    <?php endif; ?>
</div>
