@extends('public.layouts.main')

@php
    $isEn = app()->getLocale() === 'en';
    $totale = $totale ?? 0;
@endphp

@section('title', ($isEn ? 'Your Cart - ' : 'Il Tuo Carrello - ') . config('app.name'))

@section('content')
<div class="bg-gray-50 min-h-screen pt-12 pb-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex items-center space-x-2 text-sm text-gray-500 mb-8">
            <a href="{{ route('public.home') }}" class="hover:text-gray-900">Home</a>
            <span>/</span>
            <a href="{{ route('public.home') }}" class="hover:text-gray-900">Shop</a>
            <span>/</span>
            <span class="text-gray-900 font-medium">{{ $isEn ? 'Cart' : 'Carrello' }}</span>
        </div>

        <h1 class="text-3xl font-extrabold text-gray-900 mb-8">{{ $isEn ? 'Your Cart' : 'Il Tuo Carrello' }}</h1>

        @if(session('success'))
            <div class="mb-6 bg-green-100 text-green-700 px-4 py-3 rounded text-sm font-bold border border-green-200">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 bg-red-100 text-red-700 px-4 py-3 rounded text-sm font-bold border border-red-200">
                {{ session('error') }}
            </div>
        @endif

        @if(empty($cart))
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900">{{ $isEn ? 'Your cart is empty' : 'Il tuo carrello è vuoto' }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ $isEn ? 'Add products to proceed with the purchase.' : 'Aggiungi dei prodotti per poter procedere all\'acquisto.' }}</p>
                <div class="mt-6">
                    <a href="{{ route('public.home') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-bold rounded-none shadow-sm text-white bg-black hover:bg-zinc-800 transition">
                        {{ $isEn ? 'Start Shopping' : 'Inizia lo Shopping' }}
                    </a>
                </div>
            </div>
        @else
            <div class="lg:grid lg:grid-cols-12 lg:gap-x-12 lg:items-start" x-data="cartSummary()">
                
                <div class="lg:col-span-8">
                    <ul role="list" class="border-t border-b border-gray-200 divide-y divide-gray-200 bg-white shadow rounded-lg px-6">
                        @php $totale = 0; @endphp
                        @foreach($cart as $variant_id => $item)
                            @php 
                                $subtotale = $item['prezzo'] * $item['quantita'];
                                $totale += $subtotale;
                            @endphp
                            <li class="flex py-6 sm:py-10">
                                <div class="flex-shrink-0">
                                    @if(!empty($item['foto']))
                                        <img src="{{ Str::startsWith($item['foto'], 'http') ? $item['foto'] : url('/') . $item['foto'] }}" class="w-24 h-24 rounded-md object-contain object-center sm:w-32 sm:h-32 border bg-white p-1">
                                    @else
                                        <div class="w-24 h-24 rounded-md bg-gray-100 sm:w-32 sm:h-32 flex items-center justify-center border">
                                            <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif
                                </div>

                                <div class="ml-4 flex-1 flex flex-col justify-between sm:ml-6">
                                    <div class="relative pr-9 sm:grid sm:grid-cols-2 sm:gap-x-6 sm:pr-0">
                                        <div>
                                            <div class="flex justify-between">
                                                <h3 class="text-base">
                                                    <a href="{{ !empty($item['b2b_product_id']) ? route('public.product.detail', $item['b2b_product_id']) : '#' }}" class="font-bold text-gray-900 hover:text-[#e35205] transition-colors">
                                                        {{ $item['prodotto_nome'] }}
                                                    </a>
                                                </h3>
                                            </div>
                                            <div class="mt-1 flex text-sm">
                                                @if(!empty($item['colore']))
                                                    <p class="text-gray-500">{{ $isEn ? 'Color:' : 'Colore:' }} {{ $item['colore'] }}</p>
                                                @endif
                                                @if(!empty($item['taglia']))
                                                    <p class="ml-4 pl-4 border-l border-gray-200 text-gray-500">{{ $isEn ? 'Size:' : 'Taglia:' }} {{ $item['taglia'] }}</p>
                                                @endif
                                            </div>
                                            <p class="mt-1 text-sm font-medium text-gray-900">€ {{ number_format($item['prezzo'], 2, ',', '.') }} x {{ $item['quantita'] }}</p>
                                        </div>

                                        <div class="mt-4 sm:mt-0 sm:pr-9">
                                            <label for="quantity-{{ $variant_id }}" class="sr-only">{{ $isEn ? 'Quantity,' : 'Quantità,' }} {{ $item['prodotto_nome'] }}</label>
                                            <p class="text-sm font-bold text-gray-900">{{ $isEn ? 'Subtotal:' : 'Subtotale:' }} € {{ number_format($subtotale, 2, ',', '.') }}</p>

                                            <div class="absolute top-0 right-0">
                                                <form action="{{ route('public.shop.cart.remove', $variant_id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="-m-2 p-2 inline-flex text-gray-400 hover:text-red-500 cursor-pointer">
                                                        <span class="sr-only">{{ $isEn ? 'Remove' : 'Rimuovi' }}</span>
                                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                          <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mt-4 flex text-sm text-gray-700 space-x-2">
                                        <svg class="flex-shrink-0 h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        <span>{{ $isEn ? 'Available for shipping' : 'Disponibile per la spedizione' }}</span>
                                    </p>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-6 flex items-center justify-between">
                        <a href="{{ route('public.home') }}" class="inline-flex items-center gap-2 text-xs sm:text-sm font-bold uppercase tracking-wider text-black hover:text-[#e35205] transition-colors py-2">
                            <span>&larr; {{ $isEn ? 'Continue Shopping' : 'Continua lo Shopping' }}</span>
                        </a>
                    </div>
                </div>

                <!-- Riepilogo Ordine -->
                <section aria-labelledby="summary-heading" class="mt-16 bg-white rounded-lg px-4 py-6 sm:p-6 lg:p-8 lg:mt-0 lg:col-span-4 shadow border-t-2 border-indigo-500 sticky top-6">
                    <h2 id="summary-heading" class="text-xl font-bold text-gray-900 border-b pb-4 mb-6">{{ $isEn ? 'Order Summary' : 'Riepilogo Ordine' }}</h2>

                    <div class="mb-6">
                        <label for="estimate_nation" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ $isEn ? 'Destination for estimate' : 'Destinazione per la stima' }}</label>
                        <select id="estimate_nation" x-model="nazione" @change="updateShipping()" class="w-full bg-slate-50 border-slate-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 transition-all p-3">
                            <option value="">{{ $isEn ? 'Select country...' : 'Seleziona nazione...' }}</option>
                            @foreach($shippingCosts as $cost)
                                <option value="{{ $cost->nazione }}">{{ $cost->nazione }}</option>
                            @endforeach
                        </select>
                    </div>

                    <dl class="mt-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <dt class="text-sm text-gray-600">{{ $isEn ? 'Products Subtotal' : 'Subtotale Prodotti' }}</dt>
                            <dd class="text-sm font-bold text-gray-900">€ <span x-text="formatPrice(subtotal)"></span></dd>
                        </div>
                        <div class="flex items-center justify-between border-t border-gray-100 pt-4 group relative">
                            <dt class="text-sm text-gray-600 flex items-center">
                                {{ $isEn ? 'Estimated shipping' : 'Spedizione stimata' }}
                                <svg class="w-3 h-3 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </dt>
                            <dd class="text-sm font-bold text-gray-900">
                                <span x-show="shippingCost > 0">€ <span x-text="formatPrice(shippingCost)"></span></span>
                                <span x-show="shippingCost == 0" class="text-emerald-600 uppercase tracking-widest text-xs">{{ $isEn ? 'FREE' : 'Gratis' }}</span>
                            </dd>

                            <template x-if="isFreeEligible() && freeThreshold > 0 && subtotal < freeThreshold">
                                <div class="absolute -top-10 right-0 bg-slate-800 text-white text-[10px] px-2 py-2 rounded shadow-xl whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all z-20">
                                    @if($isEn)
                                        Add € <span x-text="formatPrice(freeThreshold - subtotal)"></span> for <span class="text-emerald-400 font-bold">FREE</span> shipping<span x-text="freeShippingScope === 'all' ? '' : ' in Italy'"></span>!
                                    @else
                                        Aggiungi € <span x-text="formatPrice(freeThreshold - subtotal)"></span> per la spedizione <span class="text-emerald-400 font-bold">Gratis</span><span x-text="freeShippingScope === 'all' ? '' : ' in Italy'"></span>!
                                    @endif
                                </div>
                            </template>
                        </div>
                        <div class="flex items-center justify-between border-t border-gray-200 pt-4">
                            <dt class="text-base font-bold text-gray-900">{{ $isEn ? 'Order Total' : 'Totale Ordine' }}</dt>
                            <dd class="text-2xl font-black text-black">€ <span x-text="formatPrice(parseFloat(subtotal) + parseFloat(shippingCost))"></span></dd>
                        </div>
                    </dl>

                    <div class="mt-8">
                        <a href="{{ route('public.shop.cart.checkout') }}" 
                           class="w-full flex justify-center items-center h-14 uppercase tracking-widest text-center transition-all cursor-pointer shadow-md"
                           onmouseover="this.style.backgroundColor='#c94500'" 
                           onmouseout="this.style.backgroundColor='#e35205'"
                           style="background-color: #e35205 !important; color: #ffffff !important; font-family: 'Bebas Neue', 'Montserrat', sans-serif !important; font-size: 22px !important; letter-spacing: 1px !important; text-decoration: none !important; border: none !important; border-radius: 0 !important; font-weight: bold !important;">
                            {{ $isEn ? 'PROCEED TO CHECKOUT →' : 'PROCEDI AL CHECKOUT →' }}
                        </a>
                    </div>
                </section>
            </div>
        @endif
        
    </div>
</div>
@push('scripts')
<script>
    function cartSummary() {
        return {
            nazione: 'Italia',
            subtotal: {{ $totale ?? 0 }},
            shippingCost: 0,
            freeThreshold: {{ $freeThreshold }},
            freeShippingScope: '{{ $freeShippingScope ?? "italy" }}',
            costs: @json($shippingCosts->pluck('costo', 'nazione')),
            
            init() {
                this.updateShipping();
            },

            isFreeEligible() {
                return (this.freeShippingScope === 'all') || (this.nazione && this.nazione.toLowerCase() === 'italia');
            },
            
            updateShipping() {
                if (!this.nazione) {
                    this.shippingCost = 0;
                    return;
                }
                let cost = this.costs[this.nazione] !== undefined ? parseFloat(this.costs[this.nazione]) : 20.00;
                
                if (this.isFreeEligible() && this.freeThreshold > 0 && this.subtotal >= this.freeThreshold) {
                    this.shippingCost = 0;
                } else {
                    this.shippingCost = cost;
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
