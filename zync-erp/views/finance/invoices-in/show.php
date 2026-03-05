<?php
$invoice = $invoice ?? []; $lines = $lines ?? []; $articles = $articles ?? []; $accounts = $accounts ?? []; $costCenters = $costCenters ?? [];
$statusLabels = ['registered'=>'Registrerad','approved'=>'Godkänd','payment_pending'=>'Betalning pågår','paid'=>'Betald','disputed'=>'Tvist','credited'=>'Krediterad','cancelled'=>'Annullerad'];
$statusColors = ['registered'=>'yellow','approved'=>'blue','payment_pending'=>'orange','paid'=>'green','disputed'=>'red','credited'=>'purple','cancelled'=>'gray'];
$color = $statusColors[$invoice['status']] ?? 'gray';
?>
<div class="space-y-6">
    <?php if (!empty($success)): ?><div class="rounded-lg bg-green-50 dark:bg-green-900/30 px-4 py-3 text-sm text-green-700 dark:text-green-400"><?= htmlspecialchars($success) ?></div><?php endif; ?>

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($invoice['internal_number']) ?></h1>
            <p class="text-sm text-gray-500">Lev.fakturanr: <?= htmlspecialchars($invoice['invoice_number']) ?></p>
            <span class="px-3 py-1 rounded-full text-xs font-medium bg-<?= $color ?>-100 text-<?= $color ?>-800 dark:bg-<?= $color ?>-900/30 dark:text-<?= $color ?>-400"><?= $statusLabels[$invoice['status']] ?? $invoice['status'] ?></span>
        </div>
        <div class="flex gap-2">
            <a href="/finance/invoices-in" class="text-sm text-gray-500 hover:text-indigo-600">← Tillbaka</a>
            <?php if ($invoice['status'] === 'registered'): ?>
            <a href="/finance/invoices-in/<?= $invoice['id'] ?>/edit" class="bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded text-sm hover:bg-gray-200 transition">Redigera</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
        <div><span class="text-gray-500">Leverantör</span><p class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($invoice['supplier_name'] ?? '') ?></p></div>
        <div><span class="text-gray-500">Fakturadatum</span><p class="font-medium"><?= $invoice['invoice_date'] ?></p></div>
        <div><span class="text-gray-500">Förfaller</span><p class="font-medium <?= strtotime($invoice['due_date']) < time() && !in_array($invoice['status'], ['paid','cancelled']) ? 'text-red-600' : '' ?>"><?= $invoice['due_date'] ?></p></div>
        <div><span class="text-gray-500">Inköpsorder</span><p class="font-medium"><?= htmlspecialchars($invoice['po_number'] ?? '—') ?></p></div>
        <div><span class="text-gray-500">Godkänd av</span><p class="font-medium"><?= htmlspecialchars($invoice['approved_by_name'] ?? '—') ?></p></div>
        <div><span class="text-gray-500">Referens</span><p class="font-medium"><?= htmlspecialchars($invoice['reference'] ?? '—') ?></p></div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
        <div><span class="text-gray-500">Netto</span><p class="text-lg font-bold font-mono"><?= number_format((float)$invoice['subtotal'], 2, ',', ' ') ?></p></div>
        <div><span class="text-gray-500">Moms</span><p class="text-lg font-bold font-mono"><?= number_format((float)$invoice['vat_amount'], 2, ',', ' ') ?></p></div>
        <div><span class="text-gray-500">Totalt</span><p class="text-lg font-bold font-mono text-gray-900 dark:text-white"><?= number_format((float)$invoice['total_amount'], 2, ',', ' ') ?></p></div>
        <div><span class="text-gray-500">Kvar att betala</span><p class="text-lg font-bold font-mono <?= (float)$invoice['remaining_amount'] > 0 ? 'text-orange-600' : 'text-green-600' ?>"><?= number_format((float)$invoice['remaining_amount'], 2, ',', ' ') ?></p></div>
    </div>

    <!-- Status -->
    <?php if (in_array($invoice['status'], ['registered','approved','payment_pending'])): ?>
    <div class="flex gap-2 flex-wrap">
        <?php if ($invoice['status'] === 'registered'): ?>
        <form method="POST" action="/finance/invoices-in/<?= $invoice['id'] ?>/status"><?= \App\Core\Csrf::field() ?><input type="hidden" name="status" value="approved"><button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm transition">✅ Godkänn</button></form>
        <?php endif; ?>
        <?php if ($invoice['status'] === 'approved'): ?>
        <form method="POST" action="/finance/invoices-in/<?= $invoice['id'] ?>/status"><?= \App\Core\Csrf::field() ?><input type="hidden" name="status" value="payment_pending"><button class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded text-sm transition">💳 Sätt betalningsklar</button></form>
        <?php endif; ?>
        <form method="POST" action="/finance/invoices-in/<?= $invoice['id'] ?>/status"><?= \App\Core\Csrf::field() ?><input type="hidden" name="status" value="disputed"><button class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm transition">Tvist</button></form>
        <form method="POST" action="/finance/invoices-in/<?= $invoice['id'] ?>/status"><?= \App\Core\Csrf::field() ?><input type="hidden" name="status" value="cancelled"><button class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-sm transition">Annullera</button></form>
    </div>
    <?php endif; ?>

    <!-- Rader -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700"><h2 class="text-lg font-semibold text-gray-900 dark:text-white">Fakturarader</h2></div>
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-500">Beskrivning</th>
                    <th class="px-4 py-3 text-right text-gray-500">Antal</th>
                    <th class="px-4 py-3 text-left text-gray-500">Enhet</th>
                    <th class="px-4 py-3 text-right text-gray-500">Á-pris</th>
                    <th class="px-4 py-3 text-right text-gray-500">Moms%</th>
                    <th class="px-4 py-3 text-right text-gray-500">Radtotal</th>
                    <th class="px-4 py-3 text-left text-gray-500">Konto</th>
                    <th class="px-4 py-3 text-left text-gray-500">KS</th>
                    <?php if ($invoice['status'] === 'registered'): ?><th class="px-4 py-3"></th><?php endif; ?>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($lines as $l): ?>
                <tr>
                    <td class="px-4 py-3 text-gray-900 dark:text-white"><?= htmlspecialchars($l['description']) ?></td>
                    <td class="px-4 py-3 text-right font-mono"><?= $l['quantity'] ?></td>
                    <td class="px-4 py-3 text-gray-600"><?= htmlspecialchars($l['unit']) ?></td>
                    <td class="px-4 py-3 text-right font-mono"><?= number_format((float)$l['unit_price'], 2, ',', ' ') ?></td>
                    <td class="px-4 py-3 text-right"><?= $l['vat_rate'] ?>%</td>
                    <td class="px-4 py-3 text-right font-mono font-medium"><?= number_format((float)$l['line_total'], 2, ',', ' ') ?></td>
                    <td class="px-4 py-3 text-xs font-mono text-gray-500"><?= htmlspecialchars(($l['account_number'] ?? '') ? $l['account_number'] . ' ' . $l['account_name'] : '—') ?></td>
                    <td class="px-4 py-3 text-xs font-mono text-gray-500"><?= htmlspecialchars(($l['cost_center_code'] ?? '') ? $l['cost_center_code'] . ' ' . $l['cost_center_name'] : '—') ?></td>
                    <?php if ($invoice['status'] === 'registered'): ?>
                    <td class="px-4 py-3"><form method="POST" action="/finance/invoices-in/<?= $invoice['id'] ?>/lines/<?= $l['id'] ?>/delete" onsubmit="return confirm('Ta bort denna rad?')"><?= \App\Core\Csrf::field() ?><button class="text-red-500 hover:text-red-700 text-xs">Ta bort</button></form></td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($lines)): ?>
                <tr><td colspan="9" class="px-4 py-6 text-center text-gray-400">Inga rader</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if ($invoice['status'] === 'registered'): ?>
        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 border-t">
            <form method="POST" action="/finance/invoices-in/<?= $invoice['id'] ?>/lines" class="grid grid-cols-1 sm:grid-cols-9 gap-3 items-end">
                <?= \App\Core\Csrf::field() ?>
                <div class="sm:col-span-2"><label class="block text-xs text-gray-500 mb-1">Beskrivning *</label><input type="text" name="description" required class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"></div>
                <div><label class="block text-xs text-gray-500 mb-1">Antal</label><input type="number" name="quantity" value="1" step="0.001" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"></div>
                <div><label class="block text-xs text-gray-500 mb-1">Á-pris</label><input type="number" name="unit_price" step="0.01" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"></div>
                <div><label class="block text-xs text-gray-500 mb-1">Moms%</label><select name="vat_rate" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"><option value="25" selected>25%</option><option value="12">12%</option><option value="6">6%</option><option value="0">0%</option></select></div>
                <div><label class="block text-xs text-gray-500 mb-1">Konto</label><select name="account_id" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"><option value="">—</option><?php foreach ($accounts as $acc): ?><option value="<?= $acc['id'] ?>"><?= htmlspecialchars($acc['account_number'] . ' ' . $acc['name']) ?></option><?php endforeach; ?></select></div>
                <div><label class="block text-xs text-gray-500 mb-1">KS</label><select name="cost_center_id" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"><option value="">—</option><?php foreach ($costCenters as $cc): ?><option value="<?= $cc['id'] ?>"><?= htmlspecialchars($cc['code'] . ' ' . $cc['name']) ?></option><?php endforeach; ?></select></div>
                <div><button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 rounded text-sm font-medium transition">Lägg till</button></div>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <!-- Betalning -->
    <?php if (in_array($invoice['status'], ['approved','payment_pending']) && (float)$invoice['remaining_amount'] > 0): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Registrera betalning</h2>
        <form method="POST" action="/finance/invoices-in/<?= $invoice['id'] ?>/payment" class="grid grid-cols-1 sm:grid-cols-5 gap-4 items-end">
            <?= \App\Core\Csrf::field() ?>
            <div><label class="block text-sm text-gray-500 mb-1">Belopp *</label><input type="number" name="amount" step="0.01" value="<?= $invoice['remaining_amount'] ?>" required class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></div>
            <div><label class="block text-sm text-gray-500 mb-1">Datum *</label><input type="date" name="payment_date" value="<?= date('Y-m-d') ?>" required class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></div>
            <div><label class="block text-sm text-gray-500 mb-1">Metod</label><select name="payment_method" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"><option value="bank_transfer">Banköverföring</option><option value="bankgiro">Bankgiro</option><option value="plusgiro">Plusgiro</option><option value="autogiro">Autogiro</option></select></div>
            <div><label class="block text-sm text-gray-500 mb-1">Referens</label><input type="text" name="bank_reference" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></div>
            <div><button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-3 rounded font-medium transition">Betala</button></div>
        </form>
    </div>
    <?php endif; ?>
</div>
