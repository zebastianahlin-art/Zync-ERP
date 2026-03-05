<?php
$employmentTypeMap = [
    'full_time'   => 'Heltid',
    'part_time'   => 'Deltid',
    'consultant'  => 'Konsult',
    'intern'      => 'Praktikant',
];
$attendanceTypeMap = [
    'presence' => 'Närvaro',
    'absence'  => 'Frånvaro',
    'vacation' => 'Semester',
    'sick'     => 'Sjukfrånvaro',
    'other'    => 'Övrigt',
];
$payslipStatusMap = [
    'draft'    => ['bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400', 'Utkast'],
    'approved' => ['bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400', 'Godkänd'],
    'paid'     => ['bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400', 'Utbetald'],
];
$ticketStatusMap = [
    'open'             => ['bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400', 'Öppet'],
    'in_progress'      => ['bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400', 'Pågående'],
    'waiting_customer' => ['bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400', 'Väntar kund'],
    'waiting_internal' => ['bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400', 'Väntar internt'],
    'resolved'         => ['bg-teal-100 text-teal-700 dark:bg-teal-900/30 dark:text-teal-400', 'Löst'],
    'closed'           => ['bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400', 'Stängt'],
];
$ticketPriorityMap = [
    'low'    => ['bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400', 'Låg'],
    'normal' => ['bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400', 'Normal'],
    'high'   => ['bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400', 'Hög'],
    'urgent' => ['bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400', 'Brådskande'],
];
$openTickets = array_filter($tickets ?? [], fn($t) => !in_array($t['status'], ['closed', 'resolved']));
?>
<div class="space-y-8">

    <?php if ($success ?? null): ?>
    <div class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 px-4 py-3 text-sm text-green-800 dark:text-green-300">
        <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php endif; ?>

    <!-- ─── Header ─────────────────────────────────────────────────────────── -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="h-14 w-14 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                <?= htmlspecialchars(mb_strtoupper(mb_substr($user['full_name'] ?? $user['username'] ?? 'U', 0, 1)), ENT_QUOTES, 'UTF-8') ?>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?= htmlspecialchars($user['full_name'] ?? $user['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    <?= htmlspecialchars($user['role_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    <?php if ($employee['department_name'] ?? null): ?>
                        · <?= htmlspecialchars($employee['department_name'], ENT_QUOTES, 'UTF-8') ?>
                    <?php endif; ?>
                </p>
            </div>
        </div>
        <a href="/my-page/edit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Redigera profil</a>
    </div>

    <!-- ─── E2: KPI-boxar ──────────────────────────────────────────────────── -->
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center">
            <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400"><?= (int) ($kpi['open_work_orders'] ?? 0) ?></div>
            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Öppna arbetsordrar</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center">
            <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400"><?= (int) ($kpi['active_tasks'] ?? 0) ?></div>
            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Aktiva uppgifter</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center">
            <div class="text-3xl font-bold <?= ($kpi['expiring_certs'] ?? 0) > 0 ? 'text-red-500 dark:text-red-400' : 'text-indigo-600 dark:text-indigo-400' ?>">
                <?= (int) ($kpi['expiring_certs'] ?? 0) ?>
            </div>
            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Certifikat löper ut</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center">
            <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400"><?= (int) ($kpi['open_tickets'] ?? 0) ?></div>
            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Öppna ärenden</div>
        </div>
    </div>

    <!-- ─── E2: Snabbknappar ────────────────────────────────────────────────── -->
    <div class="flex flex-wrap gap-3">
        <a href="/maintenance/faults/create" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-sm font-medium text-gray-700 dark:text-gray-200 shadow-sm hover:bg-indigo-50 dark:hover:bg-gray-700 transition">
            <svg class="h-4 w-4 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            Ny felanmälan
        </a>
        <a href="/hr/expenses/create" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-sm font-medium text-gray-700 dark:text-gray-200 shadow-sm hover:bg-indigo-50 dark:hover:bg-gray-700 transition">
            <svg class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            Ny reseräkning
        </a>
        <a href="/cs/tickets/create" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-sm font-medium text-gray-700 dark:text-gray-200 shadow-sm hover:bg-indigo-50 dark:hover:bg-gray-700 transition">
            <svg class="h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
            Nytt ärende
        </a>
        <a href="/safety/risks/create" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-sm font-medium text-gray-700 dark:text-gray-200 shadow-sm hover:bg-indigo-50 dark:hover:bg-gray-700 transition">
            <svg class="h-4 w-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            Rapportera risk
        </a>
    </div>

    <!-- ─── E1: Kalender ────────────────────────────────────────────────────── -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6"
         x-data="myPageCalendar()"
         x-init="init()">

        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Kalender</h2>
            <div class="flex items-center gap-2">
                <button @click="prevMonth()" class="p-1.5 rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 dark:text-gray-400 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 min-w-[120px] text-center" x-text="monthLabel"></span>
                <button @click="nextMonth()" class="p-1.5 rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 dark:text-gray-400 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>

        <!-- Weekday headers -->
        <div class="grid grid-cols-7 mb-1">
            <template x-for="day in ['Mån','Tis','Ons','Tor','Fre','Lör','Sön']" :key="day">
                <div class="py-1 text-center text-xs font-medium text-gray-400 dark:text-gray-500" x-text="day"></div>
            </template>
        </div>

        <!-- Calendar grid -->
        <div class="grid grid-cols-7 gap-1">
            <template x-for="(cell, idx) in calendarCells" :key="idx">
                <div class="min-h-[60px] p-1 rounded border text-xs"
                     :class="cell.isToday ? 'border-indigo-400 dark:border-indigo-500 bg-indigo-50 dark:bg-indigo-900/10' : 'border-gray-100 dark:border-gray-700'">
                    <div class="font-medium mb-0.5"
                         :class="cell.day ? 'text-gray-700 dark:text-gray-300' : 'text-transparent'">
                        <span x-text="cell.day || '.'"></span>
                    </div>
                    <template x-for="ev in cell.events" :key="ev.id">
                        <a :href="ev.url" class="block truncate rounded px-1 py-0.5 mb-0.5 text-white text-[10px] leading-tight"
                           :class="{
                               'bg-blue-500': ev.color === 'blue',
                               'bg-green-500': ev.color === 'green',
                               'bg-red-500': ev.color === 'red',
                               'bg-yellow-500': ev.color === 'yellow'
                           }"
                           :title="ev.title"
                           x-text="ev.title">
                        </a>
                    </template>
                </div>
            </template>
        </div>

        <!-- Legend -->
        <div class="mt-3 flex flex-wrap gap-3 text-xs text-gray-500 dark:text-gray-400">
            <span class="flex items-center gap-1"><span class="inline-block h-2 w-2 rounded-full bg-blue-500"></span> Arbetsorder</span>
            <span class="flex items-center gap-1"><span class="inline-block h-2 w-2 rounded-full bg-green-500"></span> Utbildning</span>
            <span class="flex items-center gap-1"><span class="inline-block h-2 w-2 rounded-full bg-red-500"></span> Certifikat löper ut</span>
            <span class="flex items-center gap-1"><span class="inline-block h-2 w-2 rounded-full bg-yellow-500"></span> Frånvaro/Semester</span>
        </div>
    </div>

    <!-- ─── E3: Personlig HR-info ────────────────────────────────────────────── -->
    <?php if ($employee): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Anställningsinformation</h2>
        </div>

        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <dt class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Anställningsdatum</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($employee['hire_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
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
                <dt class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Anställningstyp</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($employmentTypeMap[$employee['employment_type'] ?? ''] ?? ($employee['employment_type'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Chef/Manager</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($employee['manager_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Anställningsnummer</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($employee['employee_number'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
        </dl>

        <?php if (!empty($certificates)): ?>
        <hr class="my-5 border-gray-200 dark:border-gray-700">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Aktiva certifikat</h3>
        <div class="space-y-2">
            <?php foreach ($certificates as $cert): ?>
            <?php
            $daysLeft = null;
            if ($cert['expiry_date']) {
                $daysLeft = (int) round((strtotime($cert['expiry_date']) - time()) / 86400);
            }
            $certColor = match(true) {
                $daysLeft !== null && $daysLeft < 0  => 'text-red-600 dark:text-red-400',
                $daysLeft !== null && $daysLeft <= 30 => 'text-orange-500 dark:text-orange-400',
                default => 'text-gray-500 dark:text-gray-400',
            };
            ?>
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-800 dark:text-gray-200"><?= htmlspecialchars($cert['type_name'] ?? 'Certifikat', ENT_QUOTES, 'UTF-8') ?></span>
                <span class="<?= $certColor ?>">
                    <?php if ($cert['expiry_date']): ?>
                        Löper ut <?= htmlspecialchars($cert['expiry_date'], ENT_QUOTES, 'UTF-8') ?>
                        <?php if ($daysLeft !== null && $daysLeft >= 0): ?>
                            (<?= $daysLeft ?> dagar)
                        <?php elseif ($daysLeft !== null): ?>
                            (utgånget)
                        <?php endif; ?>
                    <?php else: ?>
                        Inget slutdatum
                    <?php endif; ?>
                </span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($attendance)): ?>
        <hr class="my-5 border-gray-200 dark:border-gray-700">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Senaste frånvaroposter</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs text-gray-400 dark:text-gray-500 uppercase">
                        <th class="pb-2 text-left">Datum</th>
                        <th class="pb-2 text-left">Typ</th>
                        <th class="pb-2 text-left">Notering</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php foreach ($attendance as $rec): ?>
                    <tr>
                        <td class="py-1.5 text-gray-700 dark:text-gray-300"><?= htmlspecialchars($rec['date'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="py-1.5 text-gray-700 dark:text-gray-300"><?= htmlspecialchars($attendanceTypeMap[$rec['type']] ?? $rec['type'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="py-1.5 text-gray-500 dark:text-gray-400"><?= htmlspecialchars($rec['notes'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- ─── E4: Mina lönespecar ─────────────────────────────────────────────── -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Mina lönespecar</h2>
            <a href="/my-page/payslips" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Visa alla</a>
        </div>
        <?php if (!empty($payslips)): ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs text-gray-400 dark:text-gray-500 uppercase">
                        <th class="pb-2 text-left">Period</th>
                        <th class="pb-2 text-right">Bruttolön</th>
                        <th class="pb-2 text-right">Nettolön</th>
                        <th class="pb-2 text-left">Status</th>
                        <th class="pb-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php foreach ($payslips as $ps): ?>
                    <?php $s = $payslipStatusMap[$ps['status']] ?? ['bg-gray-100 text-gray-600', $ps['status']]; ?>
                    <tr>
                        <td class="py-2 text-gray-700 dark:text-gray-300"><?= htmlspecialchars($ps['period_name'] ?? ($ps['period_from'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="py-2 text-right text-gray-700 dark:text-gray-300"><?= number_format((float)($ps['gross_pay'] ?? 0), 0, ',', ' ') ?> kr</td>
                        <td class="py-2 text-right font-medium text-gray-900 dark:text-white"><?= number_format((float)($ps['net_pay'] ?? 0), 0, ',', ' ') ?> kr</td>
                        <td class="py-2"><span class="px-2 py-0.5 rounded text-xs <?= $s[0] ?>"><?= $s[1] ?></span></td>
                        <td class="py-2 text-right"><a href="/my-page/payslips/<?= (int)$ps['id'] ?>" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Visa</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="text-sm text-gray-400 dark:text-gray-500">Inga lönespecar hittades.</p>
        <?php endif; ?>
    </div>

    <!-- ─── E5: Anställningsavtal ─────────────────────────────────────────────── -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Anställningsavtal</h2>
            <a href="/my-page/contract" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Detaljer</a>
        </div>
        <?php if ($contract ?? null): ?>
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
        ?>
        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
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
                <dd class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($contract['weekly_hours'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Arbetsplats</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($contract['workplace'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
        </dl>
        <?php else: ?>
        <p class="text-sm text-gray-400 dark:text-gray-500">Inget anställningsavtal registrerat.</p>
        <?php endif; ?>
    </div>

    <!-- ─── E6: Mina ärenden ──────────────────────────────────────────────────── -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Mina ärenden</h2>
            <a href="/my-page/tickets" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Visa alla</a>
        </div>
        <?php if (!empty($openTickets)): ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs text-gray-400 dark:text-gray-500 uppercase">
                        <th class="pb-2 text-left">Ärendenr</th>
                        <th class="pb-2 text-left">Titel</th>
                        <th class="pb-2 text-left">Prioritet</th>
                        <th class="pb-2 text-left">Status</th>
                        <th class="pb-2 text-left">Datum</th>
                        <th class="pb-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php foreach (array_slice(array_values($openTickets), 0, 5) as $t): ?>
                    <?php $p = $ticketPriorityMap[$t['priority']] ?? ['bg-gray-100 text-gray-600', $t['priority']]; ?>
                    <?php $s = $ticketStatusMap[$t['status']] ?? ['bg-gray-100 text-gray-600', $t['status']]; ?>
                    <tr>
                        <td class="py-2 font-mono text-xs text-indigo-600 dark:text-indigo-400"><?= htmlspecialchars($t['ticket_number'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="py-2 text-gray-800 dark:text-gray-200 max-w-[200px] truncate"><?= htmlspecialchars($t['title'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="py-2"><span class="px-2 py-0.5 rounded text-xs <?= $p[0] ?>"><?= $p[1] ?></span></td>
                        <td class="py-2"><span class="px-2 py-0.5 rounded text-xs <?= $s[0] ?>"><?= $s[1] ?></span></td>
                        <td class="py-2 text-gray-500 dark:text-gray-400 text-xs"><?= htmlspecialchars(substr($t['created_at'], 0, 10), ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="py-2 text-right"><a href="/cs/tickets/<?= (int)$t['id'] ?>" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Visa</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="text-sm text-gray-400 dark:text-gray-500">Inga öppna ärenden.</p>
        <?php endif; ?>
    </div>

</div>

<script>
function myPageCalendar() {
    return {
        year: new Date().getFullYear(),
        month: new Date().getMonth(), // 0-indexed
        events: [],
        calendarCells: [],
        get monthLabel() {
            return new Date(this.year, this.month, 1).toLocaleString('sv-SE', { month: 'long', year: 'numeric' });
        },
        init() {
            this.loadEvents();
        },
        prevMonth() {
            if (this.month === 0) { this.month = 11; this.year--; }
            else { this.month--; }
            this.loadEvents();
        },
        nextMonth() {
            if (this.month === 11) { this.month = 0; this.year++; }
            else { this.month++; }
            this.loadEvents();
        },
        async loadEvents() {
            const from = `${this.year}-${String(this.month + 1).padStart(2, '0')}-01`;
            const lastDay = new Date(this.year, this.month + 1, 0).getDate();
            const to = `${this.year}-${String(this.month + 1).padStart(2, '0')}-${String(lastDay).padStart(2, '0')}`;
            try {
                const res = await fetch(`/my-page/calendar-events?from=${from}&to=${to}`);
                if (res.ok) {
                    this.events = await res.json();
                }
            } catch (e) {
                this.events = [];
            }
            this.buildGrid();
        },
        buildGrid() {
            const firstDay = new Date(this.year, this.month, 1);
            // Monday-first: 0=Mon … 6=Sun
            let startOffset = (firstDay.getDay() + 6) % 7;
            const daysInMonth = new Date(this.year, this.month + 1, 0).getDate();
            const today = new Date();
            const todayStr = `${today.getFullYear()}-${String(today.getMonth()+1).padStart(2,'0')}-${String(today.getDate()).padStart(2,'0')}`;
            const cells = [];
            for (let i = 0; i < startOffset; i++) {
                cells.push({ day: null, isToday: false, events: [] });
            }
            for (let d = 1; d <= daysInMonth; d++) {
                const dateStr = `${this.year}-${String(this.month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                cells.push({
                    day: d,
                    isToday: dateStr === todayStr,
                    events: this.events.filter(ev => ev.date === dateStr),
                });
            }
            // Pad to complete last row
            while (cells.length % 7 !== 0) {
                cells.push({ day: null, isToday: false, events: [] });
            }
            this.calendarCells = cells;
        },
    };
}
</script>
