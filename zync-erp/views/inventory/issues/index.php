<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Uttag</h1>
        <a href="/inventory/issues/create"
           class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nytt uttag
        </a>
    </div>

    <!-- Flash messages -->
    <?php if (!empty($success)): ?>
    <div class="rounded-lg bg-green-50 dark:bg-green-900/30 p-4 text-green-800 dark:text-green-200">
        <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
    <div class="rounded-lg bg-red-50 dark:bg-red-900/30 p-4 text-red-800 dark:text-red-200">
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php endif; ?>

    <!-- Filters -->
    <form method="GET" action="/inventory/issues"
          class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Från datum</label>
            <input type="date" name="date_from" value="<?= htmlspecialchars($filters['date_from'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                   class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Till datum</label>
            <input type="date" name="date_to" value="<?= htmlspecialchars($filters['date_to'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                   class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>
        <div>
            <button type="submit"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
                Filtrera
            </button>
        </div>
    </form>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Datum</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Artikel</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Lagerställe</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Antal</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Referens</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Noteringar</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Skapad av</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($issues as $issue): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400 whitespace-nowrap">
                            <?= htmlspecialchars(substr($issue['created_at'] ?? '—', 0, 10), ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3 text-gray-900 dark:text-white">
                            <div><?= htmlspecialchars($issue['article_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></div>
                            <div class="text-xs font-mono text-indigo-600 dark:text-indigo-400">
                                <?= htmlspecialchars($issue['article_number'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            <?= htmlspecialchars($issue['warehouse_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">
                            <?= htmlspecialchars((string) ($issue['quantity'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            <?= htmlspecialchars($issue['reference_type'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                            <?php if (!empty($issue['reference_id'])): ?>
                            #<?= htmlspecialchars((string) $issue['reference_id'], ENT_QUOTES, 'UTF-8') ?>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 max-w-xs truncate">
                            <?= htmlspecialchars($issue['notes'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                            <?= htmlspecialchars($issue['created_by_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($issues)): ?>
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Inga uttag registrerade</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
