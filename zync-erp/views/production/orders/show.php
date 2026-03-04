<div class="max-w-3xl space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="/production/orders" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700">← Tillbaka</a>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"><?= htmlspecialchars($item['order_number']) ?></h1>
        </div>
        <a href="/production/orders/<?= $item['id'] ?>/edit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Redigera</a>
    </div>
    <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <div class="grid grid-cols-2 gap-6">
            <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Produkt</p><p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($item['product_name']) ?></p></div>
            <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Linje</p><p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($item['line_name'] ?? '–') ?></p></div>
            <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Antal</p><p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($item['quantity']) ?> <?= htmlspecialchars($item['unit']) ?></p></div>
            <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</p><p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($item['status']) ?></p></div>
            <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Prioritet</p><p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($item['priority']) ?></p></div>
            <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Planerad start</p><p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($item['planned_start'] ?? '–') ?></p></div>
            <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Planerat slut</p><p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($item['planned_end'] ?? '–') ?></p></div>
        </div>
        <?php if ($item['notes']): ?>
        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Anteckningar</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= nl2br(htmlspecialchars($item['notes'])) ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>
