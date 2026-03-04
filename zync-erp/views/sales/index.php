<div class="space-y-6">
    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Sales – Översikt</h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
            <p class="text-sm text-gray-500 dark:text-gray-400">Offerter</p>
            <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white"><?= count($quotes) ?></p>
            <a href="/sales/quotes" class="mt-2 inline-block text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Visa alla →</a>
        </div>
        <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
            <p class="text-sm text-gray-500 dark:text-gray-400">Orderingångar</p>
            <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white"><?= count($orders) ?></p>
            <a href="/sales/orders" class="mt-2 inline-block text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Visa alla →</a>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="font-semibold text-gray-900 dark:text-white">Senaste offerter</h2>
                <a href="/sales/quotes/create" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">+ Ny</a>
            </div>
            <?php foreach (array_slice($quotes, 0, 5) as $q): ?>
            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-50 dark:border-gray-700/50">
                <a href="/sales/quotes/<?= $q['id'] ?>" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline"><?= htmlspecialchars($q['quote_number']) ?></a>
                <span class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars($q['customer_name'] ?? '–') ?></span>
            </div>
            <?php endforeach; ?>
            <?php if (empty($quotes)): ?><p class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">Inga offerter.</p><?php endif; ?>
        </div>
        <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="font-semibold text-gray-900 dark:text-white">Senaste orderingångar</h2>
                <a href="/sales/orders/create" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">+ Ny</a>
            </div>
            <?php foreach (array_slice($orders, 0, 5) as $o): ?>
            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-50 dark:border-gray-700/50">
                <a href="/sales/orders/<?= $o['id'] ?>" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline"><?= htmlspecialchars($o['order_number']) ?></a>
                <span class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars($o['customer_name'] ?? '–') ?></span>
            </div>
            <?php endforeach; ?>
            <?php if (empty($orders)): ?><p class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">Inga ordrar.</p><?php endif; ?>
        </div>
    </div>
</div>
