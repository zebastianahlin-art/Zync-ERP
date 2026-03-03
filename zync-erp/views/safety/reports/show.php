<div class="mx-auto max-w-2xl space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100"><?= htmlspecialchars($report['title'], ENT_QUOTES, 'UTF-8') ?></h1>
        <a href="/safety/reports" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">&larr; Tillbaka</a>
    </div>
    <div class="rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-md space-y-4">
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><span class="font-medium text-gray-600 dark:text-gray-400">Kategori:</span> <span class="text-gray-900 dark:text-gray-100"><?= htmlspecialchars($report['category'] ?? '–', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="font-medium text-gray-600 dark:text-gray-400">Allvarlighet:</span> <span class="text-gray-900 dark:text-gray-100"><?= htmlspecialchars($report['severity'] ?? '–', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="font-medium text-gray-600 dark:text-gray-400">Status:</span> <span class="text-gray-900 dark:text-gray-100"><?= htmlspecialchars($report['status'] ?? '–', ENT_QUOTES, 'UTF-8') ?></span></div>
            <?php if (!empty($report['location'])): ?>
            <div><span class="font-medium text-gray-600 dark:text-gray-400">Plats:</span> <span class="text-gray-900 dark:text-gray-100"><?= htmlspecialchars($report['location'], ENT_QUOTES, 'UTF-8') ?></span></div>
            <?php endif; ?>
            <div><span class="font-medium text-gray-600 dark:text-gray-400">Skapad:</span> <span class="text-gray-900 dark:text-gray-100"><?= htmlspecialchars($report['created_at'] ?? '–', ENT_QUOTES, 'UTF-8') ?></span></div>
        </div>
        <?php if (!empty($report['description'])): ?>
            <div><p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Beskrivning</p><p class="text-sm text-gray-900 dark:text-gray-100"><?= nl2br(htmlspecialchars($report['description'], ENT_QUOTES, 'UTF-8')) ?></p></div>
        <?php endif; ?>
        <?php if (!empty($report['action'])): ?>
            <div><p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Åtgärd vidtagen</p><p class="text-sm text-gray-900 dark:text-gray-100"><?= nl2br(htmlspecialchars($report['action'], ENT_QUOTES, 'UTF-8')) ?></p></div>
        <?php endif; ?>
        <div class="flex items-center space-x-3 pt-2">
            <a href="/safety/reports/<?= (int) $report['id'] ?>/edit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">Redigera</a>
            <form method="POST" action="/safety/reports/<?= (int) $report['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort denna rapport?')">
                <input type="hidden" name="_token" value="<?= \App\Core\Csrf::token() ?>">
                <button type="submit" class="rounded-lg border border-red-300 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">Ta bort</button>
            </form>
        </div>
    </div>
</div>
