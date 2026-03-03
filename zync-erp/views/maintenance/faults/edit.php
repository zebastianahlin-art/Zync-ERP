<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="/maintenance/faults/<?= (int)$fault['id'] ?>" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Redigera felanmälan – <?= htmlspecialchars($fault['fault_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
    </div>

    <?php if (!empty($error)): ?>
        <div class="rounded-lg bg-red-50 dark:bg-red-900/30 p-4 text-red-800 dark:text-red-200"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form method="POST" action="/maintenance/faults/<?= (int)$fault['id'] ?>" class="space-y-6">
        <?= \App\Core\Csrf::field() ?>
        <input type="hidden" name="_method" value="PUT">

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-5">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Felinformation</h2>

            <!-- Titel -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Titel <span class="text-red-500">*</span></label>
                <input type="text" id="title" name="title" required
                       value="<?= htmlspecialchars($_POST['title'] ?? $fault['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Maskin -->
                <div>
                    <label for="machine_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Maskin</label>
                    <select id="machine_id" name="machine_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">— Välj maskin —</option>
                        <?php foreach ($machines as $m): ?>
                        <option value="<?= (int)$m['id'] ?>" <?= (($_POST['machine_id'] ?? $fault['machine_id'] ?? '') == $m['id']) ? 'selected' : '' ?>>
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
                        <option value="<?= (int)$e['id'] ?>" <?= (($_POST['equipment_id'] ?? $fault['equipment_id'] ?? '') == $e['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($e['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Plats -->
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Plats</label>
                    <input type="text" id="location" name="location"
                           value="<?= htmlspecialchars($_POST['location'] ?? $fault['location'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>

                <!-- Avdelning -->
                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Avdelning</label>
                    <select id="department_id" name="department_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">— Välj avdelning —</option>
                        <?php foreach ($departments as $d): ?>
                        <option value="<?= (int)$d['id'] ?>" <?= (($_POST['department_id'] ?? $fault['department_id'] ?? '') == $d['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($d['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Feltyp -->
                <div>
                    <label for="fault_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Feltyp</label>
                    <select id="fault_type" name="fault_type" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <?php
                        $faultTypes = [
                            'mechanical'  => 'Mekanisk',
                            'electrical'  => 'Elektrisk',
                            'hydraulic'   => 'Hydraulisk',
                            'pneumatic'   => 'Pneumatisk',
                            'software'    => 'Mjukvara',
                            'structural'  => 'Strukturell',
                            'safety'      => 'Säkerhet',
                            'other'       => 'Övrigt',
                        ];
                        $currentFaultType = $_POST['fault_type'] ?? $fault['fault_type'] ?? '';
                        foreach ($faultTypes as $val => $label):
                        ?>
                        <option value="<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8') ?>" <?= ($currentFaultType === $val) ? 'selected' : '' ?>>
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
                        $currentPriority = $_POST['priority'] ?? $fault['priority'] ?? 'normal';
                        foreach ($priorities as $val => $label):
                        ?>
                        <option value="<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8') ?>" <?= ($currentPriority === $val) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Beskrivning -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Beskrivning <span class="text-red-500">*</span></label>
                <textarea id="description" name="description" rows="5" required
                          class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"><?= htmlspecialchars($_POST['description'] ?? $fault['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
        </div>

        <!-- Åtgärdsknappar -->
        <div class="flex items-center gap-3">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
                Spara ändringar
            </button>
            <a href="/maintenance/faults/<?= (int)$fault['id'] ?>" class="px-5 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                Avbryt
            </a>
        </div>
    </form>
</div>
