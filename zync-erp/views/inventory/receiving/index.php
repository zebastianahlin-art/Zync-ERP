<?php
$statusLabels = [
    'sent'               => 'Skickad',
    'confirmed'          => 'Bekräftad',
    'partially_received' => 'Delvis mottagen',
];
$statusBadgeClasses = [
    'sent'               => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200',
    'confirmed'          => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200',
    'partially_received' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200',
];
?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Inleverans</h1>
    </div>

    <!-- Flash messages -->
    <?php if (!empty($success)): ?>
    <div class="rounded-lg bg-green-50 dark:bg-green-900/30 p-4 text-green-800 dark:text-green-200">
        <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
    <div class="rounded-lg bg-red-50 dark:bg-red-900/30 p-4 text-red-800 dark:text-red-200">
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php endif; ?>

    <!-- Info box -->
    <div class="rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-4 text-sm text-blue-800 dark:text-blue-200">
        <svg class="inline w-4 h-4 mr-1 align-text-top" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Inleveranser skapas från godkända inköpsordrar. Ordrar med status &ldquo;Skickad&rdquo;, &ldquo;Bekräftad&rdquo; eller &ldquo;Delvis mottagen&rdquo; visas här.
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">PO-nummer</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Leverantör</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Datum</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Status</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Antal rader</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($orders as $order): ?>
                    <?php $status = $order['status'] ?? ''; ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-mono text-xs font-medium text-indigo-600 dark:text-indigo-400">
                            <?= htmlspecialchars($order['order_number'] ?? ('PO-' . $order['id']), ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3 text-gray-900 dark:text-white">
                            <?= htmlspecialchars($order['supplier_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            <?= htmlspecialchars($order['order_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium <?= htmlspecialchars($statusBadgeClasses[$status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300', ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($statusLabels[$status] ?? $status, ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400">
                            <?= htmlspecialchars((string) ($order['line_count'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="/inventory/receiving/<?= htmlspecialchars((string) $order['id'], ENT_QUOTES, 'UTF-8') ?>"
                               class="inline-flex items-center gap-1 text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                                Registrera inleverans →
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($orders)): ?>
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Inga väntande inleveranser</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
