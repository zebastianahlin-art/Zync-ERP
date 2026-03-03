<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="/maintenance/supervisor" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Statistik</h1>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
            <h2 class="font-semibold text-gray-900 dark:text-white mb-3">Totalt</h2>
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 text-center">
                    <p class="text-2xl font-bold text-indigo-600"><?= (int) $totalWOs ?></p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Arbetsordrar</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 text-center">
                    <p class="text-2xl font-bold text-orange-600"><?= (int) $totalFaults ?></p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Felanmälningar</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
            <h2 class="font-semibold text-gray-900 dark:text-white mb-3">Per status</h2>
            <div class="space-y-2">
                <?php $statusLabels = ['reported'=>'Rapporterad','assigned'=>'Tilldelad','in_progress'=>'Pågående','work_completed'=>'Arbete utfört','pending_approval'=>'Väntar attestering','approved'=>'Attesterad','rejected'=>'Avvisad','closed'=>'Avslutad','archived'=>'Arkiverad']; ?>
                <?php foreach ($byStatus as $status => $count): ?>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400"><?= htmlspecialchars($statusLabels[$status] ?? $status, ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="font-medium text-gray-900 dark:text-white"><?= (int) $count ?></span>
                </div>
                <?php endforeach; ?>
                <?php if (empty($byStatus)): ?>
                <p class="text-sm text-gray-400">Ingen data</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
            <h2 class="font-semibold text-gray-900 dark:text-white mb-3">Per typ</h2>
            <div class="space-y-2">
                <?php $typeLabels = ['corrective'=>'Avhjälpande','preventive'=>'Förebyggande','predictive'=>'Prediktivt','emergency'=>'Akut','improvement'=>'Förbättring','inspection'=>'Inspektion']; ?>
                <?php foreach ($byType as $type => $count): ?>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400"><?= htmlspecialchars($typeLabels[$type] ?? $type, ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="font-medium text-gray-900 dark:text-white"><?= (int) $count ?></span>
                </div>
                <?php endforeach; ?>
                <?php if (empty($byType)): ?>
                <p class="text-sm text-gray-400">Ingen data</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
            <h2 class="font-semibold text-gray-900 dark:text-white mb-3">Teamprestation</h2>
            <table class="w-full text-sm">
                <thead>
                    <tr>
                        <th class="text-left text-gray-500 dark:text-gray-400 pb-2">Tekniker</th>
                        <th class="text-right text-gray-500 dark:text-gray-400 pb-2">Order</th>
                        <th class="text-right text-gray-500 dark:text-gray-400 pb-2">Timmar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php foreach ($teamStats as $t): ?>
                    <tr>
                        <td class="py-1.5 text-gray-900 dark:text-white"><?= htmlspecialchars($t['full_name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="py-1.5 text-right text-gray-600 dark:text-gray-400"><?= (int) $t['total_orders'] ?></td>
                        <td class="py-1.5 text-right text-gray-600 dark:text-gray-400"><?= number_format((float)$t['total_hours'], 1) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
