<div class="max-w-md space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Flytta lagerpost</h1>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-4">
        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Nuvarande plats</p>
            <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($entry['location'] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Artikel-ID</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars((string) ($entry['article_id'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Antal</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars((string) $entry['quantity'], ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars($entry['unit'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
    </div>

    <form method="POST" action="/production/stock/<?= $entry['id'] ?>/move" class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-4">
        <?= \App\Core\Csrf::field() ?>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ny plats <span class="text-red-500">*</span></label>
            <input type="text" name="new_location" required
                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                placeholder="t.ex. B-02-1">
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Flytta</button>
            <a href="/production/stock/manage" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white text-sm font-medium rounded-lg transition">Avbryt</a>
        </div>
    </form>
</div>
