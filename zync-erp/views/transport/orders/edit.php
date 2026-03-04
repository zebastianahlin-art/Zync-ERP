<div class="max-w-2xl space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Redigera transportorder – <?= htmlspecialchars($order['transport_number'], ENT_QUOTES, 'UTF-8') ?></h1>

    <form method="POST" action="/transport/orders/<?= $order['id'] ?>" class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-4">
        <?= \App\Core\Csrf::field() ?>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Transportnummer <span class="text-red-500">*</span></label>
                <input type="text" name="transport_number" value="<?= htmlspecialchars($order['transport_number'], ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <?php if (!empty($errors['transport_number'])): ?><p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['transport_number'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Typ</label>
                <select name="type" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <?php foreach (['inbound' => 'Inkommande','outbound' => 'Utgående','internal' => 'Intern'] as $v => $l): ?>
                    <option value="<?= $v ?>" <?= $order['type'] === $v ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Transportör</label>
                <select name="carrier_id" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">— Välj transportör —</option>
                    <?php foreach ($carriers as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $order['carrier_id'] == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kund</label>
                <select name="customer_id" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">— Välj kund —</option>
                    <?php foreach ($customers as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $order['customer_id'] == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Leverantör</label>
            <select name="supplier_id" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">— Välj leverantör —</option>
                <?php foreach ($suppliers as $s): ?>
                <option value="<?= $s['id'] ?>" <?= $order['supplier_id'] == $s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['name'], ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Upphämtningsadress</label>
            <textarea name="pickup_address" rows="2" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"><?= htmlspecialchars($order['pickup_address'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Leveransadress</label>
            <textarea name="delivery_address" rows="2" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"><?= htmlspecialchars($order['delivery_address'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Hämtningsdatum</label>
                <input type="datetime-local" name="pickup_date" value="<?= htmlspecialchars($order['pickup_date'] ? str_replace(' ', 'T', substr($order['pickup_date'], 0, 16)) : '', ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Leveransdatum</label>
                <input type="datetime-local" name="delivery_date" value="<?= htmlspecialchars($order['delivery_date'] ? str_replace(' ', 'T', substr($order['delivery_date'], 0, 16)) : '', ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Vikt (kg)</label>
                <input type="number" step="0.01" name="weight" value="<?= htmlspecialchars($order['weight'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Volym (m³)</label>
                <input type="number" step="0.01" name="volume" value="<?= htmlspecialchars($order['volume'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Spårningsnummer</label>
                <input type="text" name="tracking_number" value="<?= htmlspecialchars($order['tracking_number'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <?php foreach (['planned' => 'Planerad','confirmed' => 'Bekräftad','in_transit' => 'Under transport','delivered' => 'Levererad','cancelled' => 'Avbruten'] as $v => $l): ?>
                    <option value="<?= $v ?>" <?= $order['status'] === $v ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kostnad</label>
                <input type="number" step="0.01" name="cost" value="<?= htmlspecialchars($order['cost'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Valuta</label>
                <select name="currency" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <?php foreach (['SEK', 'EUR', 'USD'] as $cur): ?>
                    <option value="<?= $cur ?>" <?= $order['currency'] === $cur ? 'selected' : '' ?>><?= $cur ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Anteckningar</label>
            <textarea name="notes" rows="2" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"><?= htmlspecialchars($order['notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Spara</button>
            <a href="/transport/orders/<?= $order['id'] ?>" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 text-sm font-medium rounded-lg transition">Avbryt</a>
        </div>
    </form>
</div>
