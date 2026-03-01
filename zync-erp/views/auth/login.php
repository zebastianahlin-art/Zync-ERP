<div class="flex min-h-[70vh] items-center justify-center">

    <div class="w-full max-w-sm rounded-2xl bg-white p-8 shadow-md">

        <div class="mb-6 text-center">
            <span class="text-2xl font-bold tracking-tight text-indigo-600">ZYNC ERP</span>
            <p class="mt-1 text-sm text-gray-500">Sign in to your account</p>
        </div>

        <?php if ($error !== null): ?>
            <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/login" class="space-y-4">

            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input
                    id="username"
                    name="username"
                    type="text"
                    required
                    autofocus
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                >
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                >
            </div>

            <button
                type="submit"
                class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors"
            >
                Sign in
            </button>

        </form>

    </div>

</div>
