<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                    {{ __('Condizioni di Pagamento B2B') }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5 font-bold">Configurazione condizioni e termini di pagamento clienti</p>
            </div>
            <a href="{{ route('admin.b2b.payment-conditions.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-black hover:bg-yellow-400 hover:text-slate-950 text-white font-black text-xs uppercase tracking-wider rounded-xl shadow transition">
                <span>➕ Aggiungi Condizione</span>
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
            <!-- Mobile View (< md) -->
            <div class="md:hidden divide-y divide-gray-100">
                @forelse($conditions as $condition)
                    <div class="p-4 space-y-2 hover:bg-gray-50/60 transition">
                        <div class="flex justify-between items-start">
                            <h3 class="text-sm font-black text-slate-900 leading-tight">{{ $condition->name }}</h3>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.b2b.payment-conditions.edit', $condition) }}" class="p-1.5 text-slate-700 hover:text-black hover:bg-yellow-400 rounded-lg transition" title="Modifica">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('admin.b2b.payment-conditions.destroy', $condition) }}" method="POST" class="inline" onsubmit="return confirm('Sei sicuro di voler eliminare questa condizione?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition" title="Elimina">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 leading-relaxed">{{ $condition->description ?? '-' }}</p>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm font-bold text-gray-400">Nessuna condizione registrata.</div>
                @endforelse
            </div>

            <!-- Desktop View (>= md) -->
            <div class="hidden md:block overflow-x-auto min-w-0">
                <table class="w-full text-left divide-y divide-gray-100 border-collapse">
                    <thead class="bg-gray-50/80 text-[11px] font-black text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3">Nome Condizione</th>
                            <th class="px-4 py-3">Descrizione</th>
                            <th class="px-4 py-3 text-right">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs">
                        @forelse($conditions as $condition)
                            <tr class="hover:bg-yellow-50/40 transition">
                                <td class="px-4 py-3 font-black text-slate-900 whitespace-nowrap">{{ $condition->name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $condition->description ?? '-' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                    <div class="inline-flex items-center gap-1">
                                        <a href="{{ route('admin.b2b.payment-conditions.edit', $condition) }}" class="p-1.5 text-slate-700 hover:text-black hover:bg-yellow-400 rounded-lg transition" title="Modifica">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <form action="{{ route('admin.b2b.payment-conditions.destroy', $condition) }}" method="POST" class="inline" onsubmit="return confirm('Sei sicuro di voler eliminare questa condizione?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition" title="Elimina">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-gray-400 font-bold">Nessuna condizione registrata.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
