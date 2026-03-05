<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Historiska Inköpsordrar</h1>
        <a href="/purchasing/orders" class="text-sm text-gray-500 hover:text-indigo-600 dark:text-gray-400">← Tillbaka</a>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-500">Ordernummer</th>
                    <th class="px-4 py-3 text-left text-gray-500">Leverantör</th>
                    <th class="px-4 py-3 text-left text-gray-500">Inköpare</th>
                    <th class="px-4 py-3 text-left text-gray-500">Status</th>
                    <th class="px-4 py-3 text-right text-gray-500">Totalt</th>
                    <th class="px-4 py-3 text-left text-gray-500">Skapad</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                <?php foreach ($orders as $o): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-4 py-3 font-mono text-xs text-gray-600 dark:text-gray-400">
                        <a href="/purchasing/orders/<?= (int)$o['id'] ?>" class="text-indigo-600 hover:underline">
                            <?= htmlspecialchars($o['order_number'] ?? $o['id'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </td>
                    <td class="px-4 py-3 text-gray-800 dark:text-gray-200"><?= htmlspecialchars($o['supplier_name'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($o['buyer_name'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs <?= ($o['status'] ?? '') === 'cancelled' ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' : 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' ?>">
                            <?= htmlspecialchars($o['status'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right font-mono font-semibold"><?= number_format((float)($o['total_amount'] ?? 0), 2, ',', ' ') ?> kr</td>
                    <td class="px-4 py-3 text-gray-500 text-xs"><?= htmlspecialchars(substr($o['created_at'] ?? '', 0, 10), ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($orders)): ?>
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Inga historiska inköpsordrar hittades.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
