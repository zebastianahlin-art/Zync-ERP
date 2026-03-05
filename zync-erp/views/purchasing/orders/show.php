<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <a href="/purchasing/orders" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($order['order_number']) ?></h1>
            <?= ordShowStatus($order['status']) ?>
        </div>
        <div class="flex gap-2 flex-wrap">
            <?php if ($order['status'] === 'draft'): ?>
                <a href="/purchasing/orders/<?= $order['id'] ?>/edit" class="px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">Redigera</a>
                <form method="POST" action="/purchasing/orders/<?= $order['id'] ?>/status" class="inline">
                    <?= \App\Core\Csrf::field() ?>
                    <input type="hidden" name="status" value="sent">
                    <button type="submit" class="px-3 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Markera som skickad</button>
                </form>
            <?php endif; ?>
            <?php if ($order['status'] === 'sent'): ?>
                <form method="POST" action="/purchasing/orders/<?= $order['id'] ?>/status" class="inline">
                    <?= \App\Core\Csrf::field() ?>
                    <input type="hidden" name="status" value="confirmed">
                    <button type="submit" class="px-3 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">Leverantör bekräftad</button>
                </form>
            <?php endif; ?>
            <?php if (in_array($order['status'], ['confirmed','partially_received'])): ?>
                <form method="POST" action="/purchasing/orders/<?= $order['id'] ?>/status" class="inline">
                    <?= \App\Core\Csrf::field() ?>
                    <input type="hidden" name="status" value="received">
                    <button type="submit" class="px-3 py-2 text-sm bg-green-600 hover:bg-green-700 text-white rounded-lg transition">Allt mottaget</button>
                </form>
            <?php endif; ?>
            <?php if ($order['status'] === 'received'): ?>
                <form method="POST" action="/purchasing/orders/<?= $order['id'] ?>/status" class="inline">
                    <?= \App\Core\Csrf::field() ?>
                    <input type="hidden" name="status" value="closed">
                    <button type="submit" class="px-3 py-2 text-sm bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">Stäng order</button>
                </form>
            <?php endif; ?>
            <?php if (!in_array($order['status'], ['closed','cancelled'])): ?>
                <form method="POST" action="/purchasing/orders/<?= $order['id'] ?>/status" class="inline" onsubmit="return confirm('Vill du avbryta denna order?')">
                    <?= \App\Core\Csrf::field() ?>
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit" class="px-3 py-2 text-sm text-red-600 hover:text-red-800 transition">Avbryt order</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($success)): ?>
        <div class="rounded-lg bg-green-50 dark:bg-green-900/30 p-4 text-green-800 dark:text-green-200"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="rounded-lg bg-red-50 dark:bg-red-900/30 p-4 text-red-800 dark:text-red-200"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Order info + Leverantör -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Orderinformation</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Ordernummer</dt><dd class="text-gray-900 dark:text-white font-mono"><?= htmlspecialchars($order['order_number']) ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Inköpare</dt><dd class="text-gray-900 dark:text-white"><?= htmlspecialchars($order['buyer_name'] ?? '') ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Referens</dt><dd class="text-gray-900 dark:text-white"><?= htmlspecialchars($order['reference'] ?? '—') ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Betalningsvillkor</dt><dd class="text-gray-900 dark:text-white"><?= htmlspecialchars($order['payment_terms'] ?? '') ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Valuta</dt><dd class="text-gray-900 dark:text-white"><?= htmlspecialchars($order['currency']) ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Leveransdatum</dt><dd class="text-gray-900 dark:text-white"><?= $order['delivery_date'] ?: '—' ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Skickad</dt><dd class="text-gray-900 dark:text-white"><?= $order['sent_at'] ?: '—' ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Skapad</dt><dd class="text-gray-900 dark:text-white"><?= $order['created_at'] ?></dd></div>
            </dl>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Leverantör</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Namn</dt><dd class="text-gray-900 dark:text-white font-medium"><?= htmlspecialchars($order['supplier_name'] ?? '') ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Kontakt</dt><dd class="text-gray-900 dark:text-white"><?= htmlspecialchars($order['supplier_contact'] ?? '—') ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">E-post</dt><dd class="text-gray-900 dark:text-white"><?= htmlspecialchars($order['supplier_email'] ?? '—') ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Telefon</dt><dd class="text-gray-900 dark:text-white"><?= htmlspecialchars($order['supplier_phone'] ?? '—') ?></dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400 mb-1">Adress</dt><dd class="text-gray-900 dark:text-white"><?= htmlspecialchars($order['supplier_address'] ?? '') ?>, <?= htmlspecialchars($order['supplier_postal_code'] ?? '') ?> <?= htmlspecialchars($order['supplier_city'] ?? '') ?></dd></div>
            </dl>
        </div>
    </div>

    <!-- Summering -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <div class="flex justify-end">
            <dl class="space-y-1 text-sm w-64">
                <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Netto</dt><dd class="text-gray-900 dark:text-white font-mono"><?= number_format((float)$order['subtotal'], 2, ',', ' ') ?> <?= $order['currency'] ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Moms</dt><dd class="text-gray-900 dark:text-white font-mono"><?= number_format((float)$order['vat_amount'], 2, ',', ' ') ?> <?= $order['currency'] ?></dd></div>
                <div class="flex justify-between border-t border-gray-200 dark:border-gray-700 pt-1"><dt class="font-semibold text-gray-900 dark:text-white">Totalt</dt><dd class="font-bold text-gray-900 dark:text-white font-mono"><?= number_format((float)$order['total_amount'], 2, ',', ' ') ?> <?= $order['currency'] ?></dd></div>
            </dl>
        </div>
    </div>

    <!-- Orderrader -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
        <div class="p-5 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Orderrader</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Artikel</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Beskrivning</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400">Antal</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Enhet</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400">À-pris</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400">Moms %</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400">Radtotal</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Konto</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">KS</th>
                        <?php if ($order['status'] === 'draft'): ?><th class="px-4 py-3"></th><?php endif; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($lines as $l): ?>
                    <tr>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400 font-mono text-xs"><?= htmlspecialchars($l['article_number'] ?? '—') ?></td>
                        <td class="px-4 py-3 text-gray-900 dark:text-white"><?= htmlspecialchars($l['description']) ?></td>
                        <td class="px-4 py-3 text-right text-gray-900 dark:text-white"><?= rtrim(rtrim(number_format((float)$l['quantity'], 3, ',', ' '), '0'), ',') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($l['unit']) ?></td>
                        <td class="px-4 py-3 text-right font-mono text-gray-900 dark:text-white"><?= number_format((float)$l['unit_price'], 2, ',', ' ') ?></td>
                        <td class="px-4 py-3 text-right text-gray-500 dark:text-gray-400"><?= $l['vat_rate'] ?>%</td>
                        <td class="px-4 py-3 text-right font-mono font-medium text-gray-900 dark:text-white"><?= number_format((float)$l['line_total'], 2, ',', ' ') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400 font-mono text-xs"><?= htmlspecialchars(($l['account_number'] ?? '') ? $l['account_number'] . ' ' . $l['account_name'] : '—') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400 font-mono text-xs"><?= htmlspecialchars(($l['cost_center_code'] ?? '') ? $l['cost_center_code'] . ' ' . $l['cost_center_name'] : '—') ?></td>
                        <?php if ($order['status'] === 'draft'): ?>
                        <td class="px-4 py-3">
                            <form method="POST" action="/purchasing/orders/<?= $order['id'] ?>/lines/<?= $l['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort denna orderrad?')">
                                <?= \App\Core\Csrf::field() ?>
                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs">Ta bort</button>
                            </form>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($lines)): ?>
                    <tr><td colspan="10" class="px-4 py-6 text-center text-gray-400">Inga orderrader ännu</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Lägg till rad -->
        <?php if ($order['status'] === 'draft'): ?>
        <div class="p-5 border-t border-gray-200 dark:border-gray-700">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Lägg till orderrad</h3>
            <form method="POST" action="/purchasing/orders/<?= $order['id'] ?>/lines" class="grid grid-cols-1 sm:grid-cols-9 gap-3 items-end">
                <?= \App\Core\Csrf::field() ?>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Artikel</label>
                    <select name="article_id" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm" onchange="fillOrdArticle(this)">
                        <option value="">— Fritext —</option>
                        <?php foreach ($articles as $a): ?>
                        <option value="<?= $a['id'] ?>" data-name="<?= htmlspecialchars($a['name']) ?>" data-price="<?= $a['purchase_price'] ?>" data-unit="<?= $a['unit'] ?>" data-vat="<?= $a['vat_rate'] ?>"><?= htmlspecialchars($a['article_number'] . ' — ' . $a['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Beskrivning *</label>
                    <input type="text" name="description" id="ol-desc" required class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Antal</label>
                    <input type="number" name="quantity" value="1" step="0.001" min="0" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">À-pris</label>
                    <input type="number" name="unit_price" id="ol-price" value="0" step="0.01" min="0" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Moms %</label>
                    <input type="number" name="vat_rate" id="ol-vat" value="25" step="0.01" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Enhet</label>
                    <input type="text" name="unit" value="st" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Konto</label>
                    <select name="account_id" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="">—</option>
                        <?php foreach ($accounts as $acc): ?>
                        <option value="<?= $acc['id'] ?>"><?= htmlspecialchars($acc['account_number'] . ' ' . $acc['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">KS</label>
                    <select name="cost_center_id" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="">—</option>
                        <?php foreach ($costCenters as $cc): ?>
                        <option value="<?= $cc['id'] ?>"><?= htmlspecialchars($cc['code'] . ' ' . $cc['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 rounded text-sm font-medium transition">Lägg till</button>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($order['notes'])): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Anteckningar</h2>
        <p class="text-sm text-gray-700 dark:text-gray-300"><?= nl2br(htmlspecialchars($order['notes'])) ?></p>
    </div>
    <?php endif; ?>
</div>

<script>
function fillOrdArticle(sel) {
    const opt = sel.options[sel.selectedIndex];
    if (opt.value) {
        document.getElementById('ol-desc').value = opt.dataset.name || '';
        document.getElementById('ol-price').value = opt.dataset.price || '0';
        document.getElementById('ol-vat').value = opt.dataset.vat || '25';
    }
}
</script>

<?php
function ordShowStatus(string $s): string {
    $m = [
        'draft' => ['Utkast','bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'],
        'sent' => ['Skickad','bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'],
        'confirmed' => ['Bekräftad','bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300'],
        'partially_received' => ['Dellevererad','bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'],
        'received' => ['Mottagen','bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'],
        'invoiced' => ['Fakturerad','bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300'],
        'closed' => ['Stängd','bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
        'cancelled' => ['Avbruten','bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'],
    ];
    return '<span class="inline-flex px-2 py-1 rounded-full text-xs font-medium '.($m[$s][1]??'bg-gray-100 text-gray-700').'">'.($m[$s][0]??$s).'</span>';
}
?>
