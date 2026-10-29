@extends('public.layouts.main')

@php
    $isEn = app()->getLocale() === 'en';
@endphp

@section('title', ($isEn ? 'Newsletter Subscription Confirmed - ' : 'Iscrizione Newsletter Confermata - ') . config('app.name'))

@section('content')
<div class="bg-gray-50 min-h-[70vh] flex items-center justify-center py-16 px-4 sm:px-6 lg:px-8">
    <div class="max-w-xl w-full bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100 text-center">
        
        <!-- Header Banner -->
        <div class="bg-black py-8 px-6 border-b-4 border-[#fedb00]">
            <a href="{{ route('public.home') }}" class="inline-block">
                <img src="https://bicap.shop/images/bicap-logo-popup.png" alt="BICAP" class="h-10 mx-auto object-contain">
            </a>
        </div>

        <div class="p-8 sm:p-10">
            @if(!empty($alreadyConfirmed))
                <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-5 text-2xl font-bold">
                    ℹ️
                </div>
                <h1 class="text-2xl font-black text-gray-900 uppercase tracking-wide mb-3" style="font-family: 'Bebas Neue', 'Montserrat', sans-serif; font-size: 32px; letter-spacing: 1px;">
                    {{ $isEn ? 'Already Confirmed' : 'Iscrizione Già Confermata' }}
                </h1>
                <p class="text-sm text-gray-600 leading-relaxed mb-6 font-medium">
                    {{ $isEn ? 'Your newsletter subscription has already been confirmed.' : 'La tua iscrizione alla newsletter risulta già confermata con successo.' }}
                </p>
            @else
                <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-5 text-2xl font-bold">
                    🎉
                </div>
                <h1 class="text-2xl font-black text-gray-900 uppercase tracking-wide mb-3" style="font-family: 'Bebas Neue', 'Montserrat', sans-serif; font-size: 34px; letter-spacing: 1px;">
                    {{ $isEn ? 'Subscription Confirmed!' : 'Iscrizione Confermata!' }}
                </h1>
                <p class="text-sm text-gray-600 leading-relaxed mb-6 font-medium">
                    @if($isEn)
                        Thank you for verifying your email address! Welcome to the <strong>BICAP</strong> community.<br>
                        Here is your exclusive welcome coupon code:
                    @else
                        Grazie per aver confermato il tuo indirizzo email! Benvenuto nella community di <strong>BICAP</strong>.<br>
                        Ecco il tuo codice coupon sconto esclusivo di benvenuto:
                    @endif
                </p>
            @endif

            @if(!empty($coupon))
                <!-- Coupon Box -->
                <div class="bg-[#fffdf0] border-2 border-dashed border-[#fedb00] rounded-xl p-6 mb-6">
                    <span class="text-xs font-black uppercase tracking-widest text-[#7a6100]">
                        {{ $isEn ? '★ YOUR WELCOME COUPON ★' : '★ IL TUO COUPON DI BENVENUTO ★' }}
                    </span>
                    <div class="my-3">
                        <span class="inline-block font-mono font-black text-2xl sm:text-3xl bg-white text-black px-6 py-2.5 rounded-lg border-2 border-black tracking-wider shadow-sm select-all">
                            {{ $coupon->code }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-600 leading-normal">
                        @if($isEn)
                            5% discount on your next order on a minimum purchase of €100.<br>
                            <span class="text-[11px] text-gray-500">A copy of this coupon has also been sent to your email.</span>
                        @else
                            Sconto del 5% sul tuo prossimo ordine con spesa minima di 100€.<br>
                            <span class="text-[11px] text-gray-500">Una copia di questo coupon è stata inviata anche alla tua email.</span>
                        @endif
                    </p>
                </div>
            @endif

            <div class="mt-8 flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('public.shop.index') }}" 
                   class="inline-flex justify-center items-center h-12 px-8 uppercase tracking-widest text-center transition-all cursor-pointer font-bold"
                   style="background-color: #fedb00 !important; color: #000000 !important; font-family: 'Bebas Neue', 'Montserrat', sans-serif !important; font-size: 20px !important; letter-spacing: 1px !important; text-decoration: none !important;">
                    {{ $isEn ? 'SHOP NOW →' : 'INIZIA LO SHOPPING →' }}
                </a>
                <a href="{{ route('public.home') }}" 
                   class="inline-flex justify-center items-center h-12 px-6 uppercase tracking-wider text-xs font-bold text-gray-700 hover:text-black border border-gray-300 transition-colors">
                    {{ $isEn ? 'Back to Home' : 'Torna alla Home' }}
                </a>
            </div>

        </div>
    </div>
</div>
@endsection

