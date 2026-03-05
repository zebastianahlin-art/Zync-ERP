<div class="space-y-8">

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Site-inställningar</h1>
        <a href="/admin" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">← Admin</a>
    </div>

    <form method="POST" action="/admin/site" class="space-y-8">
        <?= \App\Core\Csrf::field() ?>

        <!-- Företagsinformation -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md space-y-5">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-3">Företagsinformation</h2>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Företagsnamn</label>
                    <input type="text" name="company_name" value="<?= htmlspecialchars((string) ($site['company_name'] ?? 'ZYNC ERP'), ENT_QUOTES, 'UTF-8') ?>"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Logotyp-URL</label>
                    <input type="text" name="company_logo" value="<?= htmlspecialchars((string) ($site['company_logo'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                           placeholder="https://..."
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Primärfärg (hex)</label>
                    <div class="flex gap-2 items-center">
                        <input type="color" name="primary_color" value="<?= htmlspecialchars((string) ($site['primary_color'] ?? '#4f46e5'), ENT_QUOTES, 'UTF-8') ?>"
                               class="h-9 w-16 rounded border border-gray-300 dark:border-gray-600 cursor-pointer">
                        <input type="text" value="<?= htmlspecialchars((string) ($site['primary_color'] ?? '#4f46e5'), ENT_QUOTES, 'UTF-8') ?>"
                               class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" readonly>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tidszon</label>
                    <input type="text" name="timezone" value="<?= htmlspecialchars((string) ($site['timezone'] ?? 'Europe/Stockholm'), ENT_QUOTES, 'UTF-8') ?>"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Datumformat</label>
                    <input type="text" name="date_format" value="<?= htmlspecialchars((string) ($site['date_format'] ?? 'Y-m-d'), ENT_QUOTES, 'UTF-8') ?>"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Valuta</label>
                    <input type="text" name="currency" value="<?= htmlspecialchars((string) ($site['currency'] ?? 'SEK'), ENT_QUOTES, 'UTF-8') ?>"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Språk</label>
                    <select name="language" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="sv" <?= ($site['language'] ?? 'sv') === 'sv' ? 'selected' : '' ?>>Svenska</option>
                        <option value="en" <?= ($site['language'] ?? 'sv') === 'en' ? 'selected' : '' ?>>English</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sidfottext</label>
                    <textarea name="footer_text" rows="2"
                              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"><?= htmlspecialchars((string) ($site['footer_text'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
            </div>
        </div>

        <!-- E-postinställningar (SMTP) -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md space-y-5">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-3">E-postinställningar (SMTP)</h2>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SMTP-server</label>
                    <input type="text" name="smtp_host" value="<?= htmlspecialchars((string) ($site['smtp_host'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                           placeholder="smtp.example.com"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Port</label>
                    <input type="number" name="smtp_port" value="<?= htmlspecialchars((string) ($site['smtp_port'] ?? '587'), ENT_QUOTES, 'UTF-8') ?>"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Användarnamn</label>
                    <input type="text" name="smtp_user" value="<?= htmlspecialchars((string) ($site['smtp_user'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lösenord</label>
                    <input type="password" name="smtp_password" value="<?= htmlspecialchars((string) ($site['smtp_password'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                           placeholder="Lämna tomt för att behålla"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kryptering</label>
                    <select name="smtp_encryption" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="tls" <?= ($site['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS</option>
                        <option value="ssl" <?= ($site['smtp_encryption'] ?? 'tls') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                        <option value="none" <?= ($site['smtp_encryption'] ?? 'tls') === 'none' ? 'selected' : '' ?>>Ingen</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">
                Spara site-inställningar
            </button>
        </div>
    </form>
</div>
