<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="/maintenance/work-orders" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Ny arbetsorder</h1>
    </div>

    <?php if (!empty($error)): ?>
        <div class="rounded-lg bg-red-50 dark:bg-red-900/30 p-4 text-red-800 dark:text-red-200"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form method="POST" action="/maintenance/work-orders" class="space-y-6">
        <?= \App\Core\Csrf::field() ?>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-5">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Grundinformation</h2>

            <!-- Titel -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Titel <span class="text-red-500">*</span></label>
                <input type="text" id="title" name="title" required
                       value="<?= htmlspecialchars($_POST['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Arbetstyp -->
                <div>
                    <label for="work_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Arbetstyp <span class="text-red-500">*</span></label>
                    <select id="work_type" name="work_type" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <?php
                        $workTypes = [
                            'corrective'  => 'Korrigerande',
                            'preventive'  => 'Förebyggande',
                            'predictive'  => 'Prediktiv',
                            'emergency'   => 'Akut',
                            'improvement' => 'Förbättring',
                            'inspection'  => 'Inspektion',
                        ];
                        foreach ($workTypes as $val => $label):
                        ?>
                        <option value="<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8') ?>" <?= (($_POST['work_type'] ?? 'corrective') === $val) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Prioritet -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prioritet</label>
                    <select id="priority" name="priority" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <?php
                        $priorities = [
                            'low'      => 'Låg',
                            'normal'   => 'Normal',
                            'high'     => 'Hög',
                            'urgent'   => 'Brådskande',
                            'critical' => 'Kritisk',
                        ];
                        foreach ($priorities as $val => $label):
                        ?>
                        <option value="<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8') ?>" <?= (($_POST['priority'] ?? 'normal') === $val) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Maskin -->
                <div>
                    <label for="machine_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Maskin</label>
                    <select id="machine_id" name="machine_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">— Välj maskin —</option>
                        <?php foreach ($machines as $m): ?>
                        <option value="<?= (int)$m['id'] ?>" <?= (($_POST['machine_id'] ?? '') == $m['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Utrustning -->
                <div>
                    <label for="equipment_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Utrustning</label>
                    <select id="equipment_id" name="equipment_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">— Välj utrustning —</option>
                        <?php foreach ($equipment as $e): ?>
                        <option value="<?= (int)$e['id'] ?>" <?= (($_POST['equipment_id'] ?? '') == $e['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($e['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Kopplad felanmälan -->
                <div>
                    <label for="fault_report_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kopplad felanmälan</label>
                    <select id="fault_report_id" name="fault_report_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">— Välj felanmälan —</option>
                        <?php foreach ($faultReports as $fr): ?>
                        <option value="<?= (int)$fr['id'] ?>" <?= (($_POST['fault_report_id'] ?? '') == $fr['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars(($fr['fault_number'] ?? '') . ' – ' . ($fr['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Plats -->
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Plats</label>
                    <input type="text" id="location" name="location"
                           value="<?= htmlspecialchars($_POST['location'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>

                <!-- Avdelning -->
                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Avdelning</label>
                    <select id="department_id" name="department_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">— Välj avdelning —</option>
                        <?php foreach ($departments as $d): ?>
                        <option value="<?= (int)$d['id'] ?>" <?= (($_POST['department_id'] ?? '') == $d['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($d['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Kostnadställe -->
                <div>
                    <label for="cost_center_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kostnadsställe</label>
                    <select id="cost_center_id" name="cost_center_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">— Välj kostnadsställe —</option>
                        <?php foreach ($costCenters as $cc): ?>
                        <option value="<?= (int)$cc['id'] ?>" <?= (($_POST['cost_center_id'] ?? '') == $cc['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars(($cc['code'] ?? '') . ' ' . ($cc['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-5">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Planering</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Planerad start -->
                <div>
                    <label for="planned_start" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Planerad start</label>
                    <input type="datetime-local" id="planned_start" name="planned_start"
                           value="<?= htmlspecialchars($_POST['planned_start'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>

                <!-- Planerat slut -->
                <div>
                    <label for="planned_end" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Planerat slut</label>
                    <input type="datetime-local" id="planned_end" name="planned_end"
                           value="<?= htmlspecialchars($_POST['planned_end'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>

                <!-- Beräknad tid -->
                <div>
                    <label for="estimated_hours" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Beräknad tid (tim)</label>
                    <input type="number" id="estimated_hours" name="estimated_hours" step="0.5" min="0"
                           value="<?= htmlspecialchars($_POST['estimated_hours'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>

                <!-- Tilldelad till (omedelbar) -->
                <div>
                    <label for="assigned_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tilldela till (valfritt)</label>
                    <select id="assigned_to" name="assigned_to" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">— Välj tekniker —</option>
                        <?php foreach ($users as $u): ?>
                        <option value="<?= (int)$u['id'] ?>" <?= (($_POST['assigned_to'] ?? '') == $u['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($u['name'] ?? ($u['full_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-5">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Beskrivning</h2>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Beskrivning av arbetet</label>
                <textarea id="description" name="description" rows="5"
                          class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"><?= htmlspecialchars($_POST['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
        </div>

        <!-- Åtgärdsknappar -->
        <div class="flex items-center gap-3">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
                Spara arbetsorder
            </button>
            <a href="/maintenance/work-orders" class="px-5 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                Avbryt
            </a>
        </div>
    </form>
</div>
