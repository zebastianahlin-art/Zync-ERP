<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">AI-ingenjör</h1>
        <a href="/maintenance/ai/recommendations" class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">💡 Rekommendationer</a>
    </div>

    <?php if (empty($aiConfigured)): ?>
    <div class="rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 p-4 text-sm text-yellow-800 dark:text-yellow-200">
        ⚠️ <strong>AI-tjänst ej konfigurerad.</strong> Ställ in <code>AI_API_KEY</code> i <code>.env</code> för att aktivera AI-funktioner.
    </div>
    <?php endif; ?>

    <!-- Stats grid -->
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">Totalt felanmälningar</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1"><?= (int) $stats['totalFaults'] ?></p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1"><?= (int) $stats['openFaults'] ?> öppna</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">Arbetsordrar totalt</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1"><?= (int) $stats['totalWOs'] ?></p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1"><?= (int) $stats['activeWOs'] ?> aktiva</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">FU-scheman</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1"><?= (int) $stats['activePM'] ?></p>
            <?php if ($stats['overduePM'] > 0): ?>
            <p class="text-xs text-red-500 font-semibold mt-1"><?= (int) $stats['overduePM'] ?> förfallna</p>
            <?php else: ?>
            <p class="text-xs text-green-500 mt-1">Inga förfallna</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top faulty machines -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="font-semibold text-gray-900 dark:text-white">Top 10 maskiner – flest fel</h2>
                <span class="text-xs text-gray-500 dark:text-gray-400">senaste 365 dagarna</span>
            </div>
            <?php if (empty($topFaulty)): ?>
            <div class="p-5 text-center text-sm text-gray-500 dark:text-gray-400">Inga data tillgängliga.</div>
            <?php else: ?>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                <?php foreach ($topFaulty as $i => $m): ?>
                <div class="flex items-center gap-3 px-5 py-3">
                    <span class="w-6 text-xs font-bold text-gray-400"><?= $i + 1 ?></span>
                    <div class="flex-1 min-w-0">
                        <a href="/maintenance/ai/machine/<?= $m['id'] ?>" class="text-sm font-medium text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 hover:underline">
                            <?= htmlspecialchars($m['name'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                        <?php if ($m['location']): ?>
                        <p class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars($m['location'], ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endif; ?>
                    </div>
                    <span class="text-sm font-bold <?= $m['fault_count'] >= 5 ? 'text-red-600 dark:text-red-400' : 'text-orange-600 dark:text-orange-400' ?>"><?= (int) $m['fault_count'] ?> fel</span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Trending faults -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="font-semibold text-gray-900 dark:text-white">Ökande felfrekvens</h2>
                <span class="text-xs text-gray-500 dark:text-gray-400">30 vs föregående 30 dagar</span>
            </div>
            <?php if (empty($trending)): ?>
            <div class="p-5 text-center text-sm text-gray-500 dark:text-gray-400">Inga ökande trender.</div>
            <?php else: ?>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                <?php foreach ($trending as $m): ?>
                <div class="flex items-center gap-3 px-5 py-3">
                    <div class="flex-1 min-w-0">
                        <a href="/maintenance/ai/machine/<?= $m['id'] ?>" class="text-sm font-medium text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 hover:underline">
                            <?= htmlspecialchars($m['name'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </div>
                    <div class="text-right">
                        <span class="block text-sm font-bold text-red-600 dark:text-red-400">↑ +<?= max(0, (int)$m['faults_last_30'] - (int)$m['faults_prev_30']) ?></span>
                        <span class="text-xs text-gray-500 dark:text-gray-400"><?= (int)$m['faults_prev_30'] ?> → <?= (int)$m['faults_last_30'] ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- MTBF table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow lg:col-span-2">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="font-semibold text-gray-900 dark:text-white">MTBF – Genomsnittlig tid mellan fel</h2>
            </div>
            <?php if (empty($mtbf)): ?>
            <div class="p-5 text-center text-sm text-gray-500 dark:text-gray-400">Ej tillräckligt med data (kräver minst 2 fel per maskin).</div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 font-medium">Maskin</th>
                            <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400 font-medium">Antal fel</th>
                            <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400 font-medium">MTBF (timmar)</th>
                            <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400 font-medium">MTBF (dagar)</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <?php foreach ($mtbf as $m): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($m['name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400"><?= (int) $m['fault_count'] ?></td>
                            <td class="px-4 py-3 text-right <?= $m['mtbf_hours'] < 168 ? 'text-red-600 dark:text-red-400 font-semibold' : 'text-gray-900 dark:text-white' ?>"><?= $m['mtbf_hours'] !== null ? round((float)$m['mtbf_hours'], 1) : '—' ?></td>
                            <?php /* 168 = hours in one week — values below 1 week are highlighted as critical */ ?>
                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400"><?= $m['mtbf_hours'] !== null ? round((float)$m['mtbf_hours'] / 24, 1) : '—' ?></td>
                            <td class="px-4 py-3 text-right">
                                <a href="/maintenance/ai/machine/<?= $m['id'] ?>" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Rapport →</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
