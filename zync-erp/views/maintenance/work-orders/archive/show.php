<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="/maintenance/work-orders/archive" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($wo['order_number'], ENT_QUOTES, 'UTF-8') ?></h1>
            <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">Arkiverad</span>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4"><?= htmlspecialchars($wo['title'], ENT_QUOTES, 'UTF-8') ?></h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
            <div><span class="text-gray-500 dark:text-gray-400">Typ:</span> <span class="ml-1 text-gray-900 dark:text-white"><?= htmlspecialchars($wo['work_type'], ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Prioritet:</span> <span class="ml-1 text-gray-900 dark:text-white"><?= htmlspecialchars($wo['priority'], ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Maskin:</span> <span class="ml-1 text-gray-900 dark:text-white"><?= htmlspecialchars($wo['machine_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Tilldelad:</span> <span class="ml-1 text-gray-900 dark:text-white"><?= htmlspecialchars($wo['assigned_to_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Godkänd av:</span> <span class="ml-1 text-gray-900 dark:text-white"><?= htmlspecialchars($wo['approved_by_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Tot. timmar:</span> <span class="ml-1 font-semibold text-gray-900 dark:text-white"><?= htmlspecialchars($wo['total_hours'], ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Materialkostnad:</span> <span class="ml-1 font-semibold text-gray-900 dark:text-white"><?= number_format((float)$wo['total_material_cost'], 0, ',', ' ') ?> kr</span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Totalkostnad:</span> <span class="ml-1 font-bold text-gray-900 dark:text-white"><?= number_format((float)$wo['total_cost'], 0, ',', ' ') ?> kr</span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Arkiverad:</span> <span class="ml-1 text-gray-900 dark:text-white"><?= htmlspecialchars(substr($wo['archived_at'] ?? '', 0, 10), ENT_QUOTES, 'UTF-8') ?></span></div>
        </div>
        <?php if (!empty($wo['completion_notes'])): ?>
        <div class="mt-4 p-3 bg-teal-50 dark:bg-teal-900/20 rounded-lg text-sm text-teal-800 dark:text-teal-200">
            <strong>Slutkommentar:</strong> <?= nl2br(htmlspecialchars($wo['completion_notes'], ENT_QUOTES, 'UTF-8')) ?>
        </div>
        <?php endif; ?>
        <?php if (!empty($wo['approval_notes'])): ?>
        <div class="mt-3 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg text-sm text-green-800 dark:text-green-200">
            <strong>Attesteringskommentar:</strong> <?= nl2br(htmlspecialchars($wo['approval_notes'], ENT_QUOTES, 'UTF-8')) ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Time entries (read-only) -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
        <div class="p-5 border-b border-gray-200 dark:border-gray-700"><h2 class="font-semibold text-gray-900 dark:text-white">Tidsregistrering</h2></div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Datum</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Tekniker</th>
                    <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400">Timmar</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Beskrivning</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($timeEntries as $te): ?>
                <tr>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($te['work_date'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-900 dark:text-white"><?= htmlspecialchars($te['user_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($te['hours'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($te['description'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($timeEntries)): ?>
                <tr><td colspan="4" class="px-4 py-4 text-center text-gray-400">Inga tidsposter</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Parts (read-only) -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
        <div class="p-5 border-b border-gray-200 dark:border-gray-700"><h2 class="font-semibold text-gray-900 dark:text-white">Material</h2></div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Artikel</th>
                    <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400">Antal</th>
                    <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400">À-pris</th>
                    <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400">Totalt</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($parts as $p): ?>
                <tr>
                    <td class="px-4 py-3 text-gray-900 dark:text-white"><?= htmlspecialchars($p['article_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-right text-gray-900 dark:text-white"><?= htmlspecialchars($p['quantity'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-right text-gray-900 dark:text-white"><?= number_format((float)$p['unit_price'], 2, ',', ' ') ?> kr</td>
                    <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white"><?= number_format((float)$p['total_price'], 2, ',', ' ') ?> kr</td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($parts)): ?>
                <tr><td colspan="4" class="px-4 py-4 text-center text-gray-400">Inget material</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
