<?php ?>
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Nytt konto</h1>
        <a href="/finance/accounts" class="text-sm text-gray-500 hover:text-indigo-600">← Tillbaka</a>
    </div>
    <form method="POST" action="/finance/accounts" class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-4">
        <?= \App\Core\Csrf::field() ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium mb-1">Kontonummer *</label><input type="text" name="account_number" required maxlength="10" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></div>
            <div><label class="block text-sm font-medium mb-1">Namn *</label><input type="text" name="name" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></div>
            <div><label class="block text-sm font-medium mb-1">Klass *</label><select name="account_class" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"><option value="1">1 — Tillgångar</option><option value="2">2 — Skulder</option><option value="3">3 — Intäkter</option><option value="4">4 — Material/varuinköp</option><option value="5">5 — Lokalkostnader</option><option value="6">6 — Övriga externa</option><option value="7">7 — Personal/avskrivning</option><option value="8">8 — Finansiellt</option></select></div>
            <div><label class="block text-sm font-medium mb-1">Kontogrupp</label><input type="text" name="account_group" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></div>
            <div><label class="block text-sm font-medium mb-1">Momskod</label><select name="vat_code" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"><option value="">Ingen</option><option value="MP1">MP1 — 25%</option><option value="MP2">MP2 — 12%</option><option value="MP3">MP3 — 6%</option></select></div>
        </div>
        <div><label class="block text-sm font-medium mb-1">Beskrivning</label><textarea name="description" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></textarea></div>
        <div class="flex justify-end pt-4"><button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition">Skapa konto</button></div>
    </form>
</div>
