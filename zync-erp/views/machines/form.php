<?php
$typeOptions = ['site'=>'Anläggning','area'=>'Område','line'=>'Linje','machine'=>'Maskin','sub_machine'=>'Delmaskin','component'=>'Komponent'];
$statusOptions = ['operational'=>'I drift','degraded'=>'Nedsatt','down'=>'Stillastående','decommissioned'=>'Avvecklad'];
$critOptions = ['A'=>'A — Kritisk','B'=>'B — Normal','C'=>'C — Låg'];
?>
<div class="mx-auto max-w-2xl space-y-6">
    <div class="flex items-center gap-4">
        <a href="<?= $isEdit ? '/machines/' . $machine['id'] : '/machines' ?>" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"><?= $isEdit ? 'Redigera maskin' : 'Ny maskin / utrustning' ?></h1>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="rounded-lg bg-red-50 dark:bg-red-900/20 px-4 py-3">
            <?php foreach ($errors as $msg): ?>
                <p class="text-sm text-red-700 dark:text-red-400"><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= $isEdit ? '/machines/' . $machine['id'] : '/machines' ?>"
          class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 p-6 space-y-5">
        <?= \App\Core\Csrf::field() ?>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Namn *</label>
                <input type="text" name="name" id="name" required value="<?= htmlspecialchars($machine['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>
            <div>
                <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kod *</label>
                <input type="text" name="code" id="code" required maxlength="50" value="<?= htmlspecialchars($machine['code'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm font-mono uppercase dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Typ *</label>
                <select name="type" id="type" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                    <?php foreach ($typeOptions as $k => $v): ?>
                        <option value="<?= $k ?>" <?= ($machine['type'] ?? '') === $k ? 'selected' : '' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status *</label>
                <select name="status" id="status" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                    <?php foreach ($statusOptions as $k => $v): ?>
                        <option value="<?= $k ?>" <?= ($machine['status'] ?? '') === $k ? 'selected' : '' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="criticality" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kritikalitet *</label>
                <select name="criticality" id="criticality" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                    <?php foreach ($critOptions as $k => $v): ?>
                        <option value="<?= $k ?>" <?= ($machine['criticality'] ?? '') === $k ? 'selected' : '' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div>
            <label for="parent_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Överordnad</label>
            <select name="parent_id" id="parent_id" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                <option value="">— Toppnivå —</option>
                <?php foreach ($parents as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= (int)($machine['parent_id'] ?? 0) === (int)$p['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['code'] . ' — ' . $p['name'], ENT_QUOTES, 'UTF-8') ?> (<?= $typeOptions[$p['type']] ?? $p['type'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <label for="manufacturer" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tillverkare</label>
                <input type="text" name="manufacturer" id="manufacturer" value="<?= htmlspecialchars($machine['manufacturer'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label for="model" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Modell</label>
                <input type="text" name="model" id="model" value="<?= htmlspecialchars($machine['model'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label for="serial_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Serienummer</label>
                <input type="text" name="serial_number" id="serial_number" value="<?= htmlspecialchars($machine['serial_number'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm font-mono dark:bg-gray-700 dark:text-white">
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label for="year_installed" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Installationsår</label>
                <input type="number" name="year_installed" id="year_installed" min="1900" max="2099" value="<?= $machine['year_installed'] ?? '' ?>"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Plats</label>
                <input type="text" name="location" id="location" value="<?= htmlspecialchars($machine['location'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
            </div>
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Beskrivning</label>
            <textarea name="description" id="description" rows="3"
                      class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white"><?= htmlspecialchars($machine['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <?php if ($isEdit): ?>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" id="is_active" value="1" <?= ($machine['is_active'] ?? 1) ? 'checked' : '' ?>
                   class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
            <label for="is_active" class="text-sm text-gray-700 dark:text-gray-300">Aktiv</label>
        </div>
        <?php endif; ?>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="<?= $isEdit ? '/machines/' . $machine['id'] : '/machines' ?>" class="rounded-lg px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">Avbryt</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-6 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors"><?= $isEdit ? 'Uppdatera' : 'Skapa' ?></button>
        </div>
    </form>
</div>
