<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ordini B2B Ricevuti') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Barra Filtri Ordini -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 p-6 border border-gray-100">
                <form method="GET" action="{{ route('admin.b2b.orders.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <!-- Filtro Agente -->
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1">Filtra per Agente</label>
                        <select name="agent_id" onchange="this.form.submit()" class="w-full rounded-xl border-gray-300 shadow-sm text-sm focus:ring-yellow-400 focus:border-yellow-400 font-medium">
                            <option value="">-- Tutti gli Agenti --</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}" {{ request('agent_id') == $agent->id ? 'selected' : '' }}>
                                    {{ $agent->name }} {{ $agent->surname }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtro Azienda/Cliente -->
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1">Filtra per Azienda</label>
                        <select name="customer_id" onchange="this.form.submit()" class="w-full rounded-xl border-gray-300 shadow-sm text-sm focus:ring-yellow-400 focus:border-yellow-400 font-medium">
                            <option value="">-- Tutte le Aziende --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->business_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtro Stato -->
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1">Stato Ordine</label>
                        <select name="status" onchange="this.form.submit()" class="w-full rounded-xl border-gray-300 shadow-sm text-sm focus:ring-yellow-400 focus:border-yellow-400 font-medium">
                            <option value="">-- Tutti gli Stati --</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>In Attesa</option>
                            <option value="revision_pending" {{ request('status') == 'revision_pending' ? 'selected' : '' }}>Attesa Cliente</option>
                            <option value="customer_approved" {{ request('status') == 'customer_approved' ? 'selected' : '' }}>Approvato da Cliente</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confermato</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annullato</option>
                        </select>
                    </div>

                    <!-- Pulsanti Filtro e Reset -->
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-black hover:bg-yellow-400 hover:text-slate-950 text-white font-black py-2.5 px-4 rounded-xl shadow transition duration-200 text-xs uppercase tracking-wider">
                            🔍 Filtra
                        </button>
                        @if(request()->hasAny(['agent_id', 'customer_id', 'status', 'search']))
                            <a href="{{ route('admin.b2b.orders.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-black py-2.5 px-3 rounded-xl transition text-xs uppercase flex items-center justify-center">
                                ✖ Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                        <!-- Mobile View -->
                        <div class="md:hidden space-y-4">
                            @forelse($orders as $order)
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm relative overflow-hidden">
                                    <div class="absolute left-0 top-0 bottom-0 w-1.5 {{ $order->status == 'pending' ? 'bg-yellow-400' : ($order->status == 'confirmed' ? 'bg-green-400' : 'bg-red-400') }}"></div>
                                    <div class="flex justify-between items-start mb-2 pl-2">
                                        <div>
                                            <h3 class="text-sm font-bold text-gray-900 leading-tight">Ordine #{{ $order->id }}</h3>
                                            <p class="text-[10px] text-gray-500 font-semibold">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold {{ $order->status == 'pending' ? 'bg-amber-100 text-amber-900 border border-amber-300' : ($order->status == 'confirmed' ? 'bg-emerald-100 text-emerald-900 border border-emerald-300' : ($order->status == 'customer_approved' ? 'bg-blue-100 text-blue-900 border border-blue-300' : 'bg-slate-100 text-slate-800 border border-slate-300')) }}">
                                                {{ $order->status_label }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="pl-2 space-y-1 mt-3">
                                        <p class="text-xs"><span class="text-gray-400">Cliente:</span> <span class="font-medium">{{ $order->customer->business_name }}</span></p>
                                        <p class="text-xs"><span class="text-gray-400">Agente:</span> <span class="font-medium text-indigo-600">{{ $order->agent->name }} {{ $order->agent->surname }}</span></p>
                                    </div>

                                    <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-100 pl-2">
                                        <p class="font-bold text-gray-900">€ {{ number_format($order->total_amount, 2, ',', '.') }}</p>
                                        <a href="{{ route('admin.b2b.orders.edit', $order) }}" class="inline-flex items-center px-3 py-1 bg-indigo-600 text-white text-[10px] font-bold rounded hover:bg-indigo-700 transition">
                                            GESTISCI
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-sm text-gray-500">Nessun ordine B2B ricevuto.</p>
                            @endforelse
                        </div>

                        <!-- Desktop View -->
                        <div class="hidden md:flex flex-col overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ordine #</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Totale</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stato</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Azioni</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($orders as $order)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-l-4 {{ $order->status == 'pending' ? 'border-yellow-400' : ($order->status == 'confirmed' ? 'border-green-400' : 'border-blue-400') }}">
                                            #{{ $order->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->agent->name }} {{ $order->agent->surname }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->customer->business_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">€ {{ number_format($order->total_amount, 2, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2.5 py-1 rounded text-xs uppercase font-bold {{ $order->status == 'pending' ? 'bg-amber-100 text-amber-900 border border-amber-300' : ($order->status == 'confirmed' ? 'bg-emerald-100 text-emerald-900 border border-emerald-300' : ($order->status == 'customer_approved' ? 'bg-blue-100 text-blue-900 border border-blue-300' : 'bg-slate-100 text-slate-800 border border-slate-300')) }}">
                                                {{ $order->status_label }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                            <a href="{{ route('admin.b2b.orders.edit', $order) }}" class="text-indigo-600 hover:text-indigo-900 font-bold">Gestisci</a>
                                            <a href="{{ route('admin.b2b.orders.pdf', $order) }}" target="_blank" class="text-slate-800 hover:text-yellow-600 font-black inline-flex items-center gap-1 bg-gray-100 hover:bg-yellow-100 px-2.5 py-1 rounded text-xs transition">
                                                <span>🖨️ PDF</span>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Nessun ordine B2B ricevuto.</td>
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
