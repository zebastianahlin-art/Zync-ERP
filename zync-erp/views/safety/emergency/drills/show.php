<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100"><?= htmlspecialchars($drill['title'], ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Övningsnr: <span class="font-mono text-indigo-600 dark:text-indigo-400"><?= htmlspecialchars($drill['drill_number'], ENT_QUOTES, 'UTF-8') ?></span></p>
        </div>
        <a href="/safety/emergency/drills/<?= (int) $drill['id'] ?>/edit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">Redigera</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md p-6 space-y-3 text-sm">
            <h2 class="font-semibold text-gray-900 dark:text-gray-100 text-base mb-2">Övningsdetaljer</h2>
            <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Typ:</span> <span class="font-medium text-gray-900 dark:text-gray-100"><?= htmlspecialchars($drill['drill_type'], ENT_QUOTES, 'UTF-8') ?></span></div>
            <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Status:</span>
                <?php
                    $sc = ['planned' => 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400', 'in_progress' => 'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-400', 'completed' => 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400', 'cancelled' => 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400'];
                ?>
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium <?= $sc[$drill['status']] ?? 'bg-gray-100 text-gray-700' ?>"><?= htmlspecialchars($drill['status'], ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Plats:</span> <span><?= htmlspecialchars($drill['location'] ?? '–', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Avdelning:</span> <span><?= htmlspecialchars($drill['department_name'] ?? '–', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Koordinator:</span> <span><?= htmlspecialchars($drill['coordinator_name'] ?? '–', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Mall:</span> <span><?= htmlspecialchars($drill['template_name'] ?? '–', ENT_QUOTES, 'UTF-8') ?></span></div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md p-6 space-y-3 text-sm">
            <h2 class="font-semibold text-gray-900 dark:text-gray-100 text-base mb-2">Datum &amp; Resultat</h2>
            <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Planerat datum:</span> <span><?= htmlspecialchars($drill['scheduled_date'], ENT_QUOTES, 'UTF-8') ?></span></div>
            <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Genomfört datum:</span> <span><?= htmlspecialchars($drill['executed_date'] ?? '–', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Varaktighet (min):</span> <span><?= $drill['duration_minutes'] !== null ? htmlspecialchars((string) $drill['duration_minutes'], ENT_QUOTES, 'UTF-8') : '–' ?></span></div>
            <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Deltagare:</span> <span><?= $drill['participants'] !== null ? htmlspecialchars((string) $drill['participants'], ENT_QUOTES, 'UTF-8') : '–' ?></span></div>
            <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Poäng:</span> <span><?= $drill['score'] !== null ? htmlspecialchars((string) $drill['score'], ENT_QUOTES, 'UTF-8') : '–' ?></span></div>
            <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Nästa övning:</span> <span><?= htmlspecialchars($drill['next_drill_date'] ?? '–', ENT_QUOTES, 'UTF-8') ?></span></div>
        </div>
    </div>

    <?php if (!empty($drill['description'])): ?>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md p-6">
        <h2 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Beskrivning</h2>
        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap"><?= htmlspecialchars($drill['description'], ENT_QUOTES, 'UTF-8') ?></p>
    </div>
    <?php endif; ?>

    <?php if (!empty($drill['evaluation'])): ?>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md p-6">
        <h2 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Utvärdering</h2>
        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap"><?= htmlspecialchars($drill['evaluation'], ENT_QUOTES, 'UTF-8') ?></p>
    </div>
    <?php endif; ?>

    <?php if (!empty($drill['improvements'])): ?>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md p-6">
        <h2 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Förbättringsförslag</h2>
        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap"><?= htmlspecialchars($drill['improvements'], ENT_QUOTES, 'UTF-8') ?></p>
    </div>
    <?php endif; ?>

    <div class="flex gap-4">
        <a href="/safety/emergency/drills" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">&larr; Tillbaka till övningar</a>
        <form method="POST" action="/safety/emergency/drills/<?= (int) $drill['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort denna övning?')">
            <?= \App\Core\Csrf::field() ?>
            <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:underline bg-transparent border-0 p-0 cursor-pointer">Ta bort</button>
        </form>
    </div>
</div>
