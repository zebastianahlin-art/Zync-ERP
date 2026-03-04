<?php
$statusMap = [
    'planned'    => ['bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400', 'Planerad'],
    'confirmed'  => ['bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400', 'Bekräftad'],
    'in_transit' => ['bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400', 'Under transport'],
    'delivered'  => ['bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400', 'Levererad'],
    'cancelled'  => ['bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400', 'Avbruten'],
];
$typeMap = [
    'inbound'  => 'Inkommande',
    'outbound' => 'Utgående',
    'internal' => 'Intern',
];
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Transportordrar</h1>
        <a href="/transport/orders/create" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">+ Ny transportorder</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Transportnr</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Typ</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Transportör</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Kund</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Status</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Hämtning</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Leverans</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($orders as $o): ?>
                    <?php $st = $statusMap[$o['status']] ?? ['bg-gray-100 text-gray-600', $o['status']]; ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-mono text-xs text-indigo-600 dark:text-indigo-400">
                            <a href="/transport/orders/<?= $o['id'] ?>" class="hover:underline"><?= htmlspecialchars($o['transport_number'], ENT_QUOTES, 'UTF-8') ?></a>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($typeMap[$o['type']] ?? $o['type'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($o['carrier_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($o['customer_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-xs <?= $st[0] ?>"><?= $st[1] ?></span></td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs"><?= htmlspecialchars($o['pickup_date'] ? substr($o['pickup_date'], 0, 10) : '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs"><?= htmlspecialchars($o['delivery_date'] ? substr($o['delivery_date'], 0, 10) : '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="/transport/orders/<?= $o['id'] ?>" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline mr-2">Visa</a>
                            <a href="/transport/orders/<?= $o['id'] ?>/edit" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline mr-2">Redigera</a>
                            <form method="POST" action="/transport/orders/<?= $o['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort transportordern?')">
                                <?= \App\Core\Csrf::field() ?>
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700">Ta bort</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($orders)): ?>
                    <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">Inga transportordrar registrerade ännu</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
