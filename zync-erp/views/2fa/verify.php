<div class="flex min-h-[70vh] items-center justify-center">

    <div class="w-full max-w-sm rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-md border border-gray-200 dark:border-gray-700">

        <div class="mb-6 text-center">
            <span class="text-2xl font-bold tracking-tight text-indigo-600 dark:text-indigo-400">ZYNC ERP</span>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Verifiera din inloggning</p>
        </div>

        <?php if ($error !== null): ?>
            <div class="mb-4 rounded-lg bg-red-50 dark:bg-red-900/30 px-4 py-3 text-sm text-red-700 dark:text-red-400">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
            Ange den sexsiffriga koden från din autentiseringsapp.
        </p>

        <form method="POST" action="/2fa/verify" class="space-y-4">

            <?= App\Core\Csrf::field() ?>

            <div>
                <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Autentiseringskod</label>
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
                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 text-center tracking-widest text-lg"
                >
            </div>

            <button
                type="submit"
                class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors"
            >
                Verifiera
            </button>

        </form>

        <div class="mt-4 text-center">
            <a href="/logout" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                Avbryt och logga ut
            </a>
        </div>

    </div>

</div>
