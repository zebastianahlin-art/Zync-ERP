<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Resurskarta – per plats</h1>
        <a href="/safety/resources" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">&larr; Tillbaka</a>
    </div>

    <?php
    $grouped = [];
    foreach ($resources as $r) {
        $grouped[$r['location'] ?? 'Okänd plats'][] = $r;
    }
    ?>

    <?php if (empty($grouped)): ?>
        <p class="text-sm text-gray-500 dark:text-gray-400">Inga resurser att visa.</p>
    <?php else: ?>
        <?php foreach ($grouped as $location => $items): ?>
            <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100"><?= htmlspecialchars($location, ENT_QUOTES, 'UTF-8') ?></h2>
                </div>
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Namn</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Typ</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Status</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Nästa kontroll</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <?php foreach ($items as $r): ?>
                            <?php
                            $status = $r['status'] ?? 'ok';
                            $statusClass = match($status) {
                                'ok'               => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400',
                                'needs_inspection' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400',
                                'out_of_service'   => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400',
                                default            => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                            };
                            ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-3 font-medium text-gray-900 dark:text-gray-100">
                                    <a href="/safety/resources/<?= (int) $r['id'] ?>" class="hover:text-indigo-600 dark:hover:text-indigo-400"><?= htmlspecialchars($r['name'], ENT_QUOTES, 'UTF-8') ?></a>
                                </td>
                                <td class="px-6 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($r['resource_type'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-6 py-3">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium <?= $statusClass ?>"><?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td class="px-6 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($r['next_inspection'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
