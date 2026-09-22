<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                    {{ __('Anagrafica Clienti B2B') }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5 font-bold">Gestione anagrafiche, codici cliente e sincronizzazione gestionale</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <form action="{{ route('admin.b2b.customers.sync_ftps') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-3.5 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-black uppercase tracking-wider rounded-xl shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Sincronizza FTPS Ora
                    </button>
                </form>
                <button type="button" onclick="document.getElementById('importCustomerModal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black uppercase tracking-wider rounded-xl shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Importa Clienti
                </button>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-2xl shadow-sm text-sm font-bold flex items-center gap-2" role="alert">
                <span>✅</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('warning'))
            <div class="bg-amber-50 border border-amber-300 text-amber-800 px-4 py-3 rounded-2xl shadow-sm text-sm font-bold flex items-center gap-2" role="alert">
                <span>⚠️</span>
                <span>{{ session('warning') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-50 border border-rose-300 text-rose-800 px-4 py-3 rounded-2xl shadow-sm text-sm font-bold flex items-center gap-2" role="alert">
                <span>❌</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Sezione Sincronizzazione Gestionale FTPS -->
        <div class="bg-white shadow-sm rounded-2xl border border-purple-100 p-5">
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <span class="p-2.5 bg-purple-50 text-purple-600 rounded-xl shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-black text-gray-900 uppercase tracking-tight">Clienti & Codici Gestionale FTPS</h3>
                            <span class="text-[9px] font-black uppercase tracking-wider bg-purple-100 text-purple-800 px-2 py-0.5 rounded-full">Server FTPS</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5 max-w-2xl leading-relaxed">
                            Scarica <strong>Clienti_Bicap.xlsx</strong> direttamente da FTPS (<code>51.75.145.169 / Output</code>) per abbinare e aggiornare le anagrafiche con i codici cliente gestionale utilizzati negli ordini.
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto shrink-0">
                    <form action="{{ route('admin.b2b.customers.sync_ftps') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-xs font-black uppercase tracking-wider rounded-xl shadow transition">
                            Sincronizza FTPS Ora
                        </button>
                    </form>
                    <button type="button" onclick="document.getElementById('importCustomerModal').classList.remove('hidden')" class="px-4 py-2.5 border border-gray-200 hover:bg-gray-50 text-gray-700 text-xs font-black uppercase tracking-wider rounded-xl transition">
                        Carica Manuale
                    </button>
                </div>
            </div>
        </div>

        <!-- Filtri Rapidi per Stato B2B -->
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.b2b.customers.index', array_merge(request()->except('status', 'page'), ['status' => 'all'])) }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition {{ ($status ?? 'all') === 'all' || empty($status) ? 'bg-slate-900 text-white shadow' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}">
                <span>Tutti i Clienti</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] {{ ($status ?? 'all') === 'all' || empty($status) ? 'bg-slate-800 text-yellow-400' : 'bg-gray-100 text-gray-700 font-bold' }}">{{ number_format($totalCount, 0, ',', '.') }}</span>
            </a>

            <a href="{{ route('admin.b2b.customers.index', array_merge(request()->except('status', 'page'), ['status' => 'enabled'])) }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition {{ ($status ?? '') === 'enabled' ? 'bg-emerald-600 text-white shadow ring-2 ring-emerald-400/50' : 'bg-white text-emerald-800 hover:bg-emerald-50 border border-emerald-200' }}">
                <span>✓ Abilitati B2B (Accesso Attivo)</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] {{ ($status ?? '') === 'enabled' ? 'bg-emerald-700 text-white font-bold' : 'bg-emerald-100 text-emerald-800 font-bold' }}">{{ number_format($enabledCount, 0, ',', '.') }}</span>
            </a>

            <a href="{{ route('admin.b2b.customers.index', array_merge(request()->except('status', 'page'), ['status' => 'disabled'])) }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition {{ ($status ?? '') === 'disabled' ? 'bg-slate-700 text-white shadow' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}">
                <span>Non Abilitati</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] {{ ($status ?? '') === 'disabled' ? 'bg-slate-800 text-gray-300' : 'bg-gray-100 text-gray-700 font-bold' }}">{{ number_format($disabledCount, 0, ',', '.') }}</span>
            </a>
        </div>

        <!-- Form di Ricerca e Filtro Agente -->
        <div class="bg-white shadow-sm rounded-2xl border border-gray-100 p-4">
            <form action="{{ route('admin.b2b.customers.index') }}" method="GET" class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
                <input type="hidden" name="status" value="{{ $status ?? 'all' }}">
                
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cerca per codice, ragione sociale o P.IVA..." class="block w-full pl-10 pr-3 py-2.5 border-gray-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-yellow-400 focus:border-yellow-400 shadow-sm">
                </div>

                <div class="w-full md:w-64">
                    <select name="agent_id" onchange="this.form.submit()" class="block w-full py-2.5 border-gray-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-yellow-400 focus:border-yellow-400 shadow-sm">
                        <option value="">👤 Tutti gli Agenti</option>
                        @foreach($agents as $agentItem)
                            <option value="{{ $agentItem->id }}" {{ ($agentId ?? '') == $agentItem->id ? 'selected' : '' }}>
                                {{ $agentItem->name }} {{ $agentItem->surname }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2 shrink-0">
                    <button type="submit" class="bg-black hover:bg-yellow-400 hover:text-slate-950 text-white font-black py-2.5 px-4 rounded-xl shadow transition text-xs uppercase tracking-wider">
                        🔍 Cerca
                    </button>
                    @if(!empty($search) || !empty($agentId) || (!empty($status) && $status !== 'all'))
                        <a href="{{ route('admin.b2b.customers.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-black py-2.5 px-3 rounded-xl transition text-xs uppercase flex items-center justify-center" title="Resetta filtri">
                            ✖ Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabella & Lista Clienti -->
        <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden min-w-0">
            
            <!-- Vista Mobile (< sm) a schede responsive -->
            <div class="sm:hidden divide-y divide-gray-100">
                @forelse($customers as $customer)
                    <div class="p-4 sm:p-5 space-y-3 relative hover:bg-gray-50/60 transition">
                        <div class="flex justify-between items-start gap-2">
                            <div>
                                <div class="flex flex-wrap items-center gap-1.5 mb-1">
                                    <span class="font-mono text-xs font-black text-indigo-700 bg-indigo-50 border border-indigo-200 px-2 py-0.5 rounded">
                                        {{ $customer->code ?: 'COD: N/D' }}
                                    </span>
                                    @if($customer->user)
                                        <span class="text-[10px] font-bold text-emerald-800 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full" title="{{ $customer->user->email }}">
                                            ✓ B2B Attivo
                                        </span>
                                    @endif
                                    @if($customer->agents->isNotEmpty())
                                        <span class="text-[10px] font-bold text-slate-900 bg-yellow-100 border border-yellow-300 px-2 py-0.5 rounded-full">
                                            👤 {{ $customer->agents->pluck('name')->join(', ') }}
                                        </span>
                                    @endif
                                </div>
                                <h3 class="font-black text-sm text-slate-900 leading-tight">
                                    {{ $customer->business_name }}
                                </h3>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <a href="{{ route('admin.b2b.customers.edit', $customer) }}" class="p-2 bg-gray-100 hover:bg-slate-900 hover:text-white rounded-xl transition text-gray-700" title="Modifica Cliente">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('admin.b2b.customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Sicuro di voler eliminare questo cliente?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 bg-rose-50 hover:bg-rose-600 hover:text-white rounded-xl transition text-rose-600" title="Elimina">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs bg-gray-50 p-2.5 rounded-xl border border-gray-100">
                            <div>
                                <span class="text-gray-400 font-bold block text-[10px] uppercase">P.IVA / CF</span>
                                <span class="font-bold text-gray-800">{{ $customer->vat_number ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400 font-bold block text-[10px] uppercase">Referente</span>
                                <span class="font-medium text-gray-700 truncate block">{{ ($customer->contact_name || $customer->contact_surname) ? ($customer->contact_name . ' ' . $customer->contact_surname) : '-' }}</span>
                            </div>
                            @if($customer->agents->isNotEmpty())
                                <div class="col-span-2 pt-1 border-t border-gray-200/50">
                                    <span class="text-gray-400 font-bold text-[10px] uppercase">Agente di Riferimento:</span>
                                    <span class="font-bold text-slate-900 ml-1">
                                        {{ $customer->agents->map(fn($a) => $a->name . ' ' . $a->surname)->join(', ') }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div>
                            <a href="{{ route('admin.b2b.orders.index', ['customer_id' => $customer->id]) }}" class="w-full inline-flex items-center justify-between px-3.5 py-2 bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 rounded-xl transition text-slate-900 group">
                                <span class="text-xs font-black uppercase tracking-wider flex items-center gap-1.5">
                                    <span>📊</span> Ordini Registrati: <span class="font-black ml-1 text-slate-950">{{ $customer->orders_count ?? 0 }}</span>
                                </span>
                                <span class="text-xs font-black text-emerald-800">
                                    € {{ number_format($customer->orders_sum_total_amount ?? 0, 2, ',', '.') }} ↗
                                </span>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm font-bold text-gray-400">Nessun cliente corrispondente ai criteri di ricerca.</div>
                @endforelse
            </div>

            <!-- Vista Desktop & Tablet (>= sm) Table Compatta -->
            <div class="hidden sm:block overflow-x-auto min-w-0">
                <table class="w-full text-left divide-y divide-gray-100 border-collapse">
                    <thead class="bg-gray-50/80 text-[11px] font-black text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-3 py-3 whitespace-nowrap">Codice</th>
                            <th class="px-4 py-3">Ragione Sociale</th>
                            <th class="px-3 py-3 whitespace-nowrap">P.IVA</th>
                            <th class="px-3 py-3">Referente</th>
                            <th class="px-3 py-3 whitespace-nowrap">Agente di Rif.</th>
                            <th class="px-4 py-3 text-right whitespace-nowrap">Ordini & Totale</th>
                            <th class="px-3 py-3 text-center whitespace-nowrap">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs">
                        @forelse($customers as $customer)
                            <tr class="hover:bg-yellow-50/40 transition">
                                <td class="px-3 py-2.5 whitespace-nowrap">
                                    @if($customer->code)
                                        <span class="inline-block font-mono font-black text-xs bg-indigo-50 text-indigo-700 border border-indigo-200 px-2 py-0.5 rounded">
                                            {{ $customer->code }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 italic">N/D</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5">
                                    <div class="font-black text-slate-900">{{ $customer->business_name }}</div>
                                    @if($customer->user)
                                        <span class="inline-block text-[10px] text-emerald-700 font-bold" title="{{ $customer->user->email }}">
                                            ✓ B2B ({{ $customer->user->email }})
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 whitespace-nowrap text-gray-600 font-mono">{{ $customer->vat_number ?? '-' }}</td>
                                <td class="px-3 py-2.5 text-gray-600 whitespace-nowrap">{{ $customer->contact_name }} {{ $customer->contact_surname }}</td>
                                <td class="px-3 py-2.5 whitespace-nowrap">
                                    @if($customer->agents->isNotEmpty())
                                        <div class="flex flex-col gap-1">
                                            @foreach($customer->agents as $ag)
                                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-900 bg-yellow-100/80 border border-yellow-300 px-2 py-0.5 rounded-lg max-w-[170px] truncate" title="{{ $ag->name }} {{ $ag->surname }} ({{ $ag->email }})">
                                                    <span>👤</span> {{ $ag->name }} {{ $ag->surname }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-300 italic text-[11px]">Nessuno</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.b2b.orders.index', ['customer_id' => $customer->id]) }}" class="inline-flex items-center gap-2 px-3 py-1 bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 rounded-xl transition text-slate-900 group" title="Vedi ordini di {{ $customer->business_name }}">
                                        <span class="text-sm">📊</span>
                                        <div class="text-right">
                                            <div class="font-black text-[11px] uppercase text-slate-900 group-hover:text-amber-600 transition">
                                                {{ $customer->orders_count ?? 0 }} Ordini
                                            </div>
                                            <div class="text-[10px] font-bold text-emerald-700">
                                                € {{ number_format($customer->orders_sum_total_amount ?? 0, 2, ',', '.') }}
                                            </div>
                                        </div>
                                    </a>
                                </td>
                                <td class="px-3 py-2.5 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1">
                                        <a href="{{ route('admin.b2b.customers.edit', $customer) }}" class="p-1.5 text-slate-700 hover:text-black hover:bg-yellow-400 rounded-lg transition" title="Modifica Cliente e Codice">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <form action="{{ route('admin.b2b.customers.destroy', $customer) }}" method="POST" class="inline" onsubmit="return confirm('Sicuro?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition" title="Elimina">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-400 font-bold">Nessun cliente B2B corrispondente ai criteri di ricerca.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($customers->hasPages())
                <div class="p-4 bg-white border-t border-gray-100">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Importazione Clienti -->
    <div id="importCustomerModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 transform transition-all">
            <div class="flex justify-between items-center pb-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-lg">
                        👥
                    </div>
                    <div>
                        <h3 class="text-base font-black text-gray-900 uppercase">Sincronizzazione / Import Anagrafica</h3>
                        <p class="text-xs text-gray-500">Da FTPS diretto oppure file locale (.xlsx / .csv)</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('importCustomerModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-xl font-bold p-1">
                    &times;
                </button>
            </div>

            <!-- Opzione 1: FTPS -->
            <div class="mt-4 p-4 bg-purple-50 rounded-xl border border-purple-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div>
                    <div class="text-xs font-bold text-purple-950 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Preleva automaticamente da FTPS
                    </div>
                    <p class="text-[11px] text-purple-700 mt-0.5">Scarica l'ultimo file <strong>Clienti_Bicap.xlsx</strong> dall'area Output server</p>
                </div>
                <form action="{{ route('admin.b2b.customers.sync_ftps') }}" method="POST" class="shrink-0 w-full sm:w-auto">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto px-3.5 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-black rounded-xl shadow transition uppercase tracking-wider">
                        Sincronizza FTPS
                    </button>
                </form>
            </div>

            <div class="relative my-4">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
                <div class="relative flex justify-center text-xs uppercase"><span class="bg-white px-2 text-gray-400 font-bold text-[10px]">Oppure seleziona manualmente dal PC</span></div>
            </div>

            <form action="{{ route('admin.b2b.customers.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs text-slate-600 space-y-1">
                    <p class="font-bold text-slate-800">Formati supportati:</p>
                    <p>• File <strong>.xlsx</strong> (es. <code>Clienti_Bicap.xlsx</code> con colonne <code>CF_CodiceCF</code> e <code>CF_Nome</code>).</p>
                    <p>• File <strong>.csv</strong> delimitato da punto e virgola o virgola.</p>
                    <p class="text-[11px] text-slate-500 mt-1">I clienti esistenti con la stessa ragione sociale verranno aggiornati con il loro codice gestionale univoco.</p>
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-2">Seleziona File (.xlsx, .csv)</label>
                    <input type="file" name="file" accept=".xlsx,.csv,.txt" required class="block w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 border border-gray-200 rounded-xl p-1 cursor-pointer">
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" onclick="document.getElementById('importCustomerModal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition">
                        Annulla
                    </button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black uppercase rounded-xl shadow transition">
                        Avvia Importazione
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
