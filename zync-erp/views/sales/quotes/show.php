<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Offert <?= htmlspecialchars($quote['quote_number'], ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Kund: <?= htmlspecialchars($quote['customer_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <a href="/sales/quotes/<?= $quote['id'] ?>/edit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Redigera</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 grid grid-cols-2 gap-4 text-sm">
        <div><span class="text-gray-500 dark:text-gray-400">Status:</span> <span class="ml-1 font-medium"><?= htmlspecialchars($quote['status'], ENT_QUOTES, 'UTF-8') ?></span></div>
        <div><span class="text-gray-500 dark:text-gray-400">Giltig till:</span> <span class="ml-1"><?= htmlspecialchars($quote['valid_until'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
        <?php if (!empty($quote['notes'])): ?>
        <div class="col-span-2"><span class="text-gray-500 dark:text-gray-400">Anteckningar:</span> <p class="mt-1"><?= nl2br(htmlspecialchars($quote['notes'], ENT_QUOTES, 'UTF-8')) ?></p></div>
        <?php endif; ?>
    </div>

    <?php if (!empty($lines)): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 font-semibold text-gray-900 dark:text-white">Offertrader</div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Beskrivning</th>
                    <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400 uppercase text-xs">Antal</th>
                    <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400 uppercase text-xs">À-pris</th>
                    <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400 uppercase text-xs">Rabatt %</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($lines as $line): ?>
                <tr>
                    <td class="px-4 py-3"><?= htmlspecialchars($line['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-right"><?= htmlspecialchars((string) $line['quantity'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-right"><?= number_format((float) $line['unit_price'], 2, ',', ' ') ?></td>
                    <td class="px-4 py-3 text-right"><?= htmlspecialchars((string) $line['discount'], ENT_QUOTES, 'UTF-8') ?> %</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <div>
        <a href="/sales/quotes" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">&larr; Tillbaka till offerter</a>
    </div>
</div>
