<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Redigera leverantörsaudit</h1>
        <a href="/purchasing/supplier-audits/<?= (int)$audit['id'] ?>"
           class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition">
            ← Tillbaka
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <form method="POST" action="/purchasing/supplier-audits/<?= (int)$audit['id'] ?>" class="p-6 space-y-6">
            <?= \App\Core\Csrf::field() ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Leverantör -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Leverantör <span class="text-red-500">*</span>
                    </label>
                    <select name="supplier_id" required
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="">— Välj leverantör —</option>
                        <?php foreach ($suppliers as $supplier): ?>
                            <option value="<?= (int)$supplier['id'] ?>"
                                <?= (int)($audit['supplier_id'] ?? 0) === (int)$supplier['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($supplier['name'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Revisionsdatum -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Revisionsdatum <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="audit_date" required
                           value="<?= htmlspecialchars($audit['audit_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                <!-- Utförare -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Utförare</label>
                    <select name="auditor_id"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="">— Välj utförare —</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= (int)$user['id'] ?>"
                                <?= (int)($audit['auditor_id'] ?? 0) === (int)$user['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($user['name'] ?? ($user['first_name'] . ' ' . $user['last_name']), ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="status"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="planned"     <?= ($audit['status'] ?? '') === 'planned'     ? 'selected' : '' ?>>Planerad</option>
                        <option value="in_progress" <?= ($audit['status'] ?? '') === 'in_progress' ? 'selected' : '' ?>>Pågående</option>
                        <option value="completed"   <?= ($audit['status'] ?? '') === 'completed'   ? 'selected' : '' ?>>Slutförd</option>
                    </select>
                </div>
            </div>

            <!-- Poängfält -->
            <div>
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Poängbedömning (1–5)</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <?php
                    $scoreFields = [
                        'delivery_score'      => 'Leveranspoäng',
                        'quality_score'       => 'Kvalitetspoäng',
                        'price_score'         => 'Prispoäng',
                        'communication_score' => 'Kommunikationspoäng',
                    ];
                    foreach ($scoreFields as $field => $fieldLabel):
                    ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            <?= $fieldLabel ?>
                        </label>
                        <input type="number" name="<?= $field ?>" min="1" max="5" step="1"
                               value="<?= htmlspecialchars((string)($audit[$field] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                               placeholder="1–5">
                    </div>
                    <?php endforeach; ?>
                </div>
                <p class="mt-2 text-xs text-gray-400 dark:text-gray-500">
                    Totalpoäng beräknas automatiskt som medelvärde av ifyllda poäng.
                </p>
            </div>

            <!-- Nästa revisionsdatum -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nästa revisionsdatum</label>
                    <input type="date" name="next_audit_date"
                           value="<?= htmlspecialchars($audit['next_audit_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
            </div>

            <!-- Noteringar -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Noteringar</label>
                <textarea name="notes" rows="4"
                          class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                          placeholder="Eventuella noteringar eller kommentarer..."><?= htmlspecialchars($audit['notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3 pt-2 border-t border-gray-200 dark:border-gray-700">
                <button type="submit"
                        class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
                    Spara
                </button>
                <a href="/purchasing/supplier-audits/<?= (int)$audit['id'] ?>"
                   class="rounded-lg px-5 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    Avbryt
                </a>
            </div>
        </form>
    </div>
</div>
