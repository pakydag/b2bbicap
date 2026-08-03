<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestione Agenti B2B') }}
            </h2>
            <a href="{{ route('admin.b2b.agents.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Aggiungi Agente
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Mobile View: Card List -->
                    <div class="md:hidden space-y-4">
                        @forelse($agents as $agent)
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-sm font-bold text-gray-900">{{ $agent->name }} {{ $agent->surname }}</h3>
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.b2b.agents.edit', $agent) }}" class="text-indigo-600">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <form action="{{ route('admin.b2b.agents.destroy', $agent) }}" method="POST" onsubmit="return confirm('Sicuro?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-600 space-y-1">
                                    <p>📧 {{ $agent->email }}</p>
                                    <p>📞 {{ $agent->phone ?? '-' }}</p>
                                    <div class="pt-2">
                                        <p class="font-bold mb-1">Brand:</p>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($agent->b2bBrands as $brand)
                                                <span class="px-2 py-0.5 bg-indigo-100 text-indigo-800 rounded-full text-[10px]">{{ $brand->name }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="pt-2 flex justify-between items-center border-t border-gray-100 mt-2">
                                        <p><span class="font-bold text-gray-900">{{ $agent->b2bCustomers->count() }}</span> clienti</p>
                                        <a href="{{ route('admin.b2b.orders.index', ['agent_id' => $agent->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-1 bg-yellow-400 text-slate-950 font-black rounded-lg text-xs hover:bg-yellow-300 transition">
                                            <span>📊 {{ $agent->b2b_orders_count ?? 0 }} Ordini (€ {{ number_format($agent->b2b_orders_sum_total_amount ?? 0, 2, ',', '.') }})</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-sm text-gray-500">Nessun agente registrato.</p>
                        @endforelse
                    </div>

                    <!-- Desktop View: Table -->
                    <div class="hidden md:flex flex-col overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome & Cognome</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contatti</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Brand Abilitati</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clienti</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ordini & Totale (€)</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Azioni</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($agents as $agent)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $agent->name }} {{ $agent->surname }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div>{{ $agent->email }}</div>
                                            <div class="text-xs">{{ $agent->phone ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            @foreach($agent->b2bBrands as $brand)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 mr-1">
                                                    {{ $brand->name }}
                                                </span>
                                            @endforeach
                                            @if($agent->b2bBrands->isEmpty())
                                                <span class="text-gray-400 italic">Nessun brand</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            @if($agent->b2bCustomers->count() > 0)
                                                <span class="font-bold text-gray-900">{{ $agent->b2bCustomers->count() }}</span> clienti
                                            @else
                                                <span class="text-red-400 italic">Nessun cliente</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <a href="{{ route('admin.b2b.orders.index', ['agent_id' => $agent->id]) }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-yellow-50 hover:bg-yellow-100 border border-yellow-300 rounded-xl transition text-slate-900 group" title="Vedi elenco ordini di {{ $agent->name }}">
                                                <span class="text-base">📊</span>
                                                <div>
                                                    <div class="font-black text-xs uppercase text-slate-900 group-hover:text-indigo-600 transition">
                                                        {{ $agent->b2b_orders_count ?? 0 }} Ordini
                                                    </div>
                                                    <div class="text-[11px] font-bold text-emerald-700">
                                                        € {{ number_format($agent->b2b_orders_sum_total_amount ?? 0, 2, ',', '.') }}
                                                    </div>
                                                </div>
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.b2b.agents.edit', $agent) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">Modifica</a>
                                            <form action="{{ route('admin.b2b.agents.destroy', $agent) }}" method="POST" class="inline" onsubmit="return confirm('Sei sicuro di voler rimuovere questo agente? L\'account verrà eliminato.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Rimuovi</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Nessun agente registrato.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
