<div class="space-y-6" x-data="{ tab: 'info' }">
    <div class="flex items-center justify-between">
        <div>
            <a href="/employees" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600">&larr; Tillbaka till personal</a>
            <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                <?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name'], ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                <?= htmlspecialchars($employee['position'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                <?php if (!empty($employee['department_name'])): ?>
                &bull; <?= htmlspecialchars($employee['department_name'], ENT_QUOTES, 'UTF-8') ?>
                <?php endif; ?>
            </p>
        </div>
        <div class="flex items-center gap-3">
            <?php
            $statusColors = [
                'active'     => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
                'on_leave'   => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300',
                'terminated' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
            ];
            $statusLabels = ['active'=>'Aktiv','on_leave'=>'Tjänstledig','terminated'=>'Avslutad'];
            $st = $employee['status'] ?? 'active';
            $stClass = $statusColors[$st] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300';
            $stLabel = $statusLabels[$st] ?? $st;
            ?>
            <span class="px-3 py-1 rounded-full text-sm font-medium <?= $stClass ?>">
                <?= htmlspecialchars($stLabel, ENT_QUOTES, 'UTF-8') ?>
            </span>
            <a href="/employees/<?= (int)$employee['id'] ?>/edit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Redigera</a>
            <form method="POST" action="/employees/<?= (int)$employee['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort anställd?')">
                <?= \App\Core\Csrf::field() ?>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">Ta bort</button>
            </form>
        </div>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="flex gap-4 -mb-px">
            <button @click="tab='info'" :class="tab==='info' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400'" class="pb-3 border-b-2 text-sm font-medium transition">Grundinfo</button>
            <button @click="tab='certs'" :class="tab==='certs' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400'" class="pb-3 border-b-2 text-sm font-medium transition">Certifikat (<?= count($certificates) ?>)</button>
            <button @click="tab='training'" :class="tab==='training' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400'" class="pb-3 border-b-2 text-sm font-medium transition">Utbildningshistorik (<?= count($training) ?>)</button>
        </nav>
    </div>

    <!-- Grundinfo -->
    <div x-show="tab==='info'">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
            <?php
            $fields = [
                'Anst.nummer'      => $employee['employee_number'] ?? null,
                'E-post'           => $employee['email'] ?? null,
                'Telefon'          => $employee['phone'] ?? null,
                'Anställd sedan'   => $employee['hire_date'] ?? null,
                'Slutdatum'        => $employee['end_date'] ?? null,
                'Anställningsform' => ['full_time'=>'Heltid','part_time'=>'Deltid','consultant'=>'Konsult','intern'=>'Praktikant'][$employee['employment_type'] ?? ''] ?? ($employee['employment_type'] ?? null),
                'Lön'              => isset($employee['salary']) && $employee['salary'] !== null ? number_format((float)$employee['salary'], 2, ',', ' ') . ' kr' : null,
                'Chef'             => isset($employee['manager_first_name']) && $employee['manager_first_name'] ? $employee['manager_first_name'] . ' ' . $employee['manager_last_name'] : null,
                'Nödkontakt'       => $employee['emergency_contact_name'] ?? null,
                'Nödkontakt tel.'  => $employee['emergency_contact_phone'] ?? null,
            ];
            foreach ($fields as $label => $value): ?>
            <div>
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></dt>
                <dd class="mt-0.5 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($value ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
            <?php endforeach; ?>
            <?php if (!empty($employee['notes'])): ?>
            <div class="md:col-span-2">
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Anteckningar</dt>
                <dd class="mt-0.5 text-sm text-gray-900 dark:text-white whitespace-pre-wrap"><?= htmlspecialchars($employee['notes'], ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Certifikat -->
    <div x-show="tab==='certs'">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
            <div class="p-4 flex justify-between items-center border-b border-gray-200 dark:border-gray-700">
                <h3 class="font-semibold text-gray-900 dark:text-white">Certifikat</h3>
                <a href="/certificates/create" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">+ Nytt certifikat</a>
            </div>
            <?php if (empty($certificates)): ?>
            <p class="px-4 py-8 text-center text-gray-400 dark:text-gray-500 text-sm">Inga certifikat registrerade</p>
            <?php else: ?>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs text-gray-500 dark:text-gray-400 uppercase">Typ</th>
                        <th class="px-4 py-3 text-left text-xs text-gray-500 dark:text-gray-400 uppercase">Utfärdat</th>
                        <th class="px-4 py-3 text-left text-xs text-gray-500 dark:text-gray-400 uppercase">Utgår</th>
                        <th class="px-4 py-3 text-left text-xs text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($certificates as $cert):
                        $expiry = $cert['expiry_date'] ?? null;
                        $now = date('Y-m-d');
                        $in30 = date('Y-m-d', strtotime('+30 days'));
                        if (!$expiry) {
                            $badge = ['text'=>'Okänt','class'=>'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300'];
                        } elseif ($expiry < $now) {
                            $badge = ['text'=>'Utgånget','class'=>'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300'];
                        } elseif ($expiry <= $in30) {
                            $badge = ['text'=>'Utgår snart','class'=>'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300'];
                        } else {
                            $badge = ['text'=>'Giltigt','class'=>'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300'];
                        }
                    ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 text-gray-900 dark:text-white"><?= htmlspecialchars($cert['type_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($cert['issued_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($cert['expiry_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-xs font-medium <?= $badge['class'] ?>"><?= htmlspecialchars($badge['text'], ENT_QUOTES, 'UTF-8') ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Utbildningshistorik -->
    <div x-show="tab==='training'">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
            <?php if (empty($training)): ?>
            <p class="px-4 py-8 text-center text-gray-400 dark:text-gray-500 text-sm">Ingen utbildningshistorik</p>
            <?php else: ?>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs text-gray-500 dark:text-gray-400 uppercase">Kurs</th>
                        <th class="px-4 py-3 text-left text-xs text-gray-500 dark:text-gray-400 uppercase">Datum</th>
                        <th class="px-4 py-3 text-left text-xs text-gray-500 dark:text-gray-400 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs text-gray-500 dark:text-gray-400 uppercase">Poäng</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($training as $t): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 text-gray-900 dark:text-white"><?= htmlspecialchars($t['course_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($t['start_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($t['status'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars((string)($t['score'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>
