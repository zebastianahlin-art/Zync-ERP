<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
        <a href="/purchasing/orders/<?= $order['id'] ?>" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Redigera inköpsorder</h1>
    </div>

    <form method="POST" action="/purchasing/orders/<?= $order['id'] ?>" class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-5">
        <?= \App\Core\Csrf::field() ?>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Leverantör *</label>
            <select name="supplier_id" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                <?php foreach ($suppliers as $s): ?>
                <option value="<?= $s['id'] ?>" <?= (int)$order['supplier_id'] === (int)$s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Referens</label>
                <input type="text" name="reference" value="<?= htmlspecialchars($order['reference'] ?? '') ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Leveransdatum</label>
                <input type="date" name="delivery_date" value="<?= $order['delivery_date'] ?? '' ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Betalningsvillkor</label>
                <select name="payment_terms" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                    <?php foreach (['30 dagar netto','15 dagar netto','60 dagar netto','10 dagar netto','Förskott','Vid leverans'] as $pt): ?>
                    <option value="<?= $pt ?>" <?= ($order['payment_terms'] ?? '') === $pt ? 'selected' : '' ?>><?= $pt ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Valuta</label>
                <select name="currency" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                    <?php foreach (['SEK','EUR','USD','NOK','DKK'] as $c): ?>
                    <option value="<?= $c ?>" <?= $order['currency'] === $c ? 'selected' : '' ?>><?= $c ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Leveransadress</label>
            <textarea name="delivery_address" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($order['delivery_address'] ?? '') ?></textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Anteckningar</label>
            <textarea name="notes" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($order['notes'] ?? '') ?></textarea>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition">Spara</button>
            <a href="/purchasing/orders/<?= $order['id'] ?>" class="px-6 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition">Avbryt</a>
        </div>
    </form>

    <form method="POST" action="/purchasing/orders/<?= $order['id'] ?>/delete" onsubmit="return confirm('Vill du verkligen radera denna order?')" class="text-right">
        <?= \App\Core\Csrf::field() ?>
        <button type="submit" class="text-sm text-red-500 hover:text-red-700">Radera order</button>
    </form>
</div>
