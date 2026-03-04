<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Nr: <span class="font-mono text-indigo-600 dark:text-indigo-400"><?= htmlspecialchars($product['product_number'], ENT_QUOTES, 'UTF-8') ?></span></p>
        </div>
        <a href="/production/products/<?= $product['id'] ?>/edit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Redigera</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 grid grid-cols-2 gap-x-8 gap-y-4">
        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Status</p>
            <?php
            $statusMap = ['active' => ['bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400', 'Aktiv'],
                          'inactive' => ['bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400', 'Inaktiv'],
                          'discontinued' => ['bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400', 'Utgången']];
            $s = $statusMap[$product['status']] ?? ['bg-gray-100 text-gray-600', $product['status']];
            ?>
            <span class="mt-1 inline-block px-2 py-0.5 rounded text-xs <?= $s[0] ?>"><?= $s[1] ?></span>
        </div>

        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Kategori</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($product['category'] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
        </div>

        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Pris</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                <?php if ($product['unit_price'] !== null): ?>
                    <?= number_format((float) $product['unit_price'], 2, ',', ' ') ?> <?= htmlspecialchars($product['currency'], ENT_QUOTES, 'UTF-8') ?>
                <?php else: ?>—<?php endif; ?>
            </p>
        </div>

        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Vikt</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                <?php if ($product['weight'] !== null): ?>
                    <?= htmlspecialchars((string) $product['weight'], ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars($product['weight_unit'], ENT_QUOTES, 'UTF-8') ?>
                <?php else: ?>—<?php endif; ?>
            </p>
        </div>

        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Mått</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($product['dimensions'] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
        </div>

        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">SKU</p>
            <p class="mt-1 text-sm font-mono text-gray-900 dark:text-white"><?= htmlspecialchars($product['sku'] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
        </div>

        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Streckkod</p>
            <p class="mt-1 text-sm font-mono text-gray-900 dark:text-white"><?= htmlspecialchars($product['barcode'] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
        </div>

        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Minsta lagernivå</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($product['min_stock_level'] !== null ? (string) $product['min_stock_level'] : '—', ENT_QUOTES, 'UTF-8') ?></p>
        </div>

        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Ledtid (dagar)</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($product['lead_time_days'] !== null ? (string) $product['lead_time_days'] : '—', ENT_QUOTES, 'UTF-8') ?></p>
        </div>

        <?php if (!empty($product['datasheet_url'])): ?>
        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Datablad</p>
            <a href="<?= htmlspecialchars($product['datasheet_url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank"
                class="mt-1 text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Öppna datablad</a>
        </div>
        <?php endif; ?>

        <?php if (!empty($product['description'])): ?>
        <div class="col-span-2">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Beskrivning</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= nl2br(htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8')) ?></p>
        </div>
        <?php endif; ?>

        <?php if (!empty($product['composition'])): ?>
        <div class="col-span-2">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Sammansättning</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= nl2br(htmlspecialchars($product['composition'], ENT_QUOTES, 'UTF-8')) ?></p>
        </div>
        <?php endif; ?>
    </div>

    <div class="flex items-center gap-4">
        <a href="/production/products" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">&larr; Tillbaka till produkter</a>
        <form method="POST" action="/production/products/<?= $product['id'] ?>/delete" onsubmit="return confirm('Ta bort produkten permanent?')">
            <?= \App\Core\Csrf::field() ?>
            <button type="submit" class="text-sm text-red-500 hover:text-red-700">Ta bort</button>
        </form>
    </div>
</div>
