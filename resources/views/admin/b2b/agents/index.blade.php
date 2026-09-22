<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                    {{ __('Gestione Agenti B2B') }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5 font-bold">Rete agenti, assegnazione brand e portafoglio clienti</p>
            </div>
            <a href="{{ route('admin.b2b.agents.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-black hover:bg-yellow-400 hover:text-slate-950 text-white font-black text-xs uppercase tracking-wider rounded-xl shadow transition">
                <span>➕ Aggiungi Agente</span>
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-2xl shadow-sm text-sm font-bold flex items-center gap-2" role="alert">
                <span>✅</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden min-w-0">
            
            <!-- Vista Mobile (< sm) -->
            <div class="sm:hidden divide-y divide-gray-100">
                @forelse($agents as $agent)
                    <div class="p-4 sm:p-5 space-y-3 relative hover:bg-gray-50/60 transition">
                        <div class="flex justify-between items-start gap-2">
                            <div>
                                <h3 class="text-sm font-black text-slate-900 leading-tight">
                                    {{ $agent->name }} {{ $agent->surname }}
                                </h3>
                                <div class="text-xs text-gray-500 space-y-0.5 mt-1 font-medium">
                                    <p>📧 {{ $agent->email }}</p>
                                    @if($agent->phone)
                                        <p>📞 {{ $agent->phone }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <a href="{{ route('admin.b2b.agents.edit', $agent) }}" class="p-2 bg-gray-100 hover:bg-slate-900 hover:text-white rounded-xl transition text-gray-700" title="Modifica Agente">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('admin.b2b.agents.destroy', $agent) }}" method="POST" onsubmit="return confirm('Sicuro di voler rimuovere questo agente? L\'account verrà eliminato.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 bg-rose-50 hover:bg-rose-600 hover:text-white rounded-xl transition text-rose-600" title="Elimina">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider block">Brand Abilitati</span>
                            <div class="flex flex-wrap gap-1">
                                @foreach($agent->b2bBrands as $brand)
                                    <span class="px-2 py-0.5 bg-indigo-50 border border-indigo-100 text-indigo-800 rounded-lg text-[10px] font-bold">{{ $brand->name }}</span>
                                @endforeach
                                @if($agent->b2bBrands->isEmpty())
                                    <span class="text-xs text-gray-400 italic">Nessun brand assegnato</span>
                                @endif
                            </div>
                        </div>

                        <div class="pt-2 flex flex-col sm:flex-row gap-2 justify-between items-start sm:items-center border-t border-gray-100">
                            <span class="text-xs font-bold text-gray-600">
                                👥 <strong class="text-slate-900">{{ $agent->b2bCustomers->count() }}</strong> clienti assegnati
                            </span>
                            <a href="{{ route('admin.b2b.orders.index', ['agent_id' => $agent->id]) }}" class="w-full sm:w-auto inline-flex items-center justify-between sm:justify-center gap-2 px-3.5 py-1.5 bg-yellow-400 hover:bg-yellow-300 text-slate-950 font-black rounded-xl text-xs uppercase tracking-wider transition shadow-sm">
                                <span>📊 {{ $agent->b2b_orders_count ?? 0 }} Ordini</span>
                                <span>€ {{ number_format($agent->b2b_orders_sum_total_amount ?? 0, 2, ',', '.') }} ↗</span>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm font-bold text-gray-400">Nessun agente registrato.</div>
                @endforelse
            </div>

            <!-- Vista Desktop & Tablet (>= sm) Table -->
            <div class="hidden sm:block overflow-x-auto min-w-0">
                <table class="w-full text-left divide-y divide-gray-100 border-collapse">
                    <thead class="bg-gray-50/80 text-[11px] font-black text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3">Agente</th>
                            <th class="px-3 py-3">Contatti</th>
                            <th class="px-3 py-3">Brand Abilitati</th>
                            <th class="px-3 py-3 text-center">Clienti</th>
                            <th class="px-4 py-3 text-right">Ordini & Totale</th>
                            <th class="px-4 py-3 text-right">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs">
                        @forelse($agents as $agent)
                            <tr class="hover:bg-yellow-50/40 transition">
                                <td class="px-4 py-2.5 font-black text-slate-900 text-sm whitespace-nowrap">
                                    {{ $agent->name }} {{ $agent->surname }}
                                </td>
                                <td class="px-3 py-2.5 text-gray-600 whitespace-nowrap">
                                    <div class="font-bold text-slate-800">{{ $agent->email }}</div>
                                    <div class="text-[11px] text-gray-400">{{ $agent->phone ?? '-' }}</div>
                                </td>
                                <td class="px-3 py-2.5">
                                    <div class="flex flex-wrap gap-1 max-w-xs">
                                        @foreach($agent->b2bBrands as $brand)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-bold bg-indigo-50 text-indigo-800 border border-indigo-100">
                                                {{ $brand->name }}
                                            </span>
                                        @endforeach
                                        @if($agent->b2bBrands->isEmpty())
                                            <span class="text-gray-400 italic">Nessun brand</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-3 py-2.5 text-center whitespace-nowrap">
                                    @if($agent->b2bCustomers->count() > 0)
                                        <span class="font-black text-slate-900 bg-gray-100 px-2.5 py-1 rounded-lg">{{ $agent->b2bCustomers->count() }}</span>
                                    @else
                                        <span class="text-gray-400 text-xs italic">0</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.b2b.orders.index', ['agent_id' => $agent->id]) }}" class="inline-flex items-center gap-2 px-3 py-1 bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 rounded-xl transition text-slate-900 group" title="Vedi elenco ordini di {{ $agent->name }}">
                                        <span class="text-sm">📊</span>
                                        <div class="text-right">
                                            <div class="font-black text-[11px] uppercase text-slate-900 group-hover:text-amber-600 transition">
                                                {{ $agent->b2b_orders_count ?? 0 }} Ordini
                                            </div>
                                            <div class="text-[10px] font-bold text-emerald-700">
                                                € {{ number_format($agent->b2b_orders_sum_total_amount ?? 0, 2, ',', '.') }}
                                            </div>
                                        </div>
                                    </a>
                                </td>
                                <td class="px-4 py-2.5 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1">
                                        <a href="{{ route('admin.b2b.agents.edit', $agent) }}" class="p-1.5 text-slate-700 hover:text-black hover:bg-yellow-400 rounded-lg transition" title="Modifica Agente">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <form action="{{ route('admin.b2b.agents.destroy', $agent) }}" method="POST" class="inline" onsubmit="return confirm('Sei sicuro di voler rimuovere questo agente? L\'account verrà eliminato.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition" title="Rimuovi">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-400 font-bold">Nessun agente registrato.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
