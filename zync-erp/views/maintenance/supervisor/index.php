<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Arbetsledar-dashboard</h1>
    </div>

    <?php if (!empty($success)): ?>
        <div class="rounded-lg bg-green-50 dark:bg-green-900/30 p-4 text-green-800 dark:text-green-200"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="rounded-lg bg-red-50 dark:bg-red-900/30 p-4 text-red-800 dark:text-red-200"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <!-- KPI-kort -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="/maintenance/supervisor/unassigned" class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 flex flex-col gap-1 hover:ring-2 hover:ring-orange-400 transition">
            <span class="text-sm text-gray-500 dark:text-gray-400">Otilldelade ordrar</span>
            <span class="text-3xl font-bold text-orange-600 dark:text-orange-400"><?= (int)($unassignedCount ?? 0) ?></span>
        </a>
        <a href="/maintenance/supervisor/pending-approval" class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 flex flex-col gap-1 hover:ring-2 hover:ring-yellow-400 transition">
            <span class="text-sm text-gray-500 dark:text-gray-400">Väntar attestering</span>
            <span class="text-3xl font-bold text-yellow-600 dark:text-yellow-400"><?= (int)($pendingApprovalCount ?? 0) ?></span>
        </a>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 flex flex-col gap-1">
            <span class="text-sm text-gray-500 dark:text-gray-400">Pågående</span>
            <span class="text-3xl font-bold text-blue-600 dark:text-blue-400"><?= (int)($stats['in_progress'] ?? 0) ?></span>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 flex flex-col gap-1">
            <span class="text-sm text-gray-500 dark:text-gray-400">Avslutade denna månad</span>
            <span class="text-3xl font-bold text-green-600 dark:text-green-400"><?= (int)($stats['closed'] ?? 0) ?></span>
        </div>
    </div>

    <!-- Snabblänkar -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
        <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-3">Snabblänkar</h2>
        <div class="flex flex-wrap gap-3">
            <a href="/maintenance/supervisor/unassigned" class="inline-flex items-center gap-2 bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-300 hover:bg-orange-100 dark:hover:bg-orange-900/40 px-4 py-2 rounded-lg text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Otilldelade ordrar
            </a>
            <a href="/maintenance/supervisor/pending-approval" class="inline-flex items-center gap-2 bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-300 hover:bg-yellow-100 dark:hover:bg-yellow-900/40 px-4 py-2 rounded-lg text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Väntar attestering
            </a>
            <a href="/maintenance/supervisor/my-team" class="inline-flex items-center gap-2 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-900/40 px-4 py-2 rounded-lg text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Mitt team
            </a>
            <a href="/maintenance/supervisor/statistics" class="inline-flex items-center gap-2 bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300 hover:bg-purple-100 dark:hover:bg-purple-900/40 px-4 py-2 rounded-lg text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Statistik
            </a>
        </div>
    </div>

    <!-- Tabeller sida vid sida -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Otilldelade arbetsordrar -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100">Otilldelade arbetsordrar</h2>
                <a href="/maintenance/supervisor/unassigned" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Visa alla</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Nummer</th>
                            <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Titel</th>
                            <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Prioritet</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <?php foreach (array_slice($unassigned ?? [], 0, 5) as $wo): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3">
                                <a href="/maintenance/work-orders/<?= (int)$wo['id'] ?>" class="text-blue-600 hover:underline font-mono text-xs"><?= htmlspecialchars($wo['order_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></a>
                            </td>
                            <td class="px-4 py-3 text-gray-900 dark:text-white"><?= htmlspecialchars($wo['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3"><?= supervisorDashPriorityBadge($wo['priority'] ?? 'normal') ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($unassigned)): ?>
                        <tr><td colspan="3" class="px-4 py-6 text-center text-gray-400 dark:text-gray-500">Inga otilldelade ordrar</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Väntar attestering -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100">Väntar attestering</h2>
                <a href="/maintenance/supervisor/pending-approval" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Visa alla</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Nummer</th>
                            <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Titel</th>
                            <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Tilldelad</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <?php foreach (array_slice($pendingApproval ?? [], 0, 5) as $wo): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3">
                                <a href="/maintenance/work-orders/<?= (int)$wo['id'] ?>" class="text-blue-600 hover:underline font-mono text-xs"><?= htmlspecialchars($wo['order_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></a>
                            </td>
                            <td class="px-4 py-3 text-gray-900 dark:text-white"><?= htmlspecialchars($wo['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($wo['assigned_to_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($pendingApproval)): ?>
                        <tr><td colspan="3" class="px-4 py-6 text-center text-gray-400 dark:text-gray-500">Inga ordrar väntar attestering</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
function supervisorDashPriorityBadge(string $p): string {
    $m = [
        'low'      => ['Låg',        'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
        'normal'   => ['Normal',     'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'],
        'high'     => ['Hög',        'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300'],
        'urgent'   => ['Brådskande', 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'],
        'critical' => ['Kritisk',    'bg-red-200 text-red-900 dark:bg-red-900/50 dark:text-red-200 font-bold'],
    ];
    $label = $m[$p][0] ?? $p;
    $class = $m[$p][1] ?? 'bg-gray-100 text-gray-700';
    return "<span class=\"inline-flex px-2 py-1 rounded-full text-xs font-medium {$class}\">" . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . "</span>";
}
?>
