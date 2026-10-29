@extends('public.layouts.main')

@php
    $isEn = app()->getLocale() === 'en';
    $totale = $totale ?? 0;
@endphp

@section('title', ($isEn ? 'Secure Checkout - ' : 'Checkout Sicuro - ') . config('app.name'))

@section('content')
<div class="bg-gray-50 min-h-screen pt-12 pb-24" x-data="checkoutForm()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex items-center justify-between text-sm text-gray-500 mb-8 w-full">
             <div class="flex space-x-2">
                <a href="{{ route('public.shop.cart.index') }}" class="hover:text-gray-900">&larr; {{ $isEn ? 'Back to Cart' : 'Torna al Carrello' }}</a>
            </div>
            <div class="font-bold text-gray-900">{{ $isEn ? 'Secure Checkout' : 'Checkout Sicuro' }}</div>
        </div>

        @if(session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4">
                <div class="flex">
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            {{ $isEn ? 'There were some problems with your request:' : 'Ci sono stati dei problemi con la tua richiesta:' }}
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="lg:grid lg:grid-cols-12 lg:gap-x-12 lg:items-start">
            
            <div class="lg:col-span-7 space-y-8">
                
                @guest
                <!-- Scelta Modalità Checkout per Utenti non Autenticati -->
                <div class="bg-white shadow rounded-lg px-4 py-6 sm:p-6 p-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">{{ $isEn ? 'How would you like to proceed?' : 'Come vuoi procedere?' }}</h2>
                    <div class="space-y-4 sm:flex sm:items-center sm:space-y-0 sm:space-x-10">
                        <div class="flex items-center">
                            <input id="mode-guest" name="mode" type="radio" value="guest" x-model="checkoutMode" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                            <label for="mode-guest" class="ml-3 block text-sm font-medium text-gray-700 cursor-pointer">
                                {{ $isEn ? 'Continue as Guest' : 'Continua come Ospite' }}
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input id="mode-register" name="mode" type="radio" value="register" x-model="checkoutMode" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                            <label for="mode-register" class="ml-3 block text-sm font-medium text-gray-700 cursor-pointer">
                                {{ $isEn ? 'Create an Account' : 'Crea un Account' }}
                            </label>
                        </div>
                    </div>
                    <!-- Dropdown / Tendina Login per Utenti già registrati -->
                    <div class="mt-4 pt-4 border-t border-gray-200" x-data="{ openLogin: {{ (isset($errors) && $errors->has('email')) ? 'true' : 'false' }} }">
                        <button type="button" 
                                @click="openLogin = !openLogin" 
                                class="flex items-center justify-between w-full text-left py-2 text-sm font-semibold text-zinc-700 hover:text-black transition-colors focus:outline-none cursor-pointer">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#e35205] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                <span>{{ $isEn ? 'Already registered?' : 'Sei già registrato?' }} <span class="text-[#e35205] font-bold underline hover:text-black">{{ $isEn ? 'Click here to login' : 'Clicca qui per accedere' }}</span></span>
                            </span>
                            <svg class="w-5 h-5 text-gray-400 transition-transform duration-200 shrink-0" :class="{ 'rotate-180': openLogin }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="openLogin" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             style="display: none;" 
                             class="mt-3 p-5 bg-zinc-50 border border-zinc-200 rounded-none">
                            
                            <p class="text-xs text-zinc-600 mb-4">
                                {{ $isEn ? 'Enter your credentials to access your profile and automatically autofill shipping data:' : 'Inserisci le tue credenziali per accedere al tuo profilo e precompilare automaticamente i dati di spedizione:' }}
                            </p>

                            @if(isset($errors) && $errors->has('email'))
                                <div class="mb-4 p-3 bg-red-50 border border-red-300 text-red-700 text-xs font-semibold rounded-none">
                                    {{ $errors->first('email') }}
                                </div>
                            @endif

                            <form action="{{ route('public.account.login.submit') }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="redirect_to" value="{{ route('public.shop.cart.checkout') }}">

                                <div>
                                    <label for="login_email" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 mb-1">
                                        {{ $isEn ? 'Email Address *' : 'Indirizzo Email *' }}
                                    </label>
                                    <input type="email" id="login_email" name="email" value="{{ old('email') }}" required autocomplete="username"
                                           class="w-full bg-white border border-gray-300 rounded-none px-3 py-2 text-sm text-black focus:ring-1 focus:ring-black focus:border-black outline-none placeholder-gray-400"
                                           placeholder="{{ $isEn ? 'example@domain.com' : 'esempio@dominio.it' }}">
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label for="login_password" class="block text-xs font-bold uppercase tracking-wider text-zinc-700">
                                            Password *
                                        </label>
                                        @if (Route::has('password.request'))
                                            <a href="{{ route('password.request') }}" target="_blank" class="text-xs text-zinc-500 hover:text-black underline">
                                                {{ $isEn ? 'Forgot Password?' : 'Password dimenticata?' }}
                                            </a>
                                        @endif
                                    </div>
                                    <input type="password" id="login_password" name="password" required autocomplete="current-password"
                                           class="w-full bg-white border border-gray-300 rounded-none px-3 py-2 text-sm text-black focus:ring-1 focus:ring-black focus:border-black outline-none placeholder-gray-400"
                                           placeholder="••••••••">
                                </div>

                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-2 border-t border-zinc-200">
                                    <label class="flex items-center gap-2 cursor-pointer text-xs font-medium text-zinc-700 select-none">
                                        <input type="checkbox" name="remember" class="w-4 h-4 rounded-none border-gray-300 text-black focus:ring-black cursor-pointer">
                                        <span>{{ $isEn ? 'Remember me' : 'Ricordami' }}</span>
                                    </label>

                                    <button type="submit" 
                                            class="inline-flex items-center justify-center px-6 py-2.5 bg-black hover:bg-zinc-800 text-white font-bold text-xs uppercase tracking-wider transition-colors cursor-pointer border-0 shadow-xs">
                                        {{ $isEn ? 'Login to your Account →' : 'Accedi al tuo Account →' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endguest

                <!-- Form Principale Checkout -->
                <form action="{{ route('public.shop.cart.process') }}" method="POST" class="space-y-8" id="checkout-form">
                    @csrf
                    
                    <input type="hidden" name="checkout_mode" :value="checkoutMode">

                    <!-- Box 1: I tuoi dati -->
                    <div class="bg-white shadow rounded-lg px-4 py-6 sm:p-6 p-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-6 border-b pb-4">{{ $isEn ? 'Your Details' : 'I tuoi dati' }}</h2>
                        
                        <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-4">
                            <!-- Anagrafica Base -->
                            <div>
                                <label for="nome" class="block text-sm font-medium text-gray-700">{{ $isEn ? 'First Name *' : 'Nome *' }}</label>
                                <div class="mt-1">
                                    <input type="text" id="nome" name="nome" value="{{ old('nome', Auth::user()?->name ? explode(' ', Auth::user()->name)[0] : '') }}" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>
                            <div>
                                <label for="cognome" class="block text-sm font-medium text-gray-700">{{ $isEn ? 'Last Name *' : 'Cognome *' }}</label>
                                <div class="mt-1">
                                    <input type="text" id="cognome" name="cognome" value="{{ old('cognome', Auth::user()?->name ? (count(explode(' ', Auth::user()->name)) > 1 ? explode(' ', Auth::user()->name)[1] : '') : '') }}" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>
                            
                            <div class="sm:col-span-2">
                                <label for="email" class="block text-sm font-medium text-gray-700">{{ $isEn ? 'Email Address *' : 'Indirizzo Email *' }}</label>
                                <div class="mt-1">
                                    <input type="email" id="email" name="email" value="{{ old('email', Auth::user()?->email) }}" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" @if(Auth::check()) readonly @endif>
                                </div>
                            </div>

                            <!-- Password Fields if Registering -->
                            <template x-if="checkoutMode === 'register'">
                                <div class="sm:col-span-2 bg-gray-50/90 p-5 rounded-lg border border-gray-200 space-y-4">
                                    
                                    <!-- Header con Titolo e Bottone Generatore -->
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-gray-200">
                                        <div>
                                            <p class="text-sm font-bold text-gray-900 flex items-center gap-2">
                                                <svg class="w-4 h-4 text-[#e35205]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                <span>{{ $isEn ? 'Create your Password' : 'Crea la tua Password' }}</span>
                                            </p>
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $isEn ? 'Choose a secure password or use the automatic generator.' : 'Scegli una password sicura o usa il generatore automatico.' }}</p>
                                        </div>
                                        
                                        <button type="button" 
                                                @click="generatePassword()" 
                                                class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-[#e35205] hover:bg-[#c94500] text-white text-xs font-bold uppercase tracking-wider rounded transition-colors shadow-xs cursor-pointer shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                            <span>{{ $isEn ? 'Generate Secure Password' : 'Genera Password Sicura' }}</span>
                                        </button>
                                    </div>

                                    @if(isset($errors) && $errors->has('password'))
                                        <div class="p-3 bg-red-50 border border-red-300 rounded text-xs text-red-700 font-semibold">
                                            <ul class="list-disc pl-5 space-y-0.5">
                                                @foreach($errors->get('password') as $passErr)
                                                    <li>{{ $passErr }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <!-- Box Password Generata Automaticamente -->
                                    <div x-show="generatedPassword" x-transition class="p-3.5 bg-amber-50 border border-amber-300 rounded-md">
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="text-xs font-bold text-amber-900 uppercase tracking-wider">{{ $isEn ? 'Generated password:' : 'Password generata:' }}</span>
                                                <span class="font-mono text-sm font-black text-black bg-white px-3 py-1 rounded border border-amber-300 select-all tracking-wider shadow-2xs" x-text="generatedPassword"></span>
                                            </div>
                                            <button type="button" 
                                                    @click="copyPassword()" 
                                                    class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-white hover:bg-gray-100 text-xs font-bold text-gray-800 rounded border border-gray-300 shadow-2xs transition-colors cursor-pointer shrink-0">
                                                <span x-show="!copiedPassword">📋 {{ $isEn ? 'Copy Password' : 'Copia Password' }}</span>
                                                <span x-show="copiedPassword" class="text-emerald-700 font-bold">✓ {{ $isEn ? 'Copied to clipboard!' : 'Copiata negli appunti!' }}</span>
                                            </button>
                                        </div>
                                        <p class="text-[11px] text-amber-800 font-medium mt-2 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                            <span>{{ $isEn ? 'Strong password automatically filled into both Password and Confirm Password fields below.' : 'Password forte compilata automaticamente in entrambi i campi Password e Conferma Password sottostanti.' }}</span>
                                        </p>
                                    </div>

                                    <!-- Campi Input Password e Conferma Password -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-4">
                                        <div>
                                            <div class="flex items-center justify-between mb-1">
                                                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-gray-700">Password *</label>
                                                <button type="button" @click="showPassword = !showPassword" class="text-xs text-zinc-500 hover:text-black font-semibold cursor-pointer">
                                                    <span x-show="!showPassword">👁 {{ $isEn ? 'Show' : 'Mostra' }}</span>
                                                    <span x-show="showPassword">🙈 {{ $isEn ? 'Hide' : 'Nascondi' }}</span>
                                                </button>
                                            </div>
                                            <div class="relative">
                                                <input :type="showPassword ? 'text' : 'password'" 
                                                       id="password" 
                                                       name="password" 
                                                       x-model="password"
                                                       @input="password = $event.target.value"
                                                       x-bind:required="checkoutMode === 'register'" 
                                                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#e35205] focus:border-[#e35205] sm:text-sm font-mono tracking-wide px-3 py-2"
                                                       placeholder="••••••••">
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <div class="flex items-center justify-between mb-1">
                                                <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-gray-700">{{ $isEn ? 'Confirm Password *' : 'Conferma Password *' }}</label>
                                                <button type="button" @click="showConfirmation = !showConfirmation" class="text-xs text-zinc-500 hover:text-black font-semibold cursor-pointer">
                                                    <span x-show="!showConfirmation">👁 {{ $isEn ? 'Show' : 'Mostra' }}</span>
                                                    <span x-show="showConfirmation">🙈 {{ $isEn ? 'Hide' : 'Nascondi' }}</span>
                                                </button>
                                            </div>
                                            <div class="relative">
                                                <input :type="showConfirmation ? 'text' : 'password'" 
                                                       id="password_confirmation" 
                                                       name="password_confirmation" 
                                                       x-model="passwordConfirmation"
                                                       @input="passwordConfirmation = $event.target.value"
                                                       x-bind:required="checkoutMode === 'register'" 
                                                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#e35205] focus:border-[#e35205] sm:text-sm font-mono tracking-wide px-3 py-2"
                                                       placeholder="••••••••">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Requisiti di Sicurezza Specificati -->
                                    <div class="p-3.5 bg-white border border-gray-200 rounded-md shadow-2xs">
                                        <div class="flex items-center justify-between mb-2 pb-1.5 border-b border-gray-100">
                                            <p class="text-xs font-bold uppercase tracking-wider text-gray-700 flex items-center gap-1.5">
                                                <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                                                <span>{{ $isEn ? 'Minimum security requirements:' : 'Requisiti minimi di sicurezza:' }}</span>
                                            </p>
                                            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">{{ $isEn ? 'Instant check' : 'Verifica immediata' }}</span>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                                            <!-- Minimo 8 caratteri -->
                                            <div class="flex items-center gap-2 transition-colors" :class="hasLength() ? 'text-emerald-700 font-bold' : 'text-gray-500'">
                                                <span class="font-bold text-sm w-4 text-center" x-text="hasLength() ? '✓' : '○'"></span>
                                                <span>{{ $isEn ? 'At least' : 'Almeno' }} <strong>{{ $isEn ? '8 characters' : '8 caratteri' }}</strong></span>
                                            </div>

                                            <!-- Almeno una maiuscola -->
                                            <div class="flex items-center gap-2 transition-colors" :class="hasUpper() ? 'text-emerald-700 font-bold' : 'text-gray-500'">
                                                <span class="font-bold text-sm w-4 text-center" x-text="hasUpper() ? '✓' : '○'"></span>
                                                <span>{{ $isEn ? 'At least' : 'Almeno' }} <strong>{{ $isEn ? 'one uppercase letter' : 'una lettera maiuscola' }}</strong> (A-Z)</span>
                                            </div>

                                            <!-- Almeno un numero -->
                                            <div class="flex items-center gap-2 transition-colors" :class="hasNumber() ? 'text-emerald-700 font-bold' : 'text-gray-500'">
                                                <span class="font-bold text-sm w-4 text-center" x-text="hasNumber() ? '✓' : '○'"></span>
                                                <span>{{ $isEn ? 'At least' : 'Almeno' }} <strong>{{ $isEn ? 'one number' : 'un numero' }}</strong> (0-9)</span>
                                            </div>

                                            <!-- Almeno un carattere speciale / strano -->
                                            <div class="flex items-center gap-2 transition-colors" :class="hasSpecial() ? 'text-emerald-700 font-bold' : 'text-gray-500'">
                                                <span class="font-bold text-sm w-4 text-center" x-text="hasSpecial() ? '✓' : '○'"></span>
                                                <span>{{ $isEn ? 'At least' : 'Almeno' }} <strong>{{ $isEn ? 'one special character' : 'un carattere speciale' }}</strong> (!@#$%&*...)</span>
                                            </div>

                                            <!-- Match conferma password -->
                                            <div class="sm:col-span-2 pt-1 border-t border-gray-100 flex items-center gap-2 transition-colors" 
                                                 :class="isMatching() ? 'text-emerald-700 font-bold' : (password.length > 0 && passwordConfirmation.length > 0 ? 'text-rose-600 font-bold' : 'text-gray-400')">
                                                <span class="font-bold text-sm w-4 text-center" x-text="isMatching() ? '✓' : (password.length > 0 && passwordConfirmation.length > 0 ? '✕' : '○')"></span>
                                                <span x-show="isMatching()">{{ $isEn ? 'Both passwords' : 'Le due password' }} <strong>{{ $isEn ? 'match perfectly' : 'coincidono perfettamente' }}</strong></span>
                                                <span x-show="!isMatching() && password.length > 0 && passwordConfirmation.length > 0">{{ $isEn ? 'Passwords' : 'Le due password' }} <strong>{{ $isEn ? 'do not match' : 'non coincidono' }}</strong></span>
                                                <span x-show="!isMatching() && !(password.length > 0 && passwordConfirmation.length > 0)">{{ $isEn ? 'Repeat identical password in confirmation field' : 'Ripeti la password identica nel campo di conferma' }}</span>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </template>
                            
                            <div class="sm:col-span-2">
                                <label for="telefono" class="block text-sm font-medium text-gray-700">{{ $isEn ? 'Phone Number *' : 'Telefono / Cellulare *' }}</label>
                                <div class="mt-1">
                                    <input type="text" id="telefono" name="telefono" value="{{ old('telefono') }}" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Box 2: Indirizzo di Spedizione -->
                    <div class="bg-white shadow rounded-lg px-4 py-6 sm:p-6 p-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-6 border-b pb-4">{{ $isEn ? 'Shipping Address' : 'Indirizzo di Spedizione' }}</h2>
                        
                        <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-4">
                            <div class="sm:col-span-2">
                                <label for="nazione" class="block text-sm font-medium text-gray-700">{{ $isEn ? 'Country *' : 'Nazione *' }}</label>
                                <div class="mt-1">
                                    <select id="nazione" name="nazione" required x-model="nazione" @change="updateShipping()" class="block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">{{ $isEn ? 'Select a country...' : 'Seleziona una nazione...' }}</option>
                                        @foreach($shippingCosts as $cost)
                                            <option value="{{ $cost->nazione }}">{{ $cost->nazione }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="indirizzo" class="block text-sm font-medium text-gray-700">{{ $isEn ? 'Address & Street Number *' : 'Indirizzo e N. Civico *' }}</label>
                                <div class="mt-1">
                                    <input type="text" id="indirizzo" name="indirizzo" value="{{ old('indirizzo') }}" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="{{ $isEn ? 'e.g. 123 Main Street' : 'Es. Via Roma, 10' }}">
                                </div>
                            </div>

                            <div>
                                <label for="citta" class="block text-sm font-medium text-gray-700">{{ $isEn ? 'City *' : 'Città *' }}</label>
                                <div class="mt-1">
                                    <input type="text" id="citta" name="citta" value="{{ old('citta') }}" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>

                            <div>
                                <label for="cap" class="block text-sm font-medium text-gray-700">{{ $isEn ? 'Postal / ZIP Code *' : 'CAP *' }}</label>
                                <div class="mt-1">
                                    <input type="text" id="cap" name="cap" value="{{ old('cap') }}" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="{{ $isEn ? 'e.g. 10001' : 'Es. 76121' }}">
                                </div>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="provincia" class="block text-sm font-medium text-gray-700">{{ $isEn ? 'Province / State / Region' : 'Provincia (Sigla es. BT, BA, MI, RM)' }}</label>
                                <div class="mt-1">
                                    <input type="text" id="provincia" name="provincia" value="{{ old('provincia') }}" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" maxlength="10">
                                </div>
                            </div>
                            
                            <div class="sm:col-span-2">
                                <label for="note_ordine" class="block text-sm font-medium text-gray-700">{{ $isEn ? 'Order Notes (Optional)' : 'Note Ordine (Opzionale)' }}</label>
                                <div class="mt-1">
                                    <textarea id="note_ordine" name="note_ordine" rows="3" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm placeholder-gray-400" placeholder="{{ $isEn ? 'Special instructions for delivery...' : 'Istruzioni particolari per la consegna...' }}">{{ old('note_ordine') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Box 3: Richiedi Fattura Aziendale -->
                    <div class="bg-white shadow rounded-lg px-4 py-6 sm:p-6 p-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-6 border-b pb-4">{{ $isEn ? 'Company Invoicing' : 'Fatturazione Aziendale' }}</h2>
                        
                        <div>
                            <div class="relative flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="is_azienda" name="is_azienda" type="checkbox" value="1" x-model="isAzienda" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded cursor-pointer">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="is_azienda" class="font-bold text-gray-900 cursor-pointer">{{ $isEn ? 'Request Company Invoice' : 'Richiedi Fattura Aziendale' }}</label>
                                    <p class="text-gray-500 text-xs sm:text-sm mt-0.5">{{ $isEn ? 'Check this box if purchasing on behalf of a company or professional with VAT number.' : 'Spunta questa casella se acquisti per conto di un\'azienda o professionista con partita IVA.' }}</p>
                                </div>
                            </div>

                            <!-- Dati Azienda -->
                            <template x-if="isAzienda">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-5 gap-x-4 bg-indigo-50/70 p-5 rounded-lg border border-indigo-100 mt-5">
                                    <div class="sm:col-span-2">
                                        <label for="ragione_sociale" class="block text-sm font-medium text-gray-700">{{ $isEn ? 'Company Name *' : 'Ragione Sociale *' }}</label>
                                        <div class="mt-1">
                                            <input type="text" id="ragione_sociale" name="ragione_sociale" value="{{ old('ragione_sociale') }}" x-bind:required="isAzienda" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="{{ $isEn ? 'Company or business name' : 'Nome azienda o ditta individuale' }}">
                                        </div>
                                    </div>
                                    <div>
                                        <label for="partita_iva" class="block text-sm font-medium text-gray-700">{{ $isEn ? 'VAT Number *' : 'Partita IVA *' }}</label>
                                        <div class="mt-1">
                                            <input type="text" id="partita_iva" name="partita_iva" value="{{ old('partita_iva') }}" x-bind:required="isAzienda" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="{{ $isEn ? 'VAT / Tax number' : '11 cifre' }}">
                                        </div>
                                    </div>
                                    <div>
                                        <label for="codice_fiscale" class="block text-sm font-medium text-gray-700">{{ $isEn ? 'Tax ID / Fiscal Code (Optional)' : 'Codice Fiscale (Opzionale)' }}</label>
                                        <div class="mt-1">
                                            <input type="text" id="codice_fiscale" name="codice_fiscale" value="{{ old('codice_fiscale') }}" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        </div>
                                    </div>
                                    <div>
                                        <label for="sdi" class="block text-sm font-medium text-gray-700">{{ $isEn ? 'SDI Recipient Code' : 'Codice Destinatario SDI' }}</label>
                                        <div class="mt-1">
                                            <input type="text" id="sdi" name="sdi" value="{{ old('sdi') }}" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Es. KRRH6B9 (oppure 0000000)">
                                        </div>
                                    </div>
                                    <div>
                                        <label for="pec" class="block text-sm font-medium text-gray-700">{{ $isEn ? 'Company Certified Email (PEC)' : 'PEC Aziendale' }}</label>
                                        <div class="mt-1">
                                            <input type="email" id="pec" name="pec" value="{{ old('pec') }}" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="azienda@pec.it">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Box 4: Metodo di Pagamento -->
                    <div class="bg-white shadow rounded-lg px-4 py-6 sm:p-6 p-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-6 border-b pb-4">{{ $isEn ? 'Payment Method' : 'Metodo di Pagamento' }}</h2>
                        
                        @php
                            $stripe_enabled = \App\Models\Setting::where('key', 'payment_stripe_enabled')->value('value') == '1';
                            $bonifico_enabled = \App\Models\Setting::where('key', 'payment_bonifico_enabled')->value('value') == '1';
                            $contrassegno_enabled = \App\Models\Setting::where('key', 'payment_contrassegno_enabled')->value('value') == '1';
                            $paypal_enabled = \App\Models\Setting::where('key', 'payment_paypal_enabled')->value('value') == '1';

                            $preferred_method = '';
                            if (auth()->check()) {
                                $preferred_method = \App\Models\Customer::where('email', auth()->user()->email)->value('metodo_pagamento_preferito');
                            }
                        @endphp

                        @if(!$stripe_enabled && !$bonifico_enabled && !$contrassegno_enabled && !$paypal_enabled)
                            <div class="p-4 bg-yellow-50 text-yellow-800 rounded border border-yellow-200">
                                {{ $isEn ? 'Currently there are no payment methods available online. Please contact support to complete your order.' : 'Attualmente non ci sono metodi di pagamento disponibili online. Contatta l\'assistenza per completare l\'ordine.' }}
                            </div>
                        @else
                            <div class="space-y-4">
                                @if($stripe_enabled)
                                <div @click="paymentMethod = 'stripe'" class="border rounded px-4 py-3 flex items-center bg-gray-50 border-gray-200 hover:border-indigo-200 transition-colors cursor-pointer" :class="{ 'border-slate-900 bg-white ring-1 ring-slate-900': paymentMethod === 'stripe' }">
                                    <input id="payment_stripe" name="payment_method" type="radio" value="stripe" x-model="paymentMethod" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 cursor-pointer" required>
                                    <label for="payment_stripe" class="ml-3 block text-sm font-medium text-gray-900 flex items-center flex-1 cursor-pointer">
                                        {{ $isEn ? 'Credit Card (Stripe)' : 'Carta di Credito (Stripe)' }}
                                    </label>
                                </div>
                                @endif

                                @if($paypal_enabled)
                                <div @click="paymentMethod = 'paypal'" class="border rounded px-4 py-3 flex items-center bg-gray-50 border-gray-200 hover:border-indigo-200 transition-colors cursor-pointer" :class="{ 'border-slate-900 bg-white ring-1 ring-slate-900': paymentMethod === 'paypal' }">
                                    <input id="payment_paypal" name="payment_method" type="radio" value="paypal" x-model="paymentMethod" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 cursor-pointer" required>
                                    <label for="payment_paypal" class="ml-3 block text-sm font-medium text-gray-900 flex items-center flex-1 cursor-pointer">
                                        PayPal Checkout
                                    </label>
                                </div>
                                @endif

                                @if($bonifico_enabled)
                                <div @click="paymentMethod = 'bonifico'" class="border rounded px-4 py-3 flex items-center bg-gray-50 border-gray-200 hover:border-indigo-200 transition-colors cursor-pointer" :class="{ 'border-slate-900 bg-white ring-1 ring-slate-900': paymentMethod === 'bonifico' }">
                                    <input id="payment_bonifico" name="payment_method" type="radio" value="bonifico" x-model="paymentMethod" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 cursor-pointer" required>
                                    <label for="payment_bonifico" class="ml-3 block text-sm font-medium text-gray-900 flex-1 cursor-pointer">
                                        {{ $isEn ? 'Advance Bank Transfer' : 'Bonifico Bancario Anticipato' }}
                                    </label>
                                </div>
                                @endif

                                @if($contrassegno_enabled)
                                <div x-show="nazione === 'Italia'" @click="paymentMethod = 'contrassegno'" class="border rounded px-4 py-3 flex items-center justify-between bg-gray-50 border-gray-200 hover:border-indigo-200 transition-colors cursor-pointer" :class="{ 'border-slate-900 bg-white ring-1 ring-slate-900': paymentMethod === 'contrassegno' }">
                                    <div class="flex items-center flex-1 cursor-pointer">
                                        <input id="payment_contrassegno" name="payment_method" type="radio" value="contrassegno" x-model="paymentMethod" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 cursor-pointer" required>
                                        <label for="payment_contrassegno" class="ml-3 block text-sm font-medium text-gray-900 flex-1 cursor-pointer">
                                            {{ $isEn ? 'Cash on Delivery' : 'Contrassegno (Pagamento alla Consegna)' }}
                                        </label>
                                    </div>
                                    <span class="text-xs font-bold px-2.5 py-1 rounded-md" :class="contrassegnoFee > 0 ? 'bg-amber-100 text-amber-900' : 'bg-emerald-100 text-emerald-800'">
                                        <span x-show="contrassegnoFee > 0">+ € <span x-text="formatPrice(contrassegnoFee)"></span></span>
                                        <span x-show="contrassegnoFee <= 0">{{ $isEn ? 'Free' : 'Gratuito' }}</span>
                                    </span>
                                </div>
                                <div x-show="nazione !== 'Italia'" class="text-xs text-slate-500 italic px-3 py-2 bg-slate-50 border border-slate-200 rounded">
                                    {{ $isEn ? 'ℹ️ Cash on delivery is only available for orders with delivery in Italy.' : 'ℹ️ Il pagamento in contrassegno è disponibile solo per ordini con consegna in Italia.' }}
                                </div>
                                @endif
                            </div>
                        @endif
                    </div>

                </form>

            </div>

            <!-- Riepilogo Ordine (Sidebar Destra) -->
            <div class="mt-10 lg:mt-0 lg:col-span-5">
                <section aria-labelledby="summary-heading" class="bg-white rounded-lg px-4 py-6 sm:p-6 lg:p-8 shadow border-t-4 border-indigo-600 sticky top-6">
                    <h2 id="summary-heading" class="text-xl font-bold text-gray-900 mb-6">{{ $isEn ? 'Your Order' : 'Il tuo Ordine' }}</h2>

                    <ul role="list" class="divide-y divide-gray-200">
                        @php $totale = 0; @endphp
                        @foreach($cart as $variant_id => $item)
                        @php 
                            $subtotale = $item['prezzo'] * $item['quantita'];
                            $totale += $subtotale;
                        @endphp
                        <li class="py-4 flex">
                            @if(!empty($item['foto']))
                                <img src="{{ Str::startsWith($item['foto'], 'http') ? $item['foto'] : url('/') . $item['foto'] }}" class="h-16 w-16 rounded border object-cover">
                            @else
                                <div class="h-16 w-16 rounded bg-gray-100 flex items-center justify-center border">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif
                            <div class="ml-4 flex-1 flex flex-col justify-between">
                                <div>
                                    <div class="flex justify-between text-sm font-medium text-gray-900">
                                        <h4>{{ $item['prodotto_nome'] }}</h4>
                                        <p class="ml-4">€ {{ number_format($subtotale, 2, ',', '.') }}</p>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">
                                        @if(!empty($item['colore'])){{ $item['colore'] }}@endif 
                                        @if(!empty($item['colore']) && !empty($item['taglia'])) / @endif 
                                        @if(!empty($item['taglia'])){{ $item['taglia'] }}@endif
                                    </p>
                                </div>
                                <div class="flex flex-1 items-end justify-between text-sm">
                                    <p class="text-gray-500">{{ $isEn ? 'Qty' : 'Q.tà' }} {{ $item['quantita'] }}</p>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>

                    <dl class="mt-6 space-y-4 border-t border-gray-200 pt-6">
                        <div class="flex items-center justify-between text-sm">
                            <dt class="text-gray-600">{{ $isEn ? 'Subtotal' : 'Subtotale' }}</dt>
                            <dd class="font-medium text-gray-900">€ <span x-text="formatPrice(subtotal)"></span></dd>
                        </div>
                        <template x-if="discount > 0">
                            <div class="flex items-center justify-between text-sm text-emerald-700 bg-emerald-50 p-2.5 rounded-lg border border-emerald-200">
                                <dt class="font-bold flex items-center gap-1.5">
                                    <span>🏷️</span>
                                    <span>{{ $isEn ? 'Discount Coupon' : 'Sconto Coupon' }}</span>
                                    <span class="font-mono text-xs uppercase" x-text="'(' + couponCode + ')'"></span>
                                </dt>
                                <dd class="font-extrabold text-emerald-800">- € <span x-text="formatPrice(discount)"></span></dd>
                            </div>
                        </template>
                        <div class="flex items-center justify-between text-sm">
                            <dt class="text-gray-600">{{ $isEn ? 'Shipping' : 'Spedizione' }}</dt>
                            <dd class="font-medium text-gray-900 group relative">
                                <span x-show="shippingCost > 0">€ <span x-text="formatPrice(shippingCost)"></span></span>
                                <span x-show="shippingCost == 0" class="text-emerald-600 font-bold uppercase tracking-wider text-xs">{{ $isEn ? 'FREE' : 'Gratis' }}</span>
                                
                                <template x-if="isFreeEligible() && freeThreshold > 0 && subtotal < freeThreshold">
                                    <div class="absolute -top-10 right-0 bg-slate-800 text-white text-[10px] px-2 py-1 rounded shadow-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity">
                                        @if($isEn)
                                            Free with another € <span x-text="formatPrice(freeThreshold - subtotal)"></span><span x-text="freeShippingScope === 'all' ? '' : ' in Italy'"></span>
                                        @else
                                            Gratis con altri € <span x-text="formatPrice(freeThreshold - subtotal)"></span><span x-text="freeShippingScope === 'all' ? '' : ' in Italia'"></span>
                                        @endif
                                    </div>
                                </template>
                            </dd>
                        </div>
                        <template x-if="nazione === 'Italia' && paymentMethod === 'contrassegno'">
                            <div class="flex items-center justify-between text-sm text-amber-900 bg-amber-50 p-2.5 rounded-lg border border-amber-200">
                                <dt class="font-bold flex items-center gap-1.5">
                                    <span>📦 {{ $isEn ? 'Cash on Delivery' : 'Contrassegno' }}</span>
                                </dt>
                                <dd class="font-extrabold text-amber-950">
                                    <span x-show="contrassegnoFee > 0">+ € <span x-text="formatPrice(contrassegnoFee)"></span></span>
                                    <span x-show="contrassegnoFee <= 0" class="text-emerald-700">{{ $isEn ? 'Free' : 'Gratuito' }}</span>
                                </dd>
                            </div>
                        </template>
                        <div class="flex items-center justify-between border-t border-gray-200 pt-4">
                            <dt class="text-base font-bold text-gray-900">{{ $isEn ? 'Total to Pay' : 'Totale Da Pagare' }}</dt>
                            <dd class="text-2xl font-black text-black">€ <span x-text="formatPrice(Math.max(0, parseFloat(subtotal) - parseFloat(discount)) + parseFloat(shippingCost) + (nazione === 'Italia' && paymentMethod === 'contrassegno' ? parseFloat(contrassegnoFee || 0) : 0))"></span></dd>
                        </div>
                    </dl>

                    <div class="mt-8">
                        <button type="submit" form="checkout-form" 
                                class="w-full flex justify-center items-center h-14 uppercase tracking-widest text-center transition-all cursor-pointer shadow-md"
                                onmouseover="this.style.backgroundColor='#c94500'" 
                                onmouseout="this.style.backgroundColor='#e35205'"
                                style="background-color: #e35205 !important; color: #ffffff !important; font-family: 'Bebas Neue', 'Montserrat', sans-serif !important; font-size: 22px !important; letter-spacing: 1px !important; border: none !important; border-radius: 0 !important; font-weight: bold !important;">
                            {{ $isEn ? 'CONFIRM AND PAY →' : 'CONFERMA E PAGA →' }}
                        </button>
                    </div>
                </section>
            </div>
            
        </div>
    </div>
</div>

@push('scripts')
<script>
    function checkoutForm() {
        return {
            checkoutMode: '{{ Auth::check() ? 'login' : (isset($errors) && ($errors->has('password') || old('checkout_mode') === 'register') ? 'register' : 'guest') }}',
            isAzienda: {{ old('is_azienda') ? 'true' : 'false' }},
            nazione: '{{ old('nazione', 'Italia') }}',
            subtotal: {{ $totale ?? 0 }},
            discount: {{ (float)($scontoCoupon ?? 0) }},
            couponCode: '{{ $appliedCoupon["code"] ?? "" }}',
            shippingCost: 0,
            freeThreshold: {{ $freeThreshold }},
            freeShippingScope: '{{ $freeShippingScope ?? "italy" }}',
            contrassegnoFee: {{ (float)($contrassegnoFee ?? 10.00) }},
            paymentMethod: '{{ old('payment_method', $preferred_method ?: 'stripe') }}',
            costs: @json($shippingCosts->pluck('costo', 'nazione')),

            password: '{{ old('password', '') }}',
            passwordConfirmation: '{{ old('password_confirmation', '') }}',
            showPassword: false,
            showConfirmation: false,
            generatedPassword: '',
            copiedPassword: false,

            hasLength() {
                return (this.password || '').length >= 8;
            },
            hasUpper() {
                return /[A-Z]/.test(this.password || '');
            },
            hasNumber() {
                return /[0-9]/.test(this.password || '');
            },
            hasSpecial() {
                return /[-!@#$%^&*()_+=\[\]{};':"\\|,.<>\/?~]/.test(this.password || '');
            },
            isMatching() {
                return (this.password || '').length > 0 && this.password === this.passwordConfirmation;
            },
            generatePassword() {
                const uppers = "ABCDEFGHJKLMNPQRSTUVWXYZ";
                const lowers = "abcdefghijkmnopqrstuvwxyz";
                const numbers = "23456789";
                const symbols = "!@#$%^&*_-+=";

                let pass = "";
                for (let i = 0; i < 2; i++) {
                    pass += uppers.charAt(Math.floor(Math.random() * uppers.length));
                    pass += lowers.charAt(Math.floor(Math.random() * lowers.length));
                    pass += numbers.charAt(Math.floor(Math.random() * numbers.length));
                    pass += symbols.charAt(Math.floor(Math.random() * symbols.length));
                }
                const allChars = uppers + lowers + numbers + symbols;
                for (let i = 0; i < 4; i++) {
                    pass += allChars.charAt(Math.floor(Math.random() * allChars.length));
                }

                // Shuffle
                pass = pass.split('').sort(() => 0.5 - Math.random()).join('');

                this.password = pass;
                this.passwordConfirmation = pass;
                this.generatedPassword = pass;
                this.showPassword = true;
                this.showConfirmation = true;

                this.$nextTick(() => {
                    const p = document.getElementById('password');
                    const pc = document.getElementById('password_confirmation');
                    if (p) p.value = pass;
                    if (pc) pc.value = pass;
                });
            },
            copyPassword() {
                if (!this.generatedPassword) return;
                navigator.clipboard.writeText(this.generatedPassword).then(() => {
                    this.copiedPassword = true;
                    setTimeout(() => {
                        this.copiedPassword = false;
                    }, 2500);
                });
            },
            
            init() {
                this.updateShipping();
            },
            
            isFreeEligible() {
                return (this.freeShippingScope === 'all') || (this.nazione && this.nazione.toLowerCase() === 'italia');
            },

            updateShipping() {
                let cost = this.costs[this.nazione] !== undefined ? parseFloat(this.costs[this.nazione]) : 20.00;
                
                if (this.isFreeEligible() && this.freeThreshold > 0 && this.subtotal >= this.freeThreshold) {
                    this.shippingCost = 0;
                } else {
                    this.shippingCost = cost;
                }

                if (this.nazione !== 'Italia' && this.paymentMethod === 'contrassegno') {
                    this.paymentMethod = 'stripe';
                }
            },
            
            formatPrice(price) {
                return parseFloat(price).toLocaleString('it-IT', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
        }
    }
</script>
@endpush
@endsection
