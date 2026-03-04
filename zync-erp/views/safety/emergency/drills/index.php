<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Nödlägesövningar</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Planera och dokumentera nödlägesövningar</p>
        </div>
        <div class="flex gap-2">
            <a href="/safety/emergency/drills/templates" class="rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Mallar</a>
            <a href="/safety/emergency/drills/create" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">+ Ny övning</a>
        </div>
    </div>

    <?php if ($success !== null): ?>
        <div class="rounded-lg bg-green-50 dark:bg-green-900/30 px-4 py-3 text-sm text-green-700 dark:text-green-400"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="flex flex-wrap gap-2">
        <a href="/safety/emergency/drills" class="px-3 py-1.5 rounded-lg text-sm <?= empty($filters['status']) ? 'bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-400 font-semibold' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' ?> transition-colors">Alla</a>
        <?php foreach (['planned' => 'Planerade', 'in_progress' => 'Pågående', 'completed' => 'Slutförda', 'cancelled' => 'Avbrutna'] as $s => $label): ?>
            <a href="/safety/emergency/drills?status=<?= htmlspecialchars($s, ENT_QUOTES, 'UTF-8') ?>" class="px-3 py-1.5 rounded-lg text-sm <?= ($filters['status'] ?? '') === $s ? 'bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-400 font-semibold' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' ?> transition-colors"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></a>
        <?php endforeach; ?>
    </div>

    <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-md">
        <?php if (empty($drills)): ?>
            <p class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">Inga nödlägesövningar hittades. <a href="/safety/emergency/drills/create" class="text-indigo-600 dark:text-indigo-400 hover:underline">Skapa den första.</a></p>
        <?php else: ?>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Övningsnr</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Titel</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Typ</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Plats</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Datum</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Status</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Åtgärder</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php foreach ($drills as $d): ?>
                        <?php
                            $statusColors = [
                                'planned'     => 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400',
                                'in_progress' => 'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-400',
                                'completed'   => 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400',
                                'cancelled'   => 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400',
                            ];
                            $statusColor = $statusColors[$d['status']] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300';
                        ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4 font-mono text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars($d['drill_number'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100">
                                <a href="/safety/emergency/drills/<?= (int) $d['id'] ?>" class="hover:text-indigo-600 dark:hover:text-indigo-400"><?= htmlspecialchars($d['title'], ENT_QUOTES, 'UTF-8') ?></a>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($d['drill_type'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($d['location'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($d['scheduled_date'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium <?= $statusColor ?>"><?= htmlspecialchars($d['status'], ENT_QUOTES, 'UTF-8') ?></span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-3">
                                <a href="/safety/emergency/drills/<?= (int) $d['id'] ?>" class="text-indigo-600 dark:text-indigo-400 hover:underline">Visa</a>
                                <a href="/safety/emergency/drills/<?= (int) $d['id'] ?>/edit" class="text-gray-600 dark:text-gray-400 hover:underline">Redigera</a>
                                <form method="POST" action="/safety/emergency/drills/<?= (int) $d['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort denna övning?')">
                                    <?= \App\Core\Csrf::field() ?>
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
