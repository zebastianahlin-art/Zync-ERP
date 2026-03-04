<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Produktion</h1>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <p class="text-sm text-gray-500 dark:text-gray-400">Produktionslinjer</p>
            <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 mt-1"><?= (int) ($stats['lines'] ?? 0) ?></p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <p class="text-sm text-gray-500 dark:text-gray-400">Produktionsordrar</p>
            <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 mt-1"><?= (int) ($stats['orders'] ?? 0) ?></p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <p class="text-sm text-gray-500 dark:text-gray-400">Lagerposter</p>
            <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 mt-1"><?= (int) ($stats['stock'] ?? 0) ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="/production/lines" class="block bg-white dark:bg-gray-800 rounded-xl shadow p-5 hover:shadow-md transition">
            <h2 class="font-semibold text-gray-900 dark:text-white">Produktionslinjer</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Hantera produktionslinjer</p>
        </a>
        <a href="/production/orders" class="block bg-white dark:bg-gray-800 rounded-xl shadow p-5 hover:shadow-md transition">
            <h2 class="font-semibold text-gray-900 dark:text-white">Produktionsordrar</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Planera och följ ordrar</p>
        </a>
        <a href="/production/stock" class="block bg-white dark:bg-gray-800 rounded-xl shadow p-5 hover:shadow-md transition">
            <h2 class="font-semibold text-gray-900 dark:text-white">Produktionslager</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Lagersaldo för produktion</p>
        </a>
    </div>

    <?php if (!empty($orders)): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="font-semibold text-gray-900 dark:text-white">Senaste ordrar</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Order</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Linje</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($orders as $order): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($order['order_number'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($order['line_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($order['status'], ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>
