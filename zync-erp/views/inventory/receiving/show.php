<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Inleverans – <?= htmlspecialchars($order['order_number'] ?? ('PO-' . $order['id']), ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                <?= htmlspecialchars($order['supplier_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
            </p>
        </div>
        <a href="/inventory/receiving"
           class="inline-flex items-center gap-2 rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
            ← Tillbaka
        </a>
    </div>

    <!-- Order details card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Orderdetaljer</h2>
        <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div>
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">PO-nummer</dt>
                <dd class="mt-1 text-sm font-mono text-indigo-600 dark:text-indigo-400">
                    <?= htmlspecialchars($order['order_number'] ?? ('PO-' . $order['id']), ENT_QUOTES, 'UTF-8') ?>
                </dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Leverantör</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                    <?= htmlspecialchars($order['supplier_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                </dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Status</dt>
                <dd class="mt-1">
                    <span class="inline-flex items-center rounded-full bg-blue-100 dark:bg-blue-900/30 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:text-blue-200">
                        <?= htmlspecialchars($order['status'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Datum</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                    <?= htmlspecialchars($order['order_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                </dd>
            </div>
        </dl>
    </div>

    <!-- Receiving form -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <form method="POST" action="/inventory/receiving/<?= htmlspecialchars((string) $order['id'], ENT_QUOTES, 'UTF-8') ?>">
            <?= \App\Core\Csrf::field() ?>

            <!-- Warehouse select -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Inlevereras till lagerställe <span class="text-red-500">*</span>
                </label>
                <select name="warehouse_id" required
                        class="w-full sm:w-72 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    <option value="">— Välj lagerställe —</option>
                    <?php foreach ($warehouses as $wh): ?>
                    <option value="<?= htmlspecialchars((string) $wh['id'], ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($wh['name'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Order lines -->
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($lines as $line): ?>
                <?php
                $lineId = $line['id'] ?? null;
                if (empty($lineId)) {
                    continue;
                }
                $lineId = (int) $lineId;
                ?>
                <input type="hidden" name="lines[<?= htmlspecialchars((string) $lineId, ENT_QUOTES, 'UTF-8') ?>][article_id]"
                       value="<?= htmlspecialchars((string) ($line['article_id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                <div class="px-6 py-4">
                    <div class="flex items-start gap-4">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                <?= htmlspecialchars($line['article_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                            </p>
                            <p class="text-xs font-mono text-indigo-600 dark:text-indigo-400 mt-0.5">
                                <?= htmlspecialchars($line['article_number'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Beställt: <?= htmlspecialchars((string) ($line['quantity'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                                <?php if (!empty($line['received_quantity'])): ?>
                                &nbsp;|&nbsp; Redan mottaget: <?= htmlspecialchars((string) $line['received_quantity'], ENT_QUOTES, 'UTF-8') ?>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="flex gap-3 items-end">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Mottaget antal</label>
                                <input type="number" name="lines[<?= htmlspecialchars((string) $lineId, ENT_QUOTES, 'UTF-8') ?>][quantity]"
                                       step="0.01" min="0"
                                       placeholder="0"
                                       class="w-28 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Noteringar</label>
                                <input type="text" name="lines[<?= htmlspecialchars((string) $lineId, ENT_QUOTES, 'UTF-8') ?>][notes]"
                                       class="w-48 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($lines)): ?>
                <div class="px-6 py-8 text-center text-gray-400">Inga orderrader</div>
                <?php endif; ?>
            </div>

            <!-- Submit -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center gap-3">
                <button type="submit"
                        class="rounded-lg bg-indigo-600 px-6 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
                    Registrera inleverans
                </button>
                <a href="/inventory/receiving"
                   class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Avbryt</a>
            </div>
        </form>
    </div>
</div>
