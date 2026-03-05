<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($list['name'], ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                <?= htmlspecialchars($list['currency'], ENT_QUOTES, 'UTF-8') ?>
                <?php if ($list['valid_from'] || $list['valid_to']): ?>
                    &middot; <?= htmlspecialchars($list['valid_from'] ?? '—', ENT_QUOTES, 'UTF-8') ?> – <?= htmlspecialchars($list['valid_to'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                <?php endif; ?>
            </p>
        </div>
        <div class="flex gap-3">
            <a href="/sales/pricing/<?= (int) $list['id'] ?>/edit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Redigera lista</a>
            <a href="/sales/pricing/manage" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline self-center">&larr; Alla prislistor</a>
        </div>
    </div>

    <?php if ($list['description']): ?>
    <p class="text-gray-600 dark:text-gray-400 text-sm"><?= htmlspecialchars($list['description'], ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <!-- Articles table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
            <h2 class="font-semibold text-gray-900 dark:text-white text-sm">Artiklar</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Produkt</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Beskrivning</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">À-pris</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Valuta</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Enhet</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($items as $item): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($item['description'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-right text-gray-900 dark:text-white"><?= number_format((float) $item['unit_price'], 2, ',', ' ') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($item['currency'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($item['unit'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-right">
                            <form method="POST" action="/sales/pricing/<?= (int) $list['id'] ?>/items/<?= (int) $item['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort artikel?')">
                                <?= \App\Core\Csrf::field() ?>
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700">Ta bort</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($items)): ?>
                    <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400 dark:text-gray-500">Inga artiklar tillagda ännu</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add item form -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h2 class="font-semibold text-gray-900 dark:text-white text-sm mb-4">Lägg till artikel</h2>
        <form method="POST" action="/sales/pricing/<?= (int) $list['id'] ?>/items" class="space-y-4"
              x-data="{ selectedArticle: '' }" @change.from-select="fillFromArticle($event)">
            <?= \App\Core\Csrf::field() ?>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Välj från artikelregister</label>
                <select name="article_id" x-model="selectedArticle" @change="fillFromArticle($event)"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">— Välj artikel (valfritt) —</option>
                    <?php foreach ($articles ?? [] as $art): ?>
                    <option value="<?= $art['id'] ?>"
                        data-name="<?= htmlspecialchars($art['name'], ENT_QUOTES, 'UTF-8') ?>"
                        data-price="<?= htmlspecialchars((string)$art['selling_price'], ENT_QUOTES, 'UTF-8') ?>"
                        data-unit="<?= htmlspecialchars($art['unit'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($art['article_number'] . ' – ' . $art['name'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Produktnamn</label>
                    <input type="text" name="product_name" id="pli_name"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Enhet</label>
                    <input type="text" name="unit" id="pli_unit" placeholder="st, kg, h …"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Beskrivning</label>
                <textarea name="description" rows="2"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">À-pris</label>
                    <input type="number" name="unit_price" id="pli_price" step="0.01" min="0" value="0"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Valuta</label>
                    <select name="currency" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="SEK" selected>SEK</option>
                        <option value="EUR">EUR</option>
                        <option value="USD">USD</option>
                    </select>
                </div>
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Lägg till</button>
            </div>
        </form>
    </div>
</div>

<script>
function fillFromArticle(evt) {
    const opt = evt.target.selectedOptions[0];
    if (!opt || !opt.value) return;
    const nameEl  = document.getElementById('pli_name');
    const priceEl = document.getElementById('pli_price');
    const unitEl  = document.getElementById('pli_unit');
    if (nameEl  && !nameEl.value)  nameEl.value  = opt.dataset.name  || '';
    if (priceEl) priceEl.value = opt.dataset.price || '0';
    if (unitEl  && !unitEl.value)  unitEl.value  = opt.dataset.unit  || '';
}
</script>

