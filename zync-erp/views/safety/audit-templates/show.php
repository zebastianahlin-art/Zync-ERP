<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100"><?= htmlspecialchars($template['name'], ENT_QUOTES, 'UTF-8') ?></h1>
        <a href="/safety/audit-templates" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">&larr; Tillbaka</a>
    </div>

    <div class="rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-md space-y-4">
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><span class="font-medium text-gray-600 dark:text-gray-400">Kategori:</span> <span class="text-gray-900 dark:text-gray-100"><?= htmlspecialchars($template['category'] ?? '–', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="font-medium text-gray-600 dark:text-gray-400">Version:</span> <span class="text-gray-900 dark:text-gray-100"><?= htmlspecialchars((string) ($template['version'] ?? '1'), ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="font-medium text-gray-600 dark:text-gray-400">Aktiv:</span>
                <?php if (!empty($template['is_active'])): ?>
                    <span class="inline-flex items-center rounded-full bg-green-100 dark:bg-green-900/40 px-2 py-0.5 text-xs font-medium text-green-700 dark:text-green-400">Ja</span>
                <?php else: ?>
                    <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-700 px-2 py-0.5 text-xs font-medium text-gray-500 dark:text-gray-400">Nej</span>
                <?php endif; ?>
            </div>
        </div>
        <?php if (!empty($template['description'])): ?>
            <div><p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Beskrivning</p><p class="text-sm text-gray-900 dark:text-gray-100"><?= nl2br(htmlspecialchars($template['description'], ENT_QUOTES, 'UTF-8')) ?></p></div>
        <?php endif; ?>
        <div class="flex items-center space-x-3 pt-2">
            <a href="/safety/audit-templates/<?= (int) $template['id'] ?>/edit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">Redigera</a>
            <form method="POST" action="/safety/audit-templates/<?= (int) $template['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort denna mall?')">
                <input type="hidden" name="_token" value="<?= \App\Core\Csrf::token() ?>">
                <button type="submit" class="rounded-lg border border-red-300 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">Ta bort</button>
            </form>
        </div>
    </div>

    <!-- Checklist items -->
    <div class="rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-md space-y-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Checklistepunkter</h2>
        <?php if (!empty($items)): ?>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm mb-4">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Fråga</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Sektion</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Typ</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Obligatorisk</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Ta bort</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php foreach ($items as $item): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100"><?= htmlspecialchars($item['question'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($item['section'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($item['response_type'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3">
                                <?php if (!empty($item['is_required'])): ?>
                                    <span class="inline-flex items-center rounded-full bg-green-100 dark:bg-green-900/40 px-2 py-0.5 text-xs font-medium text-green-700 dark:text-green-400">Ja</span>
                                <?php else: ?>
                                    <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-700 px-2 py-0.5 text-xs font-medium text-gray-500 dark:text-gray-400">Nej</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="/safety/audit-templates/<?= (int) $template['id'] ?>/items/<?= (int) $item['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort denna punkt?')">
                                    <input type="hidden" name="_token" value="<?= \App\Core\Csrf::token() ?>">
                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:underline bg-transparent border-0 p-0 cursor-pointer text-sm">Ta bort</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Inga punkter ännu.</p>
        <?php endif; ?>

        <!-- Add new item -->
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Lägg till punkt</h3>
        <form method="POST" action="/safety/audit-templates/<?= (int) $template['id'] ?>/items" class="space-y-4">
            <input type="hidden" name="_token" value="<?= \App\Core\Csrf::token() ?>">
            <div>
                <label for="question" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fråga <span class="text-red-500">*</span></label>
                <input id="question" name="question" type="text" required
                       class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="section" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sektion</label>
                    <input id="section" name="section" type="text"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="response_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Svarstyp</label>
                    <select id="response_type" name="response_type" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <option value="yes_no">Ja/Nej</option>
                        <option value="text">Text</option>
                        <option value="number">Nummer</option>
                    </select>
                </div>
                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sortering</label>
                    <input id="sort_order" name="sort_order" type="number" min="0" value="0"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div class="flex items-end pb-2">
                    <div class="flex items-center space-x-2">
                        <input id="is_required" name="is_required" type="checkbox" value="1"
                               class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="is_required" class="text-sm font-medium text-gray-700 dark:text-gray-300">Obligatorisk</label>
                    </div>
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">Lägg till punkt</button>
            </div>
        </form>
    </div>
</div>
