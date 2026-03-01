<div class="mx-auto max-w-2xl">

    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Ny artikel</h1>
        <a href="/articles" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">&larr; Tillbaka</a>
    </div>

    <?php if (!empty($errors['general'])): ?>
        <div class="mb-4 rounded-lg bg-red-50 dark:bg-red-900/30 px-4 py-3 text-sm text-red-700 dark:text-red-400">
            <?= htmlspecialchars($errors['general'], ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <div class="rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-md">
        <form method="POST" action="/articles" class="space-y-5">
            <input type="hidden" name="_token" value="<?= \App\Core\Csrf::token() ?>">

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                <div>
                    <label for="article_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Artikelnummer <span class="text-red-500">*</span></label>
                    <input id="article_number" name="article_number" type="text" required
                           value="<?= htmlspecialchars($old['article_number'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border <?= isset($errors['article_number']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <?php if (isset($errors['article_number'])): ?>
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400"><?= htmlspecialchars($errors['article_number'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="unit" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Enhet <span class="text-red-500">*</span></label>
                    <select id="unit" name="unit" required
                            class="mt-1 block w-full rounded-lg border <?= isset($errors['unit']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <?php foreach (['st', 'kg', 'm', 'm²', 'm³', 'l', 'tim', 'paket'] as $unit): ?>
                            <option value="<?= htmlspecialchars($unit, ENT_QUOTES, 'UTF-8') ?>"
                                <?= ($old['unit'] ?? '') === $unit ? 'selected' : '' ?>>
                                <?= htmlspecialchars($unit, ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['unit'])): ?>
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400"><?= htmlspecialchars($errors['unit'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>

                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Namn <span class="text-red-500">*</span></label>
                    <input id="name" name="name" type="text" required
                           value="<?= htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border <?= isset($errors['name']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <?php if (isset($errors['name'])): ?>
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400"><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>

                <div class="sm:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Beskrivning</label>
                    <textarea id="description" name="description" rows="3"
                              class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"><?= htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <div>
                    <label for="purchase_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Inköpspris (kr)</label>
                    <input id="purchase_price" name="purchase_price" type="number" step="0.01" min="0"
                           value="<?= htmlspecialchars($old['purchase_price'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border <?= isset($errors['purchase_price']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <?php if (isset($errors['purchase_price'])): ?>
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400"><?= htmlspecialchars($errors['purchase_price'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="selling_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Försäljningspris (kr) <span class="text-red-500">*</span></label>
                    <input id="selling_price" name="selling_price" type="number" step="0.01" min="0" required
                           value="<?= htmlspecialchars($old['selling_price'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border <?= isset($errors['selling_price']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <?php if (isset($errors['selling_price'])): ?>
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400"><?= htmlspecialchars($errors['selling_price'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="vat_rate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Momssats <span class="text-red-500">*</span></label>
                    <select id="vat_rate" name="vat_rate" required
                            class="mt-1 block w-full rounded-lg border <?= isset($errors['vat_rate']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <?php foreach (['25.00' => '25%', '12.00' => '12%', '6.00' => '6%', '0.00' => '0%'] as $value => $label): ?>
                            <option value="<?= $value ?>" <?= ($old['vat_rate'] ?? '25.00') === $value ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['vat_rate'])): ?>
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400"><?= htmlspecialchars($errors['vat_rate'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori</label>
                    <input id="category" name="category" type="text"
                           value="<?= htmlspecialchars($old['category'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>

                <div class="sm:col-span-2">
                    <label for="supplier_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Leverantör</label>
                    <select id="supplier_id" name="supplier_id"
                            class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <option value="">– Ingen –</option>
                        <?php foreach ($suppliers as $s): ?>
                            <option value="<?= (int) $s['id'] ?>"
                                <?= ($old['supplier_id'] ?? '') === (string) $s['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['name'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="sm:col-span-2 flex items-center space-x-2">
                    <input id="is_active" name="is_active" type="checkbox" value="1"
                           <?= (($old['is_active'] ?? '1') === '1') ? 'checked' : '' ?>
                           class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="is_active" class="text-sm font-medium text-gray-700 dark:text-gray-300">Aktiv</label>
                </div>

            </div>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <a href="/articles" class="rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Avbryt</a>
                <button type="submit"
                        class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">
                    Skapa artikel
                </button>
            </div>

        </form>
    </div>

</div>
