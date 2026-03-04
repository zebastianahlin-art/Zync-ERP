<div class="max-w-4xl space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="/hr/recruitment" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700">← Tillbaka</a>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"><?= htmlspecialchars($position['title']) ?></h1>
            <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400"><?= htmlspecialchars($position['status']) ?></span>
        </div>
        <a href="/hr/recruitment/positions/<?= $position['id'] ?>/edit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Redigera</a>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 shadow-sm space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Avdelning</p><p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($position['department_name'] ?? '–') ?></p></div>
                <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Deadline</p><p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($position['deadline'] ?? '–') ?></p></div>
            </div>
            <?php if ($position['description']): ?><div><p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Beskrivning</p><p class="mt-1 text-sm text-gray-900 dark:text-white"><?= nl2br(htmlspecialchars($position['description'])) ?></p></div><?php endif; ?>
            <?php if ($position['requirements']): ?><div class="pt-2 border-t border-gray-100 dark:border-gray-700"><p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Krav</p><p class="mt-1 text-sm text-gray-900 dark:text-white"><?= nl2br(htmlspecialchars($position['requirements'])) ?></p></div><?php endif; ?>
        </div>
        <div class="space-y-4">
            <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
                <p class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Snabblänkar</p>
                <form method="post" action="/hr/recruitment/positions/<?= $position['id'] ?>/delete" onsubmit="return confirm('Ta bort?')">
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                    <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:underline">Ta bort tjänst</button>
                </form>
            </div>
        </div>
    </div>
    <!-- Applicants -->
    <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between">
            <h2 class="font-semibold text-gray-900 dark:text-white">Sökande (<?= count($applicants) ?>)</h2>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            <?php foreach ($applicants as $a): ?>
            <div class="flex items-center justify-between px-5 py-3">
                <div>
                    <a href="/hr/recruitment/applicants/<?= $a['id'] ?>" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline"><?= htmlspecialchars($a['name']) ?></a>
                    <p class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars($a['email']) ?></p>
                </div>
                <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300"><?= htmlspecialchars($a['status']) ?></span>
            </div>
            <?php endforeach; ?>
            <?php if (empty($applicants)): ?><p class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">Inga sökande.</p><?php endif; ?>
        </div>
        <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700">
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Registrera sökande</h3>
            <form method="post" action="/hr/recruitment/positions/<?= $position['id'] ?>/applicants" class="space-y-3">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <div class="grid grid-cols-2 gap-3">
                    <input type="text" name="name" placeholder="Namn" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none">
                    <input type="email" name="email" placeholder="E-post" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Lägg till sökande</button>
            </form>
        </div>
    </div>
</div>
