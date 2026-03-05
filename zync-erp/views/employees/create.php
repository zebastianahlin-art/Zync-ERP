<div class="mx-auto max-w-4xl">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Ny anst&#228;lld</h1>
        <a href="/employees" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600 transition-colors">&larr; Tillbaka</a>
    </div>

    <div class="rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-md">
        <form method="POST" action="/employees" class="space-y-8">
            <?= \App\Core\Csrf::field() ?>

            <!-- Personuppgifter -->
            <div>
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Personuppgifter</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">F&#246;rnamn <span class="text-red-500">*</span></label>
                        <input id="first_name" name="first_name" type="text" required
                               value="<?= htmlspecialchars($old['first_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               class="mt-1 block w-full rounded-lg border <?= isset($errors['first_name']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <?php if (isset($errors['first_name'])): ?>
                            <p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['first_name'], ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Efternamn <span class="text-red-500">*</span></label>
                        <input id="last_name" name="last_name" type="text" required
                               value="<?= htmlspecialchars($old['last_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               class="mt-1 block w-full rounded-lg border <?= isset($errors['last_name']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <?php if (isset($errors['last_name'])): ?>
                            <p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['last_name'], ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4 mt-4">
                    <div>
                        <label for="personal_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Personnummer</label>
                        <input id="personal_number" name="personal_number" type="text" placeholder="YYYYMMDD-XXXX"
                               value="<?= htmlspecialchars($old['personal_number'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="birth_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">F&#246;delsedatum</label>
                        <input id="birth_date" name="birth_date" type="date"
                               value="<?= htmlspecialchars($old['birth_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 dark:text-gray-300">K&#246;n</label>
                        <select id="gender" name="gender"
                                class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            <option value="">&#8212; V&#228;lj &#8212;</option>
                            <option value="male" <?= ($old['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Man</option>
                            <option value="female" <?= ($old['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Kvinna</option>
                            <option value="other" <?= ($old['gender'] ?? '') === 'other' ? 'selected' : '' ?>>&#214;vrigt</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div>
                        <label for="nationality" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nationalitet</label>
                        <input id="nationality" name="nationality" type="text"
                               value="<?= htmlspecialchars($old['nationality'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="civil_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Civilst&#229;nd</label>
                        <select id="civil_status" name="civil_status"
                                class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            <option value="">&#8212; V&#228;lj &#8212;</option>
                            <option value="single" <?= ($old['civil_status'] ?? '') === 'single' ? 'selected' : '' ?>>Ogift</option>
                            <option value="married" <?= ($old['civil_status'] ?? '') === 'married' ? 'selected' : '' ?>>Gift</option>
                            <option value="cohabiting" <?= ($old['civil_status'] ?? '') === 'cohabiting' ? 'selected' : '' ?>>Sambo</option>
                            <option value="divorced" <?= ($old['civil_status'] ?? '') === 'divorced' ? 'selected' : '' ?>>Skild</option>
                            <option value="widowed" <?= ($old['civil_status'] ?? '') === 'widowed' ? 'selected' : '' ?>>&#196;nka/&#228;nkling</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Kontaktinfo -->
            <div>
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Kontaktinformation</h2>
                <div class="space-y-4">
                    <div>
                        <label for="address_street" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gatuadress</label>
                        <input id="address_street" name="address_street" type="text"
                               value="<?= htmlspecialchars($old['address_street'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="address_zip" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Postnummer</label>
                            <input id="address_zip" name="address_zip" type="text"
                                   value="<?= htmlspecialchars($old['address_zip'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="address_city" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ort</label>
                            <input id="address_city" name="address_city" type="text"
                                   value="<?= htmlspecialchars($old['address_city'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tj&#228;nstetelefon</label>
                            <input id="phone" name="phone" type="text"
                                   value="<?= htmlspecialchars($old['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tj&#228;ste-e-post</label>
                            <input id="email" name="email" type="email"
                                   value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   class="mt-1 block w-full rounded-lg border <?= isset($errors['email']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            <?php if (isset($errors['email'])): ?>
                                <p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="private_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Privat telefon</label>
                            <input id="private_phone" name="private_phone" type="text"
                                   value="<?= htmlspecialchars($old['private_phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="private_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Privat e-post</label>
                            <input id="private_email" name="private_email" type="email"
                                   value="<?= htmlspecialchars($old['private_email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="ice_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ICE-kontakt (namn)</label>
                            <input id="ice_name" name="ice_name" type="text"
                                   value="<?= htmlspecialchars($old['ice_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="ice_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ICE-kontakt (telefon)</label>
                            <input id="ice_phone" name="ice_phone" type="text"
                                   value="<?= htmlspecialchars($old['ice_phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">N&#246;dkontakt (namn)</label>
                            <input id="emergency_contact_name" name="emergency_contact_name" type="text"
                                   value="<?= htmlspecialchars($old['emergency_contact_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">N&#246;dkontakt (telefon)</label>
                            <input id="emergency_contact_phone" name="emergency_contact_phone" type="text"
                                   value="<?= htmlspecialchars($old['emergency_contact_phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Anställningsinfo -->
            <div>
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Anst&#228;llningsinformation</h2>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="employee_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Anst&#228;llningsnummer</label>
                            <input id="employee_number" name="employee_number" type="text"
                                   value="<?= htmlspecialchars($old['employee_number'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="hire_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Anst&#228;llningsdatum</label>
                            <input id="hire_date" name="hire_date" type="date"
                                   value="<?= htmlspecialchars($old['hire_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="department_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Avdelning</label>
                            <select id="department_id" name="department_id"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                <option value="">&#8212; V&#228;lj avdelning &#8212;</option>
                                <?php foreach ($departments as $d): ?>
                                    <option value="<?= $d['id'] ?>" <?= ($old['department_id'] ?? '') == $d['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($d['name'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="position" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Befattning/Titel</label>
                            <input id="position" name="position" type="text"
                                   value="<?= htmlspecialchars($old['position'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label for="employment_category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Anst&#228;llningsform</label>
                            <select id="employment_category" name="employment_category"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                <option value="">&#8212; V&#228;lj &#8212;</option>
                                <option value="tillsvidare" <?= ($old['employment_category'] ?? '') === 'tillsvidare' ? 'selected' : '' ?>>Tillsvidare</option>
                                <option value="visstid" <?= ($old['employment_category'] ?? '') === 'visstid' ? 'selected' : '' ?>>Visstid</option>
                                <option value="provanstallning" <?= ($old['employment_category'] ?? '') === 'provanstallning' ? 'selected' : '' ?>>Provanst&#228;llning</option>
                                <option value="timme" <?= ($old['employment_category'] ?? '') === 'timme' ? 'selected' : '' ?>>Timanst&#228;lld</option>
                            </select>
                        </div>
                        <div>
                            <label for="employment_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Syssels&#228;ttningstyp</label>
                            <select id="employment_type" name="employment_type"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                <option value="">&#8212; V&#228;lj &#8212;</option>
                                <option value="full_time" <?= ($old['employment_type'] ?? '') === 'full_time' ? 'selected' : '' ?>>Heltid</option>
                                <option value="part_time" <?= ($old['employment_type'] ?? '') === 'part_time' ? 'selected' : '' ?>>Deltid</option>
                                <option value="consultant" <?= ($old['employment_type'] ?? '') === 'consultant' ? 'selected' : '' ?>>Konsult</option>
                                <option value="intern" <?= ($old['employment_type'] ?? '') === 'intern' ? 'selected' : '' ?>>Praktikant</option>
                            </select>
                        </div>
                        <div>
                            <label for="work_percentage" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Arbetstid %</label>
                            <input id="work_percentage" name="work_percentage" type="number" min="0" max="100" step="0.5"
                                   value="<?= htmlspecialchars($old['work_percentage'] ?? '100', ENT_QUOTES, 'UTF-8') ?>"
                                   class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label for="pay_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">L&#246;netyp</label>
                            <select id="pay_type" name="pay_type"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                <option value="monthly" <?= ($old['pay_type'] ?? 'monthly') === 'monthly' ? 'selected' : '' ?>>M&#229;nadsl&#246;n</option>
                                <option value="hourly" <?= ($old['pay_type'] ?? '') === 'hourly' ? 'selected' : '' ?>>Timl&#246;n</option>
                            </select>
                        </div>
                        <div>
                            <label for="salary" class="block text-sm font-medium text-gray-700 dark:text-gray-300">L&#246;n (kr)</label>
                            <input id="salary" name="salary" type="number" step="0.01" min="0"
                                   value="<?= htmlspecialchars($old['salary'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                            <select id="status" name="status"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                <option value="active" <?= ($old['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Aktiv</option>
                                <option value="on_leave" <?= ($old['status'] ?? '') === 'on_leave' ? 'selected' : '' ?>>Tj&#228;nstledig</option>
                                <option value="terminated" <?= ($old['status'] ?? '') === 'terminated' ? 'selected' : '' ?>>Avslutad</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Slutdatum</label>
                            <input id="end_date" name="end_date" type="date"
                                   value="<?= htmlspecialchars($old['end_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="manager_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Chef (Manager)</label>
                            <select id="manager_id" name="manager_id"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                <option value="">&#8212; Ingen chef &#8212;</option>
                                <?php foreach ($managers as $mgr): ?>
                                    <option value="<?= $mgr['id'] ?>" <?= ($old['manager_id'] ?? '') == $mgr['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($mgr['last_name'] . ', ' . $mgr['first_name'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="profile_image_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Profilbild (URL)</label>
                        <input id="profile_image_url" name="profile_image_url" type="url" placeholder="https://..."
                               value="<?= htmlspecialchars($old['profile_image_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Anteckningar</label>
                        <textarea id="notes" name="notes" rows="3"
                                  class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"><?= htmlspecialchars($old['notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-2 border-t border-gray-200 dark:border-gray-700">
                <a href="/employees" class="rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Avbryt</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">
                    Skapa anst&#228;lld
                </button>
            </div>
        </form>
    </div>
</div>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Ny anst&#228;lld</h1>
        <a href="/employees" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600 transition-colors">&larr; Tillbaka</a>
    </div>

    <div class="rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-md">
        <form method="POST" action="/employees" class="space-y-5">
            <?= \App\Core\Csrf::field() ?>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">F&#246;rnamn <span class="text-red-500">*</span></label>
                    <input id="first_name" name="first_name" type="text" required
                           value="<?= htmlspecialchars($old['first_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border <?= isset($errors['first_name']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <?php if (isset($errors['first_name'])): ?>
                        <p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['first_name'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Efternamn <span class="text-red-500">*</span></label>
                    <input id="last_name" name="last_name" type="text" required
                           value="<?= htmlspecialchars($old['last_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border <?= isset($errors['last_name']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <?php if (isset($errors['last_name'])): ?>
                        <p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['last_name'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <label for="employee_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Anst&#228;llningsnummer</label>
                <input id="employee_number" name="employee_number" type="text"
                       value="<?= htmlspecialchars($old['employee_number'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>

            <div>
                <label for="department_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Avdelning</label>
                <select id="department_id" name="department_id"
                        class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">&#8212; V&#228;lj avdelning &#8212;</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['id'] ?>" <?= ($old['department_id'] ?? '') == $d['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($d['name'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="position" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Befattning</label>
                <input id="position" name="position" type="text"
                       value="<?= htmlspecialchars($old['position'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Telefon</label>
                    <input id="phone" name="phone" type="text"
                           value="<?= htmlspecialchars($old['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">E-post</label>
                    <input id="email" name="email" type="email"
                           value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border <?= isset($errors['email']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <?php if (isset($errors['email'])): ?>
                        <p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="hire_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Anst&#228;llningsdatum</label>
                    <input id="hire_date" name="hire_date" type="date"
                           value="<?= htmlspecialchars($old['hire_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Slutdatum</label>
                    <input id="end_date" name="end_date" type="date"
                           value="<?= htmlspecialchars($old['end_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="employment_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Anst&#228;llningsform</label>
                    <select id="employment_type" name="employment_type"
                            class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <option value="">&#8212; V&#228;lj &#8212;</option>
                        <option value="full_time" <?= ($old['employment_type'] ?? '') === 'full_time' ? 'selected' : '' ?>>Heltid</option>
                        <option value="part_time" <?= ($old['employment_type'] ?? '') === 'part_time' ? 'selected' : '' ?>>Deltid</option>
                        <option value="consultant" <?= ($old['employment_type'] ?? '') === 'consultant' ? 'selected' : '' ?>>Konsult</option>
                        <option value="intern" <?= ($old['employment_type'] ?? '') === 'intern' ? 'selected' : '' ?>>Praktikant</option>
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select id="status" name="status"
                            class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <option value="active" <?= ($old['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Aktiv</option>
                        <option value="on_leave" <?= ($old['status'] ?? '') === 'on_leave' ? 'selected' : '' ?>>Tj&#228;nstledig</option>
                        <option value="terminated" <?= ($old['status'] ?? '') === 'terminated' ? 'selected' : '' ?>>Avslutad</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="salary" class="block text-sm font-medium text-gray-700 dark:text-gray-300">M&#229;nadsl&#246;n (kr)</label>
                <input id="salary" name="salary" type="number" step="0.01" min="0"
                       value="<?= htmlspecialchars($old['salary'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>

            <div>
                <label for="manager_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Chef</label>
                <select id="manager_id" name="manager_id"
                        class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">&#8212; Ingen chef &#8212;</option>
                    <?php foreach ($managers as $mgr): ?>
                        <option value="<?= $mgr['id'] ?>" <?= ($old['manager_id'] ?? '') == $mgr['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($mgr['last_name'] . ', ' . $mgr['first_name'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">N&#246;dkontakt (namn)</label>
                    <input id="emergency_contact_name" name="emergency_contact_name" type="text"
                           value="<?= htmlspecialchars($old['emergency_contact_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">N&#246;dkontakt (telefon)</label>
                    <input id="emergency_contact_phone" name="emergency_contact_phone" type="text"
                           value="<?= htmlspecialchars($old['emergency_contact_phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Anteckningar</label>
                <textarea id="notes" name="notes" rows="3"
                          class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"><?= htmlspecialchars($old['notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <a href="/employees" class="rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Avbryt</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">
                    Skapa anst&#228;lld
                </button>
            </div>
        </form>
    </div>
</div>
