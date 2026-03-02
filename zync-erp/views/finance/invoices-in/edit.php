<?php $invoice = $invoice ?? []; $suppliers = $suppliers ?? []; $purchaseOrders = $purchaseOrders ?? []; ?>
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Redigera <?= htmlspecialchars($invoice['internal_number']) ?></h1>
        <a href="/finance/invoices-in/<?= $invoice['id'] ?>" class="text-sm text-gray-500 hover:text-indigo-600">← Tillbaka</a>
    </div>
    <form method="POST" action="/finance/invoices-in/<?= $invoice['id'] ?>" class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-4">
        <?= \App\Core\Csrf::field() ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium mb-1">Leverantör *</label><select name="supplier_id" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"><?php foreach ($suppliers as $s): ?><option value="<?= $s['id'] ?>" <?= $s['id'] == $invoice['supplier_id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option><?php endforeach; ?></select></div>
            <div><label class="block text-sm font-medium mb-1">Lev. fakturanummer *</label><input type="text" name="invoice_number" value="<?= htmlspecialchars($invoice['invoice_number']) ?>" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></div>
            <div><label class="block text-sm font-medium mb-1">Inköpsorder</label><select name="purchase_order_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"><option value="">Ingen</option><?php foreach ($purchaseOrders as $po): ?><option value="<?= $po['id'] ?>" <?= ($po['id'] ?? '') == ($invoice['purchase_order_id'] ?? '') ? 'selected' : '' ?>><?= htmlspecialchars($po['order_number'] . ' — ' . ($po['supplier_name'] ?? '')) ?></option><?php endforeach; ?></select></div>
            <div><label class="block text-sm font-medium mb-1">Betalningsvillkor</label><input type="text" name="payment_terms" value="<?= htmlspecialchars($invoice['payment_terms'] ?? '') ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></div>
            <div><label class="block text-sm font-medium mb-1">Fakturadatum</label><input type="date" name="invoice_date" value="<?= $invoice['invoice_date'] ?>" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></div>
            <div><label class="block text-sm font-medium mb-1">Förfallodag</label><input type="date" name="due_date" value="<?= $invoice['due_date'] ?>" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></div>
            <div><label class="block text-sm font-medium mb-1">Netto</label><input type="number" name="subtotal" step="0.01" value="<?= $invoice['subtotal'] ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></div>
            <div><label class="block text-sm font-medium mb-1">Moms</label><input type="number" name="vat_amount" step="0.01" value="<?= $invoice['vat_amount'] ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></div>
            <div><label class="block text-sm font-medium mb-1">Total</label><input type="number" name="total_amount" step="0.01" value="<?= $invoice['total_amount'] ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></div>
            <div><label class="block text-sm font-medium mb-1">Referens</label><input type="text" name="reference" value="<?= htmlspecialchars($invoice['reference'] ?? '') ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></div>
        </div>
        <div><label class="block text-sm font-medium mb-1">Anteckningar</label><textarea name="notes" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"><?= htmlspecialchars($invoice['notes'] ?? '') ?></textarea></div>
        <div class="flex justify-between pt-4">
            <form method="POST" action="/finance/invoices-in/<?= $invoice['id'] ?>/delete"><?= \App\Core\Csrf::field() ?><button type="submit" onclick="return confirm('Radera?')" class="text-red-600 hover:text-red-800 text-sm">Ta bort</button></form>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition">Spara</button>
        </div>
    </form>
</div>
