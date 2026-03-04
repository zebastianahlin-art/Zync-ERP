<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Aktiva projekt</h1>
        <div class="flex gap-3">
            <a href="/projects/archive" class="rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Avslutade</a>
            <a href="/projects/create" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">+ Nytt projekt</a>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        <?php foreach ($projects as $project): ?>
        <a href="/projects/<?= $project['id'] ?>" class="block rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-400 dark:text-gray-500"><?= htmlspecialchars($project['project_number']) ?></p>
                    <h3 class="mt-0.5 font-semibold text-gray-900 dark:text-white"><?= htmlspecialchars($project['name']) ?></h3>
                </div>
                <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400"><?= htmlspecialchars($project['status']) ?></span>
            </div>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($project['customer_name'] ?? '–') ?></p>
            <?php if ($project['start_date'] || $project['end_date']): ?>
            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500"><?= htmlspecialchars($project['start_date'] ?? '?') ?> – <?= htmlspecialchars($project['end_date'] ?? '?') ?></p>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>
        <?php if (empty($projects)): ?>
        <div class="col-span-3 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-10 text-center text-sm text-gray-500 dark:text-gray-400">Inga aktiva projekt.</div>
        <?php endif; ?>
    </div>
</div>
