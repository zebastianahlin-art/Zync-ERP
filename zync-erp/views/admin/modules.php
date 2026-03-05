<div class="space-y-6">

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Moduladministration</h1>
        <a href="/admin" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">← Admin</a>
    </div>

    <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Modul</th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Beskrivning</th>
                    <th class="px-6 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Version</th>
                    <th class="px-6 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Status</th>
                    <th class="px-6 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Åtgärd</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                <?php foreach ($modules as $mod): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-3 font-medium text-gray-900 dark:text-gray-100">
                            <?= htmlspecialchars((string) $mod['name'], ENT_QUOTES, 'UTF-8') ?>
                            <span class="ml-1 text-xs text-gray-400 dark:text-gray-500">(<?= htmlspecialchars((string) $mod['slug'], ENT_QUOTES, 'UTF-8') ?>)</span>
                        </td>
                        <td class="px-6 py-3 text-gray-600 dark:text-gray-400 max-w-xs truncate">
                            <?= htmlspecialchars((string) ($mod['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-6 py-3 text-center text-gray-600 dark:text-gray-400">
                            <?= htmlspecialchars((string) $mod['version'], ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-6 py-3 text-center">
                            <?php if ($mod['is_active']): ?>
                                <span class="inline-flex items-center rounded-full bg-green-100 dark:bg-green-900/30 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:text-green-400">Aktiv</span>
                            <?php else: ?>
                                <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-700 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:text-gray-400">Inaktiv</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-3 text-right">
                            <form method="POST" action="/admin/modules/<?= (int) $mod['id'] ?>/toggle" class="inline">
                                <?= \App\Core\Csrf::field() ?>
                                <button type="submit"
                                        class="rounded-lg px-3 py-1.5 text-xs font-medium transition-colors <?= $mod['is_active'] ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-900/50' : 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 hover:bg-green-200 dark:hover:bg-green-900/50' ?>">
                                    <?= $mod['is_active'] ? 'Inaktivera' : 'Aktivera' ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>
