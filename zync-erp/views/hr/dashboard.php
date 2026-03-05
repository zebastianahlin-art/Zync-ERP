<?php if (!empty($success)): ?>
<div class="mb-4 rounded-lg bg-green-50 dark:bg-green-900/20 p-4 text-green-800 dark:text-green-300 text-sm">
    <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
</div>
<?php endif; ?>

<div class="space-y-8">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">HR Dashboard</h1>
    </div>

    <!-- KPI Boxes -->
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">
        <a href="/employees" class="rounded-xl bg-white dark:bg-gray-800 shadow p-5 flex flex-col items-center hover:shadow-md transition">
            <span class="text-3xl font-bold text-indigo-600 dark:text-indigo-400"><?= (int)$stats['total_employees'] ?></span>
            <span class="mt-1 text-xs text-gray-500 dark:text-gray-400 text-center">Aktiva anst&#228;llda</span>
        </a>
        <a href="/hr/recruitment" class="rounded-xl bg-white dark:bg-gray-800 shadow p-5 flex flex-col items-center hover:shadow-md transition">
            <span class="text-3xl font-bold text-blue-600 dark:text-blue-400"><?= (int)$stats['open_positions'] ?></span>
            <span class="mt-1 text-xs text-gray-500 dark:text-gray-400 text-center">&#214;ppna tj&#228;nster</span>
        </a>
        <a href="/hr/training/sessions" class="rounded-xl bg-white dark:bg-gray-800 shadow p-5 flex flex-col items-center hover:shadow-md transition">
            <span class="text-3xl font-bold text-emerald-600 dark:text-emerald-400"><?= (int)$stats['upcoming_trainings'] ?></span>
            <span class="mt-1 text-xs text-gray-500 dark:text-gray-400 text-center">Planerade utbildningar</span>
        </a>
        <a href="/certificates/expiring" class="rounded-xl bg-white dark:bg-gray-800 shadow p-5 flex flex-col items-center hover:shadow-md transition">
            <span class="text-3xl font-bold text-yellow-500 dark:text-yellow-400"><?= (int)$stats['expiring_certificates'] ?></span>
            <span class="mt-1 text-xs text-gray-500 dark:text-gray-400 text-center">Certifikat utg&#229;r &lt;30d</span>
        </a>
        <a href="/certificates/expired" class="rounded-xl bg-white dark:bg-gray-800 shadow p-5 flex flex-col items-center hover:shadow-md transition">
            <span class="text-3xl font-bold text-red-600 dark:text-red-400"><?= (int)$stats['expired_certificates'] ?></span>
            <span class="mt-1 text-xs text-gray-500 dark:text-gray-400 text-center">Utg&#229;ngna certifikat</span>
        </a>
        <a href="/hr/payroll" class="rounded-xl bg-white dark:bg-gray-800 shadow p-5 flex flex-col items-center hover:shadow-md transition">
            <span class="text-3xl font-bold text-purple-600 dark:text-purple-400"><?= (int)$stats['open_payroll_periods'] ?></span>
            <span class="mt-1 text-xs text-gray-500 dark:text-gray-400 text-center">&#214;ppna l&#246;neperioder</span>
        </a>
    </div>

    <!-- Quick Navigation -->
    <div>
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Snabbnavigering</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <?php
            $modules = [
                ['href'=>'/employees','icon'=>'&#128101;','title'=>'Personal','desc'=>'Anst&#228;llda &amp; profiler'],
                ['href'=>'/certificates','icon'=>'&#128220;','title'=>'Certifikat','desc'=>'Beh&#246;righeter &amp; certifikat'],
                ['href'=>'/hr/payroll','icon'=>'&#128176;','title'=>'L&#246;nehantering','desc'=>'Perioder &amp; l&#246;nespecar'],
                ['href'=>'/hr/attendance','icon'=>'&#128197;','title'=>'N&#228;rvaro','desc'=>'N&#228;rvaro &amp; fr&#229;nvaro'],
                ['href'=>'/hr/training','icon'=>'&#127891;','title'=>'Utbildningar','desc'=>'Kurser &amp; tillf&#228;llen'],
                ['href'=>'/hr/recruitment','icon'=>'&#128269;','title'=>'Rekrytering','desc'=>'Tj&#228;nster &amp; s&#246;kande'],
                ['href'=>'/hr/expenses','icon'=>'&#129534;','title'=>'Utl&#228;gg','desc'=>'Resер&#228;kningar'],
                ['href'=>'/employees/create','icon'=>'&#10133;','title'=>'Ny anst&#228;lld','desc'=>'Registrera ny personal'],
            ];
            foreach ($modules as $m): ?>
            <a href="<?= htmlspecialchars($m['href'], ENT_QUOTES, 'UTF-8') ?>" class="flex items-start gap-3 rounded-xl bg-white dark:bg-gray-800 shadow p-4 hover:shadow-md transition">
                <span class="text-2xl"><?= $m['icon'] ?></span>
                <div>
                    <div class="font-semibold text-gray-900 dark:text-white text-sm"><?= htmlspecialchars($m['title'], ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars($m['desc'], ENT_QUOTES, 'UTF-8') ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Upcoming Events -->
    <div>
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Kommande kurstillf&#228;llen</h2>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
            <?php if (empty($upcomingEvents)): ?>
            <p class="px-4 py-8 text-center text-gray-400 dark:text-gray-500 text-sm">Inga kommande kurstillf&#228;llen</p>
            <?php else: ?>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Kurs</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Startdatum</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Plats</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($upcomingEvents as $ev): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                            <a href="/hr/training/sessions/<?= (int)$ev['id'] ?>" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                <?= htmlspecialchars($ev['course_name'] ?? '&#8212;', ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($ev['start_date'] ?? '&#8212;', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($ev['location'] ?? '&#8212;', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                                <?= htmlspecialchars($ev['status'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>
