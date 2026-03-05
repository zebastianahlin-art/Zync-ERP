<?php $breadcrumbs = [['label' => 'Dashboard', 'url' => '/dashboard'], ['label' => 'Underhåll']]; ?>
<?php include dirname(__DIR__) . '/partials/breadcrumbs.php'; ?>
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Underhåll — Dashboard</h1>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Öppna felanmälningar</p>
                    <p class="text-3xl font-bold text-orange-600 mt-1"><?= (int) $openFaults ?></p>
                </div>
                <div class="bg-orange-100 dark:bg-orange-900/30 rounded-full p-3">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <a href="/maintenance/faults" class="mt-3 block text-xs text-orange-600 dark:text-orange-400 hover:underline">Visa felanmälningar →</a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Aktiva arbetsordrar</p>
                    <p class="text-3xl font-bold text-blue-600 mt-1"><?= (int) $activeWOs ?></p>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900/30 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>
            <a href="/maintenance/work-orders" class="mt-3 block text-xs text-blue-600 dark:text-blue-400 hover:underline">Visa arbetsordrar →</a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Väntar attestering</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-1"><?= (int) $pendingApproval ?></p>
                </div>
                <div class="bg-yellow-100 dark:bg-yellow-900/30 rounded-full p-3">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <a href="/maintenance/supervisor/pending-approval" class="mt-3 block text-xs text-yellow-600 dark:text-yellow-400 hover:underline">Attestera →</a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Förfallna besiktningar</p>
                    <p class="text-3xl font-bold text-red-600 mt-1"><?= (int) $overdueInspections ?></p>
                </div>
                <div class="bg-red-100 dark:bg-red-900/30 rounded-full p-3">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <a href="/maintenance/inspections/overdue" class="mt-3 block text-xs text-red-600 dark:text-red-400 hover:underline">Visa förfallna →</a>
        </div>
    </div>

    <!-- Quick links -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="/maintenance/faults/create" class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex items-center gap-3 hover:shadow-md transition group">
            <div class="bg-orange-100 dark:bg-orange-900/30 rounded-lg p-2 group-hover:bg-orange-200 dark:group-hover:bg-orange-900/50 transition">
                <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">Ny felanmälan</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Rapportera ett fel</p>
            </div>
        </a>
        <a href="/maintenance/work-orders/create" class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex items-center gap-3 hover:shadow-md transition group">
            <div class="bg-blue-100 dark:bg-blue-900/30 rounded-lg p-2 group-hover:bg-blue-200 dark:group-hover:bg-blue-900/50 transition">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">Ny arbetsorder</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Skapa en arbetsorder</p>
            </div>
        </a>
        <a href="/maintenance/inspections/create" class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex items-center gap-3 hover:shadow-md transition group">
            <div class="bg-green-100 dark:bg-green-900/30 rounded-lg p-2 group-hover:bg-green-200 dark:group-hover:bg-green-900/50 transition">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">Ny besiktning</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Registrera besiktningsobjekt</p>
            </div>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent faults -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
            <div class="p-5 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="font-semibold text-gray-900 dark:text-white">Senaste felanmälningar</h2>
                <a href="/maintenance/faults" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Visa alla</a>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($recentFaults as $f): ?>
                <div class="px-5 py-3">
                    <div class="flex items-center justify-between">
                        <a href="/maintenance/faults/<?= $f['id'] ?>" class="text-sm font-medium text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400"><?= htmlspecialchars($f['title'], ENT_QUOTES, 'UTF-8') ?></a>
                        <span class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars($f['fault_number'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"><?= htmlspecialchars($f['machine_name'] ?? $f['equipment_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($recentFaults)): ?>
                <div class="px-5 py-6 text-center text-sm text-gray-400">Inga felanmälningar</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent work orders -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
            <div class="p-5 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="font-semibold text-gray-900 dark:text-white">Senaste arbetsordrar</h2>
                <a href="/maintenance/work-orders" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Visa alla</a>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($recentWOs as $wo): ?>
                <div class="px-5 py-3">
                    <div class="flex items-center justify-between">
                        <a href="/maintenance/work-orders/<?= $wo['id'] ?>" class="text-sm font-medium text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400"><?= htmlspecialchars($wo['title'], ENT_QUOTES, 'UTF-8') ?></a>
                        <span class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars($wo['wo_number'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"><?= htmlspecialchars($wo['assigned_to_name'] ?? 'Ej tilldelad', ENT_QUOTES, 'UTF-8') ?></div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($recentWOs)): ?>
                <div class="px-5 py-6 text-center text-sm text-gray-400">Inga arbetsordrar</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
