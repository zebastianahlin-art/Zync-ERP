<div class="space-y-6">
    <!-- Sidhuvud -->
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <a href="/maintenance/faults" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($fault['fault_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
            <?= faultShowPriority($fault['priority'] ?? 'normal') ?>
            <?= faultShowStatus($fault['status'] ?? 'reported') ?>
        </div>

        <!-- Åtgärdsknappar beroende på status -->
        <div class="flex flex-wrap gap-2">
            <?php if (in_array($fault['status'] ?? '', ['reported', 'acknowledged'])): ?>
                <a href="/maintenance/faults/<?= (int)$fault['id'] ?>/edit" class="px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                    Redigera
                </a>
            <?php endif; ?>

            <?php if (($fault['status'] ?? '') === 'reported'): ?>
                <form method="POST" action="/maintenance/faults/<?= (int)$fault['id'] ?>/acknowledge" class="inline">
                    <?= \App\Core\Csrf::field() ?>
                    <button type="submit" class="px-3 py-2 text-sm bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition">
                        Bekräfta
                    </button>
                </form>
                <form method="POST" action="/maintenance/faults/<?= (int)$fault['id'] ?>/delete"
                      class="inline"
                      onsubmit="return confirm('Är du säker på att du vill ta bort denna felanmälan?');">
                    <?= \App\Core\Csrf::field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="px-3 py-2 text-sm bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                        Ta bort
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($success)): ?>
        <div class="rounded-lg bg-green-50 dark:bg-green-900/30 p-4 text-green-800 dark:text-green-200"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="rounded-lg bg-red-50 dark:bg-red-900/30 p-4 text-red-800 dark:text-red-200"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <!-- Tilldela (vid status acknowledged) -->
    <?php if (($fault['status'] ?? '') === 'acknowledged'): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Tilldela till tekniker</h2>
        <form method="POST" action="/maintenance/faults/<?= (int)$fault['id'] ?>/assign" class="flex flex-wrap items-end gap-3">
            <?= \App\Core\Csrf::field() ?>
            <div class="flex-1 min-w-48">
                <label for="assigned_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tekniker <span class="text-red-500">*</span></label>
                <select id="assigned_to" name="assigned_to" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">— Välj tekniker —</option>
                    <?php foreach ($users as $u): ?>
                    <option value="<?= (int)$u['id'] ?>"><?= htmlspecialchars(($u['name'] ?? $u['full_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                Tilldela
            </button>
        </form>
    </div>
    <?php endif; ?>

    <!-- Markera som löst (vid status assigned eller in_progress) -->
    <?php if (in_array($fault['status'] ?? '', ['assigned', 'in_progress'])): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Markera som löst</h2>
        <form method="POST" action="/maintenance/faults/<?= (int)$fault['id'] ?>/resolve" class="space-y-3">
            <?= \App\Core\Csrf::field() ?>
            <div>
                <label for="resolution" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Åtgärd/lösning</label>
                <textarea id="resolution" name="resolution" rows="3"
                          class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                          placeholder="Beskriv hur felet löstes..."></textarea>
            </div>
            <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition">
                Markera löst
            </button>
        </form>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Vänster kolumn: Info + Status + Länkad arbetsorder -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Felinformation -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Felinformation</h2>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Feltyp</dt>
                        <dd class="text-gray-900 dark:text-white mt-0.5"><?= htmlspecialchars(faultTypeLabel($fault['fault_type'] ?? ''), ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Maskin</dt>
                        <dd class="text-gray-900 dark:text-white mt-0.5"><?= htmlspecialchars($fault['machine_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Utrustning</dt>
                        <dd class="text-gray-900 dark:text-white mt-0.5"><?= htmlspecialchars($fault['equipment_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Plats</dt>
                        <dd class="text-gray-900 dark:text-white mt-0.5"><?= htmlspecialchars($fault['location'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Avdelning</dt>
                        <dd class="text-gray-900 dark:text-white mt-0.5"><?= htmlspecialchars($fault['department_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Rapporterad av</dt>
                        <dd class="text-gray-900 dark:text-white mt-0.5"><?= htmlspecialchars($fault['reported_by_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Rapporterad</dt>
                        <dd class="text-gray-900 dark:text-white mt-0.5"><?= htmlspecialchars(!empty($fault['created_at']) ? date('Y-m-d H:i', strtotime($fault['created_at'])) : '—', ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                </dl>
            </div>

            <!-- Beskrivning -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3">Beskrivning</h2>
                <div class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap"><?= htmlspecialchars($fault['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
            </div>

            <!-- Länkad arbetsorder -->
            <?php if (!empty($fault['work_order_id'])): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3">Länkad arbetsorder</h2>
                <a href="/maintenance/work-orders/<?= (int)$fault['work_order_id'] ?>" class="inline-flex items-center gap-2 text-blue-600 hover:underline text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Arbetsorder #<?= (int)$fault['work_order_id'] ?>
                    <?php if (!empty($fault['work_order_number'])): ?>
                        – <?= htmlspecialchars($fault['work_order_number'], ENT_QUOTES, 'UTF-8') ?>
                    <?php endif; ?>
                </a>
            </div>
            <?php endif; ?>
        </div>

        <!-- Höger kolumn: Statushistorik -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Statushistorik</h2>
                <ol class="relative border-l border-gray-200 dark:border-gray-700 space-y-5 ml-2">

                    <!-- Rapporterad -->
                    <li class="ml-4">
                        <div class="absolute -left-1.5 w-3 h-3 rounded-full bg-blue-500"></div>
                        <p class="text-xs font-semibold text-gray-900 dark:text-white">Rapporterad</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars(!empty($fault['created_at']) ? date('Y-m-d H:i', strtotime($fault['created_at'])) : '—', ENT_QUOTES, 'UTF-8') ?></p>
                        <?php if (!empty($fault['reported_by_name'])): ?>
                        <p class="text-xs text-gray-600 dark:text-gray-400"><?= htmlspecialchars($fault['reported_by_name'], ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endif; ?>
                    </li>

                    <!-- Bekräftad -->
                    <?php if (!empty($fault['acknowledged_at'])): ?>
                    <li class="ml-4">
                        <div class="absolute -left-1.5 w-3 h-3 rounded-full bg-yellow-500"></div>
                        <p class="text-xs font-semibold text-gray-900 dark:text-white">Bekräftad</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars(date('Y-m-d H:i', strtotime($fault['acknowledged_at'])), ENT_QUOTES, 'UTF-8') ?></p>
                        <?php if (!empty($fault['acknowledged_by_name'])): ?>
                        <p class="text-xs text-gray-600 dark:text-gray-400"><?= htmlspecialchars($fault['acknowledged_by_name'], ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endif; ?>
                    </li>
                    <?php endif; ?>

                    <!-- Tilldelad -->
                    <?php if (!empty($fault['assigned_at'])): ?>
                    <li class="ml-4">
                        <div class="absolute -left-1.5 w-3 h-3 rounded-full bg-indigo-500"></div>
                        <p class="text-xs font-semibold text-gray-900 dark:text-white">Tilldelad</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars(date('Y-m-d H:i', strtotime($fault['assigned_at'])), ENT_QUOTES, 'UTF-8') ?></p>
                        <?php if (!empty($fault['assigned_to_name'])): ?>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Till: <?= htmlspecialchars($fault['assigned_to_name'], ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endif; ?>
                        <?php if (!empty($fault['assigned_by_name'])): ?>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Av: <?= htmlspecialchars($fault['assigned_by_name'], ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endif; ?>
                    </li>
                    <?php endif; ?>

                    <!-- Löst -->
                    <?php if (!empty($fault['resolved_at'])): ?>
                    <li class="ml-4">
                        <div class="absolute -left-1.5 w-3 h-3 rounded-full bg-green-500"></div>
                        <p class="text-xs font-semibold text-gray-900 dark:text-white">Löst</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars(date('Y-m-d H:i', strtotime($fault['resolved_at'])), ENT_QUOTES, 'UTF-8') ?></p>
                    </li>
                    <?php endif; ?>

                    <!-- Stängd -->
                    <?php if (!empty($fault['closed_at'])): ?>
                    <li class="ml-4">
                        <div class="absolute -left-1.5 w-3 h-3 rounded-full bg-gray-400"></div>
                        <p class="text-xs font-semibold text-gray-900 dark:text-white">Stängd</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars(date('Y-m-d H:i', strtotime($fault['closed_at'])), ENT_QUOTES, 'UTF-8') ?></p>
                    </li>
                    <?php endif; ?>
                </ol>
            </div>
        </div>
    </div>
</div>

<?php
function faultShowStatus(string $s): string {
    $m = [
        'reported'    => ['Rapporterad', 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'],
        'acknowledged'=> ['Bekräftad',   'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'],
        'assigned'    => ['Tilldelad',   'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300'],
        'in_progress' => ['Pågår',       'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300'],
        'resolved'    => ['Löst',        'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'],
        'closed'      => ['Stängd',      'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
    ];
    $label = $m[$s][0] ?? $s;
    $class = $m[$s][1] ?? 'bg-gray-100 text-gray-700';
    return "<span class=\"inline-flex px-2 py-1 rounded-full text-xs font-medium {$class}\">" . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . "</span>";
}

function faultShowPriority(string $p): string {
    $m = [
        'low'      => ['Låg',       'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
        'normal'   => ['Normal',    'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'],
        'high'     => ['Hög',       'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300'],
        'urgent'   => ['Brådskande','bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'],
        'critical' => ['Kritisk',   'bg-red-200 text-red-900 dark:bg-red-900/50 dark:text-red-200 font-bold'],
    ];
    $label = $m[$p][0] ?? $p;
    $class = $m[$p][1] ?? 'bg-gray-100 text-gray-700';
    return "<span class=\"inline-flex px-2 py-1 rounded-full text-xs font-medium {$class}\">" . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . "</span>";
}

function faultTypeLabel(string $t): string {
    $m = [
        'mechanical'  => 'Mekanisk',
        'electrical'  => 'Elektrisk',
        'hydraulic'   => 'Hydraulisk',
        'pneumatic'   => 'Pneumatisk',
        'software'    => 'Mjukvara',
        'structural'  => 'Strukturell',
        'safety'      => 'Säkerhet',
        'other'       => 'Övrigt',
    ];
    return $m[$t] ?? $t;
}
?>
