<?php if (!empty($success)): ?>
<div class="mb-4 rounded-lg bg-green-50 dark:bg-green-900/20 p-4 text-green-800 dark:text-green-300 text-sm">
    <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
</div>
<?php endif; ?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="/hr/recruitment" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600">&larr; Rekrytering</a>
            <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($position['title'], ENT_QUOTES, 'UTF-8') ?></h1>
            <?php if (!empty($position['department_name'])): ?>
            <p class="text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($position['department_name'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </div>
        <div class="flex items-center gap-3">
            <a href="/hr/recruitment/positions/<?= (int)$position['id'] ?>/applicants/create" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition">+ Ny sökande</a>
            <a href="/hr/recruitment/positions/<?= (int)$position['id'] ?>/edit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Redigera</a>
            <form method="POST" action="/hr/recruitment/positions/<?= (int)$position['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort tjänsten?')">
                <?= \App\Core\Csrf::field() ?>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">Ta bort</button>
            </form>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 grid grid-cols-2 md:grid-cols-3 gap-x-8 gap-y-4">
        <div>
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Status</dt>
            <dd class="mt-0.5 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($position['status'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
        </div>
        <div>
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Antal tjänster</dt>
            <dd class="mt-0.5 text-sm text-gray-900 dark:text-white"><?= (int)($position['num_openings'] ?? 1) ?></dd>
        </div>
        <div>
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Publicerad</dt>
            <dd class="mt-0.5 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($position['posted_at'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
        </div>
        <div>
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Stängs</dt>
            <dd class="mt-0.5 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($position['closes_at'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
        </div>
        <?php if (!empty($position['description'])): ?>
        <div class="col-span-2 md:col-span-3">
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Beskrivning</dt>
            <dd class="mt-0.5 text-sm text-gray-900 dark:text-white whitespace-pre-wrap"><?= htmlspecialchars($position['description'], ENT_QUOTES, 'UTF-8') ?></dd>
        </div>
        <?php endif; ?>
    </div>

    <!-- Statistik -->
    <?php if (!empty($stats)): ?>
    <div class="flex gap-3 flex-wrap">
        <?php
        $statLabels = ['new'=>'Ny','screening'=>'Granskning','interview'=>'Intervju','offer'=>'Erbjudande','hired'=>'Anställd','rejected'=>'Avvisad'];
        foreach ($stats as $sKey => $sCount):
        ?>
        <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
            <?= htmlspecialchars($statLabels[$sKey] ?? $sKey, ENT_QUOTES, 'UTF-8') ?>: <?= (int)$sCount ?>
        </span>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Sökande -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <span class="font-semibold text-gray-900 dark:text-white">Sökande (<?= count($applicants) ?>)</span>
        </div>
        <?php if (empty($applicants)): ?>
        <p class="px-4 py-8 text-center text-gray-400 dark:text-gray-500 text-sm">Inga sökande registrerade</p>
        <?php else: ?>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs text-gray-500 dark:text-gray-400 uppercase">Namn</th>
                    <th class="px-4 py-3 text-left text-xs text-gray-500 dark:text-gray-400 uppercase">E-post</th>
                    <th class="px-4 py-3 text-left text-xs text-gray-500 dark:text-gray-400 uppercase">Ansökt</th>
                    <th class="px-4 py-3 text-left text-xs text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($applicants as $app): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                        <a href="/hr/recruitment/applicants/<?= (int)$app['id'] ?>" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                            <?= htmlspecialchars(($app['first_name'] ?? '') . ' ' . ($app['last_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($app['email'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($app['applied_at'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($app['status'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <a href="/hr/recruitment/applicants/<?= (int)$app['id'] ?>" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline mr-2">Visa</a>
                        <a href="/hr/recruitment/applicants/<?= (int)$app['id'] ?>/edit" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline mr-2">Redigera</a>
                        <form method="POST" action="/hr/recruitment/applicants/<?= (int)$app['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort sökande?')">
                            <?= \App\Core\Csrf::field() ?>
                            <button type="submit" class="text-xs text-red-500 hover:text-red-700">Ta bort</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
