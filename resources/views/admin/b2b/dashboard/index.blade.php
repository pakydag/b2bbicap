<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
            {{ __('Dashboard Agenti & B2B') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-sm">
                <span class="text-base">✅</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-sm">
                <span class="text-base">❌</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 shadow-sm rounded-2xl border border-gray-100 overflow-hidden border-b-4 border-indigo-500">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Agenti Attivi</p>
                <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">{{ $stats['agents_count'] }}</p>
            </div>
            <div class="bg-white p-5 shadow-sm rounded-2xl border border-gray-100 overflow-hidden border-b-4 border-emerald-500">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Clienti B2B</p>
                <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">{{ $stats['customers_count'] }}</p>
            </div>
            <div class="bg-white p-5 shadow-sm rounded-2xl border border-gray-100 overflow-hidden border-b-4 border-yellow-500">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Ordini In Attesa</p>
                <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">{{ $stats['pending_orders_count'] }}</p>
            </div>
            <div class="bg-white p-5 shadow-sm rounded-2xl border border-gray-100 overflow-hidden border-b-4 border-blue-500">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Fatturato B2B</p>
                <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">€ {{ number_format($stats['total_revenue'], 2, ',', '.') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Ordini Recenti -->
            <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-black text-xs uppercase tracking-wider text-gray-700">Ultimi Ordini Ricevuti</h3>
                    <a href="{{ route('admin.b2b.orders.index') }}" class="text-xs font-black text-slate-900 hover:text-amber-600 uppercase">Vedi Tutti →</a>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($recent_orders as $order)
                        <a href="{{ route('admin.b2b.orders.edit', $order) }}" class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-yellow-50/40 transition group block">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-black text-slate-900 group-hover:text-amber-600 transition">Ordine #{{ $order->id }}</span>
                                    <span class="text-[10px] text-gray-400 font-bold">• {{ $order->created_at->format('d/m/Y') }}</span>
                                    @if($order->internal_reference)
                                        <span class="text-[10px] bg-slate-100 text-slate-800 border border-slate-200 px-1.5 py-0.2 rounded font-mono font-bold">
                                            🏷️ {{ $order->internal_reference }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs font-bold text-gray-700">{{ $order->customer->business_name }}</p>
                                <p class="text-[11px] text-gray-400 font-medium">Agente: {{ $order->agent->name }} {{ $order->agent->surname }}</p>
                            </div>
                            <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center gap-1 border-t sm:border-t-0 pt-2 sm:pt-0 border-gray-100">
                                <p class="text-sm font-black text-slate-900">€ {{ number_format($order->total_amount, 2, ',', '.') }}</p>
                                <span class="text-[10px] uppercase font-black px-2 py-0.5 rounded border {{ $order->status == 'pending' ? 'bg-amber-100 text-amber-900 border-amber-300' : ($order->status == 'confirmed' ? 'bg-emerald-100 text-emerald-900 border-emerald-300' : ($order->status == 'customer_approved' ? 'bg-blue-100 text-blue-900 border-blue-300' : 'bg-purple-100 text-purple-900 border-purple-300')) }}">
                                    {{ $order->status_label }}
                                </span>
                            </div>
                        </a>
                    @empty
                        <div class="p-8 text-center text-gray-400 font-bold text-xs">Nessun ordine recente</div>
                    @endforelse
                </div>
            </div>

            <!-- Azioni Rapide & Info -->
            <div class="bg-black shadow-sm rounded-2xl p-6 text-white flex flex-col justify-between border border-zinc-800">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-yellow-400 text-xl">⚡</span>
                        <h3 class="font-black text-lg text-white uppercase tracking-tight">Portale Amministrazione B2B</h3>
                    </div>
                    <p class="text-zinc-400 text-xs leading-relaxed">
                        Piattaforma centrale per il controllo ordini, gestione listini personalizzati, anagrafiche clienti e monitoraggio agenti commerciali Bicap.
                    </p>
                </div>
                <div class="mt-6 grid grid-cols-2 gap-3">
                    <a href="{{ route('admin.b2b.agents.index') }}" class="bg-zinc-900 hover:bg-yellow-400 hover:text-black p-4 rounded-xl text-center border border-zinc-800 transition group">
                        <span class="block text-2xl mb-1">👤</span>
                        <span class="text-xs font-black uppercase tracking-wider">Gestione Agenti</span>
                    </a>
                    <a href="{{ route('admin.b2b.products.index') }}" class="bg-zinc-900 hover:bg-yellow-400 hover:text-black p-4 rounded-xl text-center border border-zinc-800 transition group">
                        <span class="block text-2xl mb-1">📦</span>
                        <span class="text-xs font-black uppercase tracking-wider">Inventario B2B</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
