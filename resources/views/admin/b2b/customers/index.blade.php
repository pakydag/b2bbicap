<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Anagrafica Clienti B2B') }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Gestione anagrafiche, codici cliente e sincronizzazione gestionale</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="document.getElementById('importCustomerModal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold uppercase rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Importa Clienti (Excel / CSV)
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('warning'))
                <div class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('warning') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Form di Ricerca -->
                    <div class="mb-6">
                        <form action="{{ route('admin.b2b.customers.index') }}" method="GET" class="flex gap-2">
                            <div class="relative flex-1 max-w-md">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cerca per codice, ragione sociale o P.IVA..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-slate-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-slate-700 active:bg-slate-900 focus:outline-none focus:border-slate-900 focus:ring ring-slate-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Cerca
                            </button>
                            @if(!empty($search))
                                <a href="{{ route('admin.b2b.customers.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition ease-in-out duration-150">
                                    Annulla
                                </a>
                            @endif
                        </form>
                    </div>

                    <!-- Mobile View -->
                    <div class="md:hidden space-y-4">
                        @forelse($customers as $customer)
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-sm font-bold text-gray-900">
                                        {{ $customer->business_name }}
                                        @if($customer->user)
                                            <span class="block mt-1 text-[10px] text-green-700 bg-green-50 border border-green-100 px-2 py-0.5 rounded w-max font-semibold">
                                                B2B: {{ $customer->user->email }}
                                            </span>
                                        @endif
                                    </h3>
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.b2b.customers.edit', $customer) }}" class="text-indigo-600">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <form action="{{ route('admin.b2b.customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Sicuro?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-600 space-y-1">
                                    <p><span class="font-semibold">Codice Cliente:</span> <span class="font-mono font-bold text-indigo-700 bg-indigo-50 px-1.5 py-0.5 rounded border border-indigo-200">{{ $customer->code ?: 'N/D' }}</span></p>
                                    <p><span class="font-semibold">P.IVA:</span> {{ $customer->vat_number ?? '-' }}</p>
                                    <p><span class="font-semibold">Referente:</span> {{ $customer->contact_name }} {{ $customer->contact_surname }}</p>
                                    <p><span class="font-semibold text-indigo-600">Pagamento:</span> {{ $customer->paymentCondition->name ?? 'N.D.' }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-sm text-gray-500">Nessun cliente registrato.</p>
                        @endforelse
                    </div>

                    <!-- Desktop View -->
                    <div class="hidden md:flex flex-col overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Codice</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ragione Sociale</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">P.IVA</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Referente</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pagamento</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Ordini & Totale (€)</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($customers as $customer)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-4 whitespace-nowrap text-sm">
                                            @if($customer->code)
                                                <span class="inline-block font-mono font-bold text-xs bg-indigo-50 text-indigo-700 border border-indigo-200 px-2 py-1 rounded">
                                                    {{ $customer->code }}
                                                </span>
                                            @else
                                                <span class="text-xs text-gray-400 italic">N/D</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $customer->business_name }}
                                            @if($customer->user)
                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-100" title="{{ $customer->user->email }}">
                                                    B2B Abilitato
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $customer->vat_number ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $customer->contact_name }} {{ $customer->contact_surname }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $customer->paymentCondition->name ?? 'Non definita' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                            <a href="{{ route('admin.b2b.orders.index', ['customer_id' => $customer->id]) }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-yellow-50 hover:bg-yellow-100 border border-yellow-300 rounded-xl transition text-slate-900 group" title="Vedi ordini di {{ $customer->business_name }}">
                                                <span class="text-base">📊</span>
                                                <div class="text-right">
                                                    <div class="font-black text-xs uppercase text-slate-900 group-hover:text-indigo-600 transition">
                                                        {{ $customer->orders_count ?? 0 }} Ordini
                                                    </div>
                                                    <div class="text-[11px] font-bold text-emerald-700">
                                                        € {{ number_format($customer->orders_sum_total_amount ?? 0, 2, ',', '.') }}
                                                    </div>
                                                </div>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Nessun cliente B2B registrato.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Importazione Clienti -->
    <div id="importCustomerModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 transform transition-all">
            <div class="flex justify-between items-center pb-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg">
                        📥
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Importa Anagrafica Clienti</h3>
                        <p class="text-xs text-gray-500">File Excel (.xlsx) o CSV con codici cliente</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('importCustomerModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-xl font-bold p-1">
                    &times;
                </button>
            </div>

            <form action="{{ route('admin.b2b.customers.import') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                @csrf
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs text-slate-600 space-y-1">
                    <p class="font-semibold text-slate-800">Formati supportati:</p>
                    <p>• File <strong>.xlsx</strong> (es. <code>Clienti_Bicap.xlsx</code> con colonne <code>CF_CodiceCF</code> e <code>CF_Nome</code>).</p>
                    <p>• File <strong>.csv</strong> delimitato da punto e virgola o virgola.</p>
                    <p class="text-[11px] text-slate-500 mt-1">I clienti esistenti con la stessa ragione sociale verranno aggiornati con il loro codice gestionale univoco.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Seleziona File (.xlsx, .csv)</label>
                    <input type="file" name="file" accept=".xlsx,.csv,.txt" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 border border-gray-300 rounded-xl p-1 cursor-pointer">
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" onclick="document.getElementById('importCustomerModal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition">
                        Annulla
                    </button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold uppercase rounded-xl shadow-md transition">
                        Avvia Importazione
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
