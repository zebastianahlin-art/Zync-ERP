<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Slutförda åtgärder</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Audits med status slutförd</p>
        </div>
    </div>

    <?php if ($success !== null): ?>
        <div class="rounded-lg bg-green-50 dark:bg-green-900/30 px-4 py-3 text-sm text-green-700 dark:text-green-400"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="flex flex-wrap gap-2">
        <a href="/safety/audits" class="px-3 py-1.5 rounded-lg text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Alla audits</a>
        <a href="/safety/audits/pending" class="px-3 py-1.5 rounded-lg text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Ej slutförda</a>
        <span class="px-3 py-1.5 rounded-lg text-sm bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-400 font-semibold">Slutförda</span>
    </div>

    <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-md">
        <?php if (empty($audits)): ?>
            <p class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">Inga slutförda audits hittades.</p>
        <?php else: ?>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Titel</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Plats</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Ansvarig</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Status</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Planerat datum</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Slutfört datum</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Poäng</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Åtgärder</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php foreach ($audits as $a): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100">
                                <?= htmlspecialchars($a['title'], ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($a['location'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($a['assigned_name'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full bg-green-100 dark:bg-green-900/40 px-2 py-0.5 text-xs font-medium text-green-700 dark:text-green-400"><?= htmlspecialchars($a['status'] ?? '–', ENT_QUOTES, 'UTF-8') ?></span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($a['scheduled_date'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($a['completed_date'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400"><?= $a['score'] !== null ? htmlspecialchars((string) $a['score'], ENT_QUOTES, 'UTF-8') : '–' ?></td>
                            <td class="px-6 py-4 text-right">
                                <a href="/safety/audits/<?= (int) $a['id'] ?>" class="text-indigo-600 dark:text-indigo-400 hover:underline">Visa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div>
        <a href="/safety/audits" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">&larr; Tillbaka till alla audits</a>
    </div>
</div>
