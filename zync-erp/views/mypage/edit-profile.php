<?php
/** @var array $user */
/** @var array|null $employee */

$flashSuccess = \App\Core\Flash::get("success");
$flashError = \App\Core\Flash::get("error");
$csrf = \App\Core\Csrf::token();
?>

<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
        <a href="/my-page" class="text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Redigera profil</h1>
    </div>

    <?php if ($flashSuccess): ?>
        <div class="rounded-lg p-4 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400"><?= htmlspecialchars($flashSuccess) ?></div>
    <?php endif; ?>
    <?php if ($flashError): ?>
        <div class="rounded-lg p-4 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400"><?= htmlspecialchars($flashError) ?></div>
    <?php endif; ?>

    <!-- Avatar -->
    <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Profilbild</h2>
        <form action="/my-page/avatar" method="POST" enctype="multipart/form-data" class="flex items-center gap-6">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>">
            <?php if (!empty($user['avatar_path'])): ?>
                <img src="<?= htmlspecialchars($user['avatar_path']) ?>" alt="Avatar" class="h-20 w-20 rounded-full object-cover ring-2 ring-gray-200 dark:ring-gray-700">
            <?php else: ?>
                <div class="h-20 w-20 rounded-full bg-indigo-600 flex items-center justify-center text-white text-2xl font-bold">
                    <?= strtoupper(mb_substr($user['full_name'] ?? $user['username'] ?? '?', 0, 1)) ?>
                </div>
            <?php endif; ?>
            <div class="flex-1">
                <input type="file" name="avatar" accept=".jpg,.jpeg,.png,.webp"
                    class="block w-full text-sm text-gray-500 dark:text-gray-400
                        file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                        file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700
                        dark:file:bg-indigo-900/30 dark:file:text-indigo-400
                        hover:file:bg-indigo-100 dark:hover:file:bg-indigo-900/50 file:cursor-pointer">
                <p class="text-xs text-gray-400 mt-2">JPG, PNG eller WebP. Max 2 MB.</p>
            </div>
            <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
                Ladda upp
            </button>
        </form>
    </div>

    <!-- Personal Info -->
    <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Personuppgifter</h2>
        <form action="/my-page/update" method="POST">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Namn *</label>
                    <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" required
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">E-post *</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <?php if ($employee): ?>
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Telefon</label>
                    <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($employee['phone'] ?? '') ?>"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Adress</label>
                    <input type="text" id="address" name="address" value="<?= htmlspecialchars($employee['address'] ?? '') ?>"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Postnummer</label>
                    <input type="text" id="postal_code" name="postal_code" value="<?= htmlspecialchars($employee['postal_code'] ?? '') ?>"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ort</label>
                    <input type="text" id="city" name="city" value="<?= htmlspecialchars($employee['city'] ?? '') ?>"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <?php endif; ?>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="submit" class="rounded-lg bg-indigo-600 px-6 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
                    Spara ändringar
                </button>
            </div>
        </form>
    </div>

    <!-- Change Password -->
    <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Byt lösenord</h2>
        <form action="/my-page/password" method="POST">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nuvarande lösenord</label>
                    <input type="password" id="current_password" name="current_password" required
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nytt lösenord</label>
                    <input type="password" id="new_password" name="new_password" required minlength="8"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bekräfta lösenord</label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="8"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="submit" class="rounded-lg bg-red-600 px-6 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">
                    Byt lösenord
                </button>
            </div>
        </form>
    </div>

    <!-- Theme & Language -->
    <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Inställningar</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <!-- Theme Toggle -->
            <div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tema</p>
                <div class="flex gap-3">
                    <button onclick="setTheme('dark')" class="flex items-center gap-2 rounded-lg border-2 px-4 py-2 text-sm font-medium transition-colors
                        <?= ($user['theme'] ?? 'dark') === 'dark' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400' : 'border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:border-gray-300' ?>">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        Mörkt
                    </button>
                    <button onclick="setTheme('light')" class="flex items-center gap-2 rounded-lg border-2 px-4 py-2 text-sm font-medium transition-colors
                        <?= ($user['theme'] ?? 'dark') === 'light' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400' : 'border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:border-gray-300' ?>">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Ljust
                    </button>
                </div>
            </div>

            <!-- 2FA -->
            <div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tvåfaktorautentisering</p>
                <?php if (!empty($user['2fa_enabled']) || !empty($user['totp_enabled'])): ?>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center gap-1 rounded-full bg-green-100 dark:bg-green-900/30 px-3 py-1 text-sm font-medium text-green-700 dark:text-green-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Aktiverad
                        </span>
                        <a href="/2fa/setup" class="text-sm text-gray-500 hover:text-red-600 transition-colors">Hantera →</a>
                    </div>
                <?php else: ?>
                    <a href="/2fa/setup" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Aktivera 2FA
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function setTheme(theme) {
    fetch('/settings/theme', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: '_token=<?= htmlspecialchars($csrf) ?>&theme=' + theme
    }).then(() => {
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        location.reload();
    });
}
</script>
