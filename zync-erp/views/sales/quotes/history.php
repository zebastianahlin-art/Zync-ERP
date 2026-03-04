<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Historiska Offerter</h1>
        <a href="/sales/quotes" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">&larr; Alla offerter</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Offertnr</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Kund</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Giltig till</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Status</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Skapad</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($quotes as $quote): ?>
                    <?php
                        $statusColors = [
                            'expired'   => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
                            'rejected'  => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
                            'cancelled' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                        ];
                        $statusLabels = ['expired' => 'Utgången', 'rejected' => 'Avslagen', 'cancelled' => 'Avbruten'];
                        $sc = $statusColors[$quote['status']] ?? 'bg-gray-100 text-gray-700';
                        $sl = $statusLabels[$quote['status']] ?? htmlspecialchars($quote['status'], ENT_QUOTES, 'UTF-8');
                    ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                            <a href="/sales/quotes/<?= (int) $quote['id'] ?>" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                <?= htmlspecialchars($quote['quote_number'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($quote['customer_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($quote['valid_until'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium <?= $sc ?>">
                                <?= $sl ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs"><?= htmlspecialchars($quote['created_at'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-right">
                            <a href="/sales/quotes/<?= (int) $quote['id'] ?>" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Visa</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($quotes)): ?>
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">Inga historiska offerter</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
