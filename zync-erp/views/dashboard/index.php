<div class="mx-auto max-w-2xl py-10">

    <div class="rounded-2xl bg-white p-8 shadow-md">

        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                <?= htmlspecialchars($title ?? 'Dashboard', ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <a href="/logout"
               class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                Logout
            </a>
        </div>

        <p class="text-sm text-gray-500">
            Logged in as user <span class="font-semibold text-indigo-600">#<?= htmlspecialchars((string) $userId, ENT_QUOTES, 'UTF-8') ?></span>.
        </p>

        <div class="mt-6">
            <a href="/customers"
               class="inline-block rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">
                Manage Customers
            </a>
        </div>

    </div>

</div>
