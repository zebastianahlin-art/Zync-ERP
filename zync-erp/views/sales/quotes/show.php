<div class="space-y-6">
    <?php
    $statusLabels = ['draft' => 'Utkast', 'sent' => 'Skickad', 'accepted' => 'Accepterad', 'rejected' => 'Avvisad', 'expired' => 'Utgången'];
    $statusColors = ['draft' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300', 'sent' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300', 'accepted' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300', 'rejected' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300', 'expired' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300'];
    $total = 0;
    foreach ($lines as $l) {
        $total += (float)$l['quantity'] * (float)$l['unit_price'] * (1 - (float)$l['discount'] / 100);
    }
    ?>

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Offert <?= htmlspecialchars($quote['quote_number'], ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Kund: <?= htmlspecialchars($quote['customer_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div class="flex items-center gap-3">
            <?php $terminalStatuses = ['rejected', 'expired']; ?>
            <a href="/sales/quotes/<?= (int)$quote['id'] ?>/pdf" target="_blank"
               class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 text-sm font-medium rounded-lg transition">
                Skriv ut / PDF
            </a>
            <?php if ($quote['status'] === 'draft'): ?>
            <form method="POST" action="/sales/quotes/<?= (int)$quote['id'] ?>/send" onsubmit="return confirm('Markera offerten som skickad?')">
                <?= \App\Core\Csrf::field() ?>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">Skicka offert</button>
            </form>
            <?php endif; ?>
            <?php if (empty($quote['converted_to_order_id']) && !in_array($quote['status'], $terminalStatuses, true)): ?>
            <form method="POST" action="/sales/quotes/<?= $quote['id'] ?>/convert" onsubmit="return confirm('Konvertera offerten till en säljorder?')">
                <?= \App\Core\Csrf::field() ?>
                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">Konvertera till order</button>
            </form>
            <?php elseif (!empty($quote['converted_to_order_id'])): ?>
            <a href="/sales/orders/<?= (int)$quote['converted_to_order_id'] ?>" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg">Visa order &rarr;</a>
            <?php endif; ?>
            <a href="/sales/quotes/<?= $quote['id'] ?>/edit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Redigera</a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
        <div>
            <span class="text-gray-500 dark:text-gray-400 block text-xs uppercase tracking-wider mb-1">Status</span>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusColors[$quote['status']] ?? '' ?>">
                <?= htmlspecialchars($statusLabels[$quote['status']] ?? $quote['status'], ENT_QUOTES, 'UTF-8') ?>
            </span>
        </div>
        <div>
            <span class="text-gray-500 dark:text-gray-400 block text-xs uppercase tracking-wider mb-1">Giltig till</span>
            <span class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($quote['valid_until'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <?php if (!empty($quote['delivery_terms'])): ?>
        <div>
            <span class="text-gray-500 dark:text-gray-400 block text-xs uppercase tracking-wider mb-1">Leveransvillkor</span>
            <span class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($quote['delivery_terms'], ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <?php endif; ?>
        <?php if (!empty($quote['payment_terms'])): ?>
        <div>
            <span class="text-gray-500 dark:text-gray-400 block text-xs uppercase tracking-wider mb-1">Betalningsvillkor</span>
            <span class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($quote['payment_terms'], ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <?php endif; ?>
        <?php if (!empty($quote['notes'])): ?>
        <div class="col-span-2 md:col-span-3">
            <span class="text-gray-500 dark:text-gray-400 block text-xs uppercase tracking-wider mb-1">Anteckningar</span>
            <p class="text-gray-900 dark:text-white"><?= nl2br(htmlspecialchars($quote['notes'], ENT_QUOTES, 'UTF-8')) ?></p>
        </div>
        <?php endif; ?>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 font-semibold text-gray-900 dark:text-white">Offertrader</div>
        <?php if (!empty($lines)): ?>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Artikel</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Beskrivning</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Antal</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">À-pris</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Rab %</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Summa</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($lines as $line):
                    $lineTotal = (float)$line['quantity'] * (float)$line['unit_price'] * (1 - (float)$line['discount'] / 100);
                ?>
                <tr>
                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs"><?= htmlspecialchars($line['article_number'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-900 dark:text-white"><?= htmlspecialchars($line['description'] ?? ($line['article_name'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-right text-gray-900 dark:text-white"><?= number_format((float)$line['quantity'], 2, ',', ' ') ?></td>
                    <td class="px-4 py-3 text-right text-gray-900 dark:text-white"><?= number_format((float)$line['unit_price'], 2, ',', ' ') ?> kr</td>
                    <td class="px-4 py-3 text-right text-gray-900 dark:text-white"><?= number_format((float)$line['discount'], 1, ',', '') ?> %</td>
                    <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white"><?= number_format($lineTotal, 2, ',', ' ') ?> kr</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="border-t-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                <tr>
                    <td colspan="5" class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-300">Totalsumma:</td>
                    <td class="px-4 py-3 text-right font-bold text-indigo-600 dark:text-indigo-400"><?= number_format($total, 2, ',', ' ') ?> kr</td>
                </tr>
            </tfoot>
        </table>
        <?php else: ?>
        <p class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Inga offertrader har lagts till.</p>
        <?php endif; ?>
    </div>

    <div>
        <a href="/sales/quotes" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">&larr; Tillbaka till offerter</a>
    </div>
</div>

