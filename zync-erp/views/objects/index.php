<?php
$typeLabels = [
    'machine'    => 'Maskiner',
    'equipment'  => 'Utrustning',
    'article'    => 'Artiklar',
    'customer'   => 'Kunder',
    'supplier'   => 'Leverantörer',
    'employee'   => 'Anställda',
    'work_order' => 'Arbetsordrar',
];

function objectUrl(string $type, int $id): string {
    $routes = [
        'machine'    => "/machines/{$id}",
        'equipment'  => "/equipment/{$id}",
        'article'    => "/articles/{$id}",
        'customer'   => "/customers/{$id}",
        'supplier'   => "/suppliers/{$id}",
        'employee'   => "/hr/employees/{$id}",
        'work_order' => "/maintenance/work-orders/{$id}",
    ];
    return $routes[$type] ?? "/objects/{$type}/{$id}";
}
?>
<div
    x-data="{
        query: '<?= htmlspecialchars($query, ENT_QUOTES, 'UTF-8') ?>',
        type: '<?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8') ?>',
        liveResults: [],
        loading: false,
        doSearch() {
            if (this.query.length < 2 && this.type === '') { this.liveResults = []; return; }
            this.loading = true;
            fetch('/objects/search?q=' + encodeURIComponent(this.query) + '&type=' + encodeURIComponent(this.type))
                .then(r => r.json())
                .then(d => { this.liveResults = d; this.loading = false; })
                .catch(() => { this.loading = false; });
        }
    }"
    class="space-y-6"
>
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">ObjektNavigator</h1>
        <div class="flex gap-2">
            <a href="/objects/tree" class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition">🌳 Trädvy</a>
            <form method="POST" action="/objects/sync" class="inline">
                <?= \App\Core\Csrf::field() ?>
                <button type="submit" class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">↻ Synkronisera</button>
            </form>
        </div>
    </div>

    <!-- Search bar -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
        <div class="flex gap-3">
            <input
                type="text"
                x-model="query"
                @input.debounce.300ms="doSearch()"
                placeholder="Sök objekt…"
                class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
            />
            <select
                x-model="type"
                @change="doSearch()"
                class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm"
            >
                <option value="">Alla typer</option>
                <?php foreach ($typeLabels as $k => $v): ?>
                <option value="<?= htmlspecialchars($k, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($v, ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Live results -->
        <div x-show="query.length >= 2 || type !== ''" class="mt-4">
            <div x-show="loading" class="text-center py-4 text-gray-500 dark:text-gray-400 text-sm">Söker…</div>
            <div x-show="!loading && liveResults.length === 0" class="text-center py-4 text-gray-500 dark:text-gray-400 text-sm">Inga objekt hittades.</div>
            <div x-show="!loading && liveResults.length > 0" class="space-y-1">
                <template x-for="obj in liveResults" :key="obj.id">
                    <a
                        :href="'/objects/' + obj.object_type + '/' + obj.object_id"
                        class="flex items-center justify-between px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition"
                    >
                        <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="obj.display_name"></span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 ml-3" x-text="obj.object_type"></span>
                    </a>
                </template>
            </div>
        </div>
    </div>

    <!-- Type statistics -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        <?php foreach ($typeCounts as $tc): ?>
        <a href="?type=<?= htmlspecialchars($tc['object_type'], ENT_QUOTES, 'UTF-8') ?>" class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 hover:shadow-md transition flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($typeLabels[$tc['object_type']] ?? $tc['object_type'], ENT_QUOTES, 'UTF-8') ?></p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= (int) $tc['cnt'] ?></p>
            </div>
            <span class="text-3xl opacity-30">📦</span>
        </a>
        <?php endforeach; ?>
        <?php if (empty($typeCounts)): ?>
        <div class="col-span-full text-center py-8 text-gray-500 dark:text-gray-400 text-sm">
            Objektregistret är tomt. Klicka på "Synkronisera" för att indexera alla objekt.
        </div>
        <?php endif; ?>
    </div>

    <!-- Static search results (non-AJAX) -->
    <?php if (!empty($results)): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h2 class="font-semibold text-gray-900 dark:text-white">Sökresultat</h2>
            <span class="text-sm text-gray-500 dark:text-gray-400"><?= count($results) ?> objekt</span>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            <?php foreach ($results as $obj): ?>
            <a href="/objects/<?= htmlspecialchars($obj['object_type'], ENT_QUOTES, 'UTF-8') ?>/<?= (int) $obj['object_id'] ?>" class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                <span class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($obj['display_name'], ENT_QUOTES, 'UTF-8') ?></span>
                <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 ml-3"><?= htmlspecialchars($typeLabels[$obj['object_type']] ?? $obj['object_type'], ENT_QUOTES, 'UTF-8') ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
