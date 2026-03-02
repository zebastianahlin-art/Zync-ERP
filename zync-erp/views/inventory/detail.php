<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="/inventory" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Lagersaldo per plats</h1>
    </div>

    <!-- Filters -->
    <form method="GET" action="/inventory/detail" class="flex flex-wrap gap-3">
        <select name="warehouse" class="rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            <option value="">Alla lagerplatser</option>
            <?php foreach ($warehouses as $w): ?>
                <option value="<?= $w['id'] ?>" <?= (int)($filters['warehouse'] ?? 0) === (int)$w['id'] ? 'selected' : '' ?>><?= htmlspecialchars($w['code'] . ' — ' . $w['name'], ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="search" placeholder="Sök artikel…"
               value="<?= htmlspecialchars($filters['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
               class="rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
        <button type="submit" class="rounded-lg bg-gray-100 dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Filtrera</button>
        <a href="/inventory/detail" class="rounded-lg px-3 py-2 text-sm text-gray-500 hover:text-indigo-600">Rensa</a>
    </form>

    <?php if (empty($stock)): ?>
        <div class="rounded-xl bg-white dark:bg-gray-800 p-10 text-center shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Inga resultat.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Artikelnr</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Artikel</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Lagerplats</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Saldo</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Enhet</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Min</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Max</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php foreach ($stock as $row): ?>
                        <?php
                        $qty   = (float) $row['quantity'];
                        $min   = $row['min_quantity'] !== null ? (float) $row['min_quantity'] : null;
                        $isLow = $min !== null && $qty < $min;
                        ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-4 py-3 font-mono text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars($row['article_number'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($row['article_name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                <span class="inline-flex items-center gap-1">
                                    <span class="rounded bg-indigo-100 dark:bg-indigo-900/30 px-1.5 py-0.5 text-xs font-mono font-medium text-indigo-700 dark:text-indigo-400"><?= htmlspecialchars($row['warehouse_code'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <?= htmlspecialchars($row['warehouse_name'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold <?= $isLow ? 'text-red-600' : 'text-gray-900 dark:text-white' ?>"><?= number_format($qty, $qty == (int)$qty ? 0 : 1, ',', ' ') ?></td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400"><?= htmlspecialchars($row['unit'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3 text-right text-gray-500 dark:text-gray-400"><?= $min !== null ? number_format($min, 0, ',', ' ') : '—' ?></td>
                            <td class="px-4 py-3 text-right text-gray-500 dark:text-gray-400"><?= $row['max_quantity'] !== null ? number_format((float)$row['max_quantity'], 0, ',', ' ') : '—' ?></td>
                            <td class="px-4 py-3">
                                <?php if ($isLow): ?>
                                    <span class="inline-flex rounded-full bg-red-100 dark:bg-red-900/30 px-2 py-0.5 text-xs font-medium text-red-700 dark:text-red-400">Lågt</span>
                                <?php elseif ($qty > 0): ?>
                                    <span class="inline-flex rounded-full bg-green-100 dark:bg-green-900/30 px-2 py-0.5 text-xs font-medium text-green-700 dark:text-green-400">OK</span>
                                <?php else: ?>
                                    <span class="inline-flex rounded-full bg-gray-100 dark:bg-gray-700 px-2 py-0.5 text-xs font-medium text-gray-500 dark:text-gray-400">0</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
