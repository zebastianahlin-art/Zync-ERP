<?php
$typeLabels = [
    'receipt'    => 'Inleverans',
    'issue'      => 'Uttag',
    'adjustment' => 'Justering',
    'transfer'   => 'Överföring',
];
$typeBadgeClasses = [
    'receipt'    => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200',
    'issue'      => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-200',
    'adjustment' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200',
    'transfer'   => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-200',
];
$type = $transaction['type'] ?? '';
?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Transaktion</h1>
        <a href="/inventory/transactions"
           class="inline-flex items-center gap-2 rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
            ← Tillbaka
        </a>
    </div>

    <!-- Detail card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Transaktionsdetaljer</h2>
        </div>
        <dl class="divide-y divide-gray-200 dark:divide-gray-700">
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Datum</dt>
                <dd class="text-sm text-gray-900 dark:text-white col-span-2">
                    <?= htmlspecialchars($transaction['transaction_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                </dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Typ</dt>
                <dd class="text-sm col-span-2">
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium <?= htmlspecialchars($typeBadgeClasses[$type] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300', ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($typeLabels[$type] ?? $type, ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Artikel</dt>
                <dd class="text-sm text-gray-900 dark:text-white col-span-2">
                    <?= htmlspecialchars($transaction['article_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                    <?php if (!empty($transaction['article_number'])): ?>
                    <span class="ml-2 font-mono text-xs text-indigo-600 dark:text-indigo-400">
                        <?= htmlspecialchars($transaction['article_number'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                    <?php endif; ?>
                </dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Lagerställe</dt>
                <dd class="text-sm text-gray-900 dark:text-white col-span-2">
                    <?= htmlspecialchars($transaction['warehouse_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                </dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Antal</dt>
                <dd class="text-sm font-semibold text-gray-900 dark:text-white col-span-2">
                    <?= htmlspecialchars((string) ($transaction['quantity'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                </dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Referenstyp</dt>
                <dd class="text-sm text-gray-900 dark:text-white col-span-2">
                    <?= htmlspecialchars($transaction['reference_type'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                </dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Referens-ID</dt>
                <dd class="text-sm text-gray-900 dark:text-white col-span-2">
                    <?= !empty($transaction['reference_id']) ? htmlspecialchars((string) $transaction['reference_id'], ENT_QUOTES, 'UTF-8') : '—' ?>
                </dd>
            </div>
            <?php if ($type === 'transfer'): ?>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Till lagerställe</dt>
                <dd class="text-sm text-gray-900 dark:text-white col-span-2">
                    <?= htmlspecialchars($transaction['to_warehouse_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                </dd>
            </div>
            <?php endif; ?>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Noteringar</dt>
                <dd class="text-sm text-gray-900 dark:text-white col-span-2 whitespace-pre-wrap">
                    <?= htmlspecialchars($transaction['notes'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                </dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Skapad av</dt>
                <dd class="text-sm text-gray-900 dark:text-white col-span-2">
                    <?= htmlspecialchars($transaction['created_by_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                </dd>
            </div>
        </dl>
    </div>
</div>
