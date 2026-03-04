<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Säljordrar</h1>
        <a href="/sales/orders/create" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">+ Ny order</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Ordernr</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Kund</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Status</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Skapad</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($orders as $order): ?>
                    <?php
                        $statusColors = [
                            'draft'       => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                            'confirmed'   => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
                            'in_progress' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
                            'shipped'     => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300',
                            'completed'   => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
                            'cancelled'   => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
                        ];
                        $statusLabels = [
                            'draft' => 'Utkast', 'confirmed' => 'Bekräftad', 'in_progress' => 'Pågår',
                            'shipped' => 'Skickad', 'completed' => 'Klar', 'cancelled' => 'Avbruten',
                        ];
                        $sc = $statusColors[$order['status']] ?? 'bg-gray-100 text-gray-700';
                        $sl = $statusLabels[$order['status']] ?? htmlspecialchars($order['status'], ENT_QUOTES, 'UTF-8');
                    ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                            <a href="/sales/orders/<?= (int) $order['id'] ?>" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                <?= htmlspecialchars($order['order_number'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($order['customer_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium <?= $sc ?>"><?= $sl ?></span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs"><?= htmlspecialchars($order['created_at'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a href="/sales/orders/<?= (int) $order['id'] ?>" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Visa</a>
                            <a href="/sales/orders/<?= (int) $order['id'] ?>/edit" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Redigera</a>
                            <form method="POST" action="/sales/orders/<?= (int) $order['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort order?')">
                                <?= \App\Core\Csrf::field() ?>
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700">Ta bort</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($orders)): ?>
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">Inga säljordrar registrerade ännu</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
