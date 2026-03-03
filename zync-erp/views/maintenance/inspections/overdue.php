<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="/maintenance/inspections" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Förfallna besiktningar</h1>
    </div>

    <?php if (!empty($overdue)): ?>
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 text-sm text-red-800 dark:text-red-200">
        <strong><?= count($overdue) ?> besiktningsobjekt</strong> har förfallna eller saknade besiktningsdatum.
    </div>
    <?php endif; ?>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Namn</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Typ</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Plats</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Senaste besiktning</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Nästa (förfallen)</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($overdue as $insp): ?>
                    <tr class="bg-red-50 dark:bg-red-900/10">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                            <a href="/maintenance/inspections/<?= $insp['id'] ?>" class="hover:underline"><?= htmlspecialchars($insp['name'], ENT_QUOTES, 'UTF-8') ?></a>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($insp['type'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($insp['location'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($insp['last_inspection_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-red-600 dark:text-red-400 font-medium">
                            <?= htmlspecialchars($insp['next_inspection_date'] ?? 'Ej planerad', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="/maintenance/inspections/<?= $insp['id'] ?>" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Besikta nu →</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($overdue)): ?>
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Inga förfallna besiktningar — bra jobbat!</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
