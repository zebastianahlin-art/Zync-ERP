<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <a href="/maintenance/inspections" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="font-mono text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($inspection['inspection_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                    <?= inspShowTypeBadge($inspection['inspection_type'] ?? '') ?>
                    <?= inspShowStatusBadge($inspection['status'] ?? 'scheduled') ?>
                </div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white mt-1">
                    <?= htmlspecialchars($inspection['equipment_name'] ?? ($inspection['machine_name'] ?? 'Besiktning'), ENT_QUOTES, 'UTF-8') ?>
                </h1>
            </div>
        </div>

        <!-- Åtgärdsknappar -->
        <div class="flex items-center gap-2 flex-wrap">
            <?php if (!in_array($inspection['status'] ?? '', ['completed', 'cancelled'])): ?>
            <button type="button" onclick="document.getElementById('complete-form').classList.toggle('hidden')"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                Slutför besiktning
            </button>
            <?php endif; ?>
            <?php if (($inspection['status'] ?? '') === 'scheduled'): ?>
            <a href="/maintenance/inspections/<?= (int)($inspection['id'] ?? 0) ?>/edit"
                class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 px-4 py-2 rounded-lg text-sm font-medium transition">
                Redigera
            </a>
            <form method="POST" action="/maintenance/inspections/<?= (int)($inspection['id'] ?? 0) ?>/delete"
                onsubmit="return confirm('Är du säker på att du vill ta bort denna besiktning?')">
                <?= \App\Core\Csrf::field() ?>
                <button type="submit"
                    class="bg-white dark:bg-gray-700 border border-red-300 dark:border-red-700 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 px-4 py-2 rounded-lg text-sm font-medium transition">
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

    <!-- Slutför besiktning-formulär (dolt som standard) -->
    <?php if (!in_array($inspection['status'] ?? '', ['completed', 'cancelled'])): ?>
    <div id="complete-form" class="hidden bg-white dark:bg-gray-800 rounded-xl shadow p-6 border-l-4 border-green-500">
        <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-4">Slutför besiktning</h2>
        <form method="POST" action="/maintenance/inspections/<?= (int)($inspection['id'] ?? 0) ?>/complete" class="space-y-4">
            <?= \App\Core\Csrf::field() ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Slutdatum <span class="text-red-500">*</span></label>
                    <input type="date" name="completed_date" required
                        value="<?= htmlspecialchars(date('Y-m-d'), ENT_QUOTES, 'UTF-8') ?>"
                        class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Resultat <span class="text-red-500">*</span></label>
                    <select name="result" required
                        class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Välj resultat…</option>
                        <option value="pass">Godkänd</option>
                        <option value="fail">Underkänd</option>
                        <option value="conditional">Villkorlig</option>
                        <option value="na">Ej tillämpligt</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Anteckningar</label>
                <textarea name="notes" rows="3"
                    class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 resize-y"><?= htmlspecialchars($inspection['notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nästa besiktningsdatum</label>
                <input type="date" name="next_inspection_date"
                    class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
                    Bekräfta slutförande
                </button>
                <button type="button" onclick="document.getElementById('complete-form').classList.add('hidden')"
                    class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition">
                    Avbryt
                </button>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <!-- Informationskort -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-4">Besiktningsinformation</h2>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
            <div>
                <dt class="text-gray-500 dark:text-gray-400">Utrustning</dt>
                <dd class="font-medium text-gray-900 dark:text-white mt-0.5"><?= htmlspecialchars($inspection['equipment_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
            <div>
                <dt class="text-gray-500 dark:text-gray-400">Maskin</dt>
                <dd class="font-medium text-gray-900 dark:text-white mt-0.5"><?= htmlspecialchars($inspection['machine_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
            <div>
                <dt class="text-gray-500 dark:text-gray-400">Besiktningstyp</dt>
                <dd class="mt-0.5"><?= inspShowTypeBadge($inspection['inspection_type'] ?? '') ?></dd>
            </div>
            <div>
                <dt class="text-gray-500 dark:text-gray-400">Planerat datum</dt>
                <dd class="font-medium text-gray-900 dark:text-white mt-0.5"><?= htmlspecialchars(!empty($inspection['scheduled_date']) ? date('Y-m-d', strtotime($inspection['scheduled_date'])) : '—', ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
            <div>
                <dt class="text-gray-500 dark:text-gray-400">Inspektör</dt>
                <dd class="font-medium text-gray-900 dark:text-white mt-0.5"><?= htmlspecialchars($inspection['inspector_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
            <div>
                <dt class="text-gray-500 dark:text-gray-400">Avdelning</dt>
                <dd class="font-medium text-gray-900 dark:text-white mt-0.5"><?= htmlspecialchars($inspection['department_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
        </dl>
    </div>

    <!-- Resultatkort (visas bara om slutförd) -->
    <?php if (($inspection['status'] ?? '') === 'completed'): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-4">Besiktningsresultat</h2>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
            <div>
                <dt class="text-gray-500 dark:text-gray-400">Slutdatum</dt>
                <dd class="font-medium text-gray-900 dark:text-white mt-0.5"><?= htmlspecialchars(!empty($inspection['completed_date']) ? date('Y-m-d', strtotime($inspection['completed_date'])) : '—', ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
            <div>
                <dt class="text-gray-500 dark:text-gray-400">Resultat</dt>
                <dd class="mt-0.5"><?= inspShowResultBadge($inspection['result'] ?? '') ?></dd>
            </div>
            <?php if (!empty($inspection['next_inspection_date'])): ?>
            <div>
                <dt class="text-gray-500 dark:text-gray-400">Nästa besiktning</dt>
                <dd class="font-medium text-gray-900 dark:text-white mt-0.5"><?= htmlspecialchars(date('Y-m-d', strtotime($inspection['next_inspection_date'])), ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
            <?php endif; ?>
        </dl>
        <?php if (!empty($inspection['notes'])): ?>
        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Anteckningar</p>
            <p class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-line"><?= htmlspecialchars($inspection['notes'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php
function inspShowTypeBadge(string $t): string {
    $m = [
        'safety'      => ['Säkerhet',       'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'],
        'regulatory'  => ['Regulatorisk',   'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300'],
        'routine'     => ['Rutinmässig',    'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'],
        'preventive'  => ['Förebyggande',   'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'],
    ];
    $label = $m[$t][0] ?? $t;
    $class = $m[$t][1] ?? 'bg-gray-100 text-gray-700';
    return "<span class=\"inline-flex px-2 py-1 rounded-full text-xs font-medium {$class}\">" . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . "</span>";
}

function inspShowStatusBadge(string $s): string {
    $m = [
        'scheduled'   => ['Planerad',  'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'],
        'in_progress' => ['Pågår',     'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300'],
        'completed'   => ['Slutförd',  'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'],
        'overdue'     => ['Förfallen', 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'],
        'cancelled'   => ['Avbokad',   'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
    ];
    $label = $m[$s][0] ?? $s;
    $class = $m[$s][1] ?? 'bg-gray-100 text-gray-700';
    return "<span class=\"inline-flex px-2 py-1 rounded-full text-xs font-medium {$class}\">" . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . "</span>";
}

function inspShowResultBadge(string $r): string {
    if ($r === '') return '<span class="text-gray-400 dark:text-gray-500 text-sm">—</span>';
    $m = [
        'pass'        => ['Godkänd',        'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'],
        'fail'        => ['Underkänd',      'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'],
        'conditional' => ['Villkorlig',     'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300'],
        'na'          => ['Ej tillämpligt', 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
    ];
    $label = $m[$r][0] ?? $r;
    $class = $m[$r][1] ?? 'bg-gray-100 text-gray-700';
    return "<span class=\"inline-flex px-2 py-1 rounded-full text-xs font-medium {$class}\">" . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . "</span>";
}
?>
