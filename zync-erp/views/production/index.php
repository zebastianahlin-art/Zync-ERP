<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Produktion</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Översikt över produktionslinjer och ordrar</p>
        </div>
        <div class="flex gap-2">
            <a href="/production/lines" class="inline-flex items-center gap-2 rounded-lg bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 shadow-sm border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                Linjer
            </a>
            <a href="/production/orders/create" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Ny order
            </a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aktiva linjer</p>
            <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white"><?= $stats['active_lines'] ?><span class="text-sm font-normal text-gray-400"> / <?= $stats['total_lines'] ?></span></p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pågående ordrar</p>
            <p class="mt-2 text-2xl font-bold text-indigo-600 dark:text-indigo-400"><?= $stats['in_progress'] ?></p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Planerade</p>
            <p class="mt-2 text-2xl font-bold text-amber-600 dark:text-amber-400"><?= $stats['planned'] ?></p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Totalt producerat</p>
            <p class="mt-2 text-2xl font-bold text-green-600 dark:text-green-400"><?= number_format($stats['total_produced'], 0, ',', ' ') ?></p>
            <?php if ($stats['total_scrapped'] > 0): ?>
                <p class="text-xs text-red-500 mt-1">Kasserat: <?= number_format($stats['total_scrapped'], 0, ',', ' ') ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Linjer snabböversikt -->
    <?php if (!empty($lines)): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Produktionslinjer</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 p-5">
            <?php foreach ($lines as $line): ?>
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 hover:border-indigo-300 dark:hover:border-indigo-600 transition-colors">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-semibold text-gray-900 dark:text-white"><?= htmlspecialchars($line['name']) ?></h3>
                    <?php
                    $statusColor = match($line['status']) {
                        'active' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                        'maintenance' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400',
                        default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400',
                    };
                    $statusLabel = match($line['status']) {
                        'active' => 'Aktiv',
                        'maintenance' => 'Underhåll',
                        'inactive' => 'Inaktiv',
                        default => $line['status'],
                    };
                    ?>
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium <?= $statusColor ?>"><?= $statusLabel ?></span>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($line['code']) ?></p>
                <?php if ($line['capacity_per_hour']): ?>
                    <p class="text-xs text-gray-400 mt-1">Kapacitet: <?= $line['capacity_per_hour'] ?>/h</p>
                <?php endif; ?>
                <?php if ($line['department_name']): ?>
                    <p class="text-xs text-gray-400"><?= htmlspecialchars($line['department_name']) ?></p>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Senaste ordrar -->
    <?php if (!empty($orders)): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Produktionsordrar</h2>
            <a href="/production/orders" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Visa alla →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Order</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Artikel</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Linje</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Producerat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach (array_slice($orders, 0, 10) as $o): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="px-5 py-3 text-sm">
                            <a href="/production/orders/<?= $o['id'] ?>" class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline"><?= htmlspecialchars($o['order_number']) ?></a>
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-700 dark:text-gray-300"><?= htmlspecialchars($o['article_name'] ?? '—') ?></td>
                        <td class="px-5 py-3 text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($o['line_name'] ?? '—') ?></td>
                        <td class="px-5 py-3 text-sm">
                            <?php
                            $oColor = match($o['status']) {
                                'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                'planned'     => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400',
                                'released'    => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
                                'completed'   => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                'cancelled'   => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                default       => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400',
                            };
                            $oLabel = match($o['status']) {
                                'draft' => 'Utkast', 'planned' => 'Planerad', 'released' => 'Frisläppt',
                                'in_progress' => 'Pågår', 'completed' => 'Klar', 'cancelled' => 'Avbruten',
                                default => $o['status'],
                            };
                            ?>
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium <?= $oColor ?>"><?= $oLabel ?></span>
                        </td>
                        <td class="px-5 py-3 text-sm text-right text-gray-700 dark:text-gray-300">
                            <?= number_format((float)$o['quantity_produced'], 0, ',', ' ') ?> / <?= number_format((float)$o['quantity_planned'], 0, ',', ' ') ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php else: ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
        <h3 class="mt-3 text-lg font-medium text-gray-900 dark:text-white">Inga produktionsordrar</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kom igång genom att skapa din första produktionsorder.</p>
        <a href="/production/orders/create" class="mt-4 inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Skapa order
        </a>
    </div>
    <?php endif; ?>
</div>
