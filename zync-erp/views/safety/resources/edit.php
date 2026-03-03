<div class="mx-auto max-w-2xl">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Redigera nödresurs</h1>
        <a href="/safety/resources/<?= (int) $resource['id'] ?>" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">&larr; Tillbaka</a>
    </div>
    <div class="rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-md">
        <form method="POST" action="/safety/resources/<?= (int) $resource['id'] ?>" class="space-y-5">
            <input type="hidden" name="_token" value="<?= \App\Core\Csrf::token() ?>">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Namn <span class="text-red-500">*</span></label>
                <input id="name" name="name" type="text" required value="<?= htmlspecialchars($resource['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="mt-1 block w-full rounded-lg border <?= isset($errors['name']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                <?php if (isset($errors['name'])): ?><p class="mt-1 text-xs text-red-600 dark:text-red-400"><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            </div>
            <div>
                <label for="resource_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Typ</label>
                <select id="resource_type" name="resource_type" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <?php foreach (['fire_extinguisher'=>'Brandsläckare','first_aid_kit'=>'Första hjälpen-låda','aed'=>'Hjärtstartare','eye_wash'=>'Ögonduschar','emergency_shower'=>'Nöddusch','evacuation_chair'=>'Utrymningsstol','fire_blanket'=>'Brandfilt','spill_kit'=>'Spill-kit','other'=>'Annat'] as $v=>$l): ?>
                        <option value="<?= $v ?>" <?= ($resource['resource_type'] ?? 'other') === $v ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Plats <span class="text-red-500">*</span></label>
                <input id="location" name="location" type="text" required value="<?= htmlspecialchars($resource['location'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="mt-1 block w-full rounded-lg border <?= isset($errors['location']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                <?php if (isset($errors['location'])): ?><p class="mt-1 text-xs text-red-600 dark:text-red-400"><?= htmlspecialchars($errors['location'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            </div>
            <div>
                <label for="location_details" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Platsdetaljer</label>
                <input id="location_details" name="location_details" type="text" value="<?= htmlspecialchars($resource['location_details'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Antal</label>
                    <input id="quantity" name="quantity" type="number" min="1" value="<?= htmlspecialchars((string) ($resource['quantity'] ?? '1'), ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="serial_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Serienummer</label>
                    <input id="serial_number" name="serial_number" type="text" value="<?= htmlspecialchars($resource['serial_number'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                <select id="status" name="status" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <?php foreach (['ok'=>'OK','needs_inspection'=>'Behöver kontroll','out_of_service'=>'Ur drift','missing'=>'Saknas'] as $v=>$l): ?>
                        <option value="<?= $v ?>" <?= ($resource['status'] ?? 'ok') === $v ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label for="last_inspection" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Senaste kontroll</label>
                    <input id="last_inspection" name="last_inspection" type="date" value="<?= htmlspecialchars($resource['last_inspection'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="next_inspection" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nästa kontroll</label>
                    <input id="next_inspection" name="next_inspection" type="date" value="<?= htmlspecialchars($resource['next_inspection'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
            </div>
            <div>
                <label for="inspection_interval" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kontrollintervall (dagar)</label>
                <input id="inspection_interval" name="inspection_interval" type="number" min="1" value="<?= htmlspecialchars((string) ($resource['inspection_interval'] ?? '365'), ENT_QUOTES, 'UTF-8') ?>"
                       class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Anteckningar</label>
                <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"><?= htmlspecialchars($resource['notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <div class="flex items-center justify-end space-x-3 pt-2">
                <a href="/safety/resources/<?= (int) $resource['id'] ?>" class="rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Avbryt</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">Spara ändringar</button>
            </div>
        </form>
    </div>
</div>
