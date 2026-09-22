<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                    {{ __('Aggiungi Nuova Linea B2B') }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5 font-bold">Creazione manuale brand / linea prodotto</p>
            </div>
            <a href="{{ route('admin.b2b.brands.index') }}" class="inline-flex items-center gap-1 px-4 py-2 bg-slate-900 text-white font-black rounded-xl text-xs uppercase tracking-wider hover:bg-yellow-400 hover:text-slate-950 transition shadow">
                ← Torna alle Linee
            </a>
        </div>
    </x-slot>

    <div class="max-w-xl space-y-6">
        <div class="bg-white shadow-sm rounded-2xl border border-gray-100 p-6">
            <form action="{{ route('admin.b2b.brands.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-1">Nome Linea *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="block w-full border-gray-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-yellow-400 focus:border-yellow-400 shadow-sm @error('name') border-red-500 @enderror" required>
                    @error('name')
                        <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('admin.b2b.brands.index') }}" class="px-4 py-2.5 text-xs font-bold text-gray-500 hover:text-gray-900 transition">Annulla</a>
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-black hover:bg-yellow-400 hover:text-slate-950 text-white font-black text-xs uppercase tracking-wider rounded-xl shadow transition">
                        Salva Linea
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
