<?php $q = $quote ?? $old ?? []; $isEdit = !empty($quote); ?>
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="/sales/quotes" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= $title ?></h1>
    </div>

    <form method="post" action="<?= $isEdit ? "/sales/quotes/{$quote['id']}" : '/sales/quotes' ?>" class="space-y-6">
        <?= \App\Core\Csrf::field() ?>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Offertnummer</label>
                    <input type="text" value="<?= htmlspecialchars($isEdit ? $quote['quote_number'] : ($nextNumber ?? '')) ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm bg-gray-50" disabled>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kund *</label>
                    <select name="customer_id" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="">Välj kund...</option>
                        <?php foreach ($customers ?? [] as $cust): ?>
                            <option value="<?= $cust['id'] ?>" <?= ($q['customer_id'] ?? '') == $cust['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cust['customer_number'] . ' — ' . $cust['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Valuta</label>
                    <select name="currency" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <?php foreach (['SEK','EUR','USD','NOK','DKK','GBP'] as $cur): ?>
                            <option value="<?= $cur ?>" <?= ($q['currency'] ?? 'SEK') === $cur ? 'selected' : '' ?>><?= $cur ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Offertdatum *</label>
                    <input type="date" name="quote_date" value="<?= $q['quote_date'] ?? date('Y-m-d') ?>" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Giltig till *</label>
                    <input type="date" name="valid_until" value="<?= $q['valid_until'] ?? date('Y-m-d', strtotime('+30 days')) ?>" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Betalningsvillkor</label>
                    <select name="payment_terms" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <?php foreach (['10 dagar netto','15 dagar netto','20 dagar netto','30 dagar netto','45 dagar netto','60 dagar netto','Förskott'] as $pt): ?>
                            <option value="<?= $pt ?>" <?= ($q['payment_terms'] ?? '30 dagar netto') === $pt ? 'selected' : '' ?>><?= $pt ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Leveransvillkor</label>
                    <input type="text" name="delivery_terms" value="<?= htmlspecialchars($q['delivery_terms'] ?? '') ?>" placeholder="T.ex. FCA, DAP" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Vår referens</label>
                    <input type="text" name="our_reference" value="<?= htmlspecialchars($q['our_reference'] ?? '') ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Er referens</label>
                    <input type="text" name="your_reference" value="<?= htmlspecialchars($q['your_reference'] ?? '') ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Headertext (visas på offert)</label>
                    <textarea name="header_text" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"><?= htmlspecialchars($q['header_text'] ?? '') ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Footertext (visas på offert)</label>
                    <textarea name="footer_text" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"><?= htmlspecialchars($q['footer_text'] ?? '') ?></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Interna anteckningar</label>
                    <textarea name="internal_notes" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"><?= htmlspecialchars($q['internal_notes'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="/sales/quotes" class="rounded-lg bg-white dark:bg-gray-800 px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 hover:bg-gray-50">Avbryt</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"><?= $isEdit ? 'Spara ändringar' : 'Skapa offert' ?></button>
        </div>
    </form>
</div>
