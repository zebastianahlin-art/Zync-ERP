<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Produktion – Översikt</h1>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
            <p class="text-sm text-gray-500 dark:text-gray-400">Produktionslinjer</p>
            <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white"><?= count($lines) ?></p>
            <a href="/production/lines" class="mt-2 inline-block text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Visa alla →</a>
        </div>
        <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
            <p class="text-sm text-gray-500 dark:text-gray-400">Aktiva ordrar</p>
            <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white"><?= count(array_filter($orders, fn($o) => $o['status'] === 'in_progress')) ?></p>
            <a href="/production/orders" class="mt-2 inline-block text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Visa alla →</a>
        </div>
        <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
            <p class="text-sm text-gray-500 dark:text-gray-400">Lagerposter</p>
            <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white"><?= count($stock) ?></p>
            <a href="/production/stock" class="mt-2 inline-block text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Visa lager →</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="font-semibold text-gray-900 dark:text-white">Produktionslinjer</h2>
                <a href="/production/lines/create" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">+ Ny</a>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                <?php foreach (array_slice($lines, 0, 5) as $line): ?>
                <div class="flex items-center justify-between px-5 py-3">
                    <span class="text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($line['name']) ?></span>
                    <span class="text-xs px-2 py-0.5 rounded-full <?= $line['status'] === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' ?>">
                        <?= htmlspecialchars($line['status']) ?>
                    </span>
                </div>
                <?php endforeach; ?>
                <?php if (empty($lines)): ?>
                <p class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">Inga produktionslinjer.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="font-semibold text-gray-900 dark:text-white">Senaste produktionsordrar</h2>
                <a href="/production/orders/create" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">+ Ny</a>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                <?php foreach (array_slice($orders, 0, 5) as $order): ?>
                <div class="flex items-center justify-between px-5 py-3">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($order['order_number']) ?></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars($order['product_name']) ?></p>
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                        <?= htmlspecialchars($order['status']) ?>
                    </span>
                </div>
                <?php endforeach; ?>
                <?php if (empty($orders)): ?>
                <p class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">Inga produktionsordrar.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
