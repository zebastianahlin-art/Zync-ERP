<?php $q = $quote; ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="/sales/quotes" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Offert <?= $q['quote_number'] ?></h1>
                <p class="text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($q['customer_name']) ?> (<?= $q['customer_number'] ?>)</p>
            </div>
        </div>
        <div class="flex gap-2 flex-wrap">
            <?php if ($q['status'] === 'draft'): ?>
                <form method="post" action="/sales/quotes/<?= $q['id'] ?>/status" class="inline"><?= \App\Core\Csrf::field() ?><input type="hidden" name="status" value="sent"><button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">📤 Markera skickad</button></form>
            <?php endif; ?>
            <?php if ($q['status'] === 'sent'): ?>
                <form method="post" action="/sales/quotes/<?= $q['id'] ?>/convert" class="inline"><?= \App\Core\Csrf::field() ?><button class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700" onclick="return confirm('Konvertera till order?')">✅ Konvertera till order</button></form>
                <form method="post" action="/sales/quotes/<?= $q['id'] ?>/status" class="inline"><?= \App\Core\Csrf::field() ?><input type="hidden" name="status" value="rejected"><button class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">❌ Nekad</button></form>
            <?php endif; ?>
            <a href="/sales/quotes/<?= $q['id'] ?>/edit" class="rounded-lg bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">Redigera</a>
        </div>
    </div>

    <!-- Status & Info -->
    <?php
        $sc = match($q['status']) { 'draft'=>'gray','sent'=>'blue','accepted'=>'green','rejected'=>'red','expired'=>'yellow',default=>'gray' };
        $sl = match($q['status']) { 'draft'=>'Utkast','sent'=>'Skickad','accepted'=>'Accepterad','rejected'=>'Nekad','expired'=>'Utgången','revised'=>'Reviderad',default=>$q['status'] };
    ?>
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 uppercase">Status</p>
            <span class="mt-1 inline-flex items-center rounded-full bg-<?= $sc ?>-100 dark:bg-<?= $sc ?>-900/30 px-3 py-1 text-sm font-medium text-<?= $sc ?>-700 dark:text-<?= $sc ?>-400"><?= $sl ?></span>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 uppercase">Offertdatum</p>
            <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white"><?= $q['quote_date'] ?></p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 uppercase">Giltig till</p>
            <p class="mt-1 text-sm font-medium <?= $q['valid_until'] < date('Y-m-d') && $q['status'] === 'sent' ? 'text-red-500' : 'text-gray-900 dark:text-white' ?>"><?= $q['valid_until'] ?></p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 uppercase">Betalning</p>
            <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white"><?= $q['payment_terms'] ?></p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 uppercase">Totalbelopp</p>
            <p class="mt-1 text-lg font-bold text-gray-900 dark:text-white"><?= number_format((float)$q['total_amount'], 2, ',', ' ') ?> <?= $q['currency'] ?></p>
        </div>
    </div>

    <!-- Rader -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="font-semibold text-gray-900 dark:text-white">Offertrader</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Artikel</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Beskrivning</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Antal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Enhet</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">À-pris</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Rabatt</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Summa</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php foreach ($lines as $l): ?>
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-400"><?= $l['line_number'] ?></td>
                        <td class="px-4 py-3 text-sm font-mono text-gray-500"><?= $l['article_number'] ?? '—' ?></td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($l['description']) ?></td>
                        <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"><?= $l['quantity'] ?></td>
                        <td class="px-4 py-3 text-sm text-gray-500"><?= $l['unit'] ?></td>
                        <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"><?= number_format((float)$l['unit_price'], 2, ',', ' ') ?></td>
                        <td class="px-4 py-3 text-sm text-right text-gray-500"><?= (float)$l['discount_percent'] > 0 ? $l['discount_percent'] . '%' : '—' ?></td>
                        <td class="px-4 py-3 text-sm text-right font-medium text-gray-900 dark:text-white"><?= number_format((float)$l['line_total'], 2, ',', ' ') ?></td>
                        <td class="px-4 py-3 text-right">
                            <?php if (in_array($q['status'], ['draft'])): ?>
                            <form method="post" action="/sales/quotes/<?= $q['id'] ?>/lines/<?= $l['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort rad?')">
                                <?= \App\Core\Csrf::field() ?>
                                <button class="text-red-500 hover:text-red-700 text-xs">✕</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-700/50">
                    <tr><td colspan="7" class="px-4 py-2 text-sm text-right text-gray-500">Netto</td><td class="px-4 py-2 text-sm text-right font-medium text-gray-900 dark:text-white"><?= number_format((float)$q['subtotal'], 2, ',', ' ') ?></td><td></td></tr>
                    <tr><td colspan="7" class="px-4 py-2 text-sm text-right text-gray-500">Moms</td><td class="px-4 py-2 text-sm text-right text-gray-900 dark:text-white"><?= number_format((float)$q['vat_amount'], 2, ',', ' ') ?></td><td></td></tr>
                    <?php if ((float)$q['rounding'] != 0): ?><tr><td colspan="7" class="px-4 py-2 text-sm text-right text-gray-500">Öresavrundning</td><td class="px-4 py-2 text-sm text-right text-gray-900 dark:text-white"><?= number_format((float)$q['rounding'], 2, ',', ' ') ?></td><td></td></tr><?php endif; ?>
                    <tr><td colspan="7" class="px-4 py-3 text-sm text-right font-bold text-gray-900 dark:text-white">ATT BETALA</td><td class="px-4 py-3 text-right text-lg font-bold text-gray-900 dark:text-white"><?= number_format((float)$q['total_amount'], 2, ',', ' ') ?> <?= $q['currency'] ?></td><td></td></tr>
                </tfoot>
            </table>
        </div>

        <!-- Lägg till rad -->
        <?php if ($q['status'] === 'draft'): ?>
        <div class="border-t border-gray-200 dark:border-gray-700 p-5">
            <form method="post" action="/sales/quotes/<?= $q['id'] ?>/lines" class="grid grid-cols-2 md:grid-cols-7 gap-3 items-end">
                <?= \App\Core\Csrf::field() ?>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Artikel</label>
                    <select name="article_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="">Fritext...</option>
                        <?php foreach ($articles ?? [] as $a): ?>
                            <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['article_number'] . ' — ' . $a['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Beskrivning</label>
                    <input type="text" name="description" placeholder="Auto från artikel" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Antal</label>
                    <input type="number" name="quantity" value="1" step="0.001" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">À-pris</label>
                    <input type="number" name="unit_price" step="0.01" placeholder="Auto" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Rabatt %</label>
                    <input type="number" name="discount_percent" value="0" step="0.5" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Moms %</label>
                    <input type="number" name="vat_rate" value="25" step="0.01" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">+ Lägg till</button>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <?php if ($q['converted_to_order_id']): ?>
    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-xl p-4 flex items-center gap-3">
        <span class="text-lg">✅</span>
        <span class="text-sm text-green-700 dark:text-green-400">Denna offert har konverterats till <a href="/sales/orders/<?= $q['converted_to_order_id'] ?>" class="font-semibold underline">order</a></span>
    </div>
    <?php endif; ?>
</div>
