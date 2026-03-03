<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Produktionslinjer</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Hantera produktionslinjer kopplade till maskinhierarkin</p>
        </div>
        <div class="flex gap-2">
            <a href="/production" class="inline-flex items-center gap-2 rounded-lg bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 shadow-sm border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">← Översikt</a>
            <a href="/production/lines/create" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Ny linje
            </a>
        </div>
    </div>

    <?php if (!empty($lines)): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Kod</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Namn</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Avdelning</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Maskin</th>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Kapacitet/h</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Åtgärder</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($lines as $line): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                    <td class="px-5 py-3 text-sm font-mono font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($line['code']) ?></td>
                    <td class="px-5 py-3 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($line['name']) ?></td>
                    <td class="px-5 py-3 text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($line['department_name'] ?? '—') ?></td>
                    <td class="px-5 py-3 text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($line['machine_name'] ?? '—') ?></td>
                    <td class="px-5 py-3 text-sm text-center text-gray-500 dark:text-gray-400"><?= $line['capacity_per_hour'] ? number_format((float)$line['capacity_per_hour'], 0) : '—' ?></td>
                    <td class="px-5 py-3 text-sm">
                        <?php
                        $sc = match($line['status']) {
                            'active' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                            'maintenance' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400',
                            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400',
                        };
                        $sl = match($line['status']) {
                            'active' => 'Aktiv', 'maintenance' => 'Underhåll', 'inactive' => 'Inaktiv', default => $line['status'],
                        };
                        ?>
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium <?= $sc ?>"><?= $sl ?></span>
                    </td>
                    <td class="px-5 py-3 text-sm text-right">
                        <a href="/production/lines/<?= $line['id'] ?>/edit" class="text-indigo-600 dark:text-indigo-400 hover:underline mr-3">Redigera</a>
                        <form method="POST" action="/production/lines/<?= $line['id'] ?>/delete" class="inline" onsubmit="return confirm('Radera linjen?')">
                            <?= \App\Core\Csrf::field() ?>
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Radera</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
        <h3 class="mt-3 text-lg font-medium text-gray-900 dark:text-white">Inga produktionslinjer</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Skapa din första produktionslinje för att komma igång.</p>
        <a href="/production/lines/create" class="mt-4 inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Skapa linje
        </a>
    </div>
    <?php endif; ?>
</div>
