<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Hantera lager</h1>
        <a href="/production/stock/create" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">+ Ny lagerpost</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Artikel-ID</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Plats</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Antal</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Enhet</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($stock as $item): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 text-gray-900 dark:text-white"><?= htmlspecialchars((string) ($item['article_id'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($item['location'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-900 dark:text-white"><?= htmlspecialchars((string) $item['quantity'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($item['unit'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="/production/stock/<?= $item['id'] ?>/move" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline mr-3">Flytta</a>
                            <form method="POST" action="/production/stock/<?= $item['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort lagerposten?')">
                                <?= \App\Core\Csrf::field() ?>
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700">Ta bort</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($stock)): ?>
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Inga lagerposter registrerade ännu</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <a href="/production/stock" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">&larr; Lageröversikt</a>
    </div>
</div>
