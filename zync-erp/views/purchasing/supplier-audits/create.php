<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Ny leverantörsaudit</h1>
        <a href="/purchasing/supplier-audits"
           class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition">
            ← Tillbaka
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <form method="POST" action="/purchasing/supplier-audits" class="p-6 space-y-6">
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
                            <option value="<?= (int)$supplier['id'] ?>">
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
                           value="<?= date('Y-m-d') ?>"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                <!-- Utförare -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Utförare</label>
                    <select name="auditor_id"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="">— Välj utförare —</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= (int)$user['id'] ?>">
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
                        <option value="planned">Planerad</option>
                        <option value="in_progress">Pågående</option>
                        <option value="completed">Slutförd</option>
                    </select>
                </div>
            </div>

            <!-- Poängfält -->
            <div>
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Poängbedömning (1–5)</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Leveranspoäng</label>
                        <input type="number" name="delivery_score" min="1" max="5" step="1"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                               placeholder="1–5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kvalitetspoäng</label>
                        <input type="number" name="quality_score" min="1" max="5" step="1"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                               placeholder="1–5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prispoäng</label>
                        <input type="number" name="price_score" min="1" max="5" step="1"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                               placeholder="1–5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kommunikationspoäng</label>
                        <input type="number" name="communication_score" min="1" max="5" step="1"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                               placeholder="1–5">
                    </div>
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
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
            </div>

            <!-- Noteringar -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Noteringar</label>
                <textarea name="notes" rows="4"
                          class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                          placeholder="Eventuella noteringar eller kommentarer..."></textarea>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3 pt-2 border-t border-gray-200 dark:border-gray-700">
                <button type="submit"
                        class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
                    Spara
                </button>
                <a href="/purchasing/supplier-audits"
                   class="rounded-lg px-5 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    Avbryt
                </a>
            </div>
        </form>
    </div>
</div>
