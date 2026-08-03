<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase">
            {{ __('Pannello di Controllo') }}
        </h2>
    </x-slot>

    <div class="space-y-8 py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Welcome Card -->
        <div class="bg-black text-white p-8 rounded-[30px] border border-zinc-900 shadow-xl relative overflow-hidden group">
            <div class="absolute right-0 top-0 p-8 opacity-10 text-8xl group-hover:scale-110 transition duration-500">⚙️</div>
            <h3 class="text-3xl font-black mb-2 uppercase tracking-tight">Benvenuto, Amministratore!</h3>
            <p class="text-zinc-400 text-sm font-medium max-w-xl leading-relaxed">
                Da questo pannello puoi gestire la rete vendita B2B di BICAP, configurare gli agenti, monitorare i clienti autorizzati ed elaborare gli ordini ricevuti in tempo reale.
            </p>
        </div>

        <!-- Quick Access Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Card 1: Ordini Ricevuti -->
            <a href="{{ route('admin.b2b.orders.index') }}" class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:scale-[1.02] hover:border-yellow-500 transition duration-300 flex flex-col justify-between group">
                <div>
                    <div class="text-3xl mb-4">📝</div>
                    <h4 class="font-black text-gray-900 uppercase text-sm tracking-wider group-hover:text-yellow-600 transition">Ordini Ricevuti</h4>
                    <p class="text-xs text-gray-500 font-bold mt-1 leading-relaxed">Gestisci e approva gli ordini inviati dagli agenti e clienti B2B.</p>
                </div>
                <div class="mt-6 flex items-center justify-between">
                    <span class="text-xs font-black text-gray-400 uppercase tracking-widest group-hover:text-yellow-600 transition">Gestisci</span>
                    <span class="text-gray-400 group-hover:text-yellow-600 group-hover:translate-x-1 transition">➔</span>
                </div>
            </a>

            <!-- Card 2: Agenti -->
            <a href="{{ route('admin.b2b.agents.index') }}" class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:scale-[1.02] hover:border-yellow-500 transition duration-300 flex flex-col justify-between group">
                <div>
                    <div class="text-3xl mb-4">👤</div>
                    <h4 class="font-black text-gray-900 uppercase text-sm tracking-wider group-hover:text-yellow-600 transition">Rete Agenti</h4>
                    <p class="text-xs text-gray-500 font-bold mt-1 leading-relaxed">Configura i profili degli agenti di vendita e assegna i clienti e le linee.</p>
                </div>
                <div class="mt-6 flex items-center justify-between">
                    <span class="text-xs font-black text-gray-400 uppercase tracking-widest group-hover:text-yellow-600 transition">Gestisci</span>
                    <span class="text-gray-400 group-hover:text-yellow-600 group-hover:translate-x-1 transition">➔</span>
                </div>
            </a>

            <!-- Card 3: Clienti B2B -->
            <a href="{{ route('admin.b2b.customers.index') }}" class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:scale-[1.02] hover:border-yellow-500 transition duration-300 flex flex-col justify-between group">
                <div>
                    <div class="text-3xl mb-4">🏢</div>
                    <h4 class="font-black text-gray-900 uppercase text-sm tracking-wider group-hover:text-yellow-600 transition">Clienti B2B</h4>
                    <p class="text-xs text-gray-500 font-bold mt-1 leading-relaxed">Gestisci le anagrafiche aziendali, condizioni di pagamento e P.IVA.</p>
                </div>
                <div class="mt-6 flex items-center justify-between">
                    <span class="text-xs font-black text-gray-400 uppercase tracking-widest group-hover:text-yellow-600 transition">Gestisci</span>
                    <span class="text-gray-400 group-hover:text-yellow-600 group-hover:translate-x-1 transition">➔</span>
                </div>
            </a>

            <!-- Card 4: Inventario Prodotti -->
            <a href="{{ route('admin.b2b.products.index') }}" class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:scale-[1.02] hover:border-yellow-500 transition duration-300 flex flex-col justify-between group">
                <div>
                    <div class="text-3xl mb-4">📦</div>
                    <h4 class="font-black text-gray-900 uppercase text-sm tracking-wider group-hover:text-yellow-600 transition">Inventario B2B</h4>
                    <p class="text-xs text-gray-500 font-bold mt-1 leading-relaxed">Controlla la disponibilità immediata e le prenotazioni future dei prodotti.</p>
                </div>
                <div class="mt-6 flex items-center justify-between">
                    <span class="text-xs font-black text-gray-400 uppercase tracking-widest group-hover:text-yellow-600 transition">Gestisci</span>
                    <span class="text-gray-400 group-hover:text-yellow-600 group-hover:translate-x-1 transition">➔</span>
                </div>
            </a>
        </div>
    </div>
</x-app-layout>
