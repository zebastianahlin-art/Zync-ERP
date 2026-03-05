<?php if (!empty($success)): ?>
<div class="mb-4 rounded-lg bg-green-50 dark:bg-green-900/20 p-4 text-green-800 dark:text-green-300 text-sm">
    <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
</div>
<?php endif; ?>

<?php
// Warning box: expired or soon-expiring certs
$expiredCount  = (int) ($stats['expired_certificates'] ?? 0);
$expiringCount = (int) ($stats['expiring_certificates'] ?? 0);
?>

<?php if ($expiredCount > 0 || $expiringCount > 0): ?>
<div class="mb-6 flex items-start gap-3 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 p-4">
    <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
    </svg>
    <div class="text-sm">
        <p class="font-semibold text-red-800 dark:text-red-300">Certifikatsvarning</p>
        <p class="text-red-700 dark:text-red-400 mt-0.5">
            <?php if ($expiredCount > 0): ?><?= $expiredCount ?> utgångna certifikat.<?php endif; ?>
            <?php if ($expiringCount > 0): ?><?= $expiringCount ?> certifikat utgår inom 30 dagar.<?php endif; ?>
            <a href="/certificates" class="underline ml-1">Hantera certifikat →</a>
        </p>
    </div>
</div>
<?php endif; ?>

<div class="space-y-8">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">HR Dashboard</h1>
        <a href="/employees/create" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition">+ Ny anställd</a>
    </div>

    <!-- KPI Boxes -->
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">
        <a href="/employees" class="rounded-xl bg-white dark:bg-gray-800 shadow p-5 flex flex-col items-center hover:shadow-md transition group">
            <span class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 group-hover:scale-110 transition-transform"><?= (int)($stats['total_employees'] ?? 0) ?></span>
            <span class="mt-1 text-xs text-gray-500 dark:text-gray-400 text-center">Aktiva anst&#228;llda</span>
        </a>
        <a href="/hr/recruitment" class="rounded-xl bg-white dark:bg-gray-800 shadow p-5 flex flex-col items-center hover:shadow-md transition group">
            <span class="text-3xl font-bold text-blue-600 dark:text-blue-400 group-hover:scale-110 transition-transform"><?= (int)($stats['open_positions'] ?? 0) ?></span>
            <span class="mt-1 text-xs text-gray-500 dark:text-gray-400 text-center">&#214;ppna tj&#228;nster</span>
        </a>
        <a href="/hr/training/sessions" class="rounded-xl bg-white dark:bg-gray-800 shadow p-5 flex flex-col items-center hover:shadow-md transition group">
            <span class="text-3xl font-bold text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform"><?= (int)($stats['upcoming_trainings'] ?? 0) ?></span>
            <span class="mt-1 text-xs text-gray-500 dark:text-gray-400 text-center">Planerade utbildningar</span>
        </a>
        <a href="/certificates/expiring" class="rounded-xl bg-white dark:bg-gray-800 shadow p-5 flex flex-col items-center hover:shadow-md transition group">
            <span class="text-3xl font-bold text-yellow-500 dark:text-yellow-400 group-hover:scale-110 transition-transform"><?= $expiringCount ?></span>
            <span class="mt-1 text-xs text-gray-500 dark:text-gray-400 text-center">Certifikat utg&#229;r &lt;30d</span>
        </a>
        <a href="/certificates/expired" class="rounded-xl bg-white dark:bg-gray-800 shadow p-5 flex flex-col items-center hover:shadow-md transition group">
            <span class="text-3xl font-bold text-red-600 dark:text-red-400 group-hover:scale-110 transition-transform"><?= $expiredCount ?></span>
            <span class="mt-1 text-xs text-gray-500 dark:text-gray-400 text-center">Utg&#229;ngna certifikat</span>
        </a>
        <a href="/hr/payroll" class="rounded-xl bg-white dark:bg-gray-800 shadow p-5 flex flex-col items-center hover:shadow-md transition group">
            <span class="text-3xl font-bold text-purple-600 dark:text-purple-400 group-hover:scale-110 transition-transform"><?= (int)($stats['open_payroll_periods'] ?? 0) ?></span>
            <span class="mt-1 text-xs text-gray-500 dark:text-gray-400 text-center">&#214;ppna l&#246;neperioder</span>
        </a>
    </div>

    <!-- Department Distribution Chart -->
    <?php if (!empty($deptDistribution)): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Anst&#228;llda per avdelning</h2>
        <?php
        $maxCount = max(array_column($deptDistribution, 'employee_count'));
        $colors = ['bg-indigo-500','bg-blue-500','bg-emerald-500','bg-violet-500','bg-orange-500','bg-pink-500','bg-teal-500','bg-cyan-500','bg-amber-500','bg-rose-500'];
        ?>
        <div class="space-y-2">
            <?php foreach ($deptDistribution as $i => $dept): ?>
            <?php $pct = $maxCount > 0 ? round(($dept['employee_count'] / $maxCount) * 100) : 0; ?>
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-600 dark:text-gray-400 w-32 truncate text-right" title="<?= htmlspecialchars($dept['department_name'] ?? 'Okänd', ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($dept['department_name'] ?? 'Okänd', ENT_QUOTES, 'UTF-8') ?>
                </span>
                <div class="flex-1 h-6 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full <?= $colors[$i % count($colors)] ?> rounded-full transition-all duration-500" style="width: <?= $pct ?>%"></div>
                </div>
                <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 w-6 text-right"><?= (int)$dept['employee_count'] ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Upcoming Events -->
        <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Kommande kurstillf&#228;llen</h2>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                <?php if (empty($upcomingEvents)): ?>
                <p class="px-4 py-8 text-center text-gray-400 dark:text-gray-500 text-sm">Inga kommande kurstillf&#228;llen</p>
                <?php else: ?>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($upcomingEvents as $ev): ?>
                    <div class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex flex-col items-center justify-center text-center flex-shrink-0">
                            <span class="text-xs font-bold text-emerald-700 dark:text-emerald-300 leading-tight"><?= date('d', strtotime($ev['start_date'] ?? 'now')) ?></span>
                            <span class="text-xs text-emerald-600 dark:text-emerald-400 uppercase"><?= date('M', strtotime($ev['start_date'] ?? 'now')) ?></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <a href="/hr/training/sessions/<?= (int)$ev['id'] ?>" class="text-sm font-medium text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 truncate block">
                                <?= htmlspecialchars($ev['course_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                            </a>
                            <span class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars($ev['location'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 flex-shrink-0">
                            <?= htmlspecialchars($ev['status'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Activities -->
        <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Senaste aktiviteter</h2>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                <?php if (empty($recentActivities)): ?>
                <p class="px-4 py-8 text-center text-gray-400 dark:text-gray-500 text-sm">Inga aktiviteter &#228;nnu</p>
                <?php else: ?>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($recentActivities as $act): ?>
                    <div class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <?php if ($act['type'] === 'employee'): ?>
                        <span class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-base flex-shrink-0">&#128101;</span>
                        <?php else: ?>
                        <span class="w-8 h-8 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center text-yellow-600 dark:text-yellow-400 text-base flex-shrink-0">&#128220;</span>
                        <?php endif; ?>
                        <span class="flex-1 text-sm text-gray-700 dark:text-gray-300 truncate"><?= htmlspecialchars($act['label'], ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="text-xs text-gray-400 dark:text-gray-500 flex-shrink-0"><?= htmlspecialchars(substr($act['ts'] ?? '', 0, 10), ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Certificates expiring soon widget -->
    <?php if (!empty($expiringWidget)): ?>
    <div>
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Certifikat som snart utg&#229;r (60 dagar)</h2>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Anst&#228;lld</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Certifikat</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Utg&#229;r</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($expiringWidget as $cert): ?>
                    <?php
                        $today = date('Y-m-d');
                        $expired = ($cert['expiry_date'] ?? '') < $today;
                        $expiring30 = !$expired && ($cert['expiry_date'] ?? '') < date('Y-m-d', strtotime('+30 days'));
                    ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-2 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($cert['employee_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-2 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($cert['certificate_type_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-2 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($cert['expiry_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-2">
                            <?php if ($expired): ?>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Utg&#229;nget</span>
                            <?php elseif ($expiring30): ?>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">Utg&#229;r snart</span>
                            <?php else: ?>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400">Utg&#229;r inom 60d</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Quick Navigation -->
    <div>
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Snabbnavigering</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <?php
            $modules = [
                ['href'=>'/employees','icon'=>'&#128101;','title'=>'Personal','desc'=>'Anst&#228;llda &amp; profiler','color'=>'text-indigo-600 dark:text-indigo-400'],
                ['href'=>'/certificates','icon'=>'&#128220;','title'=>'Certifikat','desc'=>'Beh&#246;righeter &amp; certifikat','color'=>'text-yellow-600 dark:text-yellow-400'],
                ['href'=>'/hr/payroll','icon'=>'&#128176;','title'=>'L&#246;nehantering','desc'=>'Perioder &amp; l&#246;nespecar','color'=>'text-purple-600 dark:text-purple-400'],
                ['href'=>'/hr/attendance','icon'=>'&#128197;','title'=>'N&#228;rvaro','desc'=>'N&#228;rvaro &amp; fr&#229;nvaro','color'=>'text-blue-600 dark:text-blue-400'],
                ['href'=>'/hr/training','icon'=>'&#127891;','title'=>'Utbildningar','desc'=>'Kurser &amp; tillf&#228;llen','color'=>'text-emerald-600 dark:text-emerald-400'],
                ['href'=>'/hr/recruitment','icon'=>'&#128269;','title'=>'Rekrytering','desc'=>'Tj&#228;nster &amp; s&#246;kande','color'=>'text-sky-600 dark:text-sky-400'],
                ['href'=>'/hr/expenses','icon'=>'&#129534;','title'=>'Utl&#228;gg','desc'=>'Reser&#228;kningar','color'=>'text-orange-600 dark:text-orange-400'],
                ['href'=>'/employees/create','icon'=>'&#10133;','title'=>'Ny anst&#228;lld','desc'=>'Registrera ny personal','color'=>'text-green-600 dark:text-green-400'],
            ];
            foreach ($modules as $m): ?>
            <a href="<?= htmlspecialchars($m['href'], ENT_QUOTES, 'UTF-8') ?>" class="flex items-start gap-3 rounded-xl bg-white dark:bg-gray-800 shadow p-4 hover:shadow-md transition hover:-translate-y-0.5">
                <span class="text-2xl <?= $m['color'] ?>"><?= $m['icon'] ?></span>
                <div>
                    <div class="font-semibold text-gray-900 dark:text-white text-sm"><?= htmlspecialchars($m['title'], ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars($m['desc'], ENT_QUOTES, 'UTF-8') ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
