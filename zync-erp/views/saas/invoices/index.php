<?php
$statusColors = [
    'draft'     => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
    'sent'      => 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400',
    'paid'      => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400',
    'overdue'   => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400',
    'cancelled' => 'bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500',
];
$statusLabels = ['draft' => 'Utkast', 'sent' => 'Skickad', 'paid' => 'Betald', 'overdue' => 'Förfallen', 'cancelled' => 'Avbruten'];
?>
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Fakturering</h1>
        <div class="flex gap-2">
            <a href="/saas-admin" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">← Dashboard</a>
            <a href="/saas-admin/invoices/create" class="rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">+ Ny faktura</a>
        </div>
    </div>

    <!-- Filter -->
    <form method="GET" action="/saas-admin/invoices" class="rounded-2xl bg-white dark:bg-gray-800 p-4 shadow-md">
        <div class="flex gap-3">
            <select name="status" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <option value="">Alla statusar</option>
                <option value="draft" <?= ($filters['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Utkast</option>
                <option value="sent" <?= ($filters['status'] ?? '') === 'sent' ? 'selected' : '' ?>>Skickad</option>
                <option value="paid" <?= ($filters['status'] ?? '') === 'paid' ? 'selected' : '' ?>>Betald</option>
                <option value="overdue" <?= ($filters['status'] ?? '') === 'overdue' ? 'selected' : '' ?>>Förfallen</option>
                <option value="cancelled" <?= ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Avbruten</option>
            </select>
            <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">Filtrera</button>
        </div>
    </form>

    <!-- Table -->
    <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Fakturanr</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Kund</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Period</th>
                    <th class="px-5 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Totalt</th>
                    <th class="px-5 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Status</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Förfallodatum</th>
                    <th class="px-5 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Åtgärder</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                <?php if (empty($invoices)): ?>
                    <tr><td colspan="7" class="px-5 py-8 text-center text-gray-500 dark:text-gray-400">Inga fakturor hittades.</td></tr>
                <?php else: ?>
                    <?php foreach ($invoices as $inv): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-5 py-3 font-mono text-sm text-gray-900 dark:text-gray-100"><?= htmlspecialchars((string) $inv['invoice_number'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-5 py-3 text-gray-900 dark:text-gray-100"><?= htmlspecialchars((string) ($inv['company_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-5 py-3 text-gray-600 dark:text-gray-400 text-xs"><?= htmlspecialchars((string) $inv['period_start'], ENT_QUOTES, 'UTF-8') ?> – <?= htmlspecialchars((string) $inv['period_end'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-5 py-3 text-right font-medium text-gray-900 dark:text-gray-100"><?= number_format((float) $inv['total'], 2, ',', ' ') ?> kr</td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium <?= $statusColors[$inv['status']] ?? '' ?>">
                                    <?= htmlspecialchars($statusLabels[$inv['status']] ?? $inv['status'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                            <td class="px-5 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars((string) $inv['due_date'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="/saas-admin/invoices/<?= (int) $inv['id'] ?>" class="rounded px-2.5 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Visa</a>
                                    <a href="/saas-admin/invoices/<?= (int) $inv['id'] ?>/edit" class="rounded px-2.5 py-1 text-xs font-medium bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 hover:bg-indigo-200 dark:hover:bg-indigo-900/50 transition-colors">Redigera</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
