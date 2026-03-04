<?php $groups = $groups ?? []; ?>
<div class="space-y-6">
    <?php if (!empty($success)): ?>
    <div class="rounded-lg bg-green-50 dark:bg-green-900/30 px-4 py-3 text-sm text-green-700 dark:text-green-400"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kontoplansgrupper</h1>
        <a href="/finance/account-groups/create" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm transition">+ Ny grupp</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-500">Kod</th>
                    <th class="px-4 py-3 text-left text-gray-500">Namn</th>
                    <th class="px-4 py-3 text-left text-gray-500">Förälder</th>
                    <th class="px-4 py-3 text-left text-gray-500">Sortering</th>
                    <th class="px-4 py-3 text-right text-gray-500">Åtgärder</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php if (empty($groups)): ?>
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Inga grupper skapade</td></tr>
                <?php else: ?>
                <?php foreach ($groups as $g): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                    <td class="px-4 py-3 font-mono font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($g['code']) ?></td>
                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300"><?= htmlspecialchars($g['name']) ?></td>
                    <td class="px-4 py-3 text-gray-500"><?= htmlspecialchars($g['parent_name'] ?? '—') ?></td>
                    <td class="px-4 py-3 text-gray-500"><?= (int) $g['sort_order'] ?></td>
                    <td class="px-4 py-3 text-right flex justify-end gap-2">
                        <a href="/finance/account-groups/<?= $g['id'] ?>/edit" class="text-indigo-600 hover:underline text-xs">Redigera</a>
                        <form method="POST" action="/finance/account-groups/<?= $g['id'] ?>/delete" onsubmit="return confirm('Ta bort grupp?')">
                            <?= \App\Core\Csrf::field() ?>
                            <button class="text-red-500 hover:underline text-xs">Ta bort</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
