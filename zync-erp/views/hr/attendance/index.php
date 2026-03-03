<?php
/** @var string $title */
/** @var string $date */
/** @var array $employees */
/** @var array $records */
$existing = [];
foreach ($records as $rec) {
    $existing[$rec['employee_id']] = $rec;
}
$statusOptions = [
    'present'  => 'Närvarande',
    'absent'   => 'Frånvarande',
    'late'     => 'Sen',
    'half_day' => 'Halvdag',
    'leave'    => 'Ledig',
    'holiday'  => 'Helgdag',
];
$yesterday = date('Y-m-d', strtotime($date . ' -1 day'));
$tomorrow  = date('Y-m-d', strtotime($date . ' +1 day'));
$dayNames  = ['Söndag','Måndag','Tisdag','Onsdag','Torsdag','Fredag','Lördag'];
$monthNames = ['','januari','februari','mars','april','maj','juni','juli','augusti','september','oktober','november','december'];
$ts = strtotime($date);
$dateFormatted = $dayNames[(int)date('w', $ts)] . ' ' . (int)date('j', $ts) . ' ' . $monthNames[(int)date('n', $ts)] . ' ' . date('Y', $ts);
$activeCount = 0;
foreach ($employees as $e) {
    if (($e['status'] ?? '') === 'active' && empty($e['is_deleted'])) $activeCount++;
}
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Närvaro</h1>
        <a href="/hr" class="rounded-lg bg-gray-100 dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">← HR</a>
    </div>

    <!-- Date picker -->
    <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-4">
        <form method="get" class="flex items-center gap-3 flex-wrap">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Datum:</label>
            <input type="date" name="date" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white" value="<?= htmlspecialchars($date) ?>">
            <button class="rounded-lg bg-indigo-100 dark:bg-indigo-900/30 px-4 py-2 text-sm font-medium text-indigo-700 dark:text-indigo-400 hover:bg-indigo-200 transition-colors">Visa</button>
            <div class="flex gap-1">
                <a href="/hr/attendance?date=<?= $yesterday ?>" class="rounded-lg bg-gray-100 dark:bg-gray-700 px-3 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-200 transition-colors">←</a>
                <a href="/hr/attendance?date=<?= date('Y-m-d') ?>" class="rounded-lg bg-gray-100 dark:bg-gray-700 px-3 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-200 transition-colors">Idag</a>
                <a href="/hr/attendance?date=<?= $tomorrow ?>" class="rounded-lg bg-gray-100 dark:bg-gray-700 px-3 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-200 transition-colors">→</a>
            </div>
        </form>
    </div>

    <!-- Attendance Form -->
    <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white"><?= $dateFormatted ?></h2>
            <span class="inline-block rounded-full bg-indigo-100 dark:bg-indigo-900/30 px-3 py-0.5 text-sm font-medium text-indigo-700 dark:text-indigo-400"><?= $activeCount ?> anställda</span>
        </div>

        <form method="post" action="/hr/attendance">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars(\App\Core\Csrf::token(), ENT_QUOTES) ?>">
            <input type="hidden" name="date" value="<?= htmlspecialchars($date) ?>">

            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Anställd</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs" style="width:120px">In</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs" style="width:120px">Ut</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs" style="width:150px">Status</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Anteckning</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php foreach ($employees as $emp):
                        if (($emp['status'] ?? '') !== 'active' || !empty($emp['is_deleted'])) continue;
                        $rec = $existing[$emp['id']] ?? [];
                    ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="px-6 py-3">
                            <span class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?></span><br>
                            <span class="text-xs text-gray-500"><?= htmlspecialchars($emp['employee_number']) ?></span>
                        </td>
                        <td class="px-6 py-3">
                            <input type="time" name="attendance[<?= $emp['id'] ?>][check_in]" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm text-gray-900 dark:text-white" value="<?= htmlspecialchars($rec['check_in'] ?? '') ?>">
                        </td>
                        <td class="px-6 py-3">
                            <input type="time" name="attendance[<?= $emp['id'] ?>][check_out]" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm text-gray-900 dark:text-white" value="<?= htmlspecialchars($rec['check_out'] ?? '') ?>">
                        </td>
                        <td class="px-6 py-3">
                            <select name="attendance[<?= $emp['id'] ?>][status]" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm text-gray-900 dark:text-white">
                                <?php foreach ($statusOptions as $val => $lbl): ?>
                                    <option value="<?= $val ?>" <?= ($rec['status'] ?? 'present') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                                <?php endforeach ?>
                            </select>
                        </td>
                        <td class="px-6 py-3">
                            <input type="text" name="attendance[<?= $emp['id'] ?>][notes]" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm text-gray-900 dark:text-white" value="<?= htmlspecialchars($rec['notes'] ?? '') ?>" placeholder="Valfritt...">
                        </td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>

            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 text-right">
                <button type="submit" class="rounded-lg bg-indigo-600 px-6 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">Spara närvaro</button>
            </div>
        </form>
    </div>
</div>
