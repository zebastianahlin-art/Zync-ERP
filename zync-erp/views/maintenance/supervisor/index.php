<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Arbetsledardashboard</h1>

    <!-- 4 KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Otilldelade</p>
                    <p class="text-3xl font-bold text-orange-600 mt-1"><?= (int) $unassignedCount ?></p>
                </div>
                <div class="bg-orange-100 dark:bg-orange-900/30 rounded-full p-3">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
            </div>
            <a href="/maintenance/supervisor/unassigned" class="mt-3 block text-xs text-orange-600 dark:text-orange-400 hover:underline">Tilldela →</a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Väntar attestering</p>
                    <p class="text-3xl font-bold text-blue-600 mt-1"><?= (int) $pendingApprovalCount ?></p>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900/30 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <a href="/maintenance/supervisor/pending-approval" class="mt-3 block text-xs text-blue-600 dark:text-blue-400 hover:underline">Attestera →</a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pågående</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-1"><?= (int) $inProgressCount ?></p>
                </div>
                <div class="bg-yellow-100 dark:bg-yellow-900/30 rounded-full p-3">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Stängda denna månad</p>
                    <p class="text-3xl font-bold text-green-600 mt-1"><?= (int) $closedThisMonth ?></p>
                </div>
                <div class="bg-green-100 dark:bg-green-900/30 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Unassigned work orders -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
            <div class="p-5 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="font-semibold text-gray-900 dark:text-white">Otilldelade arbetsordrar</h2>
                <a href="/maintenance/supervisor/unassigned" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Visa alla</a>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach (array_slice($unassignedWOs, 0, 5) as $wo): ?>
                <div class="px-5 py-3 flex items-center justify-between">
                    <div>
                        <a href="/maintenance/work-orders/<?= $wo['id'] ?>" class="text-sm font-medium text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400"><?= htmlspecialchars($wo['title'], ENT_QUOTES, 'UTF-8') ?></a>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"><?= htmlspecialchars($wo['order_number'], ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars($wo['machine_name'] ?? $wo['equipment_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <a href="/maintenance/work-orders/<?= $wo['id'] ?>" class="px-2 py-1 text-xs bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded hover:bg-indigo-200 transition">Öppna →</a>
                </div>
                <?php endforeach; ?>
                <?php if (empty($unassignedWOs)): ?>
                <div class="px-5 py-6 text-center text-sm text-gray-400">Inga otilldelade arbetsordrar</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Pending approval -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
            <div class="p-5 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="font-semibold text-gray-900 dark:text-white">Väntar attestering</h2>
                <a href="/maintenance/supervisor/pending-approval" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Visa alla</a>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach (array_slice($pendingApproval, 0, 5) as $wo): ?>
                <div class="px-5 py-3 flex items-center justify-between">
                    <div>
                        <a href="/maintenance/work-orders/<?= $wo['id'] ?>" class="text-sm font-medium text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400"><?= htmlspecialchars($wo['title'], ENT_QUOTES, 'UTF-8') ?></a>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"><?= htmlspecialchars($wo['order_number'], ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars($wo['assigned_to_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <a href="/maintenance/work-orders/<?= $wo['id'] ?>" class="px-2 py-1 text-xs bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded hover:bg-green-200 transition">Granska →</a>
                </div>
                <?php endforeach; ?>
                <?php if (empty($pendingApproval)): ?>
                <div class="px-5 py-6 text-center text-sm text-gray-400">Inga arbetsordrar väntar attestering</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Team workload -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
        <div class="p-5 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h2 class="font-semibold text-gray-900 dark:text-white">Teamets arbetsbelastning</h2>
            <a href="/maintenance/supervisor/my-team" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Detaljer →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Tekniker</th>
                        <th class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">Pågående</th>
                        <th class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">Utfört</th>
                        <th class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">Totalt</th>
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
                    <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">Inga aktiva tekniker</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
