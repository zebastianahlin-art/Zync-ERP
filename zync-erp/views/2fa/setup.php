<div class="mx-auto max-w-lg">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Tvåfaktorsautentisering</h1>

    <?php if ($error !== null): ?>
        <div class="mb-4 rounded-lg bg-red-50 dark:bg-red-900/30 px-4 py-3 text-sm text-red-700 dark:text-red-400">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?php if ($success !== null): ?>
        <div class="mb-4 rounded-lg bg-green-50 dark:bg-green-900/30 px-4 py-3 text-sm text-green-700 dark:text-green-400">
            <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?php if ($enabled): ?>
        <!-- 2FA is already enabled -->
        <div class="rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 p-6 mb-6">
            <div class="flex items-center gap-3 mb-3">
                <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h2 class="text-lg font-semibold text-green-800 dark:text-green-300">Aktiverad</h2>
            </div>
            <p class="text-sm text-green-700 dark:text-green-400">
                Tvåfaktorsautentisering är aktiverad för ditt konto.
            </p>
        </div>

        <!-- Disable 2FA -->
        <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Inaktivera tvåfaktorsautentisering</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Ange din nuvarande autentiseringskod för att inaktivera tvåfaktorsautentisering.
            </p>
            <form method="POST" action="/2fa/disable" class="space-y-4">
                <?= App\Core\Csrf::field() ?>
                <div>
                    <label for="disable_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sexsiffrig kod</label>
                    <input
                        id="disable_code"
                        name="code"
                        type="text"
                        inputmode="numeric"
                        pattern="[0-9]{6}"
                        maxlength="6"
                        required
                        autofocus
                        placeholder="000000"
                        class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                    >
                </div>
                <button
                    type="submit"
                    class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-red-700 transition-colors"
                >
                    Inaktivera 2FA
                </button>
            </form>
        </div>

    <?php else: ?>
        <!-- 2FA is not enabled — show setup instructions -->
        <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Steg 1 – Skanna QR-kod</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Öppna din autentiseringsapp (t.ex. Google Authenticator, Authy eller Microsoft Authenticator)
                och skanna QR-koden nedan. Om din app inte har kamera kan du ange den manuella nyckeln.
            </p>

            <!-- QR code rendered via an external service using the otpauth URI -->
            <div class="flex justify-center mb-4">
                <img
                    src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode($qrCodeUri) ?>"
                    alt="QR-kod för tvåfaktorsautentisering"
                    width="200"
                    height="200"
                    class="rounded-lg border border-gray-200 dark:border-gray-600"
                >
            </div>

            <div class="rounded-lg bg-gray-50 dark:bg-gray-700/50 px-4 py-3">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Manuell nyckel</p>
                <code class="text-sm font-mono text-gray-900 dark:text-gray-100 tracking-widest break-all">
                    <?= htmlspecialchars(chunk_split($secret, 4, ' '), ENT_QUOTES, 'UTF-8') ?>
                </code>
            </div>
        </div>

        <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Steg 2 – Verifiera koden</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Ange den sexsiffriga koden från din autentiseringsapp för att bekräfta att allt fungerar.
            </p>
            <form method="POST" action="/2fa/enable" class="space-y-4">
                <?= App\Core\Csrf::field() ?>
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sexsiffrig kod</label>
                    <input
                        id="code"
                        name="code"
                        type="text"
                        inputmode="numeric"
                        pattern="[0-9]{6}"
                        maxlength="6"
                        required
                        autofocus
                        placeholder="000000"
                        class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                    >
                </div>
                <button
                    type="submit"
                    class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors"
                >
                    Aktivera tvåfaktorsautentisering
                </button>
            </form>
        </div>
    <?php endif; ?>
</div>
