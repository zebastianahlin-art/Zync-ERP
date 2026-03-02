<?php $invoice = $invoice ?? []; $customers = $customers ?? []; ?>
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Redigera <?= htmlspecialchars($invoice['invoice_number']) ?></h1>
        <a href="/finance/invoices-out/<?= $invoice['id'] ?>" class="text-sm text-gray-500 hover:text-indigo-600">← Tillbaka</a>
    </div>
    <form method="POST" action="/finance/invoices-out/<?= $invoice['id'] ?>" class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-4">
        <?= \App\Core\Csrf::field() ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kund *</label>
                <select name="customer_id" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <?php foreach ($customers as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $c['id'] == $invoice['customer_id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Betalningsvillkor</label>
                <input type="text" name="payment_terms" value="<?= htmlspecialchars($invoice['payment_terms'] ?? '') ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fakturadatum</label>
                <input type="date" name="invoice_date" value="<?= $invoice['invoice_date'] ?>" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Förfallodag</label>
                <input type="date" name="due_date" value="<?= $invoice['due_date'] ?>" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Vår referens</label>
                <input type="text" name="our_reference" value="<?= htmlspecialchars($invoice['our_reference'] ?? '') ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Er referens</label>
                <input type="text" name="your_reference" value="<?= htmlspecialchars($invoice['your_reference'] ?? '') ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Leveransdatum</label>
                <input type="date" name="delivery_date" value="<?= $invoice['delivery_date'] ?? '' ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">OCR</label>
                <input type="text" name="ocr_number" value="<?= htmlspecialchars($invoice['ocr_number'] ?? '') ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Meddelande</label>
            <textarea name="notes" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"><?= htmlspecialchars($invoice['notes'] ?? '') ?></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Intern anteckning</label>
            <textarea name="internal_notes" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"><?= htmlspecialchars($invoice['internal_notes'] ?? '') ?></textarea>
        </div>
        <div class="flex justify-between pt-4">
            <form method="POST" action="/finance/invoices-out/<?= $invoice['id'] ?>/delete"><?= \App\Core\Csrf::field() ?><button type="submit" onclick="return confirm('Radera?')" class="text-red-600 hover:text-red-800 text-sm">Ta bort faktura</button></form>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition">Spara</button>
        </div>
    </form>
</div>
