<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Nödresurser</h1>
        <a href="/safety/resources/create" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">+ Ny resurs</a>
    </div>

    <?php if ($success !== null): ?>
        <div class="rounded-lg bg-green-50 dark:bg-green-900/30 px-4 py-3 text-sm text-green-700 dark:text-green-400"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <?php if (!empty($stats)): ?>
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-4 shadow-md">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">OK</p>
            <p class="mt-1 text-2xl font-bold text-green-600 dark:text-green-400"><?= (int) ($stats['ok'] ?? 0) ?></p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-4 shadow-md">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Behöver kontroll</p>
            <p class="mt-1 text-2xl font-bold text-yellow-600 dark:text-yellow-400"><?= (int) ($stats['needs_inspection'] ?? 0) ?></p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-4 shadow-md">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Ur drift</p>
            <p class="mt-1 text-2xl font-bold text-red-600 dark:text-red-400"><?= (int) ($stats['out_of_service'] ?? 0) ?></p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-4 shadow-md">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Saknas</p>
            <p class="mt-1 text-2xl font-bold text-gray-600 dark:text-gray-400"><?= (int) ($stats['missing'] ?? 0) ?></p>
        </div>
    </div>
    <?php endif; ?>

    <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-md">
        <?php if (empty($resources)): ?>
            <p class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">Inga resurser ännu. <a href="/safety/resources/create" class="text-indigo-600 dark:text-indigo-400 hover:underline">Skapa den första.</a></p>
        <?php else: ?>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Namn</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Typ</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Plats</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Status</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Nästa kontroll</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Åtgärder</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php foreach ($resources as $r): ?>
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
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100">
                                <a href="/safety/resources/<?= (int) $r['id'] ?>" class="hover:text-indigo-600 dark:hover:text-indigo-400"><?= htmlspecialchars($r['name'], ENT_QUOTES, 'UTF-8') ?></a>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($r['resource_type'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($r['location'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium <?= $statusClass ?>"><?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?></span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($r['next_inspection'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-6 py-4 text-right space-x-3">
                                <a href="/safety/resources/<?= (int) $r['id'] ?>/edit" class="text-indigo-600 dark:text-indigo-400 hover:underline">Redigera</a>
                                <form method="POST" action="/safety/resources/<?= (int) $r['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort denna resurs?')">
                                    <input type="hidden" name="_token" value="<?= \App\Core\Csrf::token() ?>">
                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:underline bg-transparent border-0 p-0 cursor-pointer">Ta bort</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
