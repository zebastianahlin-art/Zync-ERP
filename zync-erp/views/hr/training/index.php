<?php
/** @var string $title */
/** @var array $stats */
/** @var array $courses */
/** @var array $overdue */
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Utbildning</h1>
        <div class="flex gap-2">
            <a href="/hr" class="rounded-lg bg-gray-100 dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">← HR</a>
            <a href="/hr/training/create" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">+ Ny utbildning</a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-5 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">Aktiva kurser</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $stats['active_courses'] ?></p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-5 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">Kommande tillfällen</p>
            <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400"><?= $stats['upcoming_sessions'] ?></p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-5 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">Genomförda i år</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400"><?= $stats['completed_this_year'] ?></p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-5 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">Förfallna obligatoriska</p>
            <p class="text-2xl font-bold <?= $stats['overdue_count'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' ?>"><?= $stats['overdue_count'] ?></p>
        </div>
    </div>

    <!-- Overdue Alert -->
    <?php if (!empty($overdue)): ?>
    <div class="rounded-2xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 px-6 py-4">
        <div class="flex items-start gap-3">
            <span class="text-red-500 text-xl mt-0.5">⚠</span>
            <div>
                <p class="font-semibold text-red-700 dark:text-red-400"><?= count($overdue) ?> förfallna obligatoriska utbildningar</p>
                <ul class="mt-2 space-y-1 text-sm text-red-600 dark:text-red-400">
                    <?php foreach (array_slice($overdue, 0, 5) as $o): ?>
                        <li>
                            <span class="font-medium"><?= htmlspecialchars($o['first_name'] . ' ' . $o['last_name']) ?></span> – <?= htmlspecialchars($o['course_name']) ?>
                            <?php if ($o['due_date']): ?>
                                <span class="text-red-400 dark:text-red-500">(förföll <?= $o['due_date'] ?>)</span>
                            <?php else: ?>
                                <span class="text-red-400 dark:text-red-500">(aldrig genomförd)</span>
                            <?php endif ?>
                        </li>
                    <?php endforeach ?>
                    <?php if (count($overdue) > 5): ?>
                        <li class="text-red-400">...och <?= count($overdue) - 5 ?> till</li>
                    <?php endif ?>
                </ul>
            </div>
        </div>
    </div>
    <?php endif ?>

    <!-- Courses Table -->
    <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-md">
        <?php if (empty($courses)): ?>
            <p class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">Inga utbildningar skapade ännu. <a href="/hr/training/create" class="text-indigo-600 dark:text-indigo-400 hover:underline">Skapa den första.</a></p>
        <?php else: ?>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Kurs</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Leverantör</th>
                        <th class="px-6 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Timmar</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Kostnad</th>
                        <th class="px-6 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Tillfällen</th>
                        <th class="px-6 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Genomförda</th>
                        <th class="px-6 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Obligatorisk</th>
                        <th class="px-6 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Status</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php foreach ($courses as $c): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="px-6 py-4"><a href="/hr/training/<?= $c['id'] ?>" class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline"><?= htmlspecialchars($c['name']) ?></a></td>
                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400"><?= htmlspecialchars($c['provider'] ?? '–') ?></td>
                        <td class="px-6 py-4 text-center text-gray-700 dark:text-gray-300"><?= $c['duration_hours'] ? $c['duration_hours'] . 'h' : '–' ?></td>
                        <td class="px-6 py-4 text-right text-gray-700 dark:text-gray-300"><?= $c['cost'] ? number_format((float)$c['cost'], 0, ',', ' ') . ' kr' : '–' ?></td>
                        <td class="px-6 py-4 text-center text-gray-700 dark:text-gray-300"><?= $c['session_count'] ?></td>
                        <td class="px-6 py-4 text-center text-gray-700 dark:text-gray-300"><?= $c['completed_count'] ?></td>
                        <td class="px-6 py-4 text-center">
                            <?php if ($c['is_mandatory']): ?>
                                <span class="inline-block rounded-full bg-red-100 dark:bg-red-900/30 px-2 py-0.5 text-xs font-medium text-red-700 dark:text-red-400">Ja</span>
                                <?php if ($c['recurrence_months']): ?><br><span class="text-xs text-gray-400">var <?= $c['recurrence_months'] ?> mån</span><?php endif ?>
                            <?php else: ?>
                                <span class="text-gray-400">–</span>
                            <?php endif ?>
                        </td>
                        <td class="px-6 py-4 text-center"><span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-medium <?= $c['status'] === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' ?>"><?= $c['status'] === 'active' ? 'Aktiv' : 'Inaktiv' ?></span></td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="/hr/training/<?= $c['id'] ?>" class="text-indigo-600 dark:text-indigo-400 hover:underline">Visa</a>
                            <a href="/hr/training/<?= $c['id'] ?>/edit" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">Redigera</a>
                        </td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        <?php endif ?>
    </div>
</div>
