<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($project['name'], ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Projektnr: <span class="font-mono text-indigo-600 dark:text-indigo-400"><?= htmlspecialchars($project['project_number'], ENT_QUOTES, 'UTF-8') ?></span></p>
        </div>
        <a href="/projects/<?= $project['id'] ?>/edit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Redigera</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 grid grid-cols-2 gap-4 text-sm">
        <div><span class="text-gray-500 dark:text-gray-400">Status:</span> <span class="ml-1 font-medium"><?= htmlspecialchars($project['status'], ENT_QUOTES, 'UTF-8') ?></span></div>
        <div><span class="text-gray-500 dark:text-gray-400">Kund:</span> <span class="ml-1"><?= htmlspecialchars($project['customer_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
        <div><span class="text-gray-500 dark:text-gray-400">Projektledare:</span> <span class="ml-1"><?= htmlspecialchars($project['manager_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
        <div><span class="text-gray-500 dark:text-gray-400">Budget:</span> <span class="ml-1"><?= number_format((float) $project['budget'], 0, ',', ' ') ?> kr</span></div>
        <div><span class="text-gray-500 dark:text-gray-400">Start:</span> <span class="ml-1"><?= htmlspecialchars($project['start_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
        <div><span class="text-gray-500 dark:text-gray-400">Slut:</span> <span class="ml-1"><?= htmlspecialchars($project['end_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
    </div>

    <?php if (!empty($tasks)): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 font-semibold text-gray-900 dark:text-white">Uppgifter</div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Titel</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Ansvarig</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Prioritet</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($tasks as $task): ?>
                <tr>
                    <td class="px-4 py-3"><?= htmlspecialchars($task['title'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3"><?= htmlspecialchars($task['assigned_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3"><?= htmlspecialchars($task['priority'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3"><?= htmlspecialchars($task['status'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <div>
        <a href="/projects" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">&larr; Tillbaka till projekt</a>
    </div>
</div>
