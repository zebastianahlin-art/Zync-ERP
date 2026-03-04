<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Produktionslinjer</h1>
        <a href="/production/lines/create" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">+ Ny linje</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Namn</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Kod</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($lines as $line): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                            <a href="/production/lines/<?= $line['id'] ?>" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                <?= htmlspecialchars($line['name'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-indigo-600 dark:text-indigo-400">
                            <?= htmlspecialchars($line['code'], ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded text-xs <?= $line['status'] === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' ?>">
                                <?= $line['status'] === 'active' ? 'Aktiv' : 'Inaktiv' ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="/production/lines/<?= $line['id'] ?>/edit" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline mr-3">Redigera</a>
                            <form method="POST" action="/production/lines/<?= $line['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort linjen?')">
                                <?= \App\Core\Csrf::field() ?>
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700">Ta bort</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($lines)): ?>
                    <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">Inga produktionslinjer registrerade ännu</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
