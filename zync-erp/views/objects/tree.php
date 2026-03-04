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
$typeIcons = [
    'machine'    => '⚙️',
    'equipment'  => '🔧',
    'article'    => '📦',
    'customer'   => '🏢',
    'supplier'   => '🚚',
    'employee'   => '👤',
    'work_order' => '📋',
];
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="/objects" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Objektträd</h1>
        </div>
        <a href="/objects" class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition">🔍 Sökning</a>
    </div>

    <?php if (empty($byType)): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-8 text-center">
        <p class="text-gray-500 dark:text-gray-400">Objektregistret är tomt.</p>
        <form method="POST" action="/objects/sync" class="mt-4 inline-block">
            <?= \App\Core\Csrf::field() ?>
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-lg transition">↻ Synkronisera nu</button>
        </form>
    </div>
    <?php else: ?>
    <div class="space-y-4">
        <?php foreach ($byType as $type => $objects): ?>
        <div
            x-data="{ open: true }"
            class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden"
        >
            <!-- Type header -->
            <button
                @click="open = !open"
                class="w-full flex items-center justify-between px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition"
            >
                <div class="flex items-center gap-3">
                    <span class="text-xl"><?= $typeIcons[$type] ?? '📄' ?></span>
                    <h2 class="font-semibold text-gray-900 dark:text-white"><?= htmlspecialchars($typeLabels[$type] ?? $type, ENT_QUOTES, 'UTF-8') ?></h2>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300"><?= count($objects) ?></span>
                </div>
                <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <!-- Objects list -->
            <div x-show="open" class="border-t border-gray-100 dark:border-gray-700 divide-y divide-gray-100 dark:divide-gray-700">
                <?php foreach ($objects as $obj): ?>
                <div
                    x-data="{ childrenOpen: false, children: null, loading: false }"
                    class="px-5 py-3"
                >
                    <div class="flex items-center justify-between">
                        <a href="/objects/<?= htmlspecialchars($obj['object_type'], ENT_QUOTES, 'UTF-8') ?>/<?= (int) $obj['object_id'] ?>"
                           class="text-sm font-medium text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400">
                            <?= htmlspecialchars($obj['display_name'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                        <button
                            @click="
                                if (!children) {
                                    loading = true;
                                    fetch('/objects/<?= htmlspecialchars($obj['object_type'], ENT_QUOTES, 'UTF-8') ?>/<?= (int) $obj['object_id'] ?>/children')
                                        .then(r => r.json())
                                        .then(d => { children = d; loading = false; childrenOpen = true; })
                                        .catch(() => loading = false);
                                } else {
                                    childrenOpen = !childrenOpen;
                                }
                            "
                            class="ml-3 text-xs text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400"
                        >
                            <span x-show="!loading">▶</span>
                            <span x-show="loading">…</span>
                        </button>
                    </div>
                    <!-- Children -->
                    <div x-show="childrenOpen && children && children.length > 0" class="mt-2 pl-4 border-l-2 border-indigo-200 dark:border-indigo-700 space-y-1">
                        <template x-for="child in (children || [])" :key="child.id">
                            <a :href="'/objects/' + child.object_type + '/' + child.object_id"
                               class="block text-xs text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 py-0.5"
                               x-text="child.display_name">
                            </a>
                        </template>
                    </div>
                    <div x-show="childrenOpen && children && children.length === 0" class="mt-2 pl-4 text-xs text-gray-400 dark:text-gray-500">Inga underobjekt</div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
