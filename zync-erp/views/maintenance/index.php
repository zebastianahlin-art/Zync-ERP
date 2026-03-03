<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Underhåll – Dashboard</h1>
    </div>

    <?php if (!empty($success)): ?>
        <div class="rounded-lg bg-green-50 dark:bg-green-900/30 p-4 text-green-800 dark:text-green-200"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="rounded-lg bg-red-50 dark:bg-red-900/30 p-4 text-red-800 dark:text-red-200"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <!-- KPI-kort -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <div class="text-sm text-gray-500 dark:text-gray-400">Öppna felanmälningar</div>
            <div class="mt-1 text-3xl font-bold <?= $openFaults > 0 ? 'text-red-600' : 'text-gray-900 dark:text-white' ?>"><?= (int)$openFaults ?></div>
            <a href="/maintenance/faults" class="text-sm text-blue-600 hover:underline">Visa alla →</a>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <div class="text-sm text-gray-500 dark:text-gray-400">Aktiva arbetsordrar</div>
            <div class="mt-1 text-3xl font-bold text-blue-600"><?= (int)$activeWorkOrders ?></div>
            <a href="/maintenance/work-orders" class="text-sm text-blue-600 hover:underline">Visa alla →</a>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <div class="text-sm text-gray-500 dark:text-gray-400">Maskiner för underhåll</div>
            <div class="mt-1 text-3xl font-bold <?= $dueMachines > 0 ? 'text-orange-600' : 'text-gray-900 dark:text-white' ?>"><?= (int)$dueMachines ?></div>
            <a href="/maintenance/machines" class="text-sm text-blue-600 hover:underline">Visa alla →</a>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <div class="text-sm text-gray-500 dark:text-gray-400">Pending attestering</div>
            <div class="mt-1 text-3xl font-bold text-yellow-600"><?= (int)($stats['pending_approval'] ?? 0) ?></div>
            <a href="/maintenance/supervisor" class="text-sm text-blue-600 hover:underline">Visa alla →</a>
        </div>
    </div>

    <!-- Snabbåtgärder -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="/maintenance/faults/create" class="flex items-center gap-3 bg-white dark:bg-gray-800 rounded-xl shadow p-5 hover:ring-2 hover:ring-blue-500 transition">
            <div class="flex-shrink-0 w-10 h-10 bg-red-100 dark:bg-red-900/30 text-red-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            </div>
            <div>
                <div class="font-semibold text-gray-900 dark:text-white">Ny felanmälan</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Rapportera ett fel eller driftstopp</div>
            </div>
        </a>
        <a href="/maintenance/work-orders/create" class="flex items-center gap-3 bg-white dark:bg-gray-800 rounded-xl shadow p-5 hover:ring-2 hover:ring-blue-500 transition">
            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900/30 text-blue-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
            <div>
                <div class="font-semibold text-gray-900 dark:text-white">Ny arbetsorder</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Planera underhållsarbete</div>
            </div>
        </a>
        <a href="/maintenance/supervisor" class="flex items-center gap-3 bg-white dark:bg-gray-800 rounded-xl shadow p-5 hover:ring-2 hover:ring-blue-500 transition">
            <div class="flex-shrink-0 w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <div class="font-semibold text-gray-900 dark:text-white">Arbetsledare</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Hantera attestering och tilldelning</div>
            </div>
        </a>
    </div>

    <!-- Senaste felanmälningar -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
        <div class="p-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Senaste felanmälningar</h2>
            <a href="/maintenance/faults" class="text-sm text-blue-600 hover:underline">Visa alla</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Nummer</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Titel</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Maskin</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Prioritet</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Status</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Datum</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach (array_slice($recentFaults, 0, 10) as $f): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3"><a href="/maintenance/faults/<?= (int)$f['id'] ?>" class="text-blue-600 hover:underline font-mono text-xs"><?= htmlspecialchars($f['fault_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></a></td>
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($f['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($f['machine_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3"><?= maintenancePriorityBadge($f['priority'] ?? 'normal') ?></td>
                        <td class="px-4 py-3"><?= maintenanceStatusBadge($f['status'] ?? 'reported') ?></td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400"><?= htmlspecialchars(!empty($f['created_at']) ? date('Y-m-d', strtotime($f['created_at'])) : '—', ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($recentFaults)): ?>
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Inga felanmälningar ännu</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Aktiva arbetsordrar -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
        <div class="p-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Aktiva arbetsordrar</h2>
            <a href="/maintenance/work-orders" class="text-sm text-blue-600 hover:underline">Visa alla</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Nummer</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Titel</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Typ</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Tilldelad</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Status</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Datum</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($activeOrders as $o): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3"><a href="/maintenance/work-orders/<?= (int)$o['id'] ?>" class="text-blue-600 hover:underline font-mono text-xs"><?= htmlspecialchars($o['order_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></a></td>
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($o['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($o['work_type'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($o['assigned_to_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3"><?= maintenanceWorkOrderStatusBadge($o['status'] ?? 'open') ?></td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400"><?= htmlspecialchars(!empty($o['created_at']) ? date('Y-m-d', strtotime($o['created_at'])) : '—', ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($activeOrders)): ?>
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Inga aktiva arbetsordrar</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
function maintenancePriorityBadge(string $p): string {
    $m = [
        'low'      => ['Låg',       'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
        'normal'   => ['Normal',    'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'],
        'high'     => ['Hög',       'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300'],
        'urgent'   => ['Brådskande','bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'],
        'critical' => ['Kritisk',   'bg-red-200 text-red-900 dark:bg-red-900/50 dark:text-red-200 font-bold'],
    ];
    $label = $m[$p][0] ?? $p;
    $class = $m[$p][1] ?? 'bg-gray-100 text-gray-700';
    return "<span class=\"inline-flex px-2 py-1 rounded-full text-xs font-medium {$class}\">" . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . "</span>";
}

function maintenanceStatusBadge(string $s): string {
    $m = [
        'reported'    => ['Rapporterad', 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'],
        'acknowledged'=> ['Bekräftad',   'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'],
        'assigned'    => ['Tilldelad',   'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300'],
        'in_progress' => ['Pågår',       'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300'],
        'resolved'    => ['Löst',        'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'],
        'closed'      => ['Stängd',      'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
    ];
    $label = $m[$s][0] ?? $s;
    $class = $m[$s][1] ?? 'bg-gray-100 text-gray-700';
    return "<span class=\"inline-flex px-2 py-1 rounded-full text-xs font-medium {$class}\">" . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . "</span>";
}

function maintenanceWorkOrderStatusBadge(string $s): string {
    $m = [
        'open'        => ['Öppen',    'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'],
        'in_progress' => ['Pågår',    'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300'],
        'on_hold'     => ['Pausad',   'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'],
        'completed'   => ['Klar',     'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'],
        'cancelled'   => ['Avbruten', 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
    ];
    $label = $m[$s][0] ?? $s;
    $class = $m[$s][1] ?? 'bg-gray-100 text-gray-700';
    return "<span class=\"inline-flex px-2 py-1 rounded-full text-xs font-medium {$class}\">" . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . "</span>";
}
?>
