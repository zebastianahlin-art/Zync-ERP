<div class="max-w-3xl space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="/production/lines" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">← Tillbaka</a>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"><?= htmlspecialchars($item['name']) ?></h1>
        </div>
        <a href="/production/lines/<?= $item['id'] ?>/edit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Redigera</a>
    </div>
    <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 shadow-sm space-y-4">
        <div class="grid grid-cols-2 gap-6">
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</p>
                <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($item['status']) ?></p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kapacitet</p>
                <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($item['capacity'] ?? '–') ?></p>
            </div>
        </div>
        <?php if ($item['description']): ?>
        <div>
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Beskrivning</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= nl2br(htmlspecialchars($item['description'])) ?></p>
        </div>
        <?php endif; ?>
        <div>
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Skapad</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($item['created_at']) ?></p>
        </div>
    </div>
</div>
