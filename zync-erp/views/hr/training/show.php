<div class="max-w-3xl space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="/hr/training" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700">← Tillbaka</a>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"><?= htmlspecialchars($course['name']) ?></h1>
        </div>
        <a href="/hr/training/<?= $course['id'] ?>/edit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Redigera</a>
    </div>
    <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 shadow-sm space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Leverantör</p><p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($course['provider'] ?? '–') ?></p></div>
            <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Kategori</p><p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($course['category'] ?? '–') ?></p></div>
            <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Timmar</p><p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($course['duration_hours'] ?? '–') ?></p></div>
            <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Återkommande</p><p class="mt-1 text-sm text-gray-900 dark:text-white"><?= $course['is_recurring'] ? 'Ja' : 'Nej' ?></p></div>
        </div>
        <?php if ($course['description']): ?><div class="pt-2 border-t border-gray-100 dark:border-gray-700"><p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Beskrivning</p><p class="mt-1 text-sm text-gray-900 dark:text-white"><?= nl2br(htmlspecialchars($course['description'])) ?></p></div><?php endif; ?>
    </div>
</div>
