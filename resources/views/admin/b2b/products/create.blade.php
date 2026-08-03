<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nuovo Prodotto B2B & Varianti') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ variants: [{size: '', color: '', quantity: 0}] }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('admin.b2b.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Dati Base -->
                    <div class="lg:col-span-1 border-r pr-6 space-y-4">
                        <div class="bg-white p-6 shadow sm:rounded-lg">
                            <h3 class="text-md font-bold mb-4 border-b pb-2">Dati Generali</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nome Prodotto *</label>
                                    <input type="text" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Codice Articolo</label>
                                    <input type="text" name="code" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Linea *</label>
                                    <select name="b2b_brand_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                        <option value="">Seleziona...</option>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Stagione</label>
                                    <input type="text" name="season" placeholder="es. PE 2024" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Prezzo B2B (€) *</label>
                                    <input type="number" step="0.01" name="price" placeholder="0.00" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">URL Foto Principale</label>
                                    <input type="text" name="image_url" placeholder="https://esempio.com/foto.jpg" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Carica Foto</label>
                                    <input type="file" name="image_file" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                </div>
                                <div>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="has_stock" value="1" checked class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-600">Gestisci Magazzino (Giacenze)</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Varianti Matrix -->
                    <div class="lg:col-span-2">
                        <div class="bg-white p-6 shadow sm:rounded-lg">
                            <div class="flex justify-between items-center mb-4 border-b pb-2">
                                <h3 class="text-md font-bold">Varianti (Taglie & Colori)</h3>
                                <button type="button" @click="variants.push({size: '', color: '', quantity: 0})" class="text-xs bg-gray-100 hover:bg-gray-200 px-3 py-1 rounded-md border">+ Aggiungi Riga</button>
                            </div>

                            <div class="space-y-2">
                                <div class="grid grid-cols-12 gap-2 text-xs font-bold text-gray-500 uppercase px-2">
                                    <div class="col-span-4">Colore</div>
                                    <div class="col-span-4">Taglia</div>
                                    <div class="col-span-3">Q.tà</div>
                                    <div class="col-span-1"></div>
                                </div>

                                <template x-for="(variant, index) in variants" :key="index">
                                    <div class="grid grid-cols-12 gap-2 items-center bg-gray-50 p-2 rounded-md border border-gray-100">
                                        <div class="col-span-4">
                                            <input type="text" :name="'variants['+index+'][color]'" x-model="variant.color" placeholder="es. Nero" class="w-full text-sm border-gray-300 rounded-md shadow-sm">
                                        </div>
                                        <div class="col-span-4">
                                            <input type="text" :name="'variants['+index+'][size]'" x-model="variant.size" placeholder="es. XL o 42" class="w-full text-sm border-gray-300 rounded-md shadow-sm">
                                        </div>
                                        <div class="col-span-3">
                                            <input type="number" :name="'variants['+index+'][quantity]'" x-model="variant.quantity" class="w-full text-sm border-gray-300 rounded-md shadow-sm">
                                        </div>
                                        <div class="col-span-1 text-center">
                                            <button type="button" @click="variants.splice(index, 1)" class="text-red-400 hover:text-red-600">×</button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- B2B Characteristics -->
                <div class="mt-8 bg-white p-6 shadow sm:rounded-lg">
                    <h3 class="text-lg font-bold text-indigo-900 mb-4 uppercase tracking-wider border-b pb-2">Caratteristiche B2B</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- CAT-SICUREZZA -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase">Categoria di Sicurezza</label>
                            <input type="text" name="characteristics[CAT-SICUREZZA]" value="{{ old('characteristics.CAT-SICUREZZA') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                        <!-- RANGE-TAGLIE-VALORE -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase">Range Taglie</label>
                            <input type="text" name="characteristics[RANGE-TAGLIE-VALORE]" value="{{ old('characteristics.RANGE-TAGLIE-VALORE') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                        <!-- NORMA -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase">Normativa</label>
                            <input type="text" name="characteristics[NORMA]" value="{{ old('characteristics.NORMA') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                        <!-- CALZATA -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase">Calzata</label>
                            <input type="text" name="characteristics[CALZATA]" value="{{ old('characteristics.CALZATA') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                        <!-- SOLETTO -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase">Soletto</label>
                            <input type="text" name="characteristics[SOLETTO]" value="{{ old('characteristics.SOLETTO') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                        <!-- MODELLO -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase">Modello</label>
                            <input type="text" name="characteristics[MODELLO]" value="{{ old('characteristics.MODELLO') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <!-- SETTORE-DI-UTILIZZO-IT -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase">Settori di Utilizzo (IT)</label>
                            <input type="text" name="characteristics[SETTORE-DI-UTILIZZO-IT]" value="{{ old('characteristics.SETTORE-DI-UTILIZZO-IT') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                        <!-- SETTORE-DI-UTILIZZO-EN -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase">Settori di Utilizzo (EN)</label>
                            <input type="text" name="characteristics[SETTORE-DI-UTILIZZO-EN]" value="{{ old('characteristics.SETTORE-DI-UTILIZZO-EN') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                    </div>

                    <!-- Dettagli Tecnici (Tomaia, Fodera, Puntale, Lamina, Suola) -->
                    <h4 class="text-sm font-bold text-gray-600 mt-6 mb-2 uppercase tracking-wide border-b pb-1">Dettagli Materiali & Costruzione</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Tomaia -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase">Tomaia (IT)</label>
                            <textarea name="characteristics[tomaia-descrizione-it]" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">{{ old('characteristics.tomaia-descrizione-it') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase">Tomaia (EN)</label>
                            <textarea name="characteristics[tomaia-descrizione-en]" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">{{ old('characteristics.tomaia-descrizione-en') }}</textarea>
                        </div>

                        <!-- Fodera -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase">Fodera (IT)</label>
                            <textarea name="characteristics[fodera-descrizione-it]" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">{{ old('characteristics.fodera-descrizione-it') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase">Fodera (EN)</label>
                            <textarea name="characteristics[fodera-descrizione-en]" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">{{ old('characteristics.fodera-descrizione-en') }}</textarea>
                        </div>

                        <!-- Puntale -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase">Puntale (IT)</label>
                            <textarea name="characteristics[puntale-descrizione-it]" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">{{ old('characteristics.puntale-descrizione-it') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase">Puntale (EN)</label>
                            <textarea name="characteristics[puntale-descrizione-en]" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">{{ old('characteristics.puntale-descrizione-en') }}</textarea>
                        </div>

                        <!-- Lamina -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase">Lamina (IT)</label>
                            <textarea name="characteristics[lamina-descrizione-it]" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">{{ old('characteristics.lamina-descrizione-it') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase">Lamina (EN)</label>
                            <textarea name="characteristics[lamina-descrizione-en]" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">{{ old('characteristics.lamina-descrizione-en') }}</textarea>
                        </div>

                        <!-- Suola -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase">Suola (IT)</label>
                            <textarea name="characteristics[suola-descrizione-it]" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">{{ old('characteristics.suola-descrizione-it') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase">Suola (EN)</label>
                            <textarea name="characteristics[suola-descrizione-en]" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">{{ old('characteristics.suola-descrizione-en') }}</textarea>
                        </div>
                    </div>

                    <!-- HTML Presentazione Prodotto -->
                    <h4 class="text-sm font-bold text-gray-600 mt-6 mb-2 uppercase tracking-wide border-b pb-1">Presentazione Estesa (HTML)</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase">Descrizione Estesa HTML (IT)</label>
                            <textarea name="characteristics[DESCRIZIONE ESTESA HTML IT]" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm font-mono">{{ old('characteristics.DESCRIZIONE ESTESA HTML IT') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase">Descrizione Estesa HTML (EN)</label>
                            <textarea name="characteristics[DESCRIZIONE ESTESA HTML EN]" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm font-mono">{{ old('characteristics.DESCRIZIONE ESTESA HTML EN') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end mt-8 pt-6 border-t">
                    <a href="{{ route('admin.b2b.products.index') }}" class="mr-4 text-sm text-gray-600 hover:text-gray-900">Annulla</a>
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                        Salva Prodotto e Inventario
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
