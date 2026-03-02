<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="/inventory" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Lagerplatser</h1>
        </div>
        <a href="/inventory/warehouses/create" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ny lagerplats
        </a>
    </div>

    <?php if (empty($warehouses)): ?>
        <div class="rounded-xl bg-white dark:bg-gray-800 p-10 text-center shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Inga lagerplatser ännu.</p>
        </div>
    <?php else: ?>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($warehouses as $w): ?>
                <div class="rounded-xl bg-white dark:bg-gray-800 p-5 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="inline-flex rounded bg-indigo-100 dark:bg-indigo-900/30 px-2 py-0.5 text-xs font-mono font-bold text-indigo-700 dark:text-indigo-400"><?= htmlspecialchars($w['code'], ENT_QUOTES, 'UTF-8') ?></span>
                            <h3 class="mt-2 font-semibold text-gray-900 dark:text-white"><?= htmlspecialchars($w['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <?php if ($w['address'] || $w['city']): ?>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars(trim(($w['address'] ?? '') . ', ' . ($w['city'] ?? ''), ', '), ENT_QUOTES, 'UTF-8') ?></p>
                            <?php endif; ?>
                        </div>
                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium <?= $w['is_active'] ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' ?>">
                            <?= $w['is_active'] ? 'Aktiv' : 'Inaktiv' ?>
                        </span>
                    </div>
                    <div class="mt-4 flex gap-2">
                        <a href="/inventory/warehouses/<?= $w['id'] ?>/edit" class="rounded-lg px-3 py-1.5 text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors">Redigera</a>
                        <form method="POST" action="/inventory/warehouses/<?= $w['id'] ?>/delete" onsubmit="return confirm('Ta bort denna lagerplats?')">
                            <button type="submit" class="rounded-lg px-3 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">Ta bort</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
