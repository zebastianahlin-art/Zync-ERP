<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Saldoöversikt</h1>
        <a href="/hr/attendance" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">&larr; Närvaro</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Anställd</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">År</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400 uppercase text-xs">Semesterdagar</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400 uppercase text-xs">Använda</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400 uppercase text-xs">Sjukdagar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($balances as $b): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars(($b['first_name'] ?? '') . ' ' . ($b['last_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3"><?= (int) $b['year'] ?></td>
                        <td class="px-4 py-3 text-right"><?= htmlspecialchars((string) $b['vacation_days'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-right"><?= htmlspecialchars((string) $b['used_days'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-right"><?= htmlspecialchars((string) $b['sick_days'], ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($balances)): ?>
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Inga saldon registrerade ännu</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
