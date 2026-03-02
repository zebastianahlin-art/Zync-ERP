<div class="space-y-6">

    <!-- Stats -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
        <div class="rounded-xl bg-white dark:bg-gray-800 p-5 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
            <p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Artiklar</p>
            <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white"><?= $stats['total_articles'] ?></p>
        </div>
        <div class="rounded-xl bg-white dark:bg-gray-800 p-5 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
            <p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Lagervärde</p>
            <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white"><?= number_format($stats['total_value'], 0, ',', ' ') ?> kr</p>
        </div>
        <div class="rounded-xl bg-white dark:bg-gray-800 p-5 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
            <p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Lågt saldo</p>
            <p class="mt-1 text-2xl font-bold <?= $stats['low_stock'] > 0 ? 'text-red-600' : 'text-green-600' ?>"><?= $stats['low_stock'] ?></p>
        </div>
        <div class="rounded-xl bg-white dark:bg-gray-800 p-5 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
            <p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Lagerplatser</p>
            <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white"><?= $stats['warehouses'] ?></p>
        </div>
    </div>

    <!-- Header + actions -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Lagersaldo</h1>
        <div class="flex flex-wrap gap-2">
            <a href="/inventory/move" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                Lagerrörelse
            </a>
            <a href="/inventory/detail" class="inline-flex items-center gap-2 rounded-lg bg-white dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 ring-1 ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">Per lagerplats</a>
            <a href="/inventory/transactions" class="inline-flex items-center gap-2 rounded-lg bg-white dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 ring-1 ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">Historik</a>
            <a href="/inventory/warehouses" class="inline-flex items-center gap-2 rounded-lg bg-white dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 ring-1 ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">Lagerplatser</a>
        </div>
    </div>

    <!-- Search -->
    <form method="GET" action="/inventory" class="flex flex-wrap gap-3">
        <input type="text" name="search" placeholder="Sök artikel…"
               value="<?= htmlspecialchars($filters['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
               class="rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
        <button type="submit" class="rounded-lg bg-gray-100 dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Sök</button>
        <?php if (!empty($filters['search'])): ?>
            <a href="/inventory" class="rounded-lg px-3 py-2 text-sm text-gray-500 hover:text-indigo-600">Rensa</a>
        <?php endif; ?>
    </form>

    <!-- Table -->
    <?php if (empty($stock)): ?>
        <div class="rounded-xl bg-white dark:bg-gray-800 p-10 text-center shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Inga artiklar hittades.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Artikelnr</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Namn</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Kategori</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Totalt saldo</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Enhet</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Inköpspris</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Värde</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php foreach ($stock as $row): ?>
                        <?php
                        $qty   = (float) $row['total_quantity'];
                        $min   = $row['min_quantity'] !== null ? (float) $row['min_quantity'] : null;
                        $value = $qty * (float) $row['purchase_price'];
                        $isLow = $min !== null && $qty < $min;
                        ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-4 py-3 font-mono text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars($row['article_number'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400"><?= htmlspecialchars($row['category'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3 text-right font-semibold <?= $isLow ? 'text-red-600' : 'text-gray-900 dark:text-white' ?>"><?= number_format($qty, $qty == (int)$qty ? 0 : 1, ',', ' ') ?></td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400"><?= htmlspecialchars($row['unit'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400"><?= number_format((float)$row['purchase_price'], 2, ',', ' ') ?></td>
                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400"><?= number_format($value, 0, ',', ' ') ?> kr</td>
                            <td class="px-4 py-3">
                                <?php if ($isLow): ?>
                                    <span class="inline-flex rounded-full bg-red-100 dark:bg-red-900/30 px-2 py-0.5 text-xs font-medium text-red-700 dark:text-red-400">Lågt</span>
                                <?php elseif ($qty > 0): ?>
                                    <span class="inline-flex rounded-full bg-green-100 dark:bg-green-900/30 px-2 py-0.5 text-xs font-medium text-green-700 dark:text-green-400">I lager</span>
                                <?php else: ?>
                                    <span class="inline-flex rounded-full bg-gray-100 dark:bg-gray-700 px-2 py-0.5 text-xs font-medium text-gray-500 dark:text-gray-400">Slut</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
