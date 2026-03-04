<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($position['title'], ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                <?= htmlspecialchars($position['department_name'] ?? 'Ingen avdelning', ENT_QUOTES, 'UTF-8') ?> &middot; Status: <?= htmlspecialchars($position['status'], ENT_QUOTES, 'UTF-8') ?>
            </p>
        </div>
    </div>

    <?php if (!empty($position['description'])): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h2 class="font-semibold text-gray-900 dark:text-white mb-2">Beskrivning</h2>
        <p class="text-sm text-gray-700 dark:text-gray-300"><?= nl2br(htmlspecialchars($position['description'], ENT_QUOTES, 'UTF-8')) ?></p>
    </div>
    <?php endif; ?>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 font-semibold text-gray-900 dark:text-white">Sökande (<?= count($applicants) ?>)</div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Namn</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">E-post</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Ansökt</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($applicants as $a): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($a['first_name'] . ' ' . $a['last_name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($a['email'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3"><?= htmlspecialchars($a['applied_at'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3"><?= htmlspecialchars($a['status'], ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($applicants)): ?>
                    <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">Inga sökande ännu</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <a href="/hr/recruitment" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">&larr; Tillbaka</a>
    </div>
</div>
