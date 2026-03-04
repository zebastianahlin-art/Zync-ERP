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
$transitions = [
    'planned'    => ['confirmed' => 'Bekräfta', 'cancelled' => 'Avbryt'],
    'confirmed'  => ['in_transit' => 'Starta transport', 'cancelled' => 'Avbryt'],
    'in_transit' => ['delivered' => 'Markera levererad'],
    'delivered'  => [],
    'cancelled'  => ['planned' => 'Återaktivera'],
];
$st = $statusMap[$order['status']] ?? ['bg-gray-100 text-gray-600', $order['status']];
$nextStatuses = $transitions[$order['status']] ?? [];
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($order['transport_number'], ENT_QUOTES, 'UTF-8') ?></h1>
            <div class="mt-2 flex gap-2">
                <span class="px-2 py-0.5 rounded text-xs <?= $st[0] ?>"><?= $st[1] ?></span>
                <span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400"><?= htmlspecialchars($typeMap[$order['type']] ?? $order['type'], ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        </div>
        <a href="/transport/orders/<?= $order['id'] ?>/edit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Redigera</a>
    </div>

    <!-- Details -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 grid grid-cols-2 gap-x-8 gap-y-4">
        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Transportör</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($order['carrier_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Kund</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($order['customer_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Upphämtningsadress</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= nl2br(htmlspecialchars($order['pickup_address'] ?? '—', ENT_QUOTES, 'UTF-8')) ?></p>
        </div>
        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Leveransadress</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= nl2br(htmlspecialchars($order['delivery_address'] ?? '—', ENT_QUOTES, 'UTF-8')) ?></p>
        </div>
        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Hämtningsdatum</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($order['pickup_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Leveransdatum</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($order['delivery_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <?php if ($order['actual_pickup']): ?>
        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Faktisk hämtning</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($order['actual_pickup'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <?php endif; ?>
        <?php if ($order['actual_delivery']): ?>
        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Faktisk leverans</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($order['actual_delivery'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <?php endif; ?>
        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Vikt / Volym</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                <?= $order['weight'] ? htmlspecialchars((string) $order['weight'], ENT_QUOTES, 'UTF-8') . ' kg' : '—' ?>
                <?= ($order['weight'] && $order['volume']) ? ' / ' : '' ?>
                <?= $order['volume'] ? htmlspecialchars((string) $order['volume'], ENT_QUOTES, 'UTF-8') . ' m³' : '' ?>
            </p>
        </div>
        <?php if ($order['tracking_number']): ?>
        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Spårningsnummer</p>
            <p class="mt-1 text-sm font-mono text-indigo-600 dark:text-indigo-400"><?= htmlspecialchars($order['tracking_number'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <?php endif; ?>
        <?php if ($order['cost']): ?>
        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Kostnad</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars(number_format((float) $order['cost'], 2), ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars($order['currency'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <?php endif; ?>
        <?php if (!empty($order['notes'])): ?>
        <div class="col-span-2">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Anteckningar</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= nl2br(htmlspecialchars($order['notes'], ENT_QUOTES, 'UTF-8')) ?></p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Status transitions -->
    <?php if (!empty($nextStatuses)): ?>
    <div class="flex flex-wrap gap-3">
        <?php foreach ($nextStatuses as $statusValue => $label): ?>
        <form method="POST" action="/transport/orders/<?= $order['id'] ?>/status">
            <?= \App\Core\Csrf::field() ?>
            <input type="hidden" name="status" value="<?= htmlspecialchars($statusValue, ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
            </button>
        </form>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Footer actions -->
    <div class="flex items-center gap-4">
        <a href="/transport/orders" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">&larr; Tillbaka till ordrar</a>
        <form method="POST" action="/transport/orders/<?= $order['id'] ?>/delete" onsubmit="return confirm('Ta bort transportordern?')">
            <?= \App\Core\Csrf::field() ?>
            <button type="submit" class="text-sm text-red-500 hover:text-red-700">Ta bort</button>
        </form>
    </div>
</div>
