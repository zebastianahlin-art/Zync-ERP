<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Produkter</h1>
        <a href="/production/products/create" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">+ Ny produkt</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
        <input type="text" id="search" placeholder="Sök produkt..."
            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
            onkeyup="filterTable()">
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="productTable">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Produktnr</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Namn</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Kategori</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Pris</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($products as $product): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-mono text-xs text-indigo-600 dark:text-indigo-400">
                            <?= htmlspecialchars($product['product_number'], ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                            <a href="/production/products/<?= $product['id'] ?>" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            <?= htmlspecialchars($product['category'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3 text-gray-900 dark:text-white">
                            <?php if ($product['unit_price'] !== null): ?>
                                <?= number_format((float) $product['unit_price'], 2, ',', ' ') ?>
                                <?= htmlspecialchars($product['currency'], ENT_QUOTES, 'UTF-8') ?>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3">
                            <?php
                            $statusMap = ['active' => ['bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400', 'Aktiv'],
                                          'inactive' => ['bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400', 'Inaktiv'],
                                          'discontinued' => ['bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400', 'Utgången']];
                            $s = $statusMap[$product['status']] ?? ['bg-gray-100 text-gray-600', $product['status']];
                            ?>
                            <span class="px-2 py-0.5 rounded text-xs <?= $s[0] ?>"><?= $s[1] ?></span>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="/production/products/<?= $product['id'] ?>/edit" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline mr-3">Redigera</a>
                            <form method="POST" action="/production/products/<?= $product['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort produkten?')">
                                <?= \App\Core\Csrf::field() ?>
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700">Ta bort</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($products)): ?>
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Inga produkter registrerade ännu</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
function filterTable() {
    const q = document.getElementById('search').value.toLowerCase();
    document.querySelectorAll('#productTable tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}
</script>
