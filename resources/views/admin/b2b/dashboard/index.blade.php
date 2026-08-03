<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Agenti & B2B') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white p-6 shadow sm:rounded-lg overflow-hidden border-b-4 border-indigo-500">
                    <p class="text-xs font-bold text-gray-400 uppercase">Agenti Attivi</p>
                    <p class="text-3xl font-black text-gray-900">{{ $stats['agents_count'] }}</p>
                </div>
                <div class="bg-white p-6 shadow sm:rounded-lg overflow-hidden border-b-4 border-emerald-500">
                    <p class="text-xs font-bold text-gray-400 uppercase">Clienti B2B</p>
                    <p class="text-3xl font-black text-gray-900">{{ $stats['customers_count'] }}</p>
                </div>
                <div class="bg-white p-6 shadow sm:rounded-lg overflow-hidden border-b-4 border-yellow-500">
                    <p class="text-xs font-bold text-gray-400 uppercase">Ordini Pendenti</p>
                    <p class="text-3xl font-black text-gray-900">{{ $stats['pending_orders_count'] }}</p>
                </div>
                <div class="bg-white p-6 shadow sm:rounded-lg overflow-hidden border-b-4 border-blue-500">
                    <p class="text-xs font-bold text-gray-400 uppercase">Fatturato B2B</p>
                    <p class="text-3xl font-black text-gray-900">€ {{ number_format($stats['total_revenue'], 2, ',', '.') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Ordini Recenti -->
                <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-bold text-gray-700">Ultimi Ordini Ricevuti</h3>
                        <a href="{{ route('admin.b2b.orders.index') }}" class="text-xs text-indigo-600 hover:underline">Vedi Tutti</a>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($recent_orders as $order)
                            <a href="{{ route('admin.b2b.orders.edit', $order) }}" class="px-6 py-4 flex justify-between items-center hover:bg-yellow-50/60 transition group cursor-pointer block border-b border-gray-100 last:border-b-0">
                                <div>
                                    <p class="text-sm font-bold text-gray-900 group-hover:text-amber-600 transition">Ordine #{{ $order->id }} - {{ $order->customer->business_name }}</p>
                                    <p class="text-xs text-gray-500">Inviato da {{ $order->agent->name }} {{ $order->agent->surname }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-gray-900">€ {{ number_format($order->total_amount, 2, ',', '.') }}</p>
                                    <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded {{ $order->status == 'pending' ? 'bg-amber-100 text-amber-900 border border-amber-300' : ($order->status == 'confirmed' ? 'bg-emerald-100 text-emerald-900 border border-emerald-300' : ($order->status == 'customer_approved' ? 'bg-blue-100 text-blue-900 border border-blue-300' : 'bg-purple-100 text-purple-900 border border-purple-300')) }}">
                                        {{ $order->status_label }}
                                    </span>
                                </div>
                            </a>
                        @empty
                            <div class="px-6 py-10 text-center text-gray-400 text-sm">Nessun ordine recente</div>
                        @endforelse
                    </div>
                </div>

                <!-- Azioni Rapide -->
                <div class="bg-indigo-600 shadow sm:rounded-lg p-6 text-white flex flex-col justify-between">
                    <div>
                        <h3 class="font-black text-xl mb-2">Benvenuto nel Portale B2B</h3>
                        <p class="text-indigo-100 text-sm leading-relaxed">Da qui puoi gestire l'intera rete vendita. Ricorda di abilitare le linee corrette per ogni agente affinché possano raccogliere ordini con precisione.</p>
                    </div>
                    <div class="mt-8 grid grid-cols-2 gap-4">
                        <a href="{{ route('admin.b2b.agents.index') }}" class="bg-white/10 hover:bg-white/20 p-4 rounded-xl text-center border border-white/20 transition">
                            <span class="block text-2xl mb-1">👤</span>
                            <span class="text-xs font-bold uppercase">Agenti</span>
                        </a>
                        <a href="{{ route('admin.b2b.products.index') }}" class="bg-white/10 hover:bg-white/20 p-4 rounded-xl text-center border border-white/20 transition">
                            <span class="block text-2xl mb-1">📦</span>
                            <span class="text-xs font-bold uppercase">Inventario Prodotti</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
