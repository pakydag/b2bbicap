<x-agent-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase">
            {{ __('Benvenuto,') }} {{ Auth::user()->name }}
        </h2>
    </x-slot>

    <div class="space-y-8">
        <!-- Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group">
                <div class="absolute right-0 top-0 p-4 opacity-10 text-6xl group-hover:scale-110 transition duration-500">📝</div>
                <p class="text-sm font-black text-gray-500 uppercase tracking-widest mb-1">Totale Ordini</p>
                <p class="text-4xl font-black text-slate-900">{{ $stats['orders_count'] }}</p>
                <p class="text-xs text-slate-400 font-bold mt-2 uppercase tracking-tight">Inviati alla sede</p>
            </div>
            
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group">
                <div class="absolute right-0 top-0 p-4 opacity-10 text-6xl group-hover:scale-110 transition duration-500">⏳</div>
                <p class="text-sm font-black text-gray-500 uppercase tracking-widest mb-1">Pendenti</p>
                <p class="text-4xl font-black text-amber-500">{{ $stats['pending_orders'] }}</p>
                <p class="text-xs text-amber-400 font-bold mt-2 uppercase tracking-tight">In attesa di conferma</p>
            </div>

            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group">
                <div class="absolute right-0 top-0 p-4 opacity-10 text-6xl group-hover:scale-110 transition duration-500">💰</div>
                <p class="text-sm font-black text-gray-500 uppercase tracking-widest mb-1">Volume Confermato</p>
                <p class="text-4xl font-black text-emerald-600">€ {{ number_format($stats['total_volume'], 2, ',', '.') }}</p>
                <p class="text-xs text-emerald-400 font-bold mt-2 uppercase tracking-tight">Valore ordini approvati</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Activity -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-black text-gray-800 uppercase text-sm tracking-wider">Ultimi Ordini Inviati</h3>
                    <a href="{{ route('agent.orders') }}" class="text-xs font-bold text-slate-800 hover:text-yellow-600 hover:underline transition">Vedi Archivio</a>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($recent_orders as $order)
                        <a href="{{ route('agent.order_detail', $order) }}" class="px-8 py-5 flex justify-between items-center hover:bg-amber-50/50 transition cursor-pointer group">
                            <div>
                                <div class="flex items-center gap-2">
                                    <p class="text-base font-black text-gray-900 group-hover:text-indigo-600 transition">#{{ $order->id }} - {{ $order->customer->business_name }}</p>
                                    @if($order->is_modified)
                                        <span class="text-[9px] bg-amber-100 text-amber-900 border border-amber-300 px-2 py-0.5 rounded-full font-black uppercase tracking-wider">
                                            ✏️ Modificato
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 font-bold uppercase mt-0.5">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="text-right flex items-center gap-4">
                                <div>
                                    <p class="text-base font-black text-slate-900">€ {{ number_format($order->total_amount, 2, ',', '.') }}</p>
                                    @php
                                        $dashStatusClasses = [
                                            'pending' => 'bg-amber-100 text-amber-800 border-amber-300',
                                            'revision_pending' => 'bg-orange-100 text-orange-800 border-orange-300',
                                            'customer_approved' => 'bg-blue-100 text-blue-800 border-blue-300',
                                            'customer_rejected' => 'bg-rose-100 text-rose-800 border-rose-300',
                                            'confirmed' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                            'cancelled' => 'bg-slate-100 text-slate-700 border-slate-300',
                                        ];
                                        $dashStatusLabels = [
                                            'pending' => 'In Attesa',
                                            'revision_pending' => 'In Attesa Cliente',
                                            'customer_approved' => 'Approvato da Cliente',
                                            'customer_rejected' => 'Rifiutato',
                                            'confirmed' => 'Confermato',
                                            'cancelled' => 'Annullato',
                                        ];
                                    @endphp
                                    <span class="text-[10px] px-2 py-0.5 rounded-full font-bold uppercase border {{ $dashStatusClasses[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $dashStatusLabels[$order->status] ?? $order->status }}
                                    </span>
                                </div>
                                <span class="text-gray-400 group-hover:text-indigo-600 group-hover:translate-x-1 transition text-lg font-bold">➔</span>
                            </div>
                        </a>
                    @empty
                        <div class="px-8 py-12 text-center text-gray-500 uppercase tracking-widest text-xs font-bold font-medium">
                            Non hai ancora inviato alcun ordine.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 gap-6">
                <a href="{{ route('agent.catalog') }}" class="bg-black border border-zinc-950 p-8 rounded-3xl text-white shadow-lg shadow-black/10 hover:scale-[1.02] hover:border-yellow-400 transition duration-300 flex items-center justify-between group">
                    <div>
                        <h4 class="text-2xl font-black mb-1 group-hover:text-yellow-400 transition">Nuovo Ordine</h4>
                        <p class="text-zinc-400 text-sm font-medium">Sfoglia il catalogo e inserisci una nuova raccolta.</p>
                    </div>
                    <span class="text-4xl text-zinc-400 group-hover:text-yellow-400 group-hover:translate-x-2 transition">➔</span>
                </a>

                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                    <h4 class="font-black text-gray-800 uppercase text-sm tracking-wider mb-4 border-b pb-2">Assistenza B2B</h4>
                    <p class="text-sm text-gray-600 leading-relaxed mb-6">
                        In caso di problemi tecnici con il portale o discrepanze nell'inventario, contatta l'amministrazione via e-mail o utilizza la chat di supporto.
                    </p>
                    <div class="flex space-x-4">
                        <button class="bg-gray-100 text-gray-800 text-xs font-black uppercase px-4 py-2 rounded-xl hover:bg-gray-200 transition">Email Supporto</button>
                        <button class="bg-gray-100 text-gray-800 text-xs font-black uppercase px-4 py-2 rounded-xl hover:bg-gray-200 transition">Manuale PDF</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-agent-layout>
