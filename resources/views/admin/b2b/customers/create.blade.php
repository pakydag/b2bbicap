<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                    {{ __('Registra Nuovo Cliente B2B') }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5 font-bold">Creazione anagrafica aziendale e associazione listino</p>
            </div>
            <a href="{{ route('admin.b2b.customers.index') }}" class="inline-flex items-center gap-1 px-4 py-2 bg-slate-900 text-white font-black rounded-xl text-xs uppercase tracking-wider hover:bg-yellow-400 hover:text-slate-950 transition shadow">
                ← Torna ai Clienti
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl space-y-6">
        <div class="bg-white shadow-sm rounded-2xl border border-gray-100 p-6">
            <form action="{{ route('admin.b2b.customers.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="business_name" class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-1">Ragione Sociale *</label>
                        <input type="text" name="business_name" id="business_name" value="{{ old('business_name') }}" class="block w-full border-gray-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-yellow-400 focus:border-yellow-400 shadow-sm @error('business_name') border-red-500 @enderror" required>
                        @error('business_name') <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="vat_number" class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-1">Partita IVA / Cod. Fiscale</label>
                        <input type="text" name="vat_number" id="vat_number" value="{{ old('vat_number') }}" class="block w-full border-gray-200 rounded-xl text-xs font-mono font-bold text-slate-800 focus:ring-yellow-400 focus:border-yellow-400 shadow-sm">
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-1">E-mail Aziendale</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" class="block w-full border-gray-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-yellow-400 focus:border-yellow-400 shadow-sm">
                    </div>

                    <div>
                        <label for="contact_name" class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-1">Nome Referente</label>
                        <input type="text" name="contact_name" id="contact_name" value="{{ old('contact_name') }}" class="block w-full border-gray-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-yellow-400 focus:border-yellow-400 shadow-sm">
                    </div>

                    <div>
                        <label for="contact_surname" class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-1">Cognome Referente</label>
                        <input type="text" name="contact_surname" id="contact_surname" value="{{ old('contact_surname') }}" class="block w-full border-gray-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-yellow-400 focus:border-yellow-400 shadow-sm">
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-1">Telefono</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="block w-full border-gray-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-yellow-400 focus:border-yellow-400 shadow-sm">
                    </div>

                    <div>
                        <label for="payment_condition_id" class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-1">Condizioni di Pagamento</label>
                        <select name="payment_condition_id" id="payment_condition_id" class="block w-full border-gray-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-yellow-400 focus:border-yellow-400 shadow-sm">
                            <option value="">Seleziona condizione...</option>
                            @foreach($conditions as $condition)
                                <option value="{{ $condition->id }}" {{ old('payment_condition_id') == $condition->id ? 'selected' : '' }}>{{ $condition->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="b2b_price_list_id" class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-1">Listino Prezzi Assegnato</label>
                        <select name="b2b_price_list_id" id="b2b_price_list_id" class="block w-full border-gray-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-yellow-400 focus:border-yellow-400 shadow-sm">
                            <option value="">Listino Predefinito di Sistema</option>
                            @foreach($priceLists as $list)
                                <option value="{{ $list->id }}" {{ old('b2b_price_list_id') == $list->id ? 'selected' : '' }}>{{ $list->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="border-t border-gray-100 pt-5 md:col-span-2">
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-wide mb-3">Accesso Diretto Portale B2B Cliente (Opzionale)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="b2b_email" class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-1">Email di Accesso / Login</label>
                                <input type="email" name="b2b_email" id="b2b_email" value="{{ old('b2b_email') }}" class="block w-full border-gray-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-yellow-400 focus:border-yellow-400 shadow-sm @error('b2b_email') border-red-500 @enderror">
                                @error('b2b_email') <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="b2b_password" class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-1">Password Iniziale</label>
                                <input type="password" name="b2b_password" id="b2b_password" class="block w-full border-gray-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-yellow-400 focus:border-yellow-400 shadow-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('admin.b2b.customers.index') }}" class="px-4 py-2.5 text-xs font-bold text-gray-500 hover:text-gray-900 transition">Annulla</a>
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-black hover:bg-yellow-400 hover:text-slate-950 text-white font-black text-xs uppercase tracking-wider rounded-xl shadow transition">
                        Salva Nuovo Cliente
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
