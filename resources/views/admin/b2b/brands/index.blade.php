<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestione Linee B2B') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('info'))
                <div class="mb-4 bg-blue-100 border border-blue-400 text-blue-800 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('info') }}</span>
                </div>
            @endif

            <div class="mb-6 bg-blue-50 border border-blue-200 text-blue-900 p-4 rounded-xl text-xs font-bold shadow-sm flex items-center gap-3">
                <span class="text-xl">ℹ️</span>
                <span>Le linee B2B vengono lette e create automaticamente durante l'importazione dei prodotti dal file CSV / FTPS.</span>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome Linea</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prodotti Abbinati</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creato il</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Azioni</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($brands as $brand)
                                    <tr class="hover:bg-gray-50/80 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400 font-bold">#{{ $brand->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <a href="{{ route('admin.b2b.products.index', ['brand_id' => $brand->id]) }}" class="font-black text-slate-900 hover:text-indigo-600 transition text-base">
                                                {{ $brand->name }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <a href="{{ route('admin.b2b.products.index', ['brand_id' => $brand->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-1 bg-yellow-100 hover:bg-yellow-200 border border-yellow-300 text-slate-900 font-black text-xs rounded-xl transition group">
                                                <span>📦 {{ $brand->products_count }} PRODOTTI</span>
                                                <span class="group-hover:translate-x-0.5 transition">↗</span>
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $brand->created_at ? $brand->created_at->format('d/m/Y H:i') : '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.b2b.products.index', ['brand_id' => $brand->id]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-lg text-xs uppercase tracking-wider shadow transition">
                                                <span>👁️ VEDI PRODOTTI</span>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Nessuna linea registrata.</td>
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
