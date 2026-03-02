<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="/inventory" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Transaktionshistorik</h1>
    </div>

    <!-- Filters -->
    <form method="GET" action="/inventory/transactions" class="flex flex-wrap gap-3">
        <select name="article" class="rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            <option value="">Alla artiklar</option>
            <?php foreach ($articles as $a): ?>
                <option value="<?= $a['id'] ?>" <?= (int)($filters['article'] ?? 0) === (int)$a['id'] ? 'selected' : '' ?>><?= htmlspecialchars($a['article_number'] . ' — ' . $a['name'], ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
        </select>
        <select name="warehouse" class="rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            <option value="">Alla lagerplatser</option>
            <?php foreach ($warehouses as $w): ?>
                <option value="<?= $w['id'] ?>" <?= (int)($filters['warehouse'] ?? 0) === (int)$w['id'] ? 'selected' : '' ?>><?= htmlspecialchars($w['code'] . ' — ' . $w['name'], ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="rounded-lg bg-gray-100 dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Filtrera</button>
        <a href="/inventory/transactions" class="rounded-lg px-3 py-2 text-sm text-gray-500 hover:text-indigo-600">Rensa</a>
    </form>

    <?php if (empty($transactions)): ?>
        <div class="rounded-xl bg-white dark:bg-gray-800 p-10 text-center shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Inga transaktioner ännu.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Datum</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Typ</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Artikel</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Lager</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Antal</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Notering</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Av</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php
                    $typeLabels = ['in' => ['Inleverans','bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'], 'out' => ['Uttag','bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'], 'adjust' => ['Justering','bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400'], 'transfer' => ['Flytt','bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400']];
                    foreach ($transactions as $t):
                        $tl = $typeLabels[$t['type']] ?? ['Okänd','bg-gray-100 text-gray-700'];
                    ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400 whitespace-nowrap"><?= date('Y-m-d H:i', strtotime($t['created_at'])) ?></td>
                            <td class="px-4 py-3"><span class="inline-flex rounded-full <?= $tl[1] ?> px-2 py-0.5 text-xs font-medium"><?= $tl[0] ?></span></td>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($t['article_number'] . ' — ' . $t['article_name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($t['warehouse_code'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white"><?= number_format((float)$t['quantity'], (float)$t['quantity'] == (int)$t['quantity'] ? 0 : 1, ',', ' ') ?> <?= htmlspecialchars($t['unit'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400"><?= htmlspecialchars($t['note'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400"><?= htmlspecialchars($t['user_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
