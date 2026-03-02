<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="/purchasing/agreements" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($agreement['agreement_number']) ?></h1>
            <?= agrShowStatus($agreement['status']) ?>
            <?= agrShowType($agreement['agreement_type']) ?>
        </div>
        <div class="flex gap-2">
            <a href="/purchasing/agreements/<?= $agreement['id'] ?>/edit" class="px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">Redigera</a>
        </div>
    </div>

    <?php if (!empty($success)): ?>
        <div class="rounded-lg bg-green-50 dark:bg-green-900/30 p-4 text-green-800 dark:text-green-200"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4"><?= htmlspecialchars($agreement['title']) ?></h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
            <div><span class="text-gray-500 dark:text-gray-400">Leverantör:</span><br><span class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($agreement['supplier_name'] ?? '') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Ansvarig:</span><br><span class="text-gray-900 dark:text-white"><?= htmlspecialchars($agreement['responsible_name'] ?? '—') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Avtalsvärde:</span><br><span class="font-semibold text-gray-900 dark:text-white"><?= $agreement['value'] ? number_format((float)$agreement['value'], 2, ',', ' ') . ' ' . $agreement['currency'] : '—' ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Startdatum:</span><br><span class="text-gray-900 dark:text-white"><?= $agreement['start_date'] ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Slutdatum:</span><br><span class="text-gray-900 dark:text-white <?= ($agreement['end_date'] && $agreement['end_date'] < date('Y-m-d')) ? 'text-red-600 font-semibold' : '' ?>"><?= $agreement['end_date'] ?: 'Tillsvidare' ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Skapad av:</span><br><span class="text-gray-900 dark:text-white"><?= htmlspecialchars($agreement['created_by_name'] ?? '—') ?></span></div>
        </div>

        <?php if (!empty($agreement['description'])): ?>
        <div class="mt-6">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Beskrivning</h3>
            <p class="text-sm text-gray-700 dark:text-gray-300"><?= nl2br(htmlspecialchars($agreement['description'])) ?></p>
        </div>
        <?php endif; ?>

        <?php if (!empty($agreement['terms'])): ?>
        <div class="mt-4">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Villkor / Klausuler</h3>
            <p class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg"><?= nl2br(htmlspecialchars($agreement['terms'])) ?></p>
        </div>
        <?php endif; ?>

        <?php if (!empty($agreement['file_path'])): ?>
        <div class="mt-4">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Bifogad fil</h3>
            <a href="<?= htmlspecialchars($agreement['file_path']) ?>" target="_blank" class="inline-flex items-center gap-2 text-blue-600 hover:underline text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Ladda ner dokument
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
function agrShowStatus(string $s): string {
    $m = ['draft'=>['Utkast','bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'],'active'=>['Aktivt','bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'],'expired'=>['Utgånget','bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'],'terminated'=>['Avslutat','bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400']];
    return '<span class="inline-flex px-2 py-1 rounded-full text-xs font-medium '.($m[$s][1]??'bg-gray-100 text-gray-700').'">'.($m[$s][0]??$s).'</span>';
}
function agrShowType(string $t): string {
    $m = ['framework'=>'Ramavtal','project'=>'Projekt','service'=>'Service','standard'=>'Standard','AB04'=>'AB04','ABT06'=>'ABT06','NL10'=>'NL10','NL17'=>'NL17','ABA99'=>'ABA99','other'=>'Övrigt'];
    return '<span class="inline-flex px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">'.($m[$t]??$t).'</span>';
}
?>
