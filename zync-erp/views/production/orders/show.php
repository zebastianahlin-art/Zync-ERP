<?php
$statusMap = [
    'draft'       => ['bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300', 'Utkast'],
    'planned'     => ['bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400', 'Planerad'],
    'in_progress' => ['bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400', 'Pågår'],
    'completed'   => ['bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400', 'Avslutad'],
    'cancelled'   => ['bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400', 'Avbruten'],
];
$s = $statusMap[$order['status']] ?? ['bg-gray-100 text-gray-600', $order['status']];

$transitions = [
    'draft'       => ['planned' => 'Markera som planerad'],
    'planned'     => ['in_progress' => 'Starta produktion'],
    'in_progress' => ['completed' => 'Markera som avslutad', 'cancelled' => 'Avbryt order'],
];
$nextStatuses = $transitions[$order['status']] ?? [];
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($order['order_number'], ENT_QUOTES, 'UTF-8') ?></h1>
            <span class="mt-1 inline-block px-2 py-0.5 rounded text-xs <?= $s[0] ?>"><?= $s[1] ?></span>
        </div>
        <a href="/production/orders/<?= $order['id'] ?>/edit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Redigera</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 grid grid-cols-2 gap-x-8 gap-y-4">
        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Produktionslinje</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($order['line_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Antal</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars((string) $order['quantity'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Planerad start</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($order['planned_start'] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Planerat slut</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($order['planned_end'] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <?php if (!empty($order['notes'])): ?>
        <div class="col-span-2">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Anteckningar</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= nl2br(htmlspecialchars($order['notes'], ENT_QUOTES, 'UTF-8')) ?></p>
        </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($nextStatuses)): ?>
    <div class="flex gap-3">
        <?php foreach ($nextStatuses as $statusValue => $label): ?>
        <form method="POST" action="/production/orders/<?= $order['id'] ?>/status">
            <?= \App\Core\Csrf::field() ?>
            <input type="hidden" name="status" value="<?= htmlspecialchars($statusValue, ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
            </button>
        </form>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="flex items-center gap-4">
        <a href="/production/orders" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">&larr; Tillbaka till ordrar</a>
        <form method="POST" action="/production/orders/<?= $order['id'] ?>/delete" onsubmit="return confirm('Ta bort ordern?')">
            <?= \App\Core\Csrf::field() ?>
            <button type="submit" class="text-sm text-red-500 hover:text-red-700">Ta bort</button>
        </form>
    </div>
</div>
