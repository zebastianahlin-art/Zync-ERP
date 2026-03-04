<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Importera kontoplan</h1>
        <a href="/finance/accounts" class="text-sm text-gray-500 hover:text-indigo-600">← Kontoplan</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-4">
        <div class="rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 p-4 text-sm text-blue-700 dark:text-blue-400">
            <strong>CSV-format:</strong> Filen ska ha kolumnerna <code class="font-mono bg-blue-100 dark:bg-blue-900 px-1 rounded">Kontonummer,Namn,Klass,Aktiv</code> (kommaseparerat, UTF-8).
        </div>

        <form method="POST" action="/finance/accounts/import" enctype="multipart/form-data" class="space-y-4">
            <?= \App\Core\Csrf::field() ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CSV-fil <span class="text-red-500">*</span></label>
                <input type="file" name="csv_file" accept=".csv,text/csv" required class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-900 dark:file:text-indigo-400 hover:file:bg-indigo-100">
            </div>
            <div>
                <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                    <input type="checkbox" name="overwrite" value="1" class="rounded border-gray-300">
                    Skriv över befintliga konton (samma kontonummer)
                </label>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded text-sm transition">Importera</button>
                <a href="/finance/accounts" class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 px-5 py-2 rounded text-sm transition text-gray-700 dark:text-gray-300">Avbryt</a>
            </div>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3">Exportera befintlig kontoplan</h2>
        <p class="text-sm text-gray-500 mb-4">Ladda ned nuvarande kontoplan som CSV-fil för redigering.</p>
        <a href="/finance/accounts/export" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded text-sm transition inline-block">⬇ Exportera kontoplan (CSV)</a>
    </div>
</div>
