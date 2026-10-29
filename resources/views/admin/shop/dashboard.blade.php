<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-2xl">🛍️</span>
                    <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                        {{ __('Dashboard Shop') }}
                    </h2>
                    <span class="inline-flex items-center gap-1 bg-yellow-400/20 text-yellow-800 border border-yellow-400/40 text-[10px] font-black uppercase px-2 py-0.5 rounded-full">
                        Live Analytics &amp; E-Commerce
                    </span>
                </div>
                <p class="text-xs text-gray-500 mt-1 font-medium">
                    Panoramica vendite, andamento ordini, comportamento visitatori e statistiche del negozio online.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('public.home') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 text-gray-700 hover:text-black hover:border-black rounded-xl text-xs font-bold transition shadow-xs">
                    <span>Negozio Online</span>
                    <span class="text-xs">↗</span>
                </a>
                <a href="{{ route('admin.shop.ordini.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition shadow-sm">
                    <span>Tutti gli Ordini</span>
                    <span class="bg-yellow-400 text-black text-[10px] font-black px-1.5 py-0.2 rounded-full">{{ $newOrdersCount }} nuovi</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6 pb-12">
        <!-- 1. KPI STATS CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Fatturato Totale -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs relative overflow-hidden border-b-4 border-emerald-500">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-black text-gray-400 uppercase tracking-wider">Fatturato Shop</p>
                    <span class="text-lg">💰</span>
                </div>
                <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">€ {{ number_format($totalRevenue, 2, ',', '.') }}</p>
                <div class="mt-2.5 flex items-center justify-between text-xs">
                    <span class="text-gray-500 font-medium">Oggi: <strong class="text-slate-800 font-bold">€ {{ number_format($todayRevenue, 2, ',', '.') }}</strong></span>
                    <span class="font-bold text-[11px] px-2 py-0.5 rounded-full {{ $revenueGrowth >= 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                        {{ $revenueGrowth >= 0 ? '↑ +' : '↓ ' }}{{ $revenueGrowth }}% vs mese prec.
                    </span>
                </div>
            </div>

            <!-- Ordini Totali & Nuovi -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs relative overflow-hidden border-b-4 border-yellow-400">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-black text-gray-400 uppercase tracking-wider">Ordini Ricevuti</p>
                    <span class="text-lg">🛒</span>
                </div>
                <div class="flex items-baseline gap-2 mt-1">
                    <p class="text-2xl sm:text-3xl font-black text-slate-900">{{ $ordersCount }}</p>
                    @if($newOrdersCount > 0)
                        <span class="text-xs bg-yellow-400 text-slate-950 font-black px-2 py-0.5 rounded-md animate-pulse">
                            {{ $newOrdersCount }} da evadere
                        </span>
                    @endif
                </div>
                <div class="mt-2.5 flex items-center justify-between text-xs text-gray-500 font-medium">
                    <span>Spediti: <strong class="text-slate-800 font-bold">{{ $shippedOrdersCount }}</strong></span>
                    <span>Annullati: <strong class="text-slate-800 font-bold">{{ $cancelledOrdersCount }}</strong></span>
                </div>
            </div>

            <!-- Scontrino Medio (AOV) -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs relative overflow-hidden border-b-4 border-indigo-500">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-black text-gray-400 uppercase tracking-wider">Scontrino Medio (AOV)</p>
                    <span class="text-lg">🏷️</span>
                </div>
                <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">€ {{ number_format($aov, 2, ',', '.') }}</p>
                <div class="mt-2.5 flex items-center justify-between text-xs text-gray-500 font-medium">
                    <span>Sconti coupon: <strong class="text-slate-800 font-bold">€ {{ number_format($totalDiscountsGiven, 2, ',', '.') }}</strong></span>
                    <span class="text-[11px] bg-indigo-50 text-indigo-700 px-1.5 py-0.5 rounded font-bold">{{ $couponOrdersCount }} con coupon</span>
                </div>
            </div>

            <!-- Clienti & Newsletter -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs relative overflow-hidden border-b-4 border-sky-500">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-black text-gray-400 uppercase tracking-wider">Utenti &amp; Lead</p>
                    <span class="text-lg">👥</span>
                </div>
                <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">{{ $shopUsersCount + $guestCustomersCount }}</p>
                <div class="mt-2.5 flex items-center justify-between text-xs text-gray-500 font-medium">
                    <span>Newsletter: <strong class="text-slate-800 font-bold">{{ $newsletterConfirmed }}</strong> conf.</span>
                    <span class="text-[11px] bg-sky-50 text-sky-700 px-1.5 py-0.5 rounded font-bold">+{{ $newUsersThisMonth }} questo mese</span>
                </div>
            </div>
        </div>

        <!-- 2. GRAFICO TREND VENDITE & HUB ANALYTICS -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Grafico Chart.js (2 Colonne) -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-xs p-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-gray-100 gap-2">
                    <div>
                        <h3 class="font-black text-sm uppercase tracking-wider text-slate-900 flex items-center gap-2">
                            <span>📈</span>
                            <span>Andamento Vendite &amp; Ordini (Ultimi 14 Giorni)</span>
                        </h3>
                        <p class="text-xs text-gray-400 font-medium">Evoluzione del fatturato giornaliero e volume ordini dello Shop</p>
                    </div>
                    <div class="flex items-center gap-3 text-xs">
                        <span class="flex items-center gap-1.5 font-bold text-slate-700">
                            <span class="w-3 h-3 rounded-full bg-[#fedb00] inline-block border border-black/20"></span>
                            <span>Fatturato (€)</span>
                        </span>
                        <span class="flex items-center gap-1.5 font-bold text-slate-700">
                            <span class="w-3 h-3 rounded-full bg-slate-900 inline-block"></span>
                            <span>N. Ordini</span>
                        </span>
                    </div>
                </div>

                <div class="mt-4 relative h-72 w-full">
                    <canvas id="shopSalesChart"></canvas>
                </div>
            </div>

            <!-- Hub Analytics & Traffico (1 Colonna) -->
            <div class="bg-gradient-to-br from-slate-950 via-zinc-900 to-black text-white rounded-2xl p-6 shadow-md flex flex-col justify-between border border-zinc-800">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[10px] font-black uppercase tracking-widest text-[#fedb00] bg-yellow-400/10 px-2.5 py-1 rounded-full border border-yellow-400/30">
                            Web Analytics
                        </span>
                        <span class="flex items-center gap-1.5 text-xs text-emerald-400 font-bold">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                            <span>Monitoraggio Attivo</span>
                        </span>
                    </div>

                    <h4 class="text-lg font-black tracking-tight text-white mb-1">
                        Google Analytics 4
                    </h4>
                    <p class="text-xs text-zinc-400 mb-4 leading-relaxed font-medium">
                        I tag di tracciamento e misurazione e-commerce sono attivi sul sito vetrina e sul catalogo prodotti.
                    </p>

                    <!-- Info Tag Box -->
                    <div class="space-y-2 bg-zinc-900/90 rounded-xl p-3 border border-zinc-800 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-zinc-400 font-medium">Measurement ID GA4:</span>
                            <span class="font-mono font-bold text-[#fedb00] bg-black/60 px-2 py-0.5 rounded border border-yellow-500/30">
                                {{ $gaId }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-zinc-400 font-medium">Google Tag Manager:</span>
                            <span class="font-mono text-zinc-300 font-bold">
                                {{ $gtmId ?: 'Non configurato' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-zinc-400 font-medium">Meta / FB Pixel:</span>
                            <span class="font-mono text-zinc-300 font-bold">
                                {{ $fbPixel ?: 'Non configurato' }}
                            </span>
                        </div>
                    </div>

                    <!-- Dispositivi Visitatori -->
                    <div class="mt-4 pt-4 border-t border-zinc-800">
                        <p class="text-[11px] font-black uppercase tracking-wider text-zinc-400 mb-2">Dispositivi Visitatori</p>
                        <div class="space-y-1.5">
                            <div>
                                <div class="flex justify-between text-xs font-bold mb-1">
                                    <span class="text-zinc-300">📱 Mobile &amp; Smartphone</span>
                                    <span class="text-[#fedb00]">{{ $deviceStats['mobile_pct'] }}%</span>
                                </div>
                                <div class="w-full bg-zinc-800 h-2 rounded-full overflow-hidden">
                                    <div class="bg-[#fedb00] h-full rounded-full" style="width: {{ $deviceStats['mobile_pct'] }}%"></div>
                                </div>
                            </div>
                            <div class="pt-1">
                                <div class="flex justify-between text-xs font-bold mb-1">
                                    <span class="text-zinc-300">💻 Desktop / Laptop</span>
                                    <span class="text-zinc-400">{{ $deviceStats['desktop_pct'] }}%</span>
                                </div>
                                <div class="w-full bg-zinc-800 h-2 rounded-full overflow-hidden">
                                    <div class="bg-zinc-500 h-full rounded-full" style="width: {{ $deviceStats['desktop_pct'] }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-zinc-800 flex flex-col gap-2">
                    <a href="https://analytics.google.com/" target="_blank" rel="noopener noreferrer" class="w-full inline-flex items-center justify-center gap-2 bg-[#fedb00] hover:bg-yellow-300 text-slate-950 font-black text-xs py-2.5 px-4 rounded-xl transition shadow-md">
                        <span>Apri Console Google Analytics</span>
                        <span>↗</span>
                    </a>
                    <a href="{{ route('admin.settings.edit') }}" class="w-full text-center text-[11px] text-zinc-400 hover:text-white transition py-1 underline">
                        Configura codici e pixel di tracciamento
                    </a>
                </div>
            </div>
        </div>

        <!-- 3. ORDINI RECENTI & RIPARTIZIONE STATI / PAGAMENTI -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Tabella Ultimi Ordini (2 Colonne) -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <div>
                        <h3 class="font-black text-xs uppercase tracking-wider text-slate-900">
                            Ultimi Ordini Ricevuti nello Shop
                        </h3>
                        <p class="text-[11px] text-gray-500">I più recenti acquisti effettuati sul sito con relativo stato e pagamento</p>
                    </div>
                    <a href="{{ route('admin.shop.ordini.index') }}" class="text-xs font-black text-slate-900 hover:text-amber-600 uppercase transition">
                        Vedi Tutti ({{ $ordersCount }}) →
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 text-[10px] font-black uppercase text-gray-400 bg-gray-50/30">
                                <th class="py-3 px-4">Ordine</th>
                                <th class="py-3 px-4">Cliente</th>
                                <th class="py-3 px-4">Data</th>
                                <th class="py-3 px-4">Pagamento</th>
                                <th class="py-3 px-4">Stato</th>
                                <th class="py-3 px-4 text-right">Totale</th>
                                <th class="py-3 px-4 text-center">Azioni</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs font-medium">
                            @forelse($recentOrders as $order)
                                <tr class="hover:bg-yellow-50/30 transition">
                                    <td class="py-3.5 px-4 font-black text-slate-900">
                                        <a href="{{ route('admin.shop.ordini.edit', $order->id) }}" class="hover:text-amber-600 transition flex items-center gap-1.5">
                                            <span>#{{ $order->id }}</span>
                                            <span class="text-[10px] font-mono text-gray-400">({{ $order->numero_ordine }})</span>
                                        </a>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if($order->customer)
                                            <div class="font-bold text-slate-800">{{ $order->customer->nome }} {{ $order->customer->cognome }}</div>
                                            <div class="text-[11px] text-gray-400">{{ $order->customer->email }}</div>
                                        @elseif($order->spedizione_nome)
                                            <div class="font-bold text-slate-800">{{ $order->spedizione_nome }}</div>
                                            <div class="text-[11px] text-gray-400">{{ $order->spedizione_citta }} ({{ $order->spedizione_provincia }})</div>
                                        @else
                                            <span class="text-gray-400 italic">Cliente Ospite</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-gray-500 whitespace-nowrap">
                                        <div>{{ $order->created_at->format('d/m/Y') }}</div>
                                        <div class="text-[10px] text-gray-400">{{ $order->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold uppercase px-2 py-0.5 rounded-full {{ $order->stato_pagamento === 'pagato' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                            <span>{{ $order->metodo_pagamento }}</span>
                                            <span>({{ $order->stato_pagamento }})</span>
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        @php
                                            $statoClass = match($order->stato) {
                                                'nuovo' => 'bg-yellow-100 text-yellow-900 border border-yellow-300 font-bold',
                                                'spedito' => 'bg-emerald-100 text-emerald-900 border border-emerald-300 font-bold',
                                                'annullato' => 'bg-rose-100 text-rose-900 border border-rose-300 font-bold',
                                                default => 'bg-gray-100 text-gray-800'
                                            };
                                        @endphp
                                        <span class="inline-block text-[10px] uppercase px-2 py-0.5 rounded {{ $statoClass }}">
                                            {{ $order->stato }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-black text-slate-900 whitespace-nowrap">
                                        € {{ number_format($order->totale_ordine, 2, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <a href="{{ route('admin.shop.ordini.edit', $order->id) }}" class="inline-flex items-center justify-center w-7 h-7 bg-gray-100 hover:bg-slate-900 hover:text-white text-gray-700 rounded-lg text-xs transition" title="Gestisci Ordine">
                                            ✏️
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-gray-400 italic">Nessun ordine registrato</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Box Metodi di Pagamento & Stato Ordini (1 Colonna) -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-5 flex flex-col justify-between">
                <div>
                    <h3 class="font-black text-xs uppercase tracking-wider text-slate-900 pb-3 border-b border-gray-100 flex items-center justify-between">
                        <span>💳 Metodi di Pagamento</span>
                        <span class="text-[10px] text-gray-400 font-bold">Ripartizione</span>
                    </h3>

                    <div class="mt-4 space-y-3">
                        @foreach($ordersByPayment as $pay)
                            @php
                                $pct = $ordersCount > 0 ? round(($pay->count / $ordersCount) * 100) : 0;
                                $icon = match(strtolower($pay->metodo_pagamento)) {
                                    'contrassegno' => '📦',
                                    'stripe', 'carta' => '💳',
                                    'paypal' => '🅿️',
                                    'bonifico' => '🏦',
                                    default => '💵'
                                };
                            @endphp
                            <div>
                                <div class="flex items-center justify-between text-xs font-bold mb-1">
                                    <span class="flex items-center gap-1.5 text-slate-700">
                                        <span>{{ $icon }}</span>
                                        <span class="uppercase">{{ $pay->metodo_pagamento }}</span>
                                    </span>
                                    <span class="text-slate-900">{{ $pay->count }} ordini ({{ $pct }}%)</span>
                                </div>
                                <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                                    <div class="bg-slate-800 h-full rounded-full" style="width: {{ $pct }}%"></div>
                                </div>
                                <div class="text-[10px] text-gray-400 mt-0.5 text-right font-medium">
                                    Totale: € {{ number_format($pay->total, 2, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Stato Ordini -->
                    <div class="mt-6 pt-4 border-t border-gray-100">
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-900 mb-3">
                            Riepilogo Avanzamento
                        </h4>
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div class="bg-amber-50 border border-amber-200 p-2.5 rounded-xl">
                                <p class="text-[10px] font-black uppercase text-amber-800">Nuovi</p>
                                <p class="text-lg font-black text-amber-900">{{ $newOrdersCount }}</p>
                            </div>
                            <div class="bg-emerald-50 border border-emerald-200 p-2.5 rounded-xl">
                                <p class="text-[10px] font-black uppercase text-emerald-800">Spediti</p>
                                <p class="text-lg font-black text-emerald-900">{{ $shippedOrdersCount }}</p>
                            </div>
                            <div class="bg-rose-50 border border-rose-200 p-2.5 rounded-xl">
                                <p class="text-[10px] font-black uppercase text-rose-800">Annullati</p>
                                <p class="text-lg font-black text-rose-900">{{ $cancelledOrdersCount }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100">
                    <a href="{{ route('admin.shop.shipping_costs.index') }}" class="w-full flex items-center justify-between text-xs font-bold text-slate-700 hover:text-black p-2 rounded-lg hover:bg-gray-50 transition">
                        <span>🚚 Configura Spese di Spedizione</span>
                        <span>→</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- 4. RICERCHE DEGLI UTENTI & PRODOTTI PIÙ VENDUTI -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Box Ricerche Utenti & Query Popolari -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-5">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div>
                        <h3 class="font-black text-xs uppercase tracking-wider text-slate-900 flex items-center gap-2">
                            <span>🔍</span>
                            <span>Ricerche &amp; Termini più Cercati</span>
                        </h3>
                        <p class="text-[11px] text-gray-500">Cosa cercano gli utenti nel catalogo dello shop e filtri più applicati</p>
                    </div>
                    <span class="text-xs bg-gray-100 text-gray-700 font-bold px-2.5 py-0.5 rounded-full">
                        {{ $totalSearchHits }} ricerche
                    </span>
                </div>

                <div class="mt-4 divide-y divide-gray-100">
                    @forelse($topSearches as $search)
                        <div class="py-2.5 flex items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="text-gray-400 font-mono text-[11px]">#{{ $loop->iteration }}</span>
                                <div class="truncate">
                                    <p class="font-bold text-slate-900 truncate">"{{ $search->query }}"</p>
                                    <div class="flex items-center gap-1.5 text-[10px] text-gray-400 mt-0.5">
                                        @php
                                            $typeBadge = match($search->type) {
                                                'filtro_categoria' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                                'filtro_settore' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                'filtro_collezione' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                default => 'bg-gray-100 text-gray-700 border-gray-200'
                                            };
                                        @endphp
                                        <span class="px-1.5 py-0.2 rounded border font-mono {{ $typeBadge }}">
                                            {{ str_replace('filtro_', '', $search->type) }}
                                        </span>
                                        <span>• Risultati trovati: <strong class="text-slate-700">{{ $search->results_count }}</strong></span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="font-black text-slate-900 bg-yellow-400/20 text-yellow-900 border border-yellow-400/30 px-2 py-0.5 rounded-full text-xs">
                                    {{ $search->hits }} ricerche
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center text-gray-400 italic text-xs">Nessuna ricerca registrata</div>
                    @endforelse
                </div>

                <!-- Box ricerche a vuoto / Alert opportunità catalogo -->
                @if($zeroResultSearches->isNotEmpty())
                    <div class="mt-4 p-3.5 bg-amber-50/80 border border-amber-200 rounded-xl">
                        <div class="flex items-center gap-1.5 text-xs font-black text-amber-900 uppercase">
                            <span>💡</span>
                            <span>Opportunità di Catalogo (Ricerche senza Risultati)</span>
                        </div>
                        <p class="text-[11px] text-amber-800 mt-1">
                            Termini cercati dai visitatori che non hanno prodotto risultati:
                        </p>
                        <div class="flex flex-wrap gap-1.5 mt-2">
                            @foreach($zeroResultSearches as $z)
                                <span class="bg-white border border-amber-300 text-amber-950 font-bold px-2 py-0.5 rounded text-[11px]">
                                    "{{ $z->query }}" ({{ $z->hits }} volte)
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Box Prodotti Più Venduti -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-5">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div>
                        <h3 class="font-black text-xs uppercase tracking-wider text-slate-900 flex items-center gap-2">
                            <span>🏆</span>
                            <span>Prodotti più Venduti (Top Sellers)</span>
                        </h3>
                        <p class="text-[11px] text-gray-500">I prodotti con il maggior volume di vendite e fatturato</p>
                    </div>
                    <a href="{{ route('admin.shop.ordini.index') }}" class="text-xs font-bold text-gray-400 hover:text-black">
                        Dettaglio →
                    </a>
                </div>

                <div class="mt-4 divide-y divide-gray-100">
                    @forelse($topProducts as $tp)
                        <div class="py-3 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-12 h-12 rounded-xl bg-gray-50 border border-gray-200 p-1 flex items-center justify-center shrink-0 overflow-hidden">
                                    <img src="{{ $tp->image_url }}" alt="{{ $tp->nome_prodotto }}" class="max-h-full max-w-full object-contain" onerror="this.src='{{ asset('images/placeholder.png') }}'">
                                </div>
                                <div class="truncate">
                                    <h4 class="font-black text-xs text-slate-900 uppercase tracking-tight truncate">
                                        {{ $tp->nome_prodotto }}
                                    </h4>
                                    <p class="text-[11px] text-gray-400 font-mono mt-0.5">SKU: {{ $tp->sku ?: '-' }}</p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <div class="text-xs font-black text-slate-900">
                                    {{ $tp->total_qty }} pz venduti
                                </div>
                                <div class="text-[11px] font-bold text-emerald-600">
                                    € {{ number_format($tp->total_revenue, 2, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-gray-400 italic text-xs">Nessun prodotto venduto finora</div>
                    @endforelse
                </div>

                <div class="mt-5 pt-3 border-t border-gray-100 text-center">
                    <a href="{{ route('public.home') }}" target="_blank" class="text-xs font-bold text-slate-800 hover:text-amber-600 transition inline-flex items-center gap-1">
                        <span>Esplora tutti i prodotti a catalogo sul sito</span>
                        <span>↗</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- 5. UTENTI, NEWSLETTER & GESTIONE COUPON -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Box Newsletter & Lead Generation -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-5">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div>
                        <h3 class="font-black text-xs uppercase tracking-wider text-slate-900 flex items-center gap-2">
                            <span>✉️</span>
                            <span>Newsletter &amp; Nuovi Iscritti</span>
                        </h3>
                        <p class="text-[11px] text-gray-500">Iscrizioni raccolte dal sito web con conferma email double opt-in</p>
                    </div>
                    <a href="{{ route('admin.shop.newsletter.index') }}" class="text-xs font-black text-slate-900 hover:text-amber-600 uppercase">
                        Gestisci ({{ $newsletterTotal }}) →
                    </a>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3 mb-4">
                    <div class="bg-gray-50 p-3 rounded-xl border border-gray-200/60">
                        <p class="text-[10px] font-black uppercase text-gray-400">Totale Iscritti</p>
                        <p class="text-xl font-black text-slate-900 mt-0.5">{{ $newsletterTotal }}</p>
                    </div>
                    <div class="bg-emerald-50 p-3 rounded-xl border border-emerald-200/60">
                        <p class="text-[10px] font-black uppercase text-emerald-700">Email Confermate</p>
                        <p class="text-xl font-black text-emerald-900 mt-0.5">{{ $newsletterConfirmed }}</p>
                    </div>
                </div>

                <div class="divide-y divide-gray-100 text-xs">
                    @forelse($newsletterRecent as $sub)
                        <div class="py-2.5 flex items-center justify-between gap-2">
                            <div class="truncate">
                                <p class="font-bold text-slate-800 truncate">{{ $sub->email }}</p>
                                <p class="text-[10px] text-gray-400">{{ $sub->created_at->format('d/m/Y H:i') }} • IP: {{ $sub->ip_address ?: 'N/D' }}</p>
                            </div>
                            <div class="shrink-0">
                                @if($sub->confirmed || $sub->confirmed_at)
                                    <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-800 font-bold text-[10px] px-2 py-0.5 rounded-full">
                                        ✓ Confermato
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-800 font-bold text-[10px] px-2 py-0.5 rounded-full">
                                        In Attesa Conferma
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="py-4 text-center text-gray-400 italic text-xs">Nessuna iscrizione recente</div>
                    @endforelse
                </div>

                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
                    <a href="{{ route('admin.shop.newsletter.export') }}" class="text-xs font-bold text-slate-700 hover:text-black flex items-center gap-1">
                        <span>📥 Esporta Lista Iscritti (.csv)</span>
                    </a>
                    <a href="{{ route('admin.shop.newsletter.index') }}" class="text-xs font-bold text-amber-600 hover:underline">
                        Visualizza tutti →
                    </a>
                </div>
            </div>

            <!-- Box Coupon & Promozioni -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-5">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div>
                        <h3 class="font-black text-xs uppercase tracking-wider text-slate-900 flex items-center gap-2">
                            <span>🎟️</span>
                            <span>Coupon Sconto &amp; Promozioni</span>
                        </h3>
                        <p class="text-[11px] text-gray-500">Codici coupon attivi per il carrello e sconti generati</p>
                    </div>
                    <a href="{{ route('admin.shop.coupons.index') }}" class="text-xs font-black text-slate-900 hover:text-amber-600 uppercase">
                        Gestione Coupon →
                    </a>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3 mb-4">
                    <div class="bg-gray-50 p-3 rounded-xl border border-gray-200/60">
                        <p class="text-[10px] font-black uppercase text-gray-400">Coupon Creati</p>
                        <p class="text-xl font-black text-slate-900 mt-0.5">{{ $couponsTotal }}</p>
                        <span class="text-[10px] text-gray-500 font-medium">Attivi: {{ $couponsActive }}</span>
                    </div>
                    <div class="bg-indigo-50 p-3 rounded-xl border border-indigo-200/60">
                        <p class="text-[10px] font-black uppercase text-indigo-700">Sconti Totali</p>
                        <p class="text-xl font-black text-indigo-900 mt-0.5">€ {{ number_format($totalDiscountsGiven, 2, ',', '.') }}</p>
                        <span class="text-[10px] text-indigo-600 font-medium">{{ $couponOrdersCount }} utilizzi</span>
                    </div>
                </div>

                <div class="p-4 bg-yellow-50/70 border border-yellow-200/80 rounded-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-yellow-800 bg-yellow-200/70 px-2 py-0.5 rounded">
                                Coupon Newsletter Automatico
                            </span>
                            <p class="font-mono font-bold text-sm text-slate-900 mt-1">
                                welcomebicapshop-X
                            </p>
                            <p class="text-[11px] text-gray-600 mt-0.5">
                                Generato univocamente con sconto 5% (min. spesa €100), monouso ad auto-disattivazione.
                            </p>
                        </div>
                        <a href="{{ route('admin.shop.coupons.index') }}" class="shrink-0 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition">
                            Vedi Tutti
                        </a>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                    <a href="{{ route('admin.shop.coupons.create') }}" class="font-bold text-slate-900 hover:text-amber-600 flex items-center gap-1">
                        <span>➕ Crea Nuovo Coupon</span>
                    </a>
                    <a href="{{ route('admin.shop.configuration') }}" class="font-bold text-gray-500 hover:text-black">
                        Configurazione Generale Shop →
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js CDN & Inizializzazione Grafico -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('shopSalesChart');
            if (!ctx) return;

            const labels = @json($chartLabels);
            const dataRevenue = @json($chartRevenue);
            const dataOrders = @json($chartOrders);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Fatturato (€)',
                            data: dataRevenue,
                            borderColor: '#fedb00',
                            backgroundColor: 'rgba(254, 219, 0, 0.15)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 3,
                            pointBackgroundColor: '#000000',
                            pointBorderColor: '#fedb00',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            yAxisID: 'y',
                        },
                        {
                            label: 'Volume Ordini',
                            data: dataOrders,
                            borderColor: '#0f172a',
                            backgroundColor: 'rgba(15, 23, 42, 0.05)',
                            fill: false,
                            tension: 0.2,
                            borderWidth: 2,
                            pointBackgroundColor: '#0f172a',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            yAxisID: 'y1',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleFont: { size: 12, weight: 'bold' },
                            bodyFont: { size: 12 },
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    if (context.dataset.yAxisID === 'y') {
                                        return ' Fatturato: € ' + Number(context.parsed.y).toLocaleString('it-IT', { minimumFractionDigits: 2 });
                                    }
                                    return ' Ordini: ' + context.parsed.y;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11, weight: '600' }, color: '#64748b' }
                        },
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            grid: { color: 'rgba(226, 232, 240, 0.6)' },
                            ticks: {
                                font: { size: 11 },
                                color: '#64748b',
                                callback: function(value) { return '€ ' + value; }
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: { drawOnChartArea: false },
                            ticks: {
                                font: { size: 11 },
                                color: '#0f172a',
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
