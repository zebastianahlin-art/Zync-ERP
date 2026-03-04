<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Lageröversikt</h1>
        <a href="/inventory/transactions/create"
           class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Ny transaktion
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

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 flex items-center gap-4">
            <div class="rounded-full bg-blue-100 dark:bg-blue-900/40 p-3">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Totalt artiklar</p>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                    <?= htmlspecialchars((string) ($kpis['total_articles'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                </p>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 flex items-center gap-4">
            <div class="rounded-full bg-red-100 dark:bg-red-900/40 p-3">
                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Under miniminivå</p>
                <p class="text-2xl font-bold text-red-600 dark:text-red-400">
                    <?= htmlspecialchars((string) ($kpis['below_minimum'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                </p>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 flex items-center gap-4">
            <div class="rounded-full bg-green-100 dark:bg-green-900/40 p-3">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Lagervärde</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                    <?= htmlspecialchars(number_format($kpis['total_value'] ?? 0, 0, ',', ' ') . ' kr', ENT_QUOTES, 'UTF-8') ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Filter bar -->
    <form method="GET" action="/inventory" class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[180px]">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sök artikel</label>
            <input type="text" name="search" value="<?= htmlspecialchars($filters['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                   placeholder="Sök artikel..."
                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>
        <div class="flex-1 min-w-[180px]">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lagerställe</label>
            <select name="warehouse_id"
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                <option value="">Alla lagerställen</option>
                <?php foreach ($warehouses as $wh): ?>
                <option value="<?= htmlspecialchars((string) $wh['id'], ENT_QUOTES, 'UTF-8') ?>"
                    <?= ((string) ($filters['warehouse_id'] ?? '') === (string) $wh['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($wh['name'], ENT_QUOTES, 'UTF-8') ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <button type="submit"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
                Filtrera
            </button>
        </div>
    </form>

    <!-- Stock table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Artikel</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Art.nr</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Lagerställe</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Saldo</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Min.nivå</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Plats</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($stockItems as $item): ?>
                    <?php $low = isset($item['min_quantity']) && $item['min_quantity'] !== null && $item['quantity'] <= $item['min_quantity']; ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50<?= $low ? ' bg-red-50 dark:bg-red-900/10' : '' ?>">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                            <a href="/inventory/<?= htmlspecialchars((string) $item['id'], ENT_QUOTES, 'UTF-8') ?>"
                               class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                <?= htmlspecialchars($item['article_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-indigo-600 dark:text-indigo-400">
                            <?= htmlspecialchars($item['article_number'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            <?= htmlspecialchars($item['warehouse_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold <?= $low ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' ?>">
                            <?= htmlspecialchars((string) ($item['quantity'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3 text-right text-gray-500 dark:text-gray-400">
                            <?= htmlspecialchars($item['min_quantity'] !== null ? (string) $item['min_quantity'] : '—', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                            <?= htmlspecialchars($item['location_code'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="/inventory/<?= htmlspecialchars((string) $item['id'], ENT_QUOTES, 'UTF-8') ?>"
                               class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Visa</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($stockItems)): ?>
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Inga lagerposter registrerade</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
