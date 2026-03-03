<?php $c = $customer ?? $old ?? []; $isEdit = !empty($customer); ?>
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="/sales/customers" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= $title ?></h1>
    </div>

    <form method="post" action="<?= $isEdit ? "/sales/customers/{$customer['id']}" : '/sales/customers' ?>" class="space-y-6">
        <?= \App\Core\Csrf::field() ?>

        <!-- Grundinfo -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Grunduppgifter</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kundnummer</label>
                    <input type="text" value="<?= htmlspecialchars($isEdit ? $customer['customer_number'] : ($nextNumber ?? '')) ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm bg-gray-50" disabled>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Företagsnamn *</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($c['name'] ?? '') ?>" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Org.nummer *</label>
                    <input type="text" name="org_number" value="<?= htmlspecialchars($c['org_number'] ?? '') ?>" required placeholder="556xxx-xxxx" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">VAT-nummer</label>
                    <input type="text" name="vat_number" value="<?= htmlspecialchars($c['vat_number'] ?? '') ?>" placeholder="SE556xxxxxxx01" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">E-post *</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($c['email'] ?? '') ?>" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Faktura e-post</label>
                    <input type="email" name="invoice_email" value="<?= htmlspecialchars($c['invoice_email'] ?? '') ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Telefon</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($c['phone'] ?? '') ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Webbplats</label>
                    <input type="url" name="website" value="<?= htmlspecialchars($c['website'] ?? '') ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
            </div>
        </div>

        <!-- Adresser -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Adresser</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fakturaadress</label>
                    <textarea name="address" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"><?= htmlspecialchars($c['address'] ?? '') ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Leveransadress</label>
                    <textarea name="delivery_address" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"><?= htmlspecialchars($c['delivery_address'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Kommersiella villkor -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Kommersiella villkor</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Betalningsvillkor</label>
                    <select name="payment_terms" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <?php foreach (['10 dagar netto','15 dagar netto','20 dagar netto','30 dagar netto','45 dagar netto','60 dagar netto','90 dagar netto','Förskott'] as $pt): ?>
                            <option value="<?= $pt ?>" <?= ($c['payment_terms'] ?? '30 dagar netto') === $pt ? 'selected' : '' ?>><?= $pt ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Valuta</label>
                    <select name="currency" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <?php foreach (['SEK','EUR','USD','NOK','DKK','GBP'] as $cur): ?>
                            <option value="<?= $cur ?>" <?= ($c['currency'] ?? 'SEK') === $cur ? 'selected' : '' ?>><?= $cur ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kreditgräns (SEK)</label>
                    <input type="number" name="credit_limit" value="<?= $c['credit_limit'] ?? 0 ?>" step="1000" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Standardrabatt (%)</label>
                    <input type="number" name="discount_percent" value="<?= $c['discount_percent'] ?? 0 ?>" step="0.5" min="0" max="100" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prislista</label>
                    <select name="price_list_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="">— Standard —</option>
                        <?php foreach ($priceLists ?? [] as $pl): ?>
                            <option value="<?= $pl['id'] ?>" <?= ($c['price_list_id'] ?? '') == $pl['id'] ? 'selected' : '' ?>><?= htmlspecialchars($pl['code'] . ' — ' . $pl['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kategori</label>
                    <select name="category" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="standard" <?= ($c['category'] ?? '') === 'standard' ? 'selected' : '' ?>>Standard</option>
                        <option value="key_account" <?= ($c['category'] ?? '') === 'key_account' ? 'selected' : '' ?>>Key Account</option>
                        <option value="distributor" <?= ($c['category'] ?? '') === 'distributor' ? 'selected' : '' ?>>Distributör</option>
                        <option value="internal" <?= ($c['category'] ?? '') === 'internal' ? 'selected' : '' ?>>Intern</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Status & Anteckningar -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="active" <?= ($c['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Aktiv</option>
                        <option value="inactive" <?= ($c['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inaktiv</option>
                        <option value="blocked" <?= ($c['status'] ?? '') === 'blocked' ? 'selected' : '' ?>>Blockerad</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Interna anteckningar</label>
                    <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"><?= htmlspecialchars($c['notes'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="/sales/customers" class="rounded-lg bg-white dark:bg-gray-800 px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">Avbryt</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"><?= $isEdit ? 'Spara ändringar' : 'Skapa kund' ?></button>
        </div>
    </form>
</div>
