<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Närvaro/Frånvaro</h1>
        <div class="flex gap-3">
            <a href="/hr/attendance/calendar" class="rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Kalender</a>
            <a href="/hr/attendance/create" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">+ Registrera</a>
        </div>
    </div>
    <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Anställd</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Typ</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Start</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Slut</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Åtgärder</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                <?php foreach ($records as $record): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($record['employee_name'] ?? '–') ?></td>
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($record['record_type']) ?></td>
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($record['start_date']) ?></td>
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($record['end_date']) ?></td>
                    <td class="px-6 py-4"><span class="text-xs px-2 py-0.5 rounded-full <?= $record['status'] === 'approved' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : ($record['status'] === 'rejected' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400') ?>"><?= htmlspecialchars($record['status']) ?></span></td>
                    <td class="px-6 py-4 text-right space-x-3 text-sm">
                        <a href="/hr/attendance/<?= $record['id'] ?>/edit" class="text-indigo-600 dark:text-indigo-400 hover:underline">Redigera</a>
                        <form method="post" action="/hr/attendance/<?= $record['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort?')">
                            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Ta bort</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($records)): ?><tr><td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Inga poster.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
