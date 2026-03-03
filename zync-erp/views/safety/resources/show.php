<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100"><?= htmlspecialchars($resource['name'], ENT_QUOTES, 'UTF-8') ?></h1>
        <a href="/safety/resources" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">&larr; Tillbaka</a>
    </div>

    <div class="rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-md space-y-4">
        <?php
        $status = $resource['status'] ?? 'ok';
        $statusClass = match($status) {
            'ok'               => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400',
            'needs_inspection' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400',
            'out_of_service'   => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400',
            default            => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
        };
        ?>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><span class="font-medium text-gray-600 dark:text-gray-400">Typ:</span> <span class="text-gray-900 dark:text-gray-100"><?= htmlspecialchars($resource['resource_type'] ?? '–', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="font-medium text-gray-600 dark:text-gray-400">Status:</span> <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium <?= $statusClass ?>"><?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="font-medium text-gray-600 dark:text-gray-400">Plats:</span> <span class="text-gray-900 dark:text-gray-100"><?= htmlspecialchars($resource['location'] ?? '–', ENT_QUOTES, 'UTF-8') ?></span></div>
            <?php if (!empty($resource['location_details'])): ?>
            <div><span class="font-medium text-gray-600 dark:text-gray-400">Platsdetaljer:</span> <span class="text-gray-900 dark:text-gray-100"><?= htmlspecialchars($resource['location_details'], ENT_QUOTES, 'UTF-8') ?></span></div>
            <?php endif; ?>
            <div><span class="font-medium text-gray-600 dark:text-gray-400">Antal:</span> <span class="text-gray-900 dark:text-gray-100"><?= htmlspecialchars((string) ($resource['quantity'] ?? '1'), ENT_QUOTES, 'UTF-8') ?></span></div>
            <?php if (!empty($resource['serial_number'])): ?>
            <div><span class="font-medium text-gray-600 dark:text-gray-400">Serienummer:</span> <span class="text-gray-900 dark:text-gray-100"><?= htmlspecialchars($resource['serial_number'], ENT_QUOTES, 'UTF-8') ?></span></div>
            <?php endif; ?>
            <div><span class="font-medium text-gray-600 dark:text-gray-400">Senaste kontroll:</span> <span class="text-gray-900 dark:text-gray-100"><?= htmlspecialchars($resource['last_inspection'] ?? '–', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="font-medium text-gray-600 dark:text-gray-400">Nästa kontroll:</span> <span class="text-gray-900 dark:text-gray-100"><?= htmlspecialchars($resource['next_inspection'] ?? '–', ENT_QUOTES, 'UTF-8') ?></span></div>
        </div>
        <?php if (!empty($resource['notes'])): ?>
            <div><p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Anteckningar</p><p class="text-sm text-gray-900 dark:text-gray-100"><?= nl2br(htmlspecialchars($resource['notes'], ENT_QUOTES, 'UTF-8')) ?></p></div>
        <?php endif; ?>
        <div class="flex items-center space-x-3 pt-2">
            <a href="/safety/resources/<?= (int) $resource['id'] ?>/edit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">Redigera</a>
            <form method="POST" action="/safety/resources/<?= (int) $resource['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort denna resurs?')">
                <input type="hidden" name="_token" value="<?= \App\Core\Csrf::token() ?>">
                <button type="submit" class="rounded-lg border border-red-300 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">Ta bort</button>
            </form>
        </div>
    </div>

    <!-- Inspection history -->
    <div class="rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-md space-y-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Kontrollhistorik</h2>
        <?php if (!empty($inspections)): ?>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm mb-4">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Datum</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Status</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Nästa kontroll</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Anteckningar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php foreach ($inspections as $insp): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100"><?= htmlspecialchars($insp['inspected_at'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($insp['status'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($insp['next_inspection'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($insp['notes'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Ingen kontrollhistorik ännu.</p>
        <?php endif; ?>

        <!-- Add new inspection -->
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Registrera kontroll</h3>
        <form method="POST" action="/safety/resources/<?= (int) $resource['id'] ?>/inspect" class="space-y-4">
            <input type="hidden" name="_token" value="<?= \App\Core\Csrf::token() ?>">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="inspected_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kontrollerat datum <span class="text-red-500">*</span></label>
                    <input id="inspected_at" name="inspected_at" type="date" required
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="insp_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select id="insp_status" name="status" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <option value="ok">OK</option>
                        <option value="needs_attention">Behöver åtgärd</option>
                        <option value="failed">Underkänd</option>
                    </select>
                </div>
                <div>
                    <label for="insp_next" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nästa kontroll</label>
                    <input id="insp_next" name="next_inspection" type="date"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="insp_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Anteckningar</label>
                    <textarea id="insp_notes" name="notes" rows="2" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"></textarea>
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">Registrera kontroll</button>
            </div>
        </form>
    </div>
</div>
