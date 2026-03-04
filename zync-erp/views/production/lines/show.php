<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($line['name'], ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Kod: <span class="font-mono text-indigo-600 dark:text-indigo-400"><?= htmlspecialchars($line['code'], ENT_QUOTES, 'UTF-8') ?></span></p>
        </div>
        <a href="/production/lines/<?= $line['id'] ?>/edit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Redigera</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-3">
        <div>
            <span class="text-sm text-gray-500 dark:text-gray-400">Status:</span>
            <span class="ml-2 px-2 py-0.5 rounded text-xs <?= $line['status'] === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-600' ?>">
                <?= $line['status'] === 'active' ? 'Aktiv' : 'Inaktiv' ?>
            </span>
        </div>
        <?php if (!empty($line['description'])): ?>
        <div>
            <span class="text-sm text-gray-500 dark:text-gray-400">Beskrivning:</span>
            <p class="text-sm text-gray-900 dark:text-white mt-1"><?= nl2br(htmlspecialchars($line['description'], ENT_QUOTES, 'UTF-8')) ?></p>
        </div>
        <?php endif; ?>
    </div>

    <div>
        <a href="/production/lines" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">&larr; Tillbaka till linjer</a>
    </div>
</div>
