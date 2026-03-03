<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="/maintenance/supervisor" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Statistik – Underhåll</h1>
    </div>

    <?php if (!empty($success)): ?>
        <div class="rounded-lg bg-green-50 dark:bg-green-900/30 p-4 text-green-800 dark:text-green-200"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="rounded-lg bg-red-50 dark:bg-red-900/30 p-4 text-red-800 dark:text-red-200"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <!-- Genomsnittlig slutförandetid -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 flex items-center gap-4">
        <div class="flex-shrink-0 w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Genomsnittlig slutförandetid</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                <?php
                $avgHours = $stats['avg_completion_hours'] ?? null;
                if ($avgHours !== null && $avgHours !== '') {
                    echo htmlspecialchars(number_format((float)$avgHours, 1) . ' timmar', ENT_QUOTES, 'UTF-8');
                } else {
                    echo '—';
                }
                ?>
            </p>
        </div>
    </div>

    <!-- Statusfördelning – kort -->
    <?php
    $statusCards = [
        'reported'         => ['Rapporterade',       'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',          'border-blue-200 dark:border-blue-800'],
        'assigned'         => ['Tilldelade',          'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300',  'border-indigo-200 dark:border-indigo-800'],
        'in_progress'      => ['Pågående',            'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',  'border-orange-200 dark:border-orange-800'],
        'work_completed'   => ['Arbete utfört',       'bg-teal-100 text-teal-700 dark:bg-teal-900/30 dark:text-teal-300',          'border-teal-200 dark:border-teal-800'],
        'pending_approval' => ['Väntar attestering',  'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',  'border-yellow-200 dark:border-yellow-800'],
        'approved'         => ['Attesterade',         'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',      'border-green-200 dark:border-green-800'],
        'rejected'         => ['Avvisade',            'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',              'border-red-200 dark:border-red-800'],
        'closed'           => ['Avslutade',           'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',             'border-gray-200 dark:border-gray-600'],
        'archived'         => ['Arkiverade',          'bg-gray-200 text-gray-500 dark:bg-gray-800 dark:text-gray-500',             'border-gray-300 dark:border-gray-600'],
    ];
    ?>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        <?php foreach ($statusCards as $key => [$label, $badgeClass, $borderClass]): ?>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border <?= $borderClass ?> p-4 flex flex-col gap-1">
            <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium <?= $badgeClass ?> self-start"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></span>
            <span class="text-2xl font-bold text-gray-900 dark:text-white mt-1"><?= (int)($stats[$key] ?? 0) ?></span>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Sammanfattningstabell -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-x-auto">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100">Statussammanfattning</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Status</th>
                    <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400">Antal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($statusCards as $key => [$label, $badgeClass]): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3">
                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium <?= $badgeClass ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></span>
                    </td>
                    <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white"><?= (int)($stats[$key] ?? 0) ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="bg-gray-50 dark:bg-gray-700/50 font-semibold">
                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">Totalt öppna</td>
                    <td class="px-4 py-3 text-right text-gray-900 dark:text-white"><?= (int)($stats['total_open'] ?? 0) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
