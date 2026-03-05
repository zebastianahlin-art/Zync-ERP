<?php $preselect = $_GET['tenant_id'] ?? ''; ?>
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Ny faktura</h1>
        <a href="/saas-admin/invoices" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">← Fakturor</a>
    </div>

    <form method="POST" action="/saas-admin/invoices" class="space-y-6">
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
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kund *</label>
                    <select name="tenant_id" required
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent <?= isset($errors['tenant_id']) ? 'border-red-400' : '' ?>">
                        <option value="">Välj kund…</option>
                        <?php foreach ($tenants as $t): ?>
                            <option value="<?= (int) $t['id'] ?>"
                                <?= ((string)($old['tenant_id'] ?? $preselect)) === (string) $t['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars((string) $t['company_name'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Periodens start *</label>
                    <input type="date" name="period_start" value="<?= htmlspecialchars((string) ($old['period_start'] ?? date('Y-m-01')), ENT_QUOTES, 'UTF-8') ?>" required
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Periodens slut *</label>
                    <input type="date" name="period_end" value="<?= htmlspecialchars((string) ($old['period_end'] ?? date('Y-m-t')), ENT_QUOTES, 'UTF-8') ?>" required
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Belopp (ex. moms, kr)</label>
                    <input type="number" name="amount" step="0.01" value="<?= htmlspecialchars((string) ($old['amount'] ?? '0'), ENT_QUOTES, 'UTF-8') ?>" min="0"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Moms (kr)</label>
                    <input type="number" name="vat" step="0.01" value="<?= htmlspecialchars((string) ($old['vat'] ?? '0'), ENT_QUOTES, 'UTF-8') ?>" min="0"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Totalt (inkl. moms, kr)</label>
                    <input type="number" name="total" step="0.01" value="<?= htmlspecialchars((string) ($old['total'] ?? '0'), ENT_QUOTES, 'UTF-8') ?>" min="0"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Förfallodatum *</label>
                    <input type="date" name="due_date" value="<?= htmlspecialchars((string) ($old['due_date'] ?? date('Y-m-d', strtotime('+30 days'))), ENT_QUOTES, 'UTF-8') ?>" required
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Anteckningar</label>
                    <textarea name="notes" rows="3"
                              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"><?= htmlspecialchars((string) ($old['notes'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="/saas-admin/invoices" class="rounded-lg px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Avbryt</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">Skapa faktura</button>
        </div>
    </form>
</div>
