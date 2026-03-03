<div class="mx-auto max-w-2xl space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100"><?= htmlspecialchars($procedure['title'], ENT_QUOTES, 'UTF-8') ?></h1>
        <a href="/safety/emergency/procedures" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">&larr; Tillbaka</a>
    </div>
    <div class="rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-md space-y-4">
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><span class="font-medium text-gray-600 dark:text-gray-400">Kategori:</span> <span class="text-gray-900 dark:text-gray-100"><?= htmlspecialchars($procedure['category'] ?? '–', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="font-medium text-gray-600 dark:text-gray-400">Ansvarig:</span> <span class="text-gray-900 dark:text-gray-100"><?= htmlspecialchars($procedure['responsible'] ?? '–', ENT_QUOTES, 'UTF-8') ?></span></div>
            <?php if (!empty($procedure['location'])): ?>
            <div><span class="font-medium text-gray-600 dark:text-gray-400">Plats:</span> <span class="text-gray-900 dark:text-gray-100"><?= htmlspecialchars($procedure['location'], ENT_QUOTES, 'UTF-8') ?></span></div>
            <?php endif; ?>
            <?php if (!empty($procedure['last_reviewed'])): ?>
            <div><span class="font-medium text-gray-600 dark:text-gray-400">Senast granskad:</span> <span class="text-gray-900 dark:text-gray-100"><?= htmlspecialchars($procedure['last_reviewed'], ENT_QUOTES, 'UTF-8') ?></span></div>
            <?php endif; ?>
            <div><span class="font-medium text-gray-600 dark:text-gray-400">Aktiv:</span>
                <?php if (!empty($procedure['is_active'])): ?>
                    <span class="inline-flex items-center rounded-full bg-green-100 dark:bg-green-900/40 px-2 py-0.5 text-xs font-medium text-green-700 dark:text-green-400">Ja</span>
                <?php else: ?>
                    <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-700 px-2 py-0.5 text-xs font-medium text-gray-500 dark:text-gray-400">Nej</span>
                <?php endif; ?>
            </div>
        </div>
        <?php if (!empty($procedure['description'])): ?>
            <div><p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Beskrivning</p><p class="text-sm text-gray-900 dark:text-gray-100"><?= nl2br(htmlspecialchars($procedure['description'], ENT_QUOTES, 'UTF-8')) ?></p></div>
        <?php endif; ?>
        <?php if (!empty($procedure['steps'])): ?>
            <div><p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Steg att följa</p><p class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-line"><?= nl2br(htmlspecialchars($procedure['steps'], ENT_QUOTES, 'UTF-8')) ?></p></div>
        <?php endif; ?>
        <div class="flex items-center space-x-3 pt-2">
            <a href="/safety/emergency/procedures/<?= (int) $procedure['id'] ?>/edit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">Redigera</a>
            <form method="POST" action="/safety/emergency/procedures/<?= (int) $procedure['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort denna procedur?')">
                <input type="hidden" name="_token" value="<?= \App\Core\Csrf::token() ?>">
                <button type="submit" class="rounded-lg border border-red-300 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">Ta bort</button>
            </form>
        </div>
    </div>
</div>
