<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifica Cliente B2B') }}: {{ $customer->business_name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.b2b.customers.update', $customer) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="mb-4 col-span-2">
                                <label for="business_name" class="block text-sm font-medium text-gray-700">Ragione Sociale *</label>
                                <input type="text" name="business_name" id="business_name" value="{{ old('business_name', $customer->business_name) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('business_name') border-red-500 @enderror" required>
                                @error('business_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label for="vat_number" class="block text-sm font-medium text-gray-700">Partita IVA / Cod. Fiscale</label>
                                <input type="text" name="vat_number" id="vat_number" value="{{ old('vat_number', $customer->vat_number) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div class="mb-4">
                                <label for="email" class="block text-sm font-medium text-gray-700">E-mail Aziendale</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $customer->email) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div class="mb-4">
                                <label for="contact_name" class="block text-sm font-medium text-gray-700">Nome Referente</label>
                                <input type="text" name="contact_name" id="contact_name" value="{{ old('contact_name', $customer->contact_name) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div class="mb-4">
                                <label for="contact_surname" class="block text-sm font-medium text-gray-700">Cognome Referente</label>
                                <input type="text" name="contact_surname" id="contact_surname" value="{{ old('contact_surname', $customer->contact_surname) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div class="mb-4">
                                <label for="phone" class="block text-sm font-medium text-gray-700">Telefono</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $customer->phone) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div class="mb-4">
                                <label for="payment_condition_id" class="block text-sm font-medium text-gray-700">Condizioni di Pagamento</label>
                                <select name="payment_condition_id" id="payment_condition_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Seleziona condizione...</option>
                                    @foreach($conditions as $condition)
                                        <option value="{{ $condition->id }}" {{ old('payment_condition_id', $customer->payment_condition_id) == $condition->id ? 'selected' : '' }}>{{ $condition->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="b2b_price_list_id" class="block text-sm font-medium text-gray-700">Listino Prezzi Assegnato</label>
                                <select name="b2b_price_list_id" id="b2b_price_list_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Listino Predefinito di Sistema</option>
                                    @foreach($priceLists as $list)
                                        <option value="{{ $list->id }}" {{ old('b2b_price_list_id', $customer->b2b_price_list_id) == $list->id ? 'selected' : '' }}>
                                            {{ $list->name }} {{ $list->is_default ? '(Predefinito)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-2 border-t border-gray-200 pt-6 mt-4">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Credenziali Accesso Portale B2B</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="mb-4">
                                        <label for="b2b_email" class="block text-sm font-medium text-gray-700">Email di Accesso / Login (lascia vuoto per disabilitare l'accesso)</label>
                                        <input type="email" name="b2b_email" id="b2b_email" value="{{ old('b2b_email', $customerUser?->email) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('b2b_email') border-red-500 @enderror">
                                        @error('b2b_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="mb-4">
                                        <label for="b2b_password" class="block text-sm font-medium text-gray-700">Password (lascia vuoto per non modificare la password attuale)</label>
                                        <input type="password" name="b2b_password" id="b2b_password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.b2b.customers.index') }}" class="mr-4 text-sm text-gray-600 hover:text-gray-900">Annulla</a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Aggiorna Cliente
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
