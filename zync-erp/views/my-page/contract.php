<?php
$contractTypeMap = [
    'permanent'    => 'Tillsvidareanställning',
    'fixed_term'   => 'Visstidsanställning',
    'part_time'    => 'Deltidsanställning',
    'probationary' => 'Provanställning',
    'consultant'   => 'Konsultavtal',
    'intern'       => 'Praktikavtal',
];
$salaryTypeMap = [
    'monthly'    => 'Månadslön',
    'hourly'     => 'Timlön',
    'commission' => 'Provisionsbaserad',
];
$employmentTypeMap = [
    'full_time'  => 'Heltid',
    'part_time'  => 'Deltid',
    'consultant' => 'Konsult',
    'intern'     => 'Praktikant',
];
?>
<div class="mx-auto max-w-2xl space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mitt anställningsavtal</h1>
        <a href="/my-page" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">&larr; Min Sida</a>
    </div>

    <?php if ($employee): ?>
    <!-- Employee summary card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Anställningsinformation</h2>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <dt class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Namn</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                    <?= htmlspecialchars(trim(($employee['first_name'] ?? '') . ' ' . ($employee['last_name'] ?? '')), ENT_QUOTES, 'UTF-8') ?>
                </dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Anställningsnummer</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($employee['employee_number'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Befattning</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($employee['position'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Avdelning</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($employee['department_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Anställd sedan</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($employee['hire_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Anställningstyp</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($employmentTypeMap[$employee['employment_type'] ?? ''] ?? ($employee['employment_type'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
        </dl>
    </div>
    <?php endif; ?>

    <?php if ($contract): ?>
    <!-- Contract details -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Avtalsinformation</h2>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <dt class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Avtalstyp</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($contractTypeMap[$contract['contract_type'] ?? ''] ?? ($contract['contract_type'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Startdatum</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($contract['start_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Slutdatum</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($contract['end_date'] ?? 'Tillsvidare', ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Lönetyp</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($salaryTypeMap[$contract['salary_type'] ?? ''] ?? ($contract['salary_type'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Arbetstid (tim/vecka)</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars((string)($contract['weekly_hours'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Arbetsplats</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($contract['workplace'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
            <?php if ($contract['notice_period'] ?? null): ?>
            <div class="sm:col-span-2">
                <dt class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Uppsägningstid</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($contract['notice_period'], ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
            <?php endif; ?>
            <?php if ($contract['notes'] ?? null): ?>
            <div class="sm:col-span-2">
                <dt class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Noteringar</dt>
                <dd class="mt-1 text-sm text-gray-600 dark:text-gray-400"><?= htmlspecialchars($contract['notes'], ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
            <?php endif; ?>
        </dl>
    </div>
    <?php else: ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-8 text-center">
        <p class="text-gray-400 dark:text-gray-500">Inget anställningsavtal är registrerat för ditt konto.</p>
        <p class="mt-2 text-sm text-gray-400 dark:text-gray-500">Kontakta din HR-avdelning för mer information.</p>
    </div>
    <?php endif; ?>
</div>
