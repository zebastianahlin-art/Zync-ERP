<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Frånvarokalender</h1>
        <a href="/hr/attendance" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">← Lista</a>
    </div>
    <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <div class="space-y-2">
            <?php foreach ($records as $record): ?>
            <div class="flex items-center gap-4 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                <div class="w-3 h-3 rounded-full <?= $record['record_type'] === 'vacation' ? 'bg-blue-500' : ($record['record_type'] === 'sick_leave' ? 'bg-red-500' : 'bg-yellow-500') ?>"></div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($record['employee_name'] ?? '–') ?></p>
                    <p class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars($record['record_type']) ?> · <?= htmlspecialchars($record['start_date']) ?> – <?= htmlspecialchars($record['end_date']) ?></p>
                </div>
                <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-600 dark:text-gray-300"><?= htmlspecialchars($record['status']) ?></span>
            </div>
            <?php endforeach; ?>
            <?php if (empty($records)): ?><p class="text-center text-sm text-gray-500 dark:text-gray-400 py-6">Inga frånvaroposter.</p><?php endif; ?>
        </div>
    </div>
</div>
