<x-agent-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase">
            {{ __('Il Mio Profilo Agente') }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 p-12">
            <div class="flex flex-col md:flex-row gap-12 items-start">
                <div class="w-32 h-32 bg-zinc-100 rounded-[40px] flex items-center justify-center text-4xl shadow-inner border border-zinc-200">
                    👤
                </div>
                
                <div class="flex-1 space-y-8">
                    <div>
                        <h3 class="text-3xl font-black text-gray-900 uppercase tracking-tight">{{ $user->name }} {{ $user->surname }}</h3>
                        <p class="text-yellow-600 font-bold uppercase text-xs tracking-[0.2em] mt-1">
                            {{ Auth::user()->role === 'customer' ? __('Cliente B2B Autorizzato') : __('Agente Autorizzato') }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 py-8 border-t border-b border-gray-50 text-base">
                        <div>
                            <p class="text-xs font-black text-gray-500 uppercase tracking-widest mb-1">{{ __('E-mail') }}</p>
                            <p class="font-black text-gray-800">{{ $user->email }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-black text-gray-500 uppercase tracking-widest mb-1">{{ __('Telefono') }}</p>
                            <p class="font-black text-gray-800">{{ $user->phone ?? 'N/D' }}</p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <p class="text-xs font-black text-gray-500 uppercase tracking-widest mb-4 italic">{{ __('Linee Abilitate') }}</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($user->b2bBrands as $brand)
                                <span class="bg-black border border-zinc-950 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest shadow-sm">
                                    {{ $brand->name }}
                                </span>
                            @endforeach
                            @if($user->b2bBrands->isEmpty())
                                <span class="text-xs text-gray-400 italic">Nessuna linea assegnata. Contatta l'amministratore.</span>
                            @endif
                        </div>
                    </div>

                    @if($user->role === 'agent')
                    <div class="mb-6">
                        <p class="text-xs font-black text-gray-500 uppercase tracking-widest mb-4 italic">{{ __('Clienti Abilitati') }}</p>
                        <div class="flex flex-wrap gap-2 max-h-40 overflow-y-auto pr-2">
                            @foreach($user->b2bCustomers as $customer)
                                <span class="bg-zinc-100 border border-zinc-200 text-gray-800 px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-tight shadow-sm">
                                    {{ $customer->business_name }} ({{ $customer->vat_number }})
                                </span>
                            @endforeach
                            @if($user->b2bCustomers->isEmpty())
                                <span class="text-xs text-gray-400 italic">Nessun cliente assegnato. Contatta l'amministratore.</span>
                            @endif
                        </div>
                    </div>
                    @endif

                    <div class="pt-8 flex flex-wrap gap-4">
                        <a href="{{ route('profile.edit') }}" class="bg-black border border-zinc-950 text-white px-8 py-3 rounded-xl text-xs font-black uppercase tracking-widest hover:border-yellow-400 hover:text-yellow-400 shadow-xl shadow-black/10 transition duration-300">
                            {{ __('Modifica Dati & Password') }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="bg-white border border-rose-200 text-rose-500 px-8 py-3 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-rose-50 transition duration-300">
                                {{ __('Chiudi Sessione') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-agent-layout>
