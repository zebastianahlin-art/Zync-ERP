<?php $outStats = $outStats ?? []; $inStats = $inStats ?? []; $overdue = $overdue ?? []; ?>
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Ekonomi — Översikt</h1>
    </div>

    <!-- KPI-kort -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <p class="text-sm text-gray-500 dark:text-gray-400">Utestående kundfordringar</p>
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1"><?= number_format($outStats['total_outstanding'] ?? 0, 0, ',', ' ') ?> kr</p>
            <p class="text-xs text-gray-400 mt-1"><?= $outStats['draft_count'] ?? 0 ?> utkast</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <p class="text-sm text-gray-500 dark:text-gray-400">Förfallna kundfordringar</p>
            <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1"><?= number_format($outStats['overdue_amount'] ?? 0, 0, ',', ' ') ?> kr</p>
            <p class="text-xs text-gray-400 mt-1"><?= $outStats['overdue_count'] ?? 0 ?> fakturor</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <p class="text-sm text-gray-500 dark:text-gray-400">Leverantörsskulder</p>
            <p class="text-2xl font-bold text-orange-600 dark:text-orange-400 mt-1"><?= number_format($inStats['total_payable'] ?? 0, 0, ',', ' ') ?> kr</p>
            <p class="text-xs text-gray-400 mt-1"><?= $inStats['unapproved_count'] ?? 0 ?> ej godkända</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <p class="text-sm text-gray-500 dark:text-gray-400">Fakturerat denna månad</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1"><?= number_format($outStats['month_invoiced'] ?? 0, 0, ',', ' ') ?> kr</p>
            <p class="text-xs text-gray-400 mt-1">Mottaget: <?= number_format($inStats['month_received'] ?? 0, 0, ',', ' ') ?> kr</p>
        </div>
    </div>

    <!-- Snabblänkar -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        <a href="/finance/invoices-out" class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center hover:shadow-md transition group">
            <div class="text-2xl mb-2">📤</div>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-indigo-600">Kundfakturor</span>
        </a>
        <a href="/finance/invoices-in" class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center hover:shadow-md transition group">
            <div class="text-2xl mb-2">📥</div>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-indigo-600">Lev.fakturor</span>
        </a>
        <a href="/finance/journal" class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center hover:shadow-md transition group">
            <div class="text-2xl mb-2">📒</div>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-indigo-600">Bokföring</span>
        </a>
        <a href="/finance/accounts" class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center hover:shadow-md transition group">
            <div class="text-2xl mb-2">📊</div>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-indigo-600">Kontoplan</span>
        </a>
        <a href="/finance/cost-centers" class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center hover:shadow-md transition group">
            <div class="text-2xl mb-2">🏢</div>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-indigo-600">Kostnadsställen</span>
        </a>
        <a href="/finance/reports/ledger" class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center hover:shadow-md transition group">
            <div class="text-2xl mb-2">📖</div>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-indigo-600">Huvudbok</span>
        </a>
    </div>

    <!-- Förfallna fakturor -->
    <?php if (!empty($overdue)): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-red-600 dark:text-red-400">⚠️ Förfallna kundfakturor</h2>
        </div>
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Faktura</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Kund</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Förfallodag</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Dagar</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Kvar att betala</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($overdue as $inv): ?>
                <tr class="hover:bg-red-50 dark:hover:bg-red-900/10">
                    <td class="px-4 py-3"><a href="/finance/invoices-out/<?= $inv['id'] ?>" class="text-indigo-600 hover:underline font-mono text-sm"><?= htmlspecialchars($inv['invoice_number']) ?></a></td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300"><?= htmlspecialchars($inv['customer_name'] ?? '') ?></td>
                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400"><?= $inv['due_date'] ?></td>
                    <td class="px-4 py-3 text-sm font-bold text-red-600"><?= (int) ((time() - strtotime($inv['due_date'])) / 86400) ?> d</td>
                    <td class="px-4 py-3 text-sm text-right font-mono font-bold text-red-600"><?= number_format((float)$inv['remaining_amount'], 2, ',', ' ') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
