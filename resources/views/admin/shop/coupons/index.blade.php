<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestione Coupon Shop</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Header Sezione -->
            <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h3 class="text-2xl font-extrabold text-slate-900 flex items-center gap-2">
                        <span>🎟️</span>
                        <span>Coupon e Codici Sconto</span>
                    </h3>
                    <p class="text-sm text-slate-500 mt-1">Crea e gestisci i coupon utilizzabili dai clienti nel carrello (sconto fisso o %, sempre attivi o mono-uso).</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('admin.shop.coupons.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-400 hover:bg-amber-500 text-slate-900 font-black text-xs uppercase tracking-wider rounded-xl shadow-sm transition-all cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Nuovo Coupon</span>
                    </a>
                </div>
            </div>

            <!-- Flash Message -->
            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-400 p-4 rounded-md shadow-sm">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-emerald-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Totale Coupon</p>
                        <p class="text-3xl font-black text-slate-900 mt-1">{{ number_format($totalCoupons, 0, ',', '.') }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl font-bold">
                        🏷️
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Coupon Attivi</p>
                        <p class="text-3xl font-black text-emerald-600 mt-1">{{ number_format($activeCoupons, 0, ',', '.') }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                        ✨
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Utilizzi Totali</p>
                        <p class="text-3xl font-black text-amber-500 mt-1">{{ number_format($totalUsages, 0, ',', '.') }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold">
                        🛒
                    </div>
                </div>
            </div>

            <!-- Ricerca -->
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 mb-6">
                <form method="GET" action="{{ route('admin.shop.coupons.index') }}" class="flex flex-wrap items-center gap-4">
                    <div class="flex-1 min-w-[240px]">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Cerca per codice o nome..." 
                               class="w-full text-sm border-slate-200 rounded-xl focus:ring-amber-400 focus:border-amber-400">
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-black text-white text-xs font-bold uppercase tracking-wider rounded-xl transition cursor-pointer">
                        Cerca
                    </button>
                    @if(request()->has('search'))
                        <a href="{{ route('admin.shop.coupons.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 transition">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- Tabella Coupon -->
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                        <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                            <tr>
                                <th class="py-3.5 px-6 text-left font-bold">Codice Coupon</th>
                                <th class="py-3.5 px-6 text-left font-bold">Nome / Descrizione</th>
                                <th class="py-3.5 px-6 text-center font-bold">Valore Sconto</th>
                                <th class="py-3.5 px-6 text-center font-bold">Spesa Minima</th>
                                <th class="py-3.5 px-6 text-center font-bold">Comportamento</th>
                                <th class="py-3.5 px-6 text-center font-bold">Utilizzi</th>
                                <th class="py-3.5 px-6 text-center font-bold">Stato</th>
                                <th class="py-3.5 px-6 text-right font-bold">Azioni</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($coupons as $coupon)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-4 px-6">
                                        <span class="font-mono font-bold text-sm bg-amber-50 text-amber-900 border border-amber-200 px-2.5 py-1 rounded-md tracking-wider">
                                            {{ $coupon->code }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 font-medium text-slate-900">
                                        {{ $coupon->name ?: '—' }}
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        @if($coupon->type === 'percentuale')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                {{ rtrim(rtrim(number_format($coupon->valore, 2, '.', ''), '0'), '.') }}%
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                € {{ number_format($coupon->valore, 2, ',', '.') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-center text-slate-600">
                                        @if($coupon->min_spesa > 0)
                                            € {{ number_format($coupon->min_spesa, 2, ',', '.') }}
                                        @else
                                            <span class="text-xs text-slate-400">Nessuna</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        @if($coupon->sempre_attivo)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                <span>🔁</span>
                                                <span>Sempre Attivo</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                                <span>🎯</span>
                                                <span>Mono-uso</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-center font-bold text-slate-800">
                                        {{ $coupon->utilizzi }}
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <form action="{{ route('admin.shop.coupons.toggle', $coupon) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="cursor-pointer inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold transition {{ $coupon->attivo ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-rose-100 text-rose-800 hover:bg-rose-200' }}">
                                                {{ $coupon->attivo ? '● Attivo' : '○ Disattivo' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="py-4 px-6 text-right space-x-2">
                                        <a href="{{ route('admin.shop.coupons.edit', $coupon) }}" class="text-indigo-600 hover:text-indigo-900 font-bold text-xs uppercase tracking-wider transition">
                                            Modifica
                                        </a>
                                        <form action="{{ route('admin.shop.coupons.destroy', $coupon) }}" method="POST" class="inline-block" onsubmit="return confirm('Confermi l\'eliminazione definitiva di questo coupon?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:text-rose-900 font-bold text-xs uppercase tracking-wider transition cursor-pointer">
                                                Elimina
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-12 text-center text-slate-400">
                                        <div class="text-4xl mb-2">🎟️</div>
                                        <p class="font-medium">Nessun coupon trovato.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($coupons->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $coupons->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
