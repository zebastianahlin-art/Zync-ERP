<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Redigera avtalsmall</h1>
        <a href="/purchasing/agreement-templates/<?= (int)$template['id'] ?>"
           class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition">
            ← Tillbaka
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <form method="POST" action="/purchasing/agreement-templates/<?= (int)$template['id'] ?>" class="p-6 space-y-6">
            <?= \App\Core\Csrf::field() ?>

            <!-- Namn -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Namn <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" required
                       value="<?= htmlspecialchars($template['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                       placeholder="Mallnamn">
            </div>

            <!-- Beskrivning -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Beskrivning</label>
                <textarea name="description" rows="3"
                          class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                          placeholder="Kort beskrivning av mallen..."><?= htmlspecialchars($template['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <!-- Leverantör -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Leverantör</label>
                <select name="supplier_id"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    <option value="">— Ingen specifik —</option>
                    <?php foreach ($suppliers as $supplier): ?>
                        <option value="<?= (int)$supplier['id'] ?>"
                            <?= (int)($template['supplier_id'] ?? 0) === (int)$supplier['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($supplier['name'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Standardvillkor -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Standardvillkor / Generella villkor
                </label>
                <textarea name="default_terms" rows="4"
                          class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                          placeholder="Ange generella avtalsvillkor..."><?= htmlspecialchars($template['default_terms'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Betalningsvillkor -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Betalningsvillkor</label>
                    <input type="text" name="default_payment_terms"
                           value="<?= htmlspecialchars($template['default_payment_terms'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                           placeholder="t.ex. 30 dagar netto">
                </div>

                <!-- Leveransvillkor -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Leveransvillkor</label>
                    <input type="text" name="default_delivery_terms"
                           value="<?= htmlspecialchars($template['default_delivery_terms'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                           placeholder="t.ex. DDP">
                </div>
            </div>

            <!-- Aktiv -->
            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       <?= !empty($template['is_active']) ? 'checked' : '' ?>
                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <label for="is_active" class="text-sm font-medium text-gray-700 dark:text-gray-300">Aktiv</label>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3 pt-2 border-t border-gray-200 dark:border-gray-700">
                <button type="submit"
                        class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
                    Spara mall
                </button>
                <a href="/purchasing/agreement-templates/<?= (int)$template['id'] ?>"
                   class="rounded-lg px-5 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    Avbryt
                </a>
            </div>
        </form>
    </div>
</div>
