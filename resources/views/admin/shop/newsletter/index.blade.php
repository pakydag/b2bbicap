<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Newsletter Shop</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Header Sezione -->
            <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h3 class="text-2xl font-extrabold text-slate-900 flex items-center gap-2">
                        <span>✉️</span>
                        <span>Iscritti alla Newsletter</span>
                    </h3>
                    <p class="text-sm text-slate-500 mt-1">Elenco degli utenti iscritti tramite il form newsletter sul sito web, con data/orario ed indirizzo IP.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('admin.shop.newsletter.export') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-sm transition-all cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Esporta CSV</span>
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
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Totale Iscritti</p>
                        <p class="text-3xl font-black text-slate-900 mt-1">{{ number_format($totalCount, 0, ',', '.') }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl font-bold">
                        📬
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Iscritti Attivi</p>
                        <p class="text-3xl font-black text-emerald-600 mt-1">{{ number_format($activeCount, 0, ',', '.') }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                        ✅
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Iscritti Oggi</p>
                        <p class="text-3xl font-black text-amber-500 mt-1">{{ number_format($todayCount, 0, ',', '.') }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold">
                        ⭐
                    </div>
                </div>
            </div>

            <!-- Filtri di ricerca -->
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 mb-6">
                <form method="GET" action="{{ route('admin.shop.newsletter.index') }}" class="flex flex-wrap items-center gap-4">
                    <div class="flex-1 min-w-[240px]">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Cerca per email o indirizzo IP..." 
                               class="w-full text-sm border-slate-200 rounded-xl focus:ring-amber-400 focus:border-amber-400">
                    </div>
                    <div>
                        <select name="status" class="text-sm border-slate-200 rounded-xl focus:ring-amber-400 focus:border-amber-400">
                            <option value="">Tutti gli stati</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Solo attivi</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Disiscritti</option>
                        </select>
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-black text-white text-xs font-bold uppercase tracking-wider rounded-xl transition cursor-pointer">
                        Filtra
                    </button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('admin.shop.newsletter.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 transition">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- Tabella Iscritti -->
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                        <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                            <tr>
                                <th class="py-3.5 px-6 text-left font-bold">Indirizzo E-mail</th>
                                <th class="py-3.5 px-6 text-left font-bold">Coupon Assegnato</th>
                                <th class="py-3.5 px-6 text-left font-bold">Data e Ora Iscrizione</th>
                                <th class="py-3.5 px-6 text-left font-bold">Indirizzo IP</th>
                                <th class="py-3.5 px-6 text-center font-bold">Lingua</th>
                                <th class="py-3.5 px-6 text-center font-bold">Stato</th>
                                <th class="py-3.5 px-6 text-right font-bold">Azioni</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($subscribers as $sub)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-4 px-6 font-bold text-slate-900">
                                        {{ $sub->email }}
                                    </td>
                                    <td class="py-4 px-6">
                                        @if($sub->coupon_code)
                                            <span class="font-mono text-xs font-bold bg-amber-50 text-amber-900 px-2.5 py-1 rounded-md border border-amber-200">
                                                {{ $sub->coupon_code }}
                                            </span>
                                        @else
                                            <span class="text-xs text-slate-400">—</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-slate-600">
                                        <div class="flex items-center gap-1.5 font-medium">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <span>{{ $sub->created_at ? $sub->created_at->format('d/m/Y H:i:s') : 'N/D' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="font-mono text-xs bg-slate-100 text-slate-700 px-2.5 py-1 rounded-md border border-slate-200">
                                            {{ $sub->ip_address ?: 'Non rilevato' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold uppercase bg-slate-100 text-slate-800">
                                            {{ strtoupper($sub->locale ?: 'IT') }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        @if($sub->active)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                ● Iscritto
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                                Cancellato
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <form action="{{ route('admin.shop.newsletter.destroy', $sub) }}" method="POST" class="inline-block" onsubmit="return confirm('Confermi la rimozione di questo indirizzo dalla lista newsletter?');">
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
                                    <td colspan="7" class="py-12 text-center text-slate-400">
                                        <div class="text-4xl mb-2">📭</div>
                                        <p class="font-medium">Nessun iscritto alla newsletter presente.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($subscribers->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $subscribers->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>

