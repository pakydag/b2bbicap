<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Modifica Coupon Shop</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h3 class="text-2xl font-extrabold text-slate-900 flex items-center gap-2">
                        <span>Modifica Coupon:</span>
                        <span class="font-mono text-amber-600 uppercase">{{ $coupon->code }}</span>
                    </h3>
                    <p class="text-sm text-slate-500 mt-1">Aggiorna le impostazioni dello sconto, spesa minima e regole di validità.</p>
                </div>
                <a href="{{ route('admin.shop.coupons.index') }}" class="text-sm font-bold text-slate-600 hover:text-black transition-colors">
                    &larr; Torna ai Coupon
                </a>
            </div>

            @if($errors->any())
                <div class="mb-6 bg-rose-50 border-l-4 border-rose-500 p-4 rounded-md">
                    <div class="flex">
                        <div class="ml-3">
                            <h3 class="text-sm font-bold text-rose-800">Si sono verificati degli errori:</h3>
                            <ul class="mt-2 list-disc pl-5 text-sm text-rose-700 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
                <form action="{{ route('admin.shop.coupons.update', $coupon) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Codice Coupon -->
                        <div>
                            <label for="code" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Codice Coupon *
                            </label>
                            <input type="text" 
                                   name="code" 
                                   id="code" 
                                   value="{{ old('code', $coupon->code) }}" 
                                   required 
                                   placeholder="es. welcomebicapshop" 
                                   class="w-full font-mono font-bold text-slate-900 border-slate-200 rounded-xl focus:ring-amber-400 focus:border-amber-400 uppercase">
                            <p class="text-xs text-slate-400 mt-1">Il codice che il cliente deve inserire nel carrello.</p>
                        </div>

                        <!-- Nome / Descrizione -->
                        <div>
                            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Nome / Descrizione Coupon
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name', $coupon->name) }}" 
                                   placeholder="es. Sconto Benvenuto Newsletter 5%" 
                                   class="w-full text-slate-900 border-slate-200 rounded-xl focus:ring-amber-400 focus:border-amber-400">
                            <p class="text-xs text-slate-400 mt-1">Riferimento interno visibile nel gestionale.</p>
                        </div>

                        <!-- Tipo di Sconto -->
                        <div>
                            <label for="type" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Tipo di Sconto *
                            </label>
                            <select name="type" 
                                    id="type" 
                                    required 
                                    class="w-full border-slate-200 rounded-xl focus:ring-amber-400 focus:border-amber-400 font-semibold text-slate-800">
                                <option value="percentuale" {{ old('type', $coupon->type) === 'percentuale' ? 'selected' : '' }}>Percentuale (%)</option>
                                <option value="fisso" {{ old('type', $coupon->type) === 'fisso' ? 'selected' : '' }}>Importo Fisso (€)</option>
                            </select>
                            <p class="text-xs text-slate-400 mt-1">Sconto in % sul totale o importo in euro detratto.</p>
                        </div>

                        <!-- Valore dello Sconto -->
                        <div>
                            <label for="valore" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Valore Sconto *
                            </label>
                            <input type="number" 
                                   step="0.01" 
                                   name="valore" 
                                   id="valore" 
                                   value="{{ old('valore', $coupon->valore) }}" 
                                   required 
                                   placeholder="5.00" 
                                   class="w-full border-slate-200 rounded-xl focus:ring-amber-400 focus:border-amber-400 font-bold text-slate-900">
                            <p class="text-xs text-slate-400 mt-1">Es. 5 per 5% di sconto oppure 10 per 10€ di sconto.</p>
                        </div>

                        <!-- Spesa Minima -->
                        <div>
                            <label for="min_spesa" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Spesa Minima Richiesta (€)
                            </label>
                            <input type="number" 
                                   step="0.01" 
                                   name="min_spesa" 
                                   id="min_spesa" 
                                   value="{{ old('min_spesa', $coupon->min_spesa) }}" 
                                   placeholder="0.00" 
                                   class="w-full border-slate-200 rounded-xl focus:ring-amber-400 focus:border-amber-400 text-slate-800">
                            <p class="text-xs text-slate-400 mt-1">Imposta 0 se non c'è spesa minima (es. 100€ per la newsletter).</p>
                        </div>

                        <!-- Durata / Riutilizzo -->
                        <div>
                            <label for="sempre_attivo" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Modalità di Utilizzo *
                            </label>
                            <select name="sempre_attivo" 
                                    id="sempre_attivo" 
                                    required 
                                    class="w-full border-slate-200 rounded-xl focus:ring-amber-400 focus:border-amber-400 font-semibold text-slate-800">
                                <option value="1" {{ old('sempre_attivo', $coupon->sempre_attivo ? '1' : '0') === '1' ? 'selected' : '' }}>🔁 Resta sempre attivo (riutilizzabile)</option>
                                <option value="0" {{ old('sempre_attivo', $coupon->sempre_attivo ? '1' : '0') === '0' ? 'selected' : '' }}>🎯 Disattiva dopo il suo utilizzo (Mono-uso)</option>
                            </select>
                            <p class="text-xs text-slate-400 mt-1">Se mono-uso, appena un cliente completa un ordine con questo coupon, verrà disattivato.</p>
                        </div>
                    </div>

                    <!-- Info Statistiche -->
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 flex flex-wrap items-center justify-between gap-4 text-xs text-slate-600">
                        <div>
                            <strong>Utilizzi Registrati:</strong> <span class="font-bold text-slate-900 text-sm ml-1">{{ $coupon->utilizzi }}</span>
                        </div>
                        <div>
                            <strong>Ultimo Utilizzo:</strong> <span class="font-medium text-slate-800 ml-1">{{ $coupon->usato_il ? $coupon->usato_il->format('d/m/Y H:i') : 'Mai utilizzato' }}</span>
                        </div>
                        <div>
                            <strong>Creato il:</strong> <span class="font-medium text-slate-800 ml-1">{{ $coupon->created_at ? $coupon->created_at->format('d/m/Y H:i') : 'N/D' }}</span>
                        </div>
                    </div>

                    <!-- Stato Attivo / Disattivo -->
                    <div class="pt-4 border-t border-slate-100">
                        <label class="inline-flex items-center gap-3 cursor-pointer">
                            <input type="hidden" name="attivo" value="0">
                            <input type="checkbox" name="attivo" value="1" {{ old('attivo', $coupon->attivo ? '1' : '0') === '1' ? 'checked' : '' }} class="rounded border-slate-300 text-amber-500 focus:ring-amber-400 w-5 h-5">
                            <div>
                                <span class="text-sm font-bold text-slate-900">Coupon Attivo</span>
                                <p class="text-xs text-slate-400">Se disabilitato, non potrà essere applicato nel carrello dai clienti.</p>
                            </div>
                        </label>
                    </div>

                    <!-- Bottoni azione -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                        <a href="{{ route('admin.shop.coupons.index') }}" class="px-5 py-2.5 text-xs font-bold uppercase text-slate-600 hover:text-slate-900 transition">
                            Annulla
                        </a>
                        <button type="submit" class="px-6 py-3 bg-amber-400 hover:bg-amber-500 text-slate-950 font-black text-xs uppercase tracking-wider rounded-xl shadow-md transition-all cursor-pointer">
                            Aggiorna Coupon
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>

