<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Avslutade projekt</h1>
        <a href="/projects" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">← Aktiva projekt</a>
    </div>
    <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Projektnr</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Namn</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Kund</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                <?php foreach ($projects as $project): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($project['project_number']) ?></td>
                    <td class="px-6 py-4"><a href="/projects/<?= $project['id'] ?>" class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline"><?= htmlspecialchars($project['name']) ?></a></td>
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($project['customer_name'] ?? '–') ?></td>
                    <td class="px-6 py-4"><span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400"><?= htmlspecialchars($project['status']) ?></span></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($projects)): ?><tr><td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Inga avslutade projekt.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
