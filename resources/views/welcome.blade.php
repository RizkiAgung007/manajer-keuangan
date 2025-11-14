<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="alternate icon" type="image/png" href="{{ asset('favicon.png') }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <script>
            if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
              document.documentElement.classList.add('dark');
            } else {
              document.documentElement.classList.remove('dark');
            }
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">

        <div class="min-h-screen flex flex-col">

            <header class="w-full shadow-sm bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
                <nav class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">

                        <div class="flex items-center">
                            <a href="/">
                                <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                            </a>
                            <span class="ms-3 font-semibold text-lg text-gray-800 dark:text-gray-200 hidden sm:inline">{{ config('app.name', 'Laravel') }}</span>
                        </div>

                        <div
                            x-data="{
                                darkMode: localStorage.getItem('darkMode') === 'true',
                                toggle() {
                                    this.darkMode = !this.darkMode;
                                    localStorage.setItem('darkMode', this.darkMode);
                                    document.documentElement.classList.toggle('dark', this.darkMode);
                                    window.dispatchEvent(new CustomEvent('dark-mode-toggle', {
                                        detail: { isDarkMode: this.darkMode }
                                    }));
                                }
                            }"
                            class="flex items-center gap-4 text-end">

                            <button @click="toggle()" type="button" class="p-2 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full focus:outline-none">
                                <span x-show="!darkMode">
                                    <x-heroicon-o-moon class="w-6 h-6"/>
                                </span>
                                <span x-show="darkMode" style="display: none;">
                                    <x-heroicon-o-sun class="w-6 h-6"/>
                                </span>
                            </button>

                            @auth
                                <a href="{{ url('/dashboard') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                                    Log in
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="ms-4 inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">Register</a>
                                @endif
                            @endauth
                        </div>
                    </div>
                </nav>
            </header>

            <main class="w-full">
                <section class="text-center px-6 py-24 sm:py-32 bg-white dark:bg-gray-800">
                    <div class="max-w-3xl mx-auto">

                        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                            Lacak Keuangan Anda.
                            <br>
                            <span class="text-green-600 dark:text-green-500">Kendalikan Masa Depan Anda.</span>
                        </h1>

                        <p class="mt-6 text-lg max-w-2xl mx-auto text-gray-600 dark:text-gray-400">
                            Aplikasi manajer keuangan minimalis yang membantu Anda melacak pengeluaran, mengatur budget, dan melihat laporan visual yang jelas.
                        </p>

                        <div class="mt-10 flex items-center justify-center gap-x-6">
                            @if (Route::has('register'))
                                <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    Mulai Gratis
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    Login
                                </a>
                            @endif
                        </div>
                    </div>
                </section>

                <section class="bg-gray-100 dark:bg-gray-900 py-24 sm:py-32">
                    <div class="max-w-screen-2xl mx-auto px-6 lg:px-8">

                        <div class="max-w-2xl mx-auto text-center">
                            <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">Semua yang Anda Perlukan</h2>
                            <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">
                                Fitur yang dirancang untuk kesederhanaan dan fungsionalitas.
                            </p>
                        </div>

                        <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-x-8 gap-y-16">

                            <div class="flex flex-col items-center text-center">
                                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300">
                                    <x-heroicon-o-banknotes class="w-6 h-6" />
                                </div>
                                <h3 class="mt-6 text-lg font-semibold text-gray-900 dark:text-white">Catat Transaksi</h3>
                                <p class="mt-2 text-base text-gray-600 dark:text-gray-400">
                                    Catat pemasukan dan pengeluaran Anda dengan cepat melalui fitur "Quick Add" di dashboard Anda.
                                </p>
                            </div>

                            <div class="flex flex-col items-center text-center">
                                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300">
                                    <x-heroicon-o-flag class="w-6 h-6" />
                                </div>
                                <h3 class="mt-6 text-lg font-semibold text-gray-900 dark:text-white">Atur Budget</h3>
                                <p class="mt-2 text-base text-gray-600 dark:text-gray-400">
                                    Tetapkan budget bulanan untuk setiap kategori dan lacak kemajuannya agar pengeluaran Anda tetap terkendali.
                                </p>
                            </div>

                            <div class="flex flex-col items-center text-center">
                                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300">
                                    <x-heroicon-o-chart-pie class="w-6 h-6" />
                                </div>
                                <h3 class="mt-6 text-lg font-semibold text-gray-900 dark:text-white">Laporan Visual</h3>
                                <p class="mt-2 text-base text-gray-600 dark:text-gray-400">
                                    Pahami kebiasaan finansial Anda dengan grafik donat yang interaktif untuk pemasukan dan pengeluaran.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
            </main>

            <footer class="bg-gray-100 dark:bg-gray-900 pt-12 pb-16">
                <p class="text-center text-sm text-gray-500 dark:text-gray-400">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.
                </p>
            </footer>
        </div>

    </body>
</html>
