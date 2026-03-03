<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100"><?= htmlspecialchars($audit['title'], ENT_QUOTES, 'UTF-8') ?></h1>
        <a href="/safety/audits" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">&larr; Tillbaka</a>
    </div>

    <div class="rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-md space-y-4">
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><span class="font-medium text-gray-600 dark:text-gray-400">Status:</span> <span class="text-gray-900 dark:text-gray-100"><?= htmlspecialchars($audit['status'] ?? '–', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="font-medium text-gray-600 dark:text-gray-400">Planerat datum:</span> <span class="text-gray-900 dark:text-gray-100"><?= htmlspecialchars($audit['scheduled_date'] ?? '–', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="font-medium text-gray-600 dark:text-gray-400">Ansvarig:</span> <span class="text-gray-900 dark:text-gray-100"><?= htmlspecialchars($audit['assigned_name'] ?? '–', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="font-medium text-gray-600 dark:text-gray-400">Mall:</span> <span class="text-gray-900 dark:text-gray-100"><?= htmlspecialchars($audit['template_name'] ?? '–', ENT_QUOTES, 'UTF-8') ?></span></div>
            <?php if (!empty($audit['location'])): ?>
            <div class="col-span-2"><span class="font-medium text-gray-600 dark:text-gray-400">Plats:</span> <span class="text-gray-900 dark:text-gray-100"><?= htmlspecialchars($audit['location'], ENT_QUOTES, 'UTF-8') ?></span></div>
            <?php endif; ?>
        </div>
        <?php if (!empty($audit['description'])): ?>
            <div><p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Beskrivning</p><p class="text-sm text-gray-900 dark:text-gray-100"><?= nl2br(htmlspecialchars($audit['description'], ENT_QUOTES, 'UTF-8')) ?></p></div>
        <?php endif; ?>
        <?php if (!empty($audit['notes'])): ?>
            <div><p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Anteckningar</p><p class="text-sm text-gray-900 dark:text-gray-100"><?= nl2br(htmlspecialchars($audit['notes'], ENT_QUOTES, 'UTF-8')) ?></p></div>
        <?php endif; ?>

        <!-- Update status -->
        <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Uppdatera status</p>
            <form method="POST" action="/safety/audits/<?= (int) $audit['id'] ?>/status" class="flex items-center space-x-3">
                <input type="hidden" name="_token" value="<?= \App\Core\Csrf::token() ?>">
                <select name="status" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <?php foreach (['planned'=>'Planerad','in_progress'=>'Pågår','completed'=>'Klar','cancelled'=>'Avbruten'] as $v=>$l): ?>
                        <option value="<?= $v ?>" <?= ($audit['status'] ?? 'planned') === $v ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">Spara status</button>
            </form>
        </div>

        <div class="flex items-center space-x-3">
            <a href="/safety/audits/<?= (int) $audit['id'] ?>/edit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">Redigera</a>
            <form method="POST" action="/safety/audits/<?= (int) $audit['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort denna audit?')">
                <input type="hidden" name="_token" value="<?= \App\Core\Csrf::token() ?>">
                <button type="submit" class="rounded-lg border border-red-300 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">Ta bort</button>
            </form>
        </div>
    </div>

    <?php if (!empty($items)): ?>
    <div class="rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-md space-y-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Checklistepunkter</h2>
        <form method="POST" action="/safety/audits/<?= (int) $audit['id'] ?>/responses" class="space-y-4">
            <input type="hidden" name="_token" value="<?= \App\Core\Csrf::token() ?>">
            <?php foreach ($items as $item): ?>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2"><?= htmlspecialchars($item['question'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php if ($item['response_type'] === 'yes_no'): ?>
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center space-x-2 text-sm text-gray-700 dark:text-gray-300"><input type="radio" name="responses[<?= (int) $item['id'] ?>]" value="yes" class="text-indigo-600"> <span>Ja</span></label>
                            <label class="flex items-center space-x-2 text-sm text-gray-700 dark:text-gray-300"><input type="radio" name="responses[<?= (int) $item['id'] ?>]" value="no" class="text-indigo-600"> <span>Nej</span></label>
                            <label class="flex items-center space-x-2 text-sm text-gray-700 dark:text-gray-300"><input type="radio" name="responses[<?= (int) $item['id'] ?>]" value="na" class="text-indigo-600"> <span>Ej tillämpligt</span></label>
                        </div>
                    <?php else: ?>
                        <textarea name="responses[<?= (int) $item['id'] ?>]" rows="2" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"></textarea>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <div class="flex justify-end">
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">Spara svar</button>
            </div>
        </form>
    </div>
    <?php endif; ?>
</div>
