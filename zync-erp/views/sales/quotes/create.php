<div class="max-w-4xl space-y-6" x-data="quoteForm()">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Ny offert</h1>

    <form method="POST" action="/sales/quotes" class="space-y-6">
        <?= \App\Core\Csrf::field() ?>

        <!-- Offertinfo -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Offertinformation</h2>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Offertnummer <span class="text-red-500">*</span></label>
                    <input type="text" name="quote_number" value="<?= htmlspecialchars($old['quote_number'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <?php if (!empty($errors['quote_number'])): ?><p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['quote_number'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="status" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <?php foreach (['draft' => 'Utkast', 'sent' => 'Skickad', 'accepted' => 'Accepterad', 'rejected' => 'Avvisad', 'expired' => 'Utgången'] as $v => $l): ?>
                        <option value="<?= $v ?>" <?= ($old['status'] ?? 'draft') === $v ? 'selected' : '' ?>><?= $l ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kund</label>
                    <select name="customer_id" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">— Välj kund —</option>
                        <?php foreach ($customers as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($old['customer_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Giltig till</label>
                    <input type="date" name="valid_until" value="<?= htmlspecialchars($old['valid_until'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Leveransvillkor</label>
                    <input type="text" name="delivery_terms" value="<?= htmlspecialchars($old['delivery_terms'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="t.ex. DAP, EXW, FCA"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Betalningsvillkor</label>
                    <input type="text" name="payment_terms" value="<?= htmlspecialchars($old['payment_terms'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="t.ex. 30 dagar netto"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Anteckningar</label>
                <textarea name="notes" rows="2" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"><?= htmlspecialchars($old['notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
        </div>

        <!-- Offertrader -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Offertrader</h2>
                <button type="button" @click="addLine()" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">+ Lägg till rad</button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Artikel</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Beskrivning</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-20">Antal</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-28">À-pris</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-20">Rab %</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-28">Summa</th>
                            <th class="px-3 py-2 w-10"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(line, index) in lines" :key="index">
                            <tr class="border-t border-gray-200 dark:border-gray-700">
                                <td class="px-3 py-2">
                                    <select :name="'line_article_id[' + index + ']'" x-model="line.article_id" @change="onArticleChange(index, $event)"
                                        class="w-36 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                        <option value="">— Välj —</option>
                                        <?php foreach ($articles as $art): ?>
                                        <option value="<?= $art['id'] ?>"
                                            data-price="<?= htmlspecialchars((string) $art['selling_price'], ENT_QUOTES, 'UTF-8') ?>"
                                            data-name="<?= htmlspecialchars($art['name'], ENT_QUOTES, 'UTF-8') ?>">
                                            <?= htmlspecialchars($art['article_number'] . ' – ' . $art['name'], ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td class="px-3 py-2">
                                    <input type="text" :name="'line_description[' + index + ']'" x-model="line.description"
                                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" step="0.01" :name="'line_quantity[' + index + ']'" x-model.number="line.quantity" @input="calcLine(index)"
                                        class="w-20 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded px-2 py-1 text-xs text-right focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" step="0.01" :name="'line_unit_price[' + index + ']'" x-model.number="line.unit_price" @input="calcLine(index)"
                                        class="w-28 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded px-2 py-1 text-xs text-right focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" step="0.01" min="0" max="100" :name="'line_discount[' + index + ']'" x-model.number="line.discount" @input="calcLine(index)"
                                        class="w-20 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded px-2 py-1 text-xs text-right focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                </td>
                                <td class="px-3 py-2 text-right text-gray-700 dark:text-gray-300 font-medium" x-text="formatMoney(line.total)"></td>
                                <td class="px-3 py-2 text-center">
                                    <button type="button" @click="removeLine(index)" class="text-red-400 hover:text-red-600 text-lg leading-none">&times;</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot class="border-t-2 border-gray-200 dark:border-gray-700">
                        <tr>
                            <td colspan="5" class="px-3 py-2 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">Totalsumma:</td>
                            <td class="px-3 py-2 text-right text-sm font-bold text-indigo-600 dark:text-indigo-400" x-text="formatMoney(total)"></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Spara offert</button>
            <a href="/sales/quotes" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 text-sm font-medium rounded-lg transition">Avbryt</a>
        </div>
    </form>
</div>

<script>
function quoteForm() {
    return {
        lines: [{ article_id: '', description: '', quantity: 1, unit_price: 0, discount: 0, total: 0 }],
        get total() {
            return this.lines.reduce((s, l) => s + (l.total || 0), 0);
        },
        addLine() {
            this.lines.push({ article_id: '', description: '', quantity: 1, unit_price: 0, discount: 0, total: 0 });
        },
        removeLine(i) {
            if (this.lines.length > 1) this.lines.splice(i, 1);
        },
        calcLine(i) {
            const l = this.lines[i];
            const qty = parseFloat(l.quantity) || 0;
            const price = parseFloat(l.unit_price) || 0;
            const disc = parseFloat(l.discount) || 0;
            l.total = qty * price * (1 - disc / 100);
        },
        onArticleChange(i, evt) {
            const opt = evt.target.selectedOptions[0];
            this.lines[i].article_id = opt.value;
            if (opt.value) {
                this.lines[i].unit_price = parseFloat(opt.dataset.price) || 0;
                if (!this.lines[i].description) {
                    this.lines[i].description = opt.dataset.name || '';
                }
            }
            this.calcLine(i);
        },
        formatMoney(v) {
            return (parseFloat(v) || 0).toLocaleString('sv-SE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' kr';
        },
    };
}
</script>

