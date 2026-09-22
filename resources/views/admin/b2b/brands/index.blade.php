<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                {{ __('Gestione Linee B2B') }}
            </h2>
            <span class="text-xs font-bold text-gray-500 uppercase">
                Totale Linee: {{ $brands->count() }}
            </span>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-2xl shadow-sm text-sm font-bold flex items-center gap-2" role="alert">
                <span>✅</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('info'))
            <div class="bg-blue-50 border border-blue-300 text-blue-800 px-4 py-3 rounded-2xl shadow-sm text-sm font-bold flex items-center gap-2" role="alert">
                <span>ℹ️</span>
                <span>{{ session('info') }}</span>
            </div>
        @endif

        <div class="bg-blue-50 border border-blue-200 text-blue-900 p-4 rounded-2xl text-xs font-bold shadow-sm flex items-center gap-3">
            <span class="text-xl">ℹ️</span>
            <span>Le linee B2B vengono lette e create automaticamente durante l'importazione dei prodotti dal file CSV / FTPS.</span>
        </div>

        <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden min-w-0">
            <!-- Mobile View (< md) -->
            <div class="md:hidden divide-y divide-gray-100">
                @forelse($brands as $brand)
                    <div class="p-4 space-y-3 hover:bg-gray-50/60 transition">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[10px] font-mono font-bold text-gray-400">#{{ $brand->id }}</span>
                                <h3 class="text-base font-black text-slate-900 leading-tight">{{ $brand->name }}</h3>
                            </div>
                            <span class="text-xs font-bold text-gray-400">{{ $brand->created_at ? $brand->created_at->format('d/m/Y') : '-' }}</span>
                        </div>
                        <div class="pt-1">
                            <a href="{{ route('admin.b2b.products.index', ['brand_id' => $brand->id]) }}" class="w-full inline-flex items-center justify-between px-3.5 py-2.5 bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 rounded-xl transition text-slate-900 font-black text-xs uppercase tracking-wider shadow-sm">
                                <span>📦 {{ $brand->products_count }} PRODOTTI IN QUESTA LINEA</span>
                                <span>VEDI ↗</span>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm font-bold text-gray-400">Nessuna linea registrata.</div>
                @endforelse
            </div>

            <!-- Desktop View (>= md) -->
            <div class="hidden md:block overflow-x-auto min-w-0">
                <table class="w-full text-left divide-y divide-gray-100 border-collapse">
                    <thead class="bg-gray-50/80 text-[11px] font-black text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3 w-16">ID</th>
                            <th class="px-4 py-3">Nome Linea</th>
                            <th class="px-4 py-3">Prodotti Abbinati</th>
                            <th class="px-4 py-3">Creato il</th>
                            <th class="px-4 py-3 text-right">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs">
                        @forelse($brands as $brand)
                            <tr class="hover:bg-yellow-50/40 transition">
                                <td class="px-4 py-3 whitespace-nowrap text-gray-400 font-bold font-mono">#{{ $brand->id }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <a href="{{ route('admin.b2b.products.index', ['brand_id' => $brand->id]) }}" class="font-black text-slate-900 hover:text-amber-600 transition text-sm">
                                        {{ $brand->name }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <a href="{{ route('admin.b2b.products.index', ['brand_id' => $brand->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-1 bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 text-slate-900 font-black text-xs rounded-xl transition group">
                                        <span>📦 {{ $brand->products_count }} PRODOTTI</span>
                                        <span class="group-hover:translate-x-0.5 transition">↗</span>
                                    </a>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-gray-500">{{ $brand->created_at ? $brand->created_at->format('d/m/Y H:i') : '-' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                    <a href="{{ route('admin.b2b.products.index', ['brand_id' => $brand->id]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-900 hover:bg-yellow-400 hover:text-slate-950 text-white font-black rounded-xl text-xs uppercase tracking-wider shadow transition">
                                        <span>👁️ VEDI PRODOTTI</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-400 font-bold">Nessuna linea registrata.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
