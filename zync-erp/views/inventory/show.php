<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                <?= htmlspecialchars($stock['article_name'] ?? 'Lagerpost', ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                <?= htmlspecialchars($stock['article_number'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                <?php if ($stock['warehouse_name'] ?? null): ?>
                    &bull; <?= htmlspecialchars($stock['warehouse_name'], ENT_QUOTES, 'UTF-8') ?>
                <?php endif; ?>
            </p>
        </div>
        <a href="/inventory" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600 transition-colors">&larr; Tillbaka till lager</a>
    </div>

    <!-- Stock summary card -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <p class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Aktuellt saldo</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1"><?= htmlspecialchars((string) ($stock['quantity'] ?? 0), ENT_QUOTES, 'UTF-8') ?></p>
            <?php if ($stock['unit'] ?? null): ?>
                <p class="text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($stock['unit'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <p class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Miniminivå</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1"><?= htmlspecialchars((string) ($stock['min_quantity'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <p class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Lager</p>
            <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1"><?= htmlspecialchars($stock['warehouse_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
        </div>
    </div>

    <!-- Transactions -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Transaktioner</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Datum</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Typ</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Antal</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Utförd av</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($transactions as $tx): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            <?= htmlspecialchars(substr($tx['created_at'] ?? '', 0, 10), ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3 text-gray-900 dark:text-white">
                            <?= htmlspecialchars($tx['type'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold <?= ($tx['quantity'] ?? 0) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' ?>">
                            <?= ($tx['quantity'] ?? 0) >= 0 ? '+' : '' ?><?= htmlspecialchars((string) ($tx['quantity'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            <?= htmlspecialchars($tx['created_by_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($transactions)): ?>
                    <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">Inga transaktioner</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
