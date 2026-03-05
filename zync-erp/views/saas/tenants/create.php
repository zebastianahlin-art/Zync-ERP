<div class="space-y-6">

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Ny kund</h1>
        <a href="/saas-admin/tenants" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">← Kunder</a>
    </div>

    <form method="POST" action="/saas-admin/tenants" class="space-y-6">
        <?= \App\Core\Csrf::field() ?>

        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md space-y-5">

            <?php if (!empty($errors)): ?>
                <div class="rounded-lg bg-red-50 dark:bg-red-900/30 p-4 text-sm text-red-700 dark:text-red-400">
                    <ul class="list-disc list-inside space-y-1">
                        <?php foreach ($errors as $err): ?>
                            <li><?= htmlspecialchars((string) $err, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Företagsnamn *</label>
                    <input type="text" name="company_name" value="<?= htmlspecialchars((string) ($old['company_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent <?= isset($errors['company_name']) ? 'border-red-400 dark:border-red-500' : '' ?>">
                    <?php if (isset($errors['company_name'])): ?><p class="mt-1 text-xs text-red-500"><?= htmlspecialchars($errors['company_name'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Organisationsnummer</label>
                    <input type="text" name="org_number" value="<?= htmlspecialchars((string) ($old['org_number'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                           placeholder="556123-4567"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kontaktperson</label>
                    <input type="text" name="contact_name" value="<?= htmlspecialchars((string) ($old['contact_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">E-post *</label>
                    <input type="email" name="contact_email" value="<?= htmlspecialchars((string) ($old['contact_email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent <?= isset($errors['contact_email']) ? 'border-red-400 dark:border-red-500' : '' ?>">
                    <?php if (isset($errors['contact_email'])): ?><p class="mt-1 text-xs text-red-500"><?= htmlspecialchars($errors['contact_email'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Telefon</label>
                    <input type="text" name="contact_phone" value="<?= htmlspecialchars((string) ($old['contact_phone'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subdomän</label>
                    <input type="text" name="subdomain" value="<?= htmlspecialchars((string) ($old['subdomain'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                           placeholder="foretaget"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <p class="mt-1 text-xs text-gray-400">foretaget.zync-erp.se</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Abonnemang</label>
                    <select name="plan" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="starter" <?= ($old['plan'] ?? 'starter') === 'starter' ? 'selected' : '' ?>>Starter</option>
                        <option value="professional" <?= ($old['plan'] ?? '') === 'professional' ? 'selected' : '' ?>>Professional</option>
                        <option value="enterprise" <?= ($old['plan'] ?? '') === 'enterprise' ? 'selected' : '' ?>>Enterprise</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Max antal användare</label>
                    <input type="number" name="max_users" value="<?= (int) ($old['max_users'] ?? 10) ?>" min="1"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="trial" <?= ($old['status'] ?? 'trial') === 'trial' ? 'selected' : '' ?>>Trial</option>
                        <option value="active" <?= ($old['status'] ?? '') === 'active' ? 'selected' : '' ?>>Aktiv</option>
                        <option value="suspended" <?= ($old['status'] ?? '') === 'suspended' ? 'selected' : '' ?>>Pausad</option>
                        <option value="cancelled" <?= ($old['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Avslutad</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Trial avslutas</label>
                    <input type="date" name="trial_ends_at" value="<?= htmlspecialchars((string) ($old['trial_ends_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Adress</label>
                    <textarea name="address" rows="2"
                              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"><?= htmlspecialchars((string) ($old['address'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Anteckningar</label>
                    <textarea name="notes" rows="3"
                              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"><?= htmlspecialchars((string) ($old['notes'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="/saas-admin/tenants" class="rounded-lg px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Avbryt</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">Skapa kund</button>
        </div>
    </form>
</div>
