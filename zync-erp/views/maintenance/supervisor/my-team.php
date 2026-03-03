<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="/maintenance/supervisor" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mitt team</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Tekniker</th>
                        <th class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">Pågående</th>
                        <th class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">Utfört</th>
                        <th class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">Totalt aktiva</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400">Tim. loggade</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($teamStats as $t): ?>
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($t['full_name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-center">
                            <?php if ($t['in_progress'] > 0): ?>
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300"><?= (int) $t['in_progress'] ?></span>
                            <?php else: ?>
                            <span class="text-gray-400">0</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400"><?= (int) $t['completed'] ?></td>
                        <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400"><?= (int) $t['total_orders'] ?></td>
                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400"><?= number_format((float)$t['total_hours'], 1) ?> h</td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($teamStats)): ?>
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Inga aktiva tekniker</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
