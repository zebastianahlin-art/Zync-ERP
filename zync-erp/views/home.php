<div class="flex min-h-[60vh] flex-col items-center justify-center text-center" x-data>

    <div class="mb-6">
        <span class="inline-flex items-center rounded-full bg-indigo-100 px-4 py-1.5 text-sm font-medium text-indigo-700">
            Phase 1 · Scaffolding
        </span>
    </div>

    <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl">
        <?= htmlspecialchars($title ?? 'Welcome to ZYNC ERP!', ENT_QUOTES, 'UTF-8') ?>
    </h1>

    <p class="mt-4 max-w-xl text-lg text-gray-500">
        The modular, lightweight ERP built for modern businesses.
        Start configuring your modules below.
    </p>

    <div class="mt-10 flex flex-wrap justify-center gap-4">
        <a href="/"
           class="rounded-lg bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">
            Get Started
        </a>
        <a href="https://github.com/zebastianahlin-art/Zync-ERP"
           target="_blank" rel="noopener noreferrer"
           class="rounded-lg border border-gray-300 px-6 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
            View on GitHub
        </a>
    </div>

    <!-- Quick stats placeholder -->
    <div class="mt-16 grid grid-cols-1 gap-6 sm:grid-cols-3 w-full max-w-3xl">
        <?php foreach ([
            ['label' => 'Modules', 'value' => '—'],
            ['label' => 'Users',   'value' => '—'],
            ['label' => 'Records', 'value' => '—'],
        ] as $stat): ?>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm text-center">
            <p class="text-3xl font-bold text-indigo-600"><?= htmlspecialchars($stat['value'], ENT_QUOTES, 'UTF-8') ?></p>
            <p class="mt-1 text-sm text-gray-500"><?= htmlspecialchars($stat['label'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <?php endforeach; ?>
    </div>

</div>
