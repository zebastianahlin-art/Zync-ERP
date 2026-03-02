<?php
/** @var array $user */
/** @var array|null $employee */
/** @var array $certificates */
/** @var array $workOrders */
/** @var string $calendarEvents */

$flashSuccess = \App\Core\Flash::get("success");
$flashError = \App\Core\Flash::get("error");
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <?php if (!empty($user['avatar_path'])): ?>
                <img src="<?= htmlspecialchars($user['avatar_path']) ?>" alt="Avatar" class="h-16 w-16 rounded-full object-cover ring-2 ring-indigo-500">
            <?php else: ?>
                <div class="h-16 w-16 rounded-full bg-indigo-600 flex items-center justify-center text-white text-xl font-bold">
                    <?= strtoupper(mb_substr($user['full_name'] ?? $user['username'] ?? '?', 0, 1)) ?>
                </div>
            <?php endif; ?>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($user['full_name'] ?? $user['username']) ?></h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    <?= htmlspecialchars($user['role_name'] ?? '') ?>
                    <?php if ($employee && !empty($employee['department_name'])): ?>
                        &middot; <?= htmlspecialchars($employee['department_name']) ?>
                    <?php endif; ?>
                </p>
            </div>
        </div>
        <a href="/my-page/edit" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Redigera profil
        </a>
    </div>

    <?php if ($flashSuccess): ?>
        <div class="rounded-lg p-4 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400"><?= htmlspecialchars($flashSuccess) ?></div>
    <?php endif; ?>
    <?php if ($flashError): ?>
        <div class="rounded-lg p-4 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400"><?= htmlspecialchars($flashError) ?></div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Active Work Orders -->
        <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Aktiva arbetsordrar</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1"><?= count($workOrders) ?></p>
                </div>
                <div class="h-10 w-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
                </div>
            </div>
        </div>

        <!-- Certificates -->
        <?php
            $expiringSoon = array_filter($certificates, function($c) {
                return !empty($c['expiry_date']) && strtotime($c['expiry_date']) < strtotime('+90 days');
            });
        ?>
        <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Certifikat</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1"><?= count($certificates) ?></p>
                    <?php if (count($expiringSoon) > 0): ?>
                        <p class="text-xs text-amber-600 dark:text-amber-400 mt-1"><?= count($expiringSoon) ?> utgår snart</p>
                    <?php endif; ?>
                </div>
                <div class="h-10 w-10 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                    <svg class="h-5 w-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                </div>
            </div>
        </div>

        <!-- Role -->
        <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Roll</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white mt-1"><?= htmlspecialchars($user['role_name'] ?? '-') ?></p>
                </div>
                <div class="h-10 w-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                    <svg class="h-5 w-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
            </div>
        </div>

        <!-- 2FA Status -->
        <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tvåfaktorautentisering</p>
                    <?php if (!empty($user['2fa_enabled']) || !empty($user['totp_enabled'])): ?>
                        <p class="text-lg font-bold text-green-600 dark:text-green-400 mt-1">Aktiverad</p>
                    <?php else: ?>
                        <p class="text-lg font-bold text-red-500 mt-1">Ej aktiverad</p>
                    <?php endif; ?>
                </div>
                <div class="h-10 w-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                    <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Calendar -->
        <div class="lg:col-span-2 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Kalender</h2>
            <div id="mypage-calendar"></div>
        </div>

        <!-- My Certificates -->
        <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Mina certifikat</h2>
            <?php if (empty($certificates)): ?>
                <p class="text-sm text-gray-500 dark:text-gray-400">Inga certifikat registrerade.</p>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($certificates as $cert): ?>
                        <?php
                            $daysLeft = !empty($cert['expiry_date']) ? (int)((strtotime($cert['expiry_date']) - time()) / 86400) : null;
                            $statusClass = $daysLeft === null ? 'text-gray-500' :
                                ($daysLeft <= 0 ? 'text-red-600 dark:text-red-400' :
                                ($daysLeft <= 30 ? 'text-red-500' :
                                ($daysLeft <= 90 ? 'text-amber-500' : 'text-green-600 dark:text-green-400')));
                        ?>
                        <a href="/certificates/<?= $cert['id'] ?>/edit" class="block rounded-lg border border-gray-100 dark:border-gray-700 p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars(($cert['name'] ?? $cert['type'] ?? 'Certifikat') ?? 'Certifikat') ?></span>
                                <?php if ($daysLeft !== null): ?>
                                    <span class="text-xs font-medium <?= $statusClass ?>">
                                        <?= $daysLeft <= 0 ? 'Utgånget' : $daysLeft . ' dagar' ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($cert['expiry_date'])): ?>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Utgår: <?= date('Y-m-d', strtotime($cert['expiry_date'])) ?></p>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- My Work Orders -->
    <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Mina arbetsordrar</h2>
            <a href="/maintenance/work-orders" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Visa alla →</a>
        </div>
        <?php if (empty($workOrders)): ?>
            <p class="text-sm text-gray-500 dark:text-gray-400">Inga aktiva arbetsordrar tilldelade.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            <th class="pb-3 pr-4">ID</th>
                            <th class="pb-3 pr-4">Titel</th>
                            <th class="pb-3 pr-4">Utrustning</th>
                            <th class="pb-3 pr-4">Prioritet</th>
                            <th class="pb-3 pr-4">Status</th>
                            <th class="pb-3">Förfaller</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <?php foreach ($workOrders as $wo): ?>
                            <?php
                                $prioColors = [
                                    'critical' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                    'high'     => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
                                    'medium'   => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                    'low'      => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                ];
                                $prioClass = $prioColors[$wo['priority'] ?? 'low'] ?? $prioColors['low'];
                                $statusLabels = [
                                    'open'        => 'Öppen',
                                    'in_progress' => 'Pågår',
                                    'on_hold'     => 'Pausad',
                                    'pending'     => 'Väntande',
                                ];
                                $statusLabel = $statusLabels[$wo['status'] ?? ''] ?? ucfirst($wo['status'] ?? '');
                            ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="py-3 pr-4">
                                    <a href="/maintenance/work-orders/<?= $wo['id'] ?>" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">#<?= $wo['id'] ?></a>
                                </td>
                                <td class="py-3 pr-4 text-gray-900 dark:text-white"><?= htmlspecialchars($wo['title']) ?></td>
                                <td class="py-3 pr-4 text-gray-500 dark:text-gray-400"><?= htmlspecialchars($wo['equipment_name'] ?? '-') ?></td>
                                <td class="py-3 pr-4">
                                    <span class="inline-block rounded-full px-2 py-0.5 text-xs font-medium <?= $prioClass ?>">
                                        <?= ucfirst($wo['priority'] ?? 'normal') ?>
                                    </span>
                                </td>
                                <td class="py-3 pr-4 text-gray-600 dark:text-gray-300"><?= $statusLabel ?></td>
                                <td class="py-3 text-gray-500 dark:text-gray-400"><?= !empty($wo['planned_end']) ? date('Y-m-d', strtotime($wo['planned_end'])) : '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Personal Information -->
    <?php if ($employee): ?>
    <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Personaluppgifter</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
            <div>
                <p class="text-gray-500 dark:text-gray-400">Anställningsnummer</p>
                <p class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($employee['employee_number'] ?? '-') ?></p>
            </div>
            <div>
                <p class="text-gray-500 dark:text-gray-400">E-post</p>
                <p class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($employee['email'] ?? $user['email']) ?></p>
            </div>
            <div>
                <p class="text-gray-500 dark:text-gray-400">Telefon</p>
                <p class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($employee['phone'] ?? '-') ?></p>
            </div>
            <div>
                <p class="text-gray-500 dark:text-gray-400">Titel</p>
                <p class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($employee['title'] ?? '-') ?></p>
            </div>
            <div>
                <p class="text-gray-500 dark:text-gray-400">Avdelning</p>
                <p class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($employee['department_name'] ?? '-') ?></p>
            </div>
            <div>
                <p class="text-gray-500 dark:text-gray-400">Anställd sedan</p>
                <p class="font-medium text-gray-900 dark:text-white"><?= !empty($employee['hire_date']) ? date('Y-m-d', strtotime($employee['hire_date'])) : '-' ?></p>
            </div>
            <div>
                <p class="text-gray-500 dark:text-gray-400">Anställningstyp</p>
                <p class="font-medium text-gray-900 dark:text-white">
                    <?php
                        $types = ['full_time' => 'Heltid', 'part_time' => 'Deltid', 'consultant' => 'Konsult', 'intern' => 'Praktikant'];
                        echo $types[$employee['employment_type'] ?? ''] ?? $employee['employment_type'] ?? '-';
                    ?>
                </p>
            </div>
            <div>
                <p class="text-gray-500 dark:text-gray-400">Adress</p>
                <p class="font-medium text-gray-900 dark:text-white">
                    <?= htmlspecialchars(implode(', ', array_filter([$employee['address'] ?? '', $employee['postal_code'] ?? '', $employee['city'] ?? ''])) ?: '-') ?>
                </p>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- FullCalendar CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" />
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('mypage-calendar');
    if (!calendarEl) return;

    const isDark = document.documentElement.classList.contains('dark');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'sv',
        height: 'auto',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,listMonth'
        },
        events: <?= $calendarEvents ?>,
        eventClick: function(info) {
            if (info.event.url) {
                info.jsEvent.preventDefault();
                window.location.href = info.event.url;
            }
        },
        eventDisplay: 'block',
        dayMaxEvents: 3,
    });
    calendar.render();

    // Dark mode styling
    if (isDark) {
        calendarEl.style.setProperty('--fc-border-color', '#374151');
        calendarEl.style.setProperty('--fc-page-bg-color', 'transparent');
        calendarEl.style.setProperty('--fc-neutral-bg-color', '#1f2937');
        calendarEl.style.setProperty('--fc-today-bg-color', 'rgba(99,102,241,0.1)');
    }
});
</script>
