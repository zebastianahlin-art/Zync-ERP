<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Inköp</h1>
    </div>

    <?php if (!empty($success)): ?>
        <div class="rounded-lg bg-green-50 dark:bg-green-900/30 p-4 text-green-800 dark:text-green-200"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- KPI-kort -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <div class="text-sm text-gray-500 dark:text-gray-400">Väntande anmodan</div>
            <div class="mt-1 text-3xl font-bold text-yellow-600"><?= $pendingReqs ?></div>
            <a href="/purchasing/requisitions" class="text-sm text-blue-600 hover:underline">Visa alla →</a>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <div class="text-sm text-gray-500 dark:text-gray-400">Aktiva ordrar</div>
            <div class="mt-1 text-3xl font-bold text-blue-600"><?= $activeOrders ?></div>
            <a href="/purchasing/orders" class="text-sm text-blue-600 hover:underline">Visa alla →</a>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <div class="text-sm text-gray-500 dark:text-gray-400">Aktiva avtal</div>
            <div class="mt-1 text-3xl font-bold text-green-600"><?= $activeAgreements ?></div>
            <a href="/purchasing/agreements" class="text-sm text-blue-600 hover:underline">Visa alla →</a>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <div class="text-sm text-gray-500 dark:text-gray-400">Totalt ordervärde</div>
            <div class="mt-1 text-3xl font-bold text-gray-900 dark:text-white"><?= number_format($totalOrderValue, 0, ',', ' ') ?> kr</div>
        </div>
    </div>

    <!-- Snabblänkar -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="/purchasing/requisitions/create" class="flex items-center gap-3 bg-white dark:bg-gray-800 rounded-xl shadow p-5 hover:ring-2 hover:ring-blue-500 transition">
            <div class="flex-shrink-0 w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <div class="font-semibold text-gray-900 dark:text-white">Ny inköpsanmodan</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Begär inköp av material</div>
            </div>
        </a>
        <a href="/purchasing/orders/create" class="flex items-center gap-3 bg-white dark:bg-gray-800 rounded-xl shadow p-5 hover:ring-2 hover:ring-blue-500 transition">
            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900/30 text-blue-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
            </div>
            <div>
                <div class="font-semibold text-gray-900 dark:text-white">Ny inköpsorder</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Skapa order till leverantör</div>
            </div>
        </a>
        <a href="/purchasing/agreements/create" class="flex items-center gap-3 bg-white dark:bg-gray-800 rounded-xl shadow p-5 hover:ring-2 hover:ring-blue-500 transition">
            <div class="flex-shrink-0 w-10 h-10 bg-green-100 dark:bg-green-900/30 text-green-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div>
                <div class="font-semibold text-gray-900 dark:text-white">Nytt avtal</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Skapa ramavtal eller kontrakt</div>
            </div>
        </a>
    </div>

    <!-- Utgående avtal varning -->
    <?php if (!empty($expiring)): ?>
    <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-xl p-5">
        <h3 class="font-semibold text-orange-800 dark:text-orange-200 mb-3">⚠️ Avtal som går ut inom 30 dagar</h3>
        <div class="space-y-2">
            <?php foreach ($expiring as $a): ?>
            <a href="/purchasing/agreements/<?= $a['id'] ?>" class="flex justify-between items-center p-2 rounded hover:bg-orange-100 dark:hover:bg-orange-900/40">
                <span class="text-gray-900 dark:text-white"><?= htmlspecialchars($a['title']) ?> — <?= htmlspecialchars($a['supplier_name']) ?></span>
                <span class="text-sm text-orange-600 font-medium"><?= $a['end_date'] ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Senaste anmodan -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
        <div class="p-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Senaste inköpsanmodan</h2>
            <a href="/purchasing/requisitions" class="text-sm text-blue-600 hover:underline">Visa alla</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Nummer</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Titel</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Begärd av</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Status</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400">Belopp</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Datum</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach (array_slice($requisitions, 0, 5) as $r): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3"><a href="/purchasing/requisitions/<?= $r['id'] ?>" class="text-blue-600 hover:underline font-mono"><?= htmlspecialchars($r['requisition_number']) ?></a></td>
                        <td class="px-4 py-3 text-gray-900 dark:text-white"><?= htmlspecialchars($r['title']) ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($r['requested_by_name'] ?? '') ?></td>
                        <td class="px-4 py-3"><?= requisitionStatusBadge($r['status']) ?></td>
                        <td class="px-4 py-3 text-right text-gray-900 dark:text-white"><?= number_format((float)$r['total_amount'], 0, ',', ' ') ?> kr</td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400"><?= date('Y-m-d', strtotime($r['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($requisitions)): ?>
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Inga anmodan ännu</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
function requisitionStatusBadge(string $status): string {
    $map = [
        'draft' => ['Utkast', 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'],
        'pending_approval' => ['Väntar godkännande', 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'],
        'approved' => ['Godkänd', 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'],
        'rejected' => ['Avvisad', 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'],
        'ordered' => ['Beställd', 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'],
        'closed' => ['Stängd', 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
    ];
    $label = $map[$status][0] ?? $status;
    $class = $map[$status][1] ?? 'bg-gray-100 text-gray-700';
    return "<span class=\"inline-flex px-2 py-1 rounded-full text-xs font-medium {$class}\">{$label}</span>";
}
?>
