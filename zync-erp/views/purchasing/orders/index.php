<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Inköpsordrar</h1>
        <a href="/purchasing/orders/create" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ny order
        </a>
    </div>

    <?php if (!empty($success)): ?>
        <div class="rounded-lg bg-green-50 dark:bg-green-900/30 p-4 text-green-800 dark:text-green-200"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="rounded-lg bg-red-50 dark:bg-red-900/30 p-4 text-red-800 dark:text-red-200"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Ordernr</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Leverantör</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Inköpare</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Status</th>
                    <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400">Netto</th>
                    <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400">Moms</th>
                    <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400">Totalt</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Leverans</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Skapad</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($orders as $o): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3"><a href="/purchasing/orders/<?= $o['id'] ?>" class="text-blue-600 hover:underline font-mono text-xs"><?= htmlspecialchars($o['order_number']) ?></a></td>
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($o['supplier_name'] ?? '') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($o['buyer_name'] ?? '') ?></td>
                    <td class="px-4 py-3"><?= orderStatusBadge($o['status']) ?></td>
                    <td class="px-4 py-3 text-right font-mono text-gray-900 dark:text-white"><?= number_format((float)$o['subtotal'], 0, ',', ' ') ?></td>
                    <td class="px-4 py-3 text-right font-mono text-gray-500 dark:text-gray-400"><?= number_format((float)$o['vat_amount'], 0, ',', ' ') ?></td>
                    <td class="px-4 py-3 text-right font-mono font-semibold text-gray-900 dark:text-white"><?= number_format((float)$o['total_amount'], 0, ',', ' ') ?> kr</td>
                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400"><?= $o['delivery_date'] ? date('Y-m-d', strtotime($o['delivery_date'])) : '—' ?></td>
                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400"><?= date('Y-m-d', strtotime($o['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($orders)): ?>
                <tr><td colspan="9" class="px-4 py-8 text-center text-gray-400">Inga inköpsordrar ännu. <a href="/purchasing/orders/create" class="text-blue-600 hover:underline">Skapa den första →</a></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
function orderStatusBadge(string $s): string {
    $m = [
        'draft' => ['Utkast','bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'],
        'sent' => ['Skickad','bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'],
        'confirmed' => ['Bekräftad','bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300'],
        'partially_received' => ['Dellevererad','bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'],
        'received' => ['Mottagen','bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'],
        'invoiced' => ['Fakturerad','bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300'],
        'closed' => ['Stängd','bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
        'cancelled' => ['Avbruten','bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'],
    ];
    return '<span class="inline-flex px-2 py-1 rounded-full text-xs font-medium '.($m[$s][1]??'bg-gray-100 text-gray-700').'">'.($m[$s][0]??$s).'</span>';
}
?>
