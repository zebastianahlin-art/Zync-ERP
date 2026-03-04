<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="/maintenance/ai" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Hälsorapport: <?= htmlspecialchars($health['machine']['name'], ENT_QUOTES, 'UTF-8') ?></h1>
    </div>

    <!-- KPI cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">Totalt antal fel</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1"><?= (int) $health['totalFaults'] ?></p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">Fel senaste 30 dagar</p>
            <p class="text-3xl font-bold <?= $health['faults30'] >= 3 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' ?> mt-1"><?= (int) $health['faults30'] ?></p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">MTBF (timmar)</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1"><?= $health['mtbf'] !== null ? $health['mtbf'] : '—' ?></p>
            <?php if ($health['mtbf'] !== null): ?>
            <p class="text-xs text-gray-400 dark:text-gray-500"><?= round($health['mtbf'] / 24, 1) ?> dagar</p>
            <?php endif; ?>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">MTTR (timmar)</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1"><?= $health['mttr'] !== null ? $health['mttr'] : '—' ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Machine info -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-900 dark:text-white mb-4">Maskininformation</h2>
            <dl class="space-y-3 text-sm">
                <?php foreach (['name' => 'Namn', 'status' => 'Status', 'location' => 'Plats', 'manufacturer' => 'Tillverkare', 'model' => 'Modell', 'serial_number' => 'Serienummer'] as $field => $label): ?>
                <?php if (!empty($health['machine'][$field])): ?>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400"><?= $label ?></dt>
                    <dd class="text-gray-900 dark:text-white font-medium"><?= htmlspecialchars($health['machine'][$field], ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <?php endif; ?>
                <?php endforeach; ?>
                <div class="flex justify-between pt-2 border-t border-gray-100 dark:border-gray-700">
                    <dt class="text-gray-500 dark:text-gray-400">Total reparationskostnad</dt>
                    <dd class="text-gray-900 dark:text-white font-medium"><?= number_format($health['totalCost'], 2, ',', ' ') ?> kr</dd>
                </div>
            </dl>

            <div class="mt-4">
                <a href="/machines/<?= $health['machine']['id'] ?>" class="block w-full text-center px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">Öppna maskinsida</a>
            </div>
        </div>

        <!-- PM Schedules -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-900 dark:text-white mb-4">FU-scheman (<?= count($health['pmSchedules']) ?>)</h2>
            <?php if (empty($health['pmSchedules'])): ?>
            <p class="text-sm text-gray-500 dark:text-gray-400">Inga aktiva FU-scheman.</p>
            <a href="/maintenance/preventive/create" class="mt-3 inline-block text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Skapa schema →</a>
            <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($health['pmSchedules'] as $pm): ?>
                <?php $isOverdue = $pm['next_due_at'] && $pm['next_due_at'] < date('Y-m-d H:i:s'); ?>
                <div class="text-sm">
                    <a href="/maintenance/preventive/<?= $pm['id'] ?>" class="font-medium text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 hover:underline">
                        <?= htmlspecialchars($pm['title'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                    <p class="text-xs <?= $isOverdue ? 'text-red-500 font-semibold' : 'text-gray-500 dark:text-gray-400' ?>">
                        <?= $pm['next_due_at'] ? 'Nästa: ' . date('Y-m-d', strtotime($pm['next_due_at'])) : 'Inget datum' ?>
                        <?= $isOverdue ? ' (FÖRFALLEN)' : '' ?>
                    </p>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Recent faults -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-900 dark:text-white mb-4">Senaste felanmälningar</h2>
            <?php if (empty($health['recentFaults'])): ?>
            <p class="text-sm text-gray-500 dark:text-gray-400">Inga registrerade fel.</p>
            <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($health['recentFaults'] as $f): ?>
                <div class="border-l-2 border-red-300 dark:border-red-700 pl-3">
                    <a href="/maintenance/faults/<?= $f['id'] ?>" class="text-sm font-medium text-gray-900 dark:text-white hover:underline">
                        <?= htmlspecialchars($f['title'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                    <p class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars(date('Y-m-d', strtotime($f['created_at'])), ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars($f['status'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
