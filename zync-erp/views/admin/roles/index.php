<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Roller</h1>
        <a href="/admin/roles/create" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">+ Ny roll</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Namn</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Slug</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Nivå</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($roles as $role): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($role['name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 font-mono text-xs text-indigo-600 dark:text-indigo-400"><?= htmlspecialchars($role['slug'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300 text-xs font-medium"><?= (int) $role['level'] ?></span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="/admin/roles/<?= $role['id'] ?>/edit" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline mr-3">Redigera</a>
                            <form method="POST" action="/admin/roles/<?= $role['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort roll?')">
                                <?= \App\Core\Csrf::field() ?>
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700">Ta bort</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($roles)): ?>
                    <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">Inga roller registrerade</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <a href="/admin" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">&larr; Admin</a>
    </div>
</div>
