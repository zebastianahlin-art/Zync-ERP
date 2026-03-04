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
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Order <?= htmlspecialchars($order['order_number'], ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Kund: <strong><?= htmlspecialchars($order['customer_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></strong>
                &middot; Skapad: <?= htmlspecialchars($order['created_at'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
            </p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?= $sc ?>"><?= $sl ?></span>
            <a href="/sales/orders/<?= (int) $order['id'] ?>/edit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Redigera</a>
            <a href="/sales/orders" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">&larr; Ordrar</a>
        </div>
    </div>

    <!-- Order details -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-3">
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-500 dark:text-gray-400">Ordernummer</span>
                <p class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($order['order_number'], ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Kund</span>
                <p class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($order['customer_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <?php if ($order['quote_id']): ?>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Kopplad offert</span>
                <p class="font-medium text-gray-900 dark:text-white">
                    <a href="/sales/quotes/<?= (int) $order['quote_id'] ?>" class="text-indigo-600 dark:text-indigo-400 hover:underline">#<?= (int) $order['quote_id'] ?></a>
                </p>
            </div>
            <?php endif; ?>
            <?php if ($order['notes']): ?>
            <div class="col-span-2">
                <span class="text-gray-500 dark:text-gray-400">Anteckningar</span>
                <p class="text-gray-900 dark:text-white mt-1"><?= htmlspecialchars($order['notes'], ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Status change -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Ändra status</h2>
        <div class="flex flex-wrap gap-2">
            <?php
            $transitions = [
                'confirmed'   => 'Bekräfta',
                'in_progress' => 'Markera som pågår',
                'shipped'     => 'Markera som skickad',
                'completed'   => 'Markera som klar',
                'cancelled'   => 'Avbryt order',
            ];
            foreach ($transitions as $st => $label):
                if ($st === $order['status']) continue;
                $btnClass = $st === 'cancelled'
                    ? 'bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-900/40 dark:hover:bg-red-900/60 dark:text-red-300'
                    : 'bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200 text-gray-700';
            ?>
            <form method="POST" action="/sales/orders/<?= (int) $order['id'] ?>/status">
                <?= \App\Core\Csrf::field() ?>
                <input type="hidden" name="status" value="<?= htmlspecialchars($st, ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit" class="px-3 py-1.5 text-xs font-medium rounded-lg transition <?= $btnClass ?>">
                    <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                </button>
            </form>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex items-center gap-4">
        <a href="/finance/invoices/create?customer_id=<?= (int) ($order['customer_id'] ?? 0) ?>&order_id=<?= (int) $order['id'] ?>"
            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
            Skapa faktura
        </a>
        <form method="POST" action="/sales/orders/<?= (int) $order['id'] ?>/delete" onsubmit="return confirm('Ta bort order?')">
            <?= \App\Core\Csrf::field() ?>
            <button type="submit" class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-900/40 dark:hover:bg-red-900/60 dark:text-red-300 text-sm font-medium rounded-lg transition">
                Ta bort order
            </button>
        </form>
    </div>
</div>
