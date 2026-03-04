<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Produktionslinjer</h1>
        <a href="/production/lines/create" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">+ Ny linje</a>
    </div>
    <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Namn</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Kapacitet</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Åtgärder</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                <?php foreach ($items as $item): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-6 py-4">
                        <a href="/production/lines/<?= $item['id'] ?>" class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline"><?= htmlspecialchars($item['name']) ?></a>
                        <?php if ($item['description']): ?>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"><?= htmlspecialchars(substr($item['description'], 0, 60)) ?></p>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300"><?= htmlspecialchars($item['capacity'] ?? '–') ?></td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $item['status'] === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : ($item['status'] === 'maintenance' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400') ?>">
                            <?= htmlspecialchars($item['status']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right space-x-3 text-sm">
                        <a href="/production/lines/<?= $item['id'] ?>/edit" class="text-indigo-600 dark:text-indigo-400 hover:underline">Redigera</a>
                        <form method="post" action="/production/lines/<?= $item['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort denna linje?')">
                            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Ta bort</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($items)): ?>
                <tr><td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Inga produktionslinjer hittades.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
