<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Hälsa &amp; Säkerhet</h1>
    </div>

    <!-- Stats grid -->
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-md">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Aktiva risker</p>
            <p class="mt-1 text-3xl font-bold text-red-600 dark:text-red-400"><?= (int) ($riskStats['active'] ?? 0) ?></p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-md">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Hög risk</p>
            <p class="mt-1 text-3xl font-bold text-orange-600 dark:text-orange-400"><?= (int) ($riskStats['high_risk'] ?? 0) ?></p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-md">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Förfallna kontroller</p>
            <p class="mt-1 text-3xl font-bold text-yellow-600 dark:text-yellow-400"><?= (int) ($resourceStats['overdue'] ?? 0) ?></p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-md">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Nödresurser OK</p>
            <p class="mt-1 text-3xl font-bold text-green-600 dark:text-green-400"><?= (int) ($resourceStats['ok'] ?? 0) ?></p>
        </div>
    </div>

    <!-- Quick links -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <a href="/safety/risks" class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md hover:shadow-lg transition-shadow">
            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Riskbedömningar</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Hantera och granska riskbedömningar</p>
        </a>
        <a href="/safety/reports" class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md hover:shadow-lg transition-shadow">
            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Riskrapporter</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Rapportera risker, faror och tillbud</p>
        </a>
        <a href="/safety/audits" class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md hover:shadow-lg transition-shadow">
            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Audits</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Planera och genomför säkerhetsaudits</p>
        </a>
        <a href="/safety/emergency" class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md hover:shadow-lg transition-shadow">
            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Krishantering</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Nödkontakter och procedurer</p>
        </a>
        <a href="/safety/resources" class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md hover:shadow-lg transition-shadow">
            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Nödresurser</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Brandsläckare, hjärtstartare m.m.</p>
        </a>
        <a href="/safety/resources/overdue" class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md hover:shadow-lg transition-shadow border-l-4 border-red-500">
            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Förfallna kontroller</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Resurser som behöver kontrolleras</p>
        </a>
    </div>

    <?php if (!empty($overdueResources)): ?>
    <div class="rounded-2xl bg-red-50 dark:bg-red-900/20 p-6 shadow-md">
        <h2 class="text-lg font-semibold text-red-700 dark:text-red-400 mb-3">⚠ Förfallna kontroller (<?= count($overdueResources) ?>)</h2>
        <ul class="space-y-1">
            <?php foreach (array_slice($overdueResources, 0, 5) as $r): ?>
                <li class="text-sm text-red-700 dark:text-red-400">
                    <a href="/safety/resources/<?= (int) $r['id'] ?>" class="hover:underline">
                        <?= htmlspecialchars($r['name'], ENT_QUOTES, 'UTF-8') ?> – <?= htmlspecialchars($r['location'], ENT_QUOTES, 'UTF-8') ?>
                        (<?= htmlspecialchars($r['next_inspection'] ?? '', ENT_QUOTES, 'UTF-8') ?>)
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
</div>
