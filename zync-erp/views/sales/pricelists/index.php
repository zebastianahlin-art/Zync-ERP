<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Prislistor</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1"><?= count($priceLists) ?> prislistor</p>
        </div>
        <a href="/sales/pricelists/create" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ny prislista
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php if (empty($priceLists)): ?>
            <p class="text-sm text-gray-400 col-span-3 py-8 text-center">Inga prislistor skapade</p>
        <?php else: foreach ($priceLists as $pl): ?>
            <a href="/sales/pricelists/<?= $pl['id'] ?>" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="inline-flex items-center rounded-md bg-indigo-100 dark:bg-indigo-900/30 px-2 py-1 text-xs font-mono font-medium text-indigo-700 dark:text-indigo-400"><?= htmlspecialchars($pl['code']) ?></span>
                        <h3 class="mt-2 text-base font-semibold text-gray-900 dark:text-white"><?= htmlspecialchars($pl['name']) ?></h3>
                    </div>
                    <?php if ($pl['is_default']): ?>
                        <span class="inline-flex items-center rounded-full bg-green-100 dark:bg-green-900/30 px-2 py-0.5 text-xs font-medium text-green-700 dark:text-green-400">Standard</span>
                    <?php endif; ?>
                </div>
                <div class="mt-3 flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                    <span><?= $pl['currency'] ?></span>
                    <span><?= $pl['line_count'] ?> artiklar</span>
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium <?= $pl['is_active'] ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' ?>"><?= $pl['is_active'] ? 'Aktiv' : 'Inaktiv' ?></span>
                </div>
                <?php if ($pl['valid_from'] || $pl['valid_to']): ?>
                    <p class="mt-2 text-xs text-gray-400"><?= $pl['valid_from'] ?? '—' ?> → <?= $pl['valid_to'] ?? 'tillsvidare' ?></p>
                <?php endif; ?>
            </a>
        <?php endforeach; endif; ?>
    </div>
</div>
