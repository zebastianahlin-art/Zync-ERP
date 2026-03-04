<div class="max-w-4xl space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="/projects" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700">← Tillbaka</a>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"><?= htmlspecialchars($project['name']) ?></h1>
            <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400"><?= htmlspecialchars($project['status']) ?></span>
        </div>
        <a href="/projects/<?= $project['id'] ?>/edit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Redigera</a>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 shadow-sm space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Projektnr</p><p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($project['project_number']) ?></p></div>
                <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Kund</p><p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($project['customer_name'] ?? '–') ?></p></div>
                <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Avdelning</p><p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($project['department_name'] ?? '–') ?></p></div>
                <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Projektledare</p><p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($project['manager_name'] ?? '–') ?></p></div>
                <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Start</p><p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($project['start_date'] ?? '–') ?></p></div>
                <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Slut</p><p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($project['end_date'] ?? '–') ?></p></div>
                <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Budget</p><p class="mt-1 text-sm text-gray-900 dark:text-white"><?= $project['budget_amount'] ? number_format((float)$project['budget_amount'], 2) . ' SEK' : '–' ?></p></div>
            </div>
            <?php if ($project['description']): ?>
            <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Beskrivning</p>
                <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= nl2br(htmlspecialchars($project['description'])) ?></p>
            </div>
            <?php endif; ?>
        </div>
        <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-2">Snabblänkar</p>
            <div class="space-y-2">
                <form method="post" action="/projects/<?= $project['id'] ?>/delete" onsubmit="return confirm('Ta bort projekt?')">
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                    <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:underline">Ta bort projekt</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Tasks -->
    <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="font-semibold text-gray-900 dark:text-white">Uppgifter</h2>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            <?php foreach ($tasks as $task): ?>
            <div class="flex items-center justify-between px-5 py-3">
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($task['title']) ?></p>
                    <p class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars($task['assigned_name'] ?? '–') ?> · <?= htmlspecialchars($task['status']) ?></p>
                </div>
                <form method="post" action="/projects/<?= $project['id'] ?>/tasks/<?= $task['id'] ?>/delete" onsubmit="return confirm('Ta bort uppgift?')">
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                    <button type="submit" class="text-xs text-red-600 dark:text-red-400 hover:underline">Ta bort</button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700">
            <form method="post" action="/projects/<?= $project['id'] ?>/tasks" class="flex gap-2">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <input type="text" name="title" placeholder="Ny uppgift..." class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none">
                <select name="assigned_to" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none">
                    <option value="">– Tilldela –</option>
                    <?php foreach ($users as $u): ?><option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['full_name']) ?></option><?php endforeach; ?>
                </select>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Lägg till</button>
            </form>
        </div>
    </div>
</div>
