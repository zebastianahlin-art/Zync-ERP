<?php $invoices = $invoices ?? []; $statusLabels = ['registered'=>'Registrerad','approved'=>'Godkänd','payment_pending'=>'Betalning pågår','paid'=>'Betald','disputed'=>'Tvist','credited'=>'Krediterad','cancelled'=>'Annullerad']; $statusColors = ['registered'=>'yellow','approved'=>'blue','payment_pending'=>'orange','paid'=>'green','disputed'=>'red','credited'=>'purple','cancelled'=>'gray']; ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Inkommande fakturor</h1>
        <div class="flex gap-3">
            <a href="/finance" class="text-sm text-gray-500 hover:text-indigo-600">← Ekonomi</a>
            <a href="/finance/invoices-in/create" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">+ Registrera faktura</a>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Internt nr</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Lev.fakturanr</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Leverantör</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Datum</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Förfaller</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Belopp</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Kvar</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($invoices as $inv): $color = $statusColors[$inv['status']] ?? 'gray'; ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer" onclick="location.href='/finance/invoices-in/<?= $inv['id'] ?>'">
                    <td class="px-4 py-3 font-mono text-sm text-indigo-600 dark:text-indigo-400 font-medium"><?= htmlspecialchars($inv['internal_number']) ?></td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300"><?= htmlspecialchars($inv['invoice_number']) ?></td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300"><?= htmlspecialchars($inv['supplier_name'] ?? '') ?></td>
                    <td class="px-4 py-3 text-sm text-gray-600"><?= $inv['invoice_date'] ?></td>
                    <td class="px-4 py-3 text-sm text-gray-600 <?= strtotime($inv['due_date']) < time() && !in_array($inv['status'], ['paid','cancelled','credited']) ? 'text-red-600 font-bold' : '' ?>"><?= $inv['due_date'] ?></td>
                    <td class="px-4 py-3 text-sm text-right font-mono"><?= number_format((float)$inv['total_amount'], 2, ',', ' ') ?></td>
                    <td class="px-4 py-3 text-sm text-right font-mono <?= (float)$inv['remaining_amount'] > 0 ? 'text-orange-600 font-bold' : 'text-green-600' ?>"><?= number_format((float)$inv['remaining_amount'], 2, ',', ' ') ?></td>
                    <td class="px-4 py-3 text-center"><span class="px-2 py-1 rounded-full text-xs font-medium bg-<?= $color ?>-100 text-<?= $color ?>-800 dark:bg-<?= $color ?>-900/30 dark:text-<?= $color ?>-400"><?= $statusLabels[$inv['status']] ?? $inv['status'] ?></span></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($invoices)): ?>
                <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">Inga leverantörsfakturor ännu</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
