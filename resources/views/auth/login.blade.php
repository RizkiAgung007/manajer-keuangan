<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Login</title>

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
                <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">

                        <div class="flex items-center">
                            <a href="/">
                                <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                            </a>
                            <span class="ms-3 font-semibold text-lg text-gray-800 dark:text-gray-200 hidden sm:inline">
                                {{ config('app.name', 'Laravel') }}
                            </span>
                        </div>

                        <div
                            x-data="{
                                darkMode: localStorage.getItem('darkMode') === 'true',
                                toggle() {
                                    this.darkMode = !this.darkMode;
                                    localStorage.setItem('darkMode', this.darkMode);
                                    document.documentElement.classList.toggle('dark', this.darkMode);
                                    window.dispatchEvent(new CustomEvent('dark-mode-toggled', {
                                        detail: { isDarkMode: this.darkMode }
                                    }));
                                }
                            }"
                            class="flex items-center gap-4 text-end"
                        >
                            <button @click="toggle()" type="button" class="p-2 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full focus:outline-none">
                                <span x-show="!darkMode"><x-heroicon-o-moon class="w-5 h-5"/></span>
                                <span x-show="darkMode" style="display: none;"><x-heroicon-o-sun class="w-5 h-5"/></span>
                            </button>

                            <!-- <div class="hidden sm:block">
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">Register</a>
                                @endif
                            </div> -->
                        </div>
                    </div>
                </nav>
            </header>

            <main class="flex-grow flex items-center justify-center py-12 sm:px-6 lg:px-8">
                <div class="w-full max-w-lg p-8 space-y-6 bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg">

                    <h2 class="text-3xl font-bold text-center text-gray-900 dark:text-white">
                        Sign in to your Account
                    </h2>

                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="password" :value="__('Password')" />
                            <x-text-input id="password" class="block mt-1 w-full"
                                            type="password"
                                            name="password"
                                            required autocomplete="current-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-between mt-4">
                            <label for="remember_me" class="inline-flex items-center">
                                <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-green-600 shadow-sm focus:ring-green-500 dark:focus:ring-green-600 dark:focus:ring-offset-gray-800" name="remember">
                                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                                    {{ __('Forgot your password?') }}
                                </a>
                            @endif
                        </div>

                        <div class="mt-6">
                            <x-primary-button class="w-full justify-center">
                                {{ __('Log in') }}
                            </x-primary-button>
                        </div>
                    </form>

                    <div class="mt-6 text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __("Don't have an account?") }}
                            <a href="{{ route('register') }}" class="font-semibold text-green-600 hover:text-green-500 dark:text-green-500 dark:hover:text-green-400 underline">
                                {{ __('Register now') }}
                            </a>
                        </p>
                    </div>
                </div>
            </main>

            <footer class="bg-gray-100 dark:bg-gray-900 py-12">
                <p class="text-center text-sm text-gray-500 dark:text-gray-400">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.
                </p>
            </footer>
        </div>

    </body>
</html>
