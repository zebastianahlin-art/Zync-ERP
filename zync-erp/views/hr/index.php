<?php
/** @var string $title */
/** @var array $payrollStats */
/** @var array $leaveStats */
/** @var array $recruitmentStats */
/** @var array $trainingStats */
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">HR</h1>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-5">
            <div class="flex items-center gap-4">
                <div class="rounded-full bg-indigo-100 dark:bg-indigo-900/30 p-3">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Löneperioder (utkast)</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $payrollStats['draft_periods'] ?></p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-5">
            <div class="flex items-center gap-4">
                <div class="rounded-full bg-amber-100 dark:bg-amber-900/30 p-3">
                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Väntande frånvaroansökningar</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $leaveStats['pending_requests'] ?></p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-5">
            <div class="flex items-center gap-4">
                <div class="rounded-full bg-green-100 dark:bg-green-900/30 p-3">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Öppna tjänster</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $recruitmentStats['open_positions'] ?></p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-5">
            <div class="flex items-center gap-4">
                <div class="rounded-full bg-cyan-100 dark:bg-cyan-900/30 p-3">
                    <svg class="w-6 h-6 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Kommande utbildningar</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $trainingStats['upcoming_sessions'] ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Module Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="/hr/payroll" class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-6 text-center hover:shadow-lg transition-shadow group">
            <svg class="w-10 h-10 mx-auto mb-3 text-indigo-500 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <h3 class="font-semibold text-gray-900 dark:text-white">Lönehantering</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Löneperioder, lönebesked, övertid och bonus.</p>
        </a>
        <a href="/hr/leave" class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-6 text-center hover:shadow-lg transition-shadow group">
            <svg class="w-10 h-10 mx-auto mb-3 text-amber-500 group-hover:text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <h3 class="font-semibold text-gray-900 dark:text-white">Frånvaro</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Semester, sjukfrånvaro, VAB och ledighet.</p>
        </a>
        <a href="/hr/attendance" class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-6 text-center hover:shadow-lg transition-shadow group">
            <svg class="w-10 h-10 mx-auto mb-3 text-gray-500 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <h3 class="font-semibold text-gray-900 dark:text-white">Närvaro</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Daglig närvaro, in- och utcheckning.</p>
        </a>
        <a href="/hr/recruitment" class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-6 text-center hover:shadow-lg transition-shadow group">
            <svg class="w-10 h-10 mx-auto mb-3 text-green-500 group-hover:text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <h3 class="font-semibold text-gray-900 dark:text-white">Rekrytering</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Lediga tjänster, kandidater och intervjuer.</p>
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="/hr/training" class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-6 text-center hover:shadow-lg transition-shadow group">
            <svg class="w-10 h-10 mx-auto mb-3 text-cyan-500 group-hover:text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            <h3 class="font-semibold text-gray-900 dark:text-white">Utbildning</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kurser, tillfällen, obligatoriska utbildningar.</p>
        </a>
        <a href="/employees" class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-6 text-center hover:shadow-lg transition-shadow group">
            <svg class="w-10 h-10 mx-auto mb-3 text-gray-500 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <h3 class="font-semibold text-gray-900 dark:text-white">Personal</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Personalregister, anställningar.</p>
        </a>
        <a href="/certificates" class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-6 text-center hover:shadow-lg transition-shadow group">
            <svg class="w-10 h-10 mx-auto mb-3 text-red-500 group-hover:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
            <h3 class="font-semibold text-gray-900 dark:text-white">Certifikat</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Certifikat och behörigheter.</p>
        </a>
        <a href="/departments" class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-6 text-center hover:shadow-lg transition-shadow group">
            <svg class="w-10 h-10 mx-auto mb-3 text-purple-500 group-hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <h3 class="font-semibold text-gray-900 dark:text-white">Avdelningar</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Organisationsstruktur.</p>
        </a>
    </div>
</div>
