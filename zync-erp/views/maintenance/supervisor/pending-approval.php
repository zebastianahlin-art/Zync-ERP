<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="/maintenance/supervisor" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Väntar attestering</h1>
    </div>

    <div class="space-y-4">
        <?php foreach ($workOrders as $wo): ?>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <a href="/maintenance/work-orders/<?= $wo['id'] ?>" class="text-lg font-semibold text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400"><?= htmlspecialchars($wo['title'], ENT_QUOTES, 'UTF-8') ?></a>
                        <span class="text-xs text-gray-500 dark:text-gray-400 font-mono"><?= htmlspecialchars($wo['order_number'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Utfört av: <strong><?= htmlspecialchars($wo['assigned_to_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></strong>
                        · Maskin: <strong><?= htmlspecialchars($wo['machine_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></strong>
                    </div>
                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Totalt: <strong><?= htmlspecialchars($wo['total_hours'], ENT_QUOTES, 'UTF-8') ?> tim</strong>
                        · Material: <strong><?= number_format((float)$wo['total_material_cost'], 0, ',', ' ') ?> kr</strong>
                    </div>
                </div>
                <div class="flex gap-2 shrink-0">
                    <a href="/maintenance/work-orders/<?= $wo['id'] ?>" class="px-3 py-2 text-sm bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-lg hover:bg-indigo-200 transition">Granska</a>
                    <form method="POST" action="/maintenance/work-orders/<?= $wo['id'] ?>/approve" class="inline">
                        <?= \App\Core\Csrf::field() ?>
                        <button type="submit" class="px-3 py-2 text-sm bg-green-600 hover:bg-green-700 text-white rounded-lg transition">✓ Godkänn</button>
                    </form>
                    <div class="relative inline-block" x-data="{ open: false }">
                        <form method="POST" action="/maintenance/work-orders/<?= $wo['id'] ?>/reject">
                            <?= \App\Core\Csrf::field() ?>
                            <button type="button" @click="open=!open" class="px-3 py-2 text-sm bg-red-600 hover:bg-red-700 text-white rounded-lg transition">✗ Avvisa</button>
                            <div x-show="open" @click.outside="open=false" class="absolute right-0 mt-2 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 z-10 min-w-64">
                                <textarea name="rejected_reason" rows="2" required placeholder="Anledning..." class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm mb-2"></textarea>
                                <button type="submit" class="w-full bg-red-600 text-white py-1.5 rounded text-sm">Bekräfta avvisning</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($workOrders)): ?>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-8 text-center text-gray-400">Inga arbetsordrar väntar attestering</div>
        <?php endif; ?>
    </div>
</div>
