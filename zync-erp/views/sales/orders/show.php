<?php $o = $order; ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="/sales/orders" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Order <?= $o['order_number'] ?></h1>
                <p class="text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($o['customer_name']) ?> (<?= $o['customer_number'] ?>)<?= $o['customer_order_number'] ? ' · Kundorder: ' . htmlspecialchars($o['customer_order_number']) : '' ?></p>
            </div>
        </div>
        <div class="flex gap-2 flex-wrap">
            <?php if ($o['status'] === 'draft'): ?>
                <form method="post" action="/sales/orders/<?= $o['id'] ?>/status" class="inline"><?= \App\Core\Csrf::field() ?><input type="hidden" name="status" value="confirmed"><button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">✅ Bekräfta order</button></form>
            <?php endif; ?>
            <?php if (in_array($o['status'], ['confirmed'])): ?>
                <form method="post" action="/sales/orders/<?= $o['id'] ?>/production" class="inline"><?= \App\Core\Csrf::field() ?><button class="rounded-lg bg-yellow-600 px-4 py-2 text-sm font-medium text-white hover:bg-yellow-700" onclick="return confirm('Skapa produktionsordrar?')">🏭 Skapa produktionsordrar</button></form>
            <?php endif; ?>
            <?php if (in_array($o['status'], ['in_production','confirmed'])): ?>
                <form method="post" action="/sales/orders/<?= $o['id'] ?>/status" class="inline"><?= \App\Core\Csrf::field() ?><input type="hidden" name="status" value="delivered"><button class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">📦 Markera levererad</button></form>
            <?php endif; ?>
            <?php if ($o['status'] === 'delivered'): ?>
                <form method="post" action="/sales/orders/<?= $o['id'] ?>/status" class="inline"><?= \App\Core\Csrf::field() ?><input type="hidden" name="status" value="invoiced"><button class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">💰 Markera fakturerad</button></form>
            <?php endif; ?>
            <?php if (!in_array($o['status'], ['cancelled','invoiced'])): ?>
                <form method="post" action="/sales/orders/<?= $o['id'] ?>/status" class="inline"><?= \App\Core\Csrf::field() ?><input type="hidden" name="status" value="cancelled"><button class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700" onclick="return confirm('Avbryt order?')">Avbryt</button></form>
            <?php endif; ?>
            <a href="/sales/orders/<?= $o['id'] ?>/edit" class="rounded-lg bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">Redigera</a>
        </div>
    </div>

    <!-- Status & Info -->
    <?php
        $sc = match($o['status']) { 'draft'=>'gray','confirmed'=>'blue','in_production'=>'yellow','partially_delivered'=>'orange','delivered'=>'green','invoiced'=>'emerald','cancelled'=>'red', default=>'gray' };
        $sl = match($o['status']) { 'draft'=>'Utkast','confirmed'=>'Bekräftad','in_production'=>'I produktion','partially_delivered'=>'Dellevererad','delivered'=>'Levererad','invoiced'=>'Fakturerad','cancelled'=>'Avbruten', default=>$o['status'] };
    ?>
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 uppercase">Status</p>
            <span class="mt-1 inline-flex items-center rounded-full bg-<?= $sc ?>-100 dark:bg-<?= $sc ?>-900/30 px-3 py-1 text-sm font-medium text-<?= $sc ?>-700 dark:text-<?= $sc ?>-400"><?= $sl ?></span>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 uppercase">Orderdatum</p>
            <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white"><?= $o['order_date'] ?></p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 uppercase">Utlovat leverans</p>
            <?php $late = $o['promised_delivery'] && $o['promised_delivery'] < date('Y-m-d') && !in_array($o['status'], ['delivered','invoiced','cancelled']); ?>
            <p class="mt-1 text-sm font-medium <?= $late ? 'text-red-500' : 'text-gray-900 dark:text-white' ?>"><?= $o['promised_delivery'] ?? '—' ?><?= $late ? ' ⚠️' : '' ?></p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 uppercase">Betalning</p>
            <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white"><?= $o['payment_terms'] ?></p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 uppercase">Leveransvillkor</p>
            <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white"><?= $o['delivery_terms'] ?: '—' ?></p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 uppercase">Totalbelopp</p>
            <p class="mt-1 text-lg font-bold text-gray-900 dark:text-white"><?= number_format((float)$o['total_amount'], 2, ',', ' ') ?> <?= $o['currency'] ?></p>
        </div>
    </div>

    <!-- Orderrader -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="font-semibold text-gray-900 dark:text-white">Orderrader</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Artikel</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Beskrivning</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Antal</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Levererat</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Enhet</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">À-pris</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Rabatt</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Summa</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Prod.order</th>
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
                        <td class="px-4 py-3 text-sm text-right <?= (float)$l['quantity_delivered'] >= (float)$l['quantity'] ? 'text-green-600' : 'text-gray-500' ?>"><?= $l['quantity_delivered'] ?></td>
                        <td class="px-4 py-3 text-sm text-gray-500"><?= $l['unit'] ?></td>
                        <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"><?= number_format((float)$l['unit_price'], 2, ',', ' ') ?></td>
                        <td class="px-4 py-3 text-sm text-right text-gray-500"><?= (float)$l['discount_percent'] > 0 ? $l['discount_percent'] . '%' : '—' ?></td>
                        <td class="px-4 py-3 text-sm text-right font-medium text-gray-900 dark:text-white"><?= number_format((float)$l['line_total'], 2, ',', ' ') ?></td>
                        <td class="px-4 py-3 text-center"><?php if ($l['production_order_id']): ?><a href="/production/orders/<?= $l['production_order_id'] ?>" class="text-indigo-600 dark:text-indigo-400 text-xs hover:underline">PO-<?= $l['production_order_id'] ?></a><?php else: ?>—<?php endif; ?></td>
                        <td class="px-4 py-3 text-right">
                            <?php if ($o['status'] === 'draft'): ?>
                            <form method="post" action="/sales/orders/<?= $o['id'] ?>/lines/<?= $l['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort rad?')">
                                <?= \App\Core\Csrf::field() ?>
                                <button class="text-red-500 hover:text-red-700 text-xs">✕</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-700/50">
                    <tr><td colspan="8" class="px-4 py-2 text-sm text-right text-gray-500">Netto</td><td class="px-4 py-2 text-sm text-right font-medium text-gray-900 dark:text-white"><?= number_format((float)$o['subtotal'], 2, ',', ' ') ?></td><td colspan="2"></td></tr>
                    <tr><td colspan="8" class="px-4 py-2 text-sm text-right text-gray-500">Moms</td><td class="px-4 py-2 text-sm text-right text-gray-900 dark:text-white"><?= number_format((float)$o['vat_amount'], 2, ',', ' ') ?></td><td colspan="2"></td></tr>
                    <?php if ((float)$o['rounding'] != 0): ?><tr><td colspan="8" class="px-4 py-2 text-sm text-right text-gray-500">Öresavrundning</td><td class="px-4 py-2 text-sm text-right text-gray-900 dark:text-white"><?= number_format((float)$o['rounding'], 2, ',', ' ') ?></td><td colspan="2"></td></tr><?php endif; ?>
                    <tr><td colspan="8" class="px-4 py-3 text-sm text-right font-bold text-gray-900 dark:text-white">TOTALT</td><td class="px-4 py-3 text-right text-lg font-bold text-gray-900 dark:text-white"><?= number_format((float)$o['total_amount'], 2, ',', ' ') ?> <?= $o['currency'] ?></td><td colspan="2"></td></tr>
                </tfoot>
            </table>
        </div>

        <!-- Lägg till rad -->
        <?php if ($o['status'] === 'draft'): ?>
        <div class="border-t border-gray-200 dark:border-gray-700 p-5">
            <form method="post" action="/sales/orders/<?= $o['id'] ?>/lines" class="grid grid-cols-2 md:grid-cols-7 gap-3 items-end">
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

    <!-- Koppling till offert -->
    <?php if ($o['quote_id']): ?>
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-xl p-4 flex items-center gap-3">
        <span class="text-lg">📋</span>
        <span class="text-sm text-blue-700 dark:text-blue-400">Skapad från <a href="/sales/quotes/<?= $o['quote_id'] ?>" class="font-semibold underline">offert</a></span>
    </div>
    <?php endif; ?>

    <!-- Aktivitetslogg -->
    <?php if (!empty($activities)): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
        <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Aktivitetslogg</h3>
        <div class="space-y-2">
            <?php foreach ($activities as $a): $ic = match($a['activity_type']) { 'status_change'=>'🔄','order_confirmed'=>'✅',default=>'📝' }; ?>
            <div class="flex gap-3 py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                <span><?= $ic ?></span>
                <div>
                    <p class="text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($a['subject']) ?></p>
                    <p class="text-xs text-gray-400"><?= $a['activity_date'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
