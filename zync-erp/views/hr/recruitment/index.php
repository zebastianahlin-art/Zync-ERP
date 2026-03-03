<?php
/** @var string $title */
/** @var array $stats */
/** @var array $positions */
/** @var array $filter */
$statusBadge = ['draft'=>'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300','open'=>'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400','interviewing'=>'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400','offered'=>'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400','filled'=>'bg-gray-800 text-white dark:bg-gray-600','cancelled'=>'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'];
$statusLabel = ['draft'=>'Utkast','open'=>'Öppen','interviewing'=>'Intervju','offered'=>'Erbjuden','filled'=>'Tillsatt','cancelled'=>'Avbruten'];
$typeLabel = ['full_time'=>'Heltid','part_time'=>'Deltid','temporary'=>'Tillfällig','internship'=>'Praktik'];
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Rekrytering</h1>
        <div class="flex gap-2">
            <a href="/hr" class="rounded-lg bg-gray-100 dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">← HR</a>
            <a href="/hr/recruitment/create" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">+ Ny tjänst</a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-5 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">Öppna tjänster</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400"><?= $stats['open_positions'] ?></p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-5 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">Nya kandidater</p>
            <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400"><?= $stats['new_candidates'] ?></p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-5 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">Kommande intervjuer</p>
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400"><?= $stats['upcoming_interviews'] ?></p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-5 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">Anställda i år</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400"><?= $stats['hired_this_year'] ?></p>
        </div>
    </div>

    <!-- Filter -->
    <form method="get" class="flex items-center gap-3">
        <select name="status" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm px-3 py-2 text-gray-700 dark:text-gray-300">
            <option value="">Alla statusar</option>
            <?php foreach ($statusLabel as $v => $l): ?>
                <option value="<?= $v ?>" <?= ($filter['status'] ?? '') === $v ? 'selected' : '' ?>><?= $l ?></option>
            <?php endforeach ?>
        </select>
        <button class="rounded-lg bg-indigo-100 dark:bg-indigo-900/30 px-4 py-2 text-sm font-medium text-indigo-700 dark:text-indigo-400 hover:bg-indigo-200 transition-colors">Filtrera</button>
        <?php if (!empty($filter['status'])): ?>
            <a href="/hr/recruitment" class="text-sm text-gray-500 hover:text-gray-700">Rensa</a>
        <?php endif ?>
    </form>

    <!-- Positions Table -->
    <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-md">
        <?php if (empty($positions)): ?>
            <p class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">Inga rekryteringar ännu. <a href="/hr/recruitment/create" class="text-indigo-600 dark:text-indigo-400 hover:underline">Skapa den första.</a></p>
        <?php else: ?>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Tjänst</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Avdelning</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Typ</th>
                        <th class="px-6 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Kandidater</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Öppnad</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Stängs</th>
                        <th class="px-6 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Status</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php foreach ($positions as $p): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="px-6 py-4"><a href="/hr/recruitment/<?= $p['id'] ?>" class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline"><?= htmlspecialchars($p['title']) ?></a></td>
                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400"><?= htmlspecialchars($p['department_name'] ?? '–') ?></td>
                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400"><?= $typeLabel[$p['employment_type']] ?? $p['employment_type'] ?></td>
                        <td class="px-6 py-4 text-center"><span class="inline-block rounded-full bg-indigo-100 dark:bg-indigo-900/30 px-2 py-0.5 text-xs font-medium text-indigo-700 dark:text-indigo-400"><?= $p['candidate_count'] ?></span></td>
                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400"><?= $p['opening_date'] ?? '–' ?></td>
                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400"><?= $p['closing_date'] ?? '–' ?></td>
                        <td class="px-6 py-4 text-center"><span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-medium <?= $statusBadge[$p['status']] ?? $statusBadge['draft'] ?>"><?= $statusLabel[$p['status']] ?? $p['status'] ?></span></td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="/hr/recruitment/<?= $p['id'] ?>" class="text-indigo-600 dark:text-indigo-400 hover:underline">Visa</a>
                            <a href="/hr/recruitment/<?= $p['id'] ?>/edit" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">Redigera</a>
                        </td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        <?php endif ?>
    </div>
</div>
