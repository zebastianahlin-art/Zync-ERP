<?php $pl = $priceList; ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="/sales/pricelists" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($pl['name']) ?></h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Kod: <?= $pl['code'] ?> · <?= $pl['currency'] ?><?= $pl['is_default'] ? ' · Standardlista' : '' ?></p>
            </div>
        </div>
        <a href="/sales/pricelists/<?= $pl['id'] ?>/edit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">Redigera</a>
    </div>

    <!-- Prisrader -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="font-semibold text-gray-900 dark:text-white">Artikelpriser (<?= count($lines) ?>)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Artikelnr</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Namn</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Pris</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Min antal</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Rabatt</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Giltig</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php if (empty($lines)): ?>
                        <tr><td colspan="7" class="px-4 py-8 text-center text-sm text-gray-400">Inga artiklar i prislistan</td></tr>
                    <?php else: foreach ($lines as $l): ?>
                    <tr>
                        <td class="px-4 py-3 text-sm font-mono text-gray-500"><?= htmlspecialchars($l['article_number']) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($l['article_name']) ?></td>
                        <td class="px-4 py-3 text-sm text-right font-medium text-gray-900 dark:text-white"><?= number_format((float)$l['unit_price'], 2, ',', ' ') ?> <?= $pl['currency'] ?></td>
                        <td class="px-4 py-3 text-sm text-right text-gray-500"><?= $l['min_quantity'] ?></td>
                        <td class="px-4 py-3 text-sm text-right text-gray-500"><?= (float)$l['discount_percent'] > 0 ? $l['discount_percent'] . '%' : '—' ?></td>
                        <td class="px-4 py-3 text-sm text-gray-500"><?= $l['valid_from'] ?? '—' ?> → <?= $l['valid_to'] ?? '∞' ?></td>
                        <td class="px-4 py-3 text-right">
                            <form method="post" action="/sales/pricelists/<?= $pl['id'] ?>/lines/<?= $l['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort?')">
                                <?= \App\Core\Csrf::field() ?>
                                <button class="text-red-500 hover:text-red-700 text-xs">✕</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Lägg till artikel -->
        <div class="border-t border-gray-200 dark:border-gray-700 p-5">
            <form method="post" action="/sales/pricelists/<?= $pl['id'] ?>/lines" class="grid grid-cols-2 md:grid-cols-6 gap-3 items-end">
                <?= \App\Core\Csrf::field() ?>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Artikel *</label>
                    <select name="article_id" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="">Välj...</option>
                        <?php foreach ($articles ?? [] as $a): ?>
                            <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['article_number'] . ' — ' . $a['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Pris *</label>
                    <input type="number" name="unit_price" step="0.01" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Min antal</label>
                    <input type="number" name="min_quantity" value="1" step="0.001" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Rabatt %</label>
                    <input type="number" name="discount_percent" value="0" step="0.5" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Giltig från/till</label>
                    <input type="date" name="valid_from" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">+ Lägg till</button>
                </div>
            </form>
        </div>
    </div>
</div>
