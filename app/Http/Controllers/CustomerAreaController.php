<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\ShopOrder;
use App\Models\User;
use App\Models\Contact;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class CustomerAreaController extends Controller
{
    /**
     * Mostra la pagina di Login dedicata allo Shop (B2C)
     */
    public function showLogin(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect()->route('dashboard');
            } elseif ($user->role === 'agent' || $user->b2b_customer_id) {
                return redirect()->route('agent.dashboard');
            }
            return redirect()->route('public.account.dashboard');
        }

        if ($request->has('redirect_to')) {
            session()->put('url.intended', $request->query('redirect_to'));
        }

        $activeTab = $request->query('tab', 'login');

        return view('public.customer.login', compact('activeTab'));
    }

    /**
     * Esegue il login per i clienti dello Shop
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'L\'indirizzo email è obbligatorio.',
            'email.email' => 'Inserisci un indirizzo email valido.',
            'password.required' => 'La password è obbligatoria.',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        // Tentativo normale tramite Auth guard web
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Sincronizza o crea il record Customer se non esiste
            $customer = Customer::where('email', $user->email)->first();
            if (!$customer) {
                $nameParts = explode(' ', $user->name, 2);
                Customer::create([
                    'nome' => $nameParts[0] ?? 'Cliente',
                    'cognome' => $nameParts[1] ?? '',
                    'email' => $user->email,
                    'telefono' => $user->phone ?? '',
                    'password' => $user->password,
                    'attivo' => true,
                ]);
            }

            $redirectTo = $request->input('redirect_to') ?: session()->pull('url.intended', route('public.account.dashboard'));

            return redirect()->to($redirectTo)->with('success', 'Bentornato! Hai effettuato l\'accesso alla tua Area Personale.');
        }

        // Se non trovato in users, controlliamo se esiste in customers (creato prima dell'unificazione)
        $customer = Customer::where('email', $request->email)->first();
        if ($customer && Hash::check($request->password, $customer->password)) {
            $user = User::firstOrCreate(
                ['email' => $customer->email],
                [
                    'name' => trim(($customer->nome ?? '') . ' ' . ($customer->cognome ?? '')) ?: 'Cliente Shop',
                    'password' => $customer->password,
                    'role' => 'customer',
                    'phone' => $customer->telefono ?? $customer->cellulare,
                ]
            );

            Auth::login($user, $remember);
            $request->session()->regenerate();

            $redirectTo = $request->input('redirect_to') ?: session()->pull('url.intended', route('public.account.dashboard'));

            return redirect()->to($redirectTo)->with('success', 'Bentornato! Hai effettuato l\'accesso alla tua Area Personale.');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Credenziali non valide. Verifica l\'indirizzo email e la password inseriti.']);
    }

    /**
     * Mostra la pagina di Registrazione per lo Shop
     */
    public function showRegister(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('public.account.dashboard');
        }

        if ($request->has('redirect_to')) {
            session()->put('url.intended', $request->query('redirect_to'));
        }

        $activeTab = 'register';

        return view('public.customer.login', compact('activeTab'));
    }

    /**
     * Registra un nuovo cliente Shop
     */
    public function register(Request $request)
    {
        $messages = [
            'nome.required' => 'Il nome è obbligatorio.',
            'cognome.required' => 'Il cognome è obbligatorio.',
            'email.required' => 'L\'indirizzo email è obbligatorio.',
            'email.email' => 'Inserisci un indirizzo email valido.',
            'email.unique' => 'Questo indirizzo email risulta già registrato. Accedi con le tue credenziali.',
            'telefono.required' => 'Il numero di telefono è obbligatorio.',
            'password.required' => 'La password è obbligatoria.',
            'password.min' => 'La password deve contenere almeno :min caratteri.',
            'password.confirmed' => 'La conferma della password non coincide con la password inserita.',
            'password.mixed' => 'La password deve contenere almeno una lettera maiuscola.',
            'password.numbers' => 'La password deve contenere almeno un numero.',
            'password.symbols' => 'La password deve contenere almeno un carattere speciale (es. !@#$%&*...).',
        ];

        $request->validate([
            'nome' => 'required|string|max:255',
            'cognome' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'telefono' => 'required|string|max:50',
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ], $messages);

        $hashedPassword = Hash::make($request->password);

        // 1. Crea Utente Web
        $user = User::create([
            'name' => trim($request->nome . ' ' . $request->cognome),
            'email' => $request->email,
            'phone' => $request->telefono,
            'password' => $hashedPassword,
            'role' => 'customer',
        ]);

        // 2. Crea / Aggiorna Profilo Customer
        $customer = Customer::where('email', $request->email)->first();
        if (!$customer) {
            $customer = new Customer();
            $customer->email = $request->email;
        }
        $customer->nome = $request->nome;
        $customer->cognome = $request->cognome;
        $customer->telefono = $request->telefono;
        $customer->cellulare = $request->telefono;
        $customer->password = $hashedPassword;
        $customer->attivo = true;
        $customer->save();

        // 3. Login automatico
        Auth::login($user);
        $request->session()->regenerate();

        $redirectTo = $request->input('redirect_to') ?: session()->pull('url.intended', route('public.account.dashboard'));

        return redirect()->to($redirectTo)->with('success', 'Registrazione completata con successo! Benvenuto nella tua Area Personale BICAP.');
    }

    /**
     * Logout per l'utente dello shop
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('public.home')->with('success', 'Sei uscito dal tuo account.');
    }

    /**
     * Dashboard cliente con riepilogo ordini
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Recupera o inizializza il cliente
        $customer = Customer::where('email', $user->email)->first();
        if (!$customer) {
            $nameParts = explode(' ', $user->name, 2);
            $customer = Customer::create([
                'nome' => $nameParts[0] ?? 'Cliente',
                'cognome' => $nameParts[1] ?? '',
                'email' => $user->email,
                'telefono' => $user->phone ?? '',
                'password' => $user->password,
                'attivo' => true,
            ]);
        }

        // Cerca tutti gli ordini associati al cliente o alla sua email
        $customerIds = Customer::where('email', $user->email)->pluck('id')->toArray();
        if ($customer && !in_array($customer->id, $customerIds)) {
            $customerIds[] = $customer->id;
        }

        $contactIds = Contact::where('email', $user->email)->pluck('id')->toArray();

        $orders = ShopOrder::where(function ($query) use ($customerIds, $contactIds) {
            if (!empty($customerIds)) {
                $query->whereIn('customer_id', $customerIds);
            }
            if (!empty($contactIds)) {
                $query->orWhereIn('contact_id', $contactIds);
            }
        })
        ->with(['items.variant.product', 'customer'])
        ->latest()
        ->get();

        return view('public.customer.dashboard', compact('user', 'customer', 'orders'));
    }

    /**
     * Visualizza il dettaglio completo di un singolo ordine
     */
    public function orderDetail($id)
    {
        $user = Auth::user();
        $customer = Customer::where('email', $user->email)->first();

        $order = ShopOrder::with(['items.variant.product', 'customer'])->findOrFail($id);

        // Verifica di sicurezza: l'ordine deve appartenere al cliente loggato (o admin)
        $isOwner = false;
        if ($customer && $order->customer_id == $customer->id) {
            $isOwner = true;
        } elseif ($order->contact && $order->contact->email === $user->email) {
            $isOwner = true;
        } elseif ($user->role === 'admin') {
            $isOwner = true;
        }

        if (!$isOwner) {
            abort(403, 'Non sei autorizzato a visualizzare questo ordine.');
        }

        return view('public.customer.order-detail', compact('order', 'customer', 'user'));
    }

    /**
     * Mostra la pagina per modificare i dati personali e di spedizione
     */
    public function profile()
    {
        $user = Auth::user();
        $customer = Customer::where('email', $user->email)->first();

        if (!$customer) {
            $nameParts = explode(' ', $user->name, 2);
            $customer = new Customer();
            $customer->email = $user->email;
            $customer->nome = $nameParts[0] ?? '';
            $customer->cognome = $nameParts[1] ?? '';
            $customer->telefono = $user->phone ?? '';
        }

        return view('public.customer.profile', compact('user', 'customer'));
    }

    /**
     * Aggiorna i dati personali, di spedizione, fatturazione e password del cliente
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'nome' => 'required|string|max:255',
            'cognome' => 'required|string|max:255',
            'telefono' => 'required|string|max:50',
            'indirizzo' => 'required|string|max:255',
            'citta' => 'required|string|max:255',
            'cap' => 'required|string|max:20',
            'nazione' => 'required|string|max:100',
            'is_azienda' => 'nullable|boolean',
            'ragione_sociale' => 'nullable|required_if:is_azienda,1|string|max:255',
            'partita_iva' => 'nullable|required_if:is_azienda,1|string|max:50',
            'sdi' => 'nullable|string|max:50',
            'pec' => 'nullable|email|max:255',
            'codice_fiscale' => 'nullable|string|max:50',
            'metodo_pagamento_preferito' => 'nullable|string|in:stripe,paypal,bonifico,contrassegno',
        ];

        // Validazione cambio password opzionale
        if ($request->filled('new_password')) {
            $rules['current_password'] = 'required|string';
            $rules['new_password'] = [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ];
        }

        $messages = [
            'nome.required' => 'Il nome è obbligatorio.',
            'cognome.required' => 'Il cognome è obbligatorio.',
            'telefono.required' => 'Il numero di telefono è obbligatorio.',
            'indirizzo.required' => 'L\'indirizzo di spedizione è obbligatorio.',
            'citta.required' => 'La città è obbligatoria.',
            'cap.required' => 'Il CAP è obbligatorio.',
            'nazione.required' => 'La nazione è obbligatoria.',
            'ragione_sociale.required_if' => 'La ragione sociale è obbligatoria per gli acquisti aziendali.',
            'partita_iva.required_if' => 'La Partita IVA è obbligatoria per gli acquisti aziendali.',
            'current_password.required' => 'Inserisci la password attuale per confermare la nuova password.',
            'new_password.min' => 'La nuova password deve contenere almeno :min caratteri.',
            'new_password.confirmed' => 'La conferma della nuova password non coincide.',
            'new_password.mixed' => 'La nuova password deve contenere almeno una lettera maiuscola.',
            'new_password.numbers' => 'La nuova password deve contenere almeno un numero.',
            'new_password.symbols' => 'La nuova password deve contenere almeno un carattere speciale.',
        ];

        $request->validate($rules, $messages);

        // Controllo della password attuale se si richiede il cambio
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()
                    ->withInput()
                    ->withErrors(['current_password' => 'La password attuale inserita non è corretta.']);
            }
            $user->password = Hash::make($request->new_password);
        }

        // Aggiorna l'utente Web
        $user->name = trim($request->nome . ' ' . $request->cognome);
        $user->phone = $request->telefono;
        $user->save();

        // Cerca o crea il profilo cliente per lo shop B2C
        $customer = Customer::where('email', $user->email)->first();
        if (!$customer) {
            $customer = new Customer();
            $customer->email = $user->email;
            $customer->password = $user->password;
        }

        if ($request->filled('new_password')) {
            $customer->password = $user->password;
        }

        $customer->nome = $request->nome;
        $customer->cognome = $request->cognome;
        $customer->telefono = $request->telefono;
        $customer->cellulare = $request->telefono;
        $customer->indirizzo = $request->indirizzo;
        $customer->citta = $request->citta;
        $customer->cap = $request->cap;
        $customer->nazione = $request->nazione;
        $customer->codice_fiscale = $request->codice_fiscale;
        $customer->metodo_pagamento_preferito = $request->metodo_pagamento_preferito;

        // Gestione Dati Aziendali
        if ($request->has('is_azienda') && $request->is_azienda) {
            $customer->ragione_sociale = $request->ragione_sociale;
            $customer->partita_iva = $request->partita_iva;
            $customer->sdi = $request->sdi;
            $customer->pec = $request->pec;
        } else {
            $customer->ragione_sociale = null;
            $customer->partita_iva = null;
            $customer->sdi = null;
            $customer->pec = null;
        }

        $customer->save();

        return redirect()->route('public.account.profile')->with('success', 'I tuoi dati personali, l\'indirizzo di spedizione e le preferenze sono stati aggiornati con successo!');
    }
}
