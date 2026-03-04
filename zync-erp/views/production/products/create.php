<div class="max-w-2xl space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Ny produkt</h1>

    <form method="POST" action="/production/products" class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-4">
        <?= \App\Core\Csrf::field() ?>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Produktnummer <span class="text-red-500">*</span></label>
                <input type="text" name="product_number" value="<?= htmlspecialchars($old['product_number'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <?php if (!empty($errors['product_number'])): ?><p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['product_number'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Namn <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="<?= htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <?php if (!empty($errors['name'])): ?><p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kategori</label>
            <input type="text" name="category" value="<?= htmlspecialchars($old['category'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Beskrivning</label>
            <textarea name="description" rows="3" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"><?= htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pris</label>
                <input type="number" step="0.01" name="unit_price" value="<?= htmlspecialchars($old['unit_price'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Valuta</label>
                <select name="currency" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="SEK" <?= ($old['currency'] ?? 'SEK') === 'SEK' ? 'selected' : '' ?>>SEK</option>
                    <option value="EUR" <?= ($old['currency'] ?? '') === 'EUR' ? 'selected' : '' ?>>EUR</option>
                    <option value="USD" <?= ($old['currency'] ?? '') === 'USD' ? 'selected' : '' ?>>USD</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Vikt</label>
                <input type="number" step="0.001" name="weight" value="<?= htmlspecialchars($old['weight'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Viktenhet</label>
                <select name="weight_unit" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="kg" <?= ($old['weight_unit'] ?? 'kg') === 'kg' ? 'selected' : '' ?>>kg</option>
                    <option value="g" <?= ($old['weight_unit'] ?? '') === 'g' ? 'selected' : '' ?>>g</option>
                    <option value="ton" <?= ($old['weight_unit'] ?? '') === 'ton' ? 'selected' : '' ?>>ton</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mått</label>
            <input type="text" name="dimensions" value="<?= htmlspecialchars($old['dimensions'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                placeholder="t.ex. 100x200x50 mm">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SKU</label>
                <input type="text" name="sku" value="<?= htmlspecialchars($old['sku'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Streckkod</label>
                <input type="text" name="barcode" value="<?= htmlspecialchars($old['barcode'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Minsta lagernivå</label>
                <input type="number" name="min_stock_level" value="<?= htmlspecialchars($old['min_stock_level'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ledtid (dagar)</label>
                <input type="number" name="lead_time_days" value="<?= htmlspecialchars($old['lead_time_days'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
            <select name="status" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="active" <?= ($old['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Aktiv</option>
                <option value="inactive" <?= ($old['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inaktiv</option>
                <option value="discontinued" <?= ($old['status'] ?? '') === 'discontinued' ? 'selected' : '' ?>>Utgången</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Datablad-URL</label>
            <input type="text" name="datasheet_url" value="<?= htmlspecialchars($old['datasheet_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sammansättning</label>
            <textarea name="composition" rows="3" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"><?= htmlspecialchars($old['composition'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Spara</button>
            <a href="/production/products" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white text-sm font-medium rounded-lg transition">Avbryt</a>
        </div>
    </form>
</div>
