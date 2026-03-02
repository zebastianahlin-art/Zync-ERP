<?php
$r = $report;
$action  = $isNew ? '/maintenance/faults' : '/maintenance/faults/' . (int) $r['id'];
$heading = $isNew ? 'Ny felanmälan' : 'Redigera felanmälan';
$faultTypes = ['mechanical' => 'Mekanisk', 'electrical' => 'Elektrisk', 'hydraulic' => 'Hydraulik', 'pneumatic' => 'Pneumatik', 'software' => 'Mjukvara', 'other' => 'Övrigt'];
$priorities = ['low' => 'Låg', 'medium' => 'Medel', 'high' => 'Hög', 'critical' => 'Kritisk'];
$statuses   = ['reported' => 'Rapporterad', 'acknowledged' => 'Bekräftad', 'in_progress' => 'Pågår', 'resolved' => 'Åtgärdad', 'closed' => 'Stängd'];
?>
<div class="mx-auto max-w-3xl">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"><?= $heading ?></h1>
        <a href="/maintenance/faults" class="text-sm text-gray-500 hover:text-indigo-600">&larr; Tillbaka</a>
    </div>

    <form method="POST" action="<?= $action ?>" enctype="multipart/form-data" class="space-y-8">
        <?= \App\Core\Csrf::field() ?>

        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
            <h2 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">Felrapport</h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rapportnr</label>
                    <input name="report_number" type="text" readonly value="<?= htmlspecialchars($r['report_number'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm bg-gray-50 dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Utrustning <span class="text-red-500">*</span></label>
                    <select name="equipment_id" required class="mt-1 block w-full rounded-lg border <?= isset($errors['equipment_id']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                        <option value="">– Välj –</option>
                        <?php foreach ($equipment as $eq): ?><option value="<?= (int) $eq['id'] ?>" <?= (int) ($r['equipment_id'] ?? 0) === (int) $eq['id'] ? 'selected' : '' ?>>[<?= $eq['equipment_number'] ?>] <?= htmlspecialchars($eq['name'], ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['equipment_id'])): ?><p class="mt-1 text-xs text-red-600"><?= $errors['equipment_id'] ?></p><?php endif; ?>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rubrik <span class="text-red-500">*</span></label>
                    <input name="title" type="text" required value="<?= htmlspecialchars($r['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="mt-1 block w-full rounded-lg border <?= isset($errors['title']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                    <?php if (isset($errors['title'])): ?><p class="mt-1 text-xs text-red-600"><?= $errors['title'] ?></p><?php endif; ?>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Beskrivning <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="4" required class="mt-1 block w-full rounded-lg border <?= isset($errors['description']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> px-3 py-2 text-sm dark:bg-gray-700 dark:text-white"><?= htmlspecialchars($r['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    <?php if (isset($errors['description'])): ?><p class="mt-1 text-xs text-red-600"><?= $errors['description'] ?></p><?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Feltyp</label>
                    <select name="fault_type" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                        <?php foreach ($faultTypes as $val => $lbl): ?><option value="<?= $val ?>" <?= ($r['fault_type'] ?? '') === $val ? 'selected' : '' ?>><?= $lbl ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prioritet</label>
                    <select name="priority" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                        <?php foreach ($priorities as $val => $lbl): ?><option value="<?= $val ?>" <?= ($r['priority'] ?? '') === $val ? 'selected' : '' ?>><?= $lbl ?></option><?php endforeach; ?>
                    </select>
                </div>
                <?php if (!$isNew): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select name="status" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                        <?php foreach ($statuses as $val => $lbl): ?><option value="<?= $val ?>" <?= ($r['status'] ?? '') === $val ? 'selected' : '' ?>><?= $lbl ?></option><?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
            <h2 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">Bild / Foto</h2>
            <?php if (!$isNew && ($r['image_name'] ?? null)): ?>
                <div class="mb-3"><img src="<?= $r['image_path'] ?>" alt="Felbild" class="max-h-48 rounded-lg"></div>
            <?php endif; ?>
            <input type="file" name="fault_image" accept=".jpg,.jpeg,.png,.webp" class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-red-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-red-700 hover:file:bg-red-100 dark:file:bg-red-900/30 dark:file:text-red-400">
        </div>

        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Anteckningar</label>
            <textarea name="notes" rows="2" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white"><?= htmlspecialchars($r['notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="flex items-center justify-end space-x-3">
            <a href="/maintenance/faults" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">Avbryt</a>
            <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-red-700"><?= $isNew ? 'Skapa felanmälan' : 'Spara ändringar' ?></button>
        </div>
    </form>

    <?php if (!$isNew): ?>
    <div class="mt-6 rounded-2xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 p-6">
        <form method="POST" action="/maintenance/faults/<?= (int) $r['id'] ?>/delete" onsubmit="return confirm('Radera?');">
            <?= \App\Core\Csrf::field() ?>
            <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-red-700">Radera</button>
        </form>
    </div>
    <?php endif; ?>
</div>
