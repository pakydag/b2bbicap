<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\WidgetController;
use App\Http\Controllers\GlobalWidgetController;
use App\Http\Controllers\ContactRequestController;
use App\Http\Controllers\Admin\HomeController as AdminHomeController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

// Rotte Pubbliche (Homepage)
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->role === 'agent' || ($user->role === 'customer' && $user->b2b_customer_id !== null)) {
            return redirect()->route('agent.dashboard');
        }
        if ($user->role === 'admin') {
            if (!$user->is_super_admin && $user->can_manage_agents && !$user->can_manage_site) {
                return redirect()->route('admin.b2b.dashboard');
            }
            return redirect()->route('dashboard');
        }
        if ($user->role === 'customer') {
            return redirect()->route('public.account.dashboard');
        }
    }
    return redirect()->route('login');
})->name('public.home');

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['it', 'en'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('set-locale');

// Rotte Autenticate / Amministrazione

Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->role === 'agent' || ($user->role === 'customer' && $user->b2b_customer_id !== null)) {
        return redirect()->route('agent.dashboard');
    }
    if ($user->role === 'admin') {
        if (!$user->is_super_admin && $user->can_manage_agents && !$user->can_manage_site) {
            return redirect()->route('admin.b2b.dashboard');
        }
        return view('dashboard');
    }
    if ($user->role === 'customer') {
        return redirect()->route('public.account.dashboard');
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/admin-emergency-migrate', function () {
    try {
        if (!\Illuminate\Support\Facades\Schema::hasColumn('booking_services', 'icona')) {
            \Illuminate\Support\Facades\Schema::table('booking_services', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->string('icona', 20)->nullable()->after('nome');
            });
            $msg = "Colonna 'icona' aggiunta con successo! ";
        } else {
            $msg = "La colonna 'icona' esiste già. ";
        }
        
        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE booking_services MODIFY booking_service_category_id BIGINT UNSIGNED NULL;");
            $msg .= "Colonna 'booking_service_category_id' resa opzionale.";
        } catch (\Exception $e) {
            $msg .= "Errore nel rendere la categoria opzionale: " . $e->getMessage();
        }
        
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        
        return "Installazione Forzata completata: <br><br><strong>" . $msg . "</strong><br><br>Adesso puoi tornare indietro e salvare il tuo servizio.";
    } catch (\Exception $e) {
        return "Errore: " . $e->getMessage();
    }
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Area Clienti B2C (Pubblica)
    Route::get('/account', [\App\Http\Controllers\CustomerAreaController::class, 'dashboard'])->name('public.account.dashboard');
    Route::get('/account/profile', [\App\Http\Controllers\CustomerAreaController::class, 'profile'])->name('public.account.profile');
    Route::post('/account/profile', [\App\Http\Controllers\CustomerAreaController::class, 'updateProfile'])->name('public.account.updateProfile');
});

Route::middleware(['auth', 'admin'])->prefix('amministrazione')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::resource('articoli', ArticleController::class);
    Route::resource('sezioni', SectionController::class);
    Route::resource('global-widgets', GlobalWidgetController::class);
    Route::resource('contatti', ContactRequestController::class)->only(['index', 'show', 'destroy']);
    Route::post('contatti/{contatto}/rispondi', [ContactRequestController::class, 'reply'])->name('contatti.reply');
    Route::resource('transfers', \App\Http\Controllers\Admin\TransferRequestController::class)->only(['index', 'show', 'destroy']);
    Route::post('transfers/{transfer}/rispondi', [\App\Http\Controllers\Admin\TransferRequestController::class, 'reply'])->name('transfers.reply');
    Route::resource('car-rentals', \App\Http\Controllers\Admin\CarRentalRequestController::class)->only(['index', 'show', 'destroy']);
    Route::post('car-rentals/{car_rental}/rispondi', [\App\Http\Controllers\Admin\CarRentalRequestController::class, 'reply'])->name('car-rentals.reply');
    // Ordinamento
    Route::post('sezioni/reorder', [SectionController::class, 'reorder'])->name('sezioni.reorder');
    Route::post('articoli/reorder', [ArticleController::class, 'reorder'])->name('articoli.reorder');

    // Gestione Home Page
    Route::get('/home-page', [AdminHomeController::class, 'edit'])->name('home.edit');
    Route::post('/home-page', [AdminHomeController::class, 'update'])->name('home.update');
    Route::post('/home-page/seo', [AdminHomeController::class, 'updateSeo'])->name('home.updateSeo');
    Route::get('/home-page/preview', [AdminHomeController::class, 'preview'])->name('home.preview');
    Route::get('/file-manager', function () {
        return view('admin.filemanager');
    })->name('filemanager');

    Route::get('/file-manager/fm-button', function () {
        return view('admin.filemanager_button');
    })->name('filemanager.button');

    // Configurazione Sito
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'edit'])->name('settings.edit');
    Route::post('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/sitemap', function () {
        \Illuminate\Support\Facades\Artisan::call('sitemap:generate');
        return redirect()->back()->with('success', 'Sitemap rigenerata con successo! ' . \Illuminate\Support\Facades\Artisan::output());
    })->name('settings.sitemap');

    // Widget Routes
    Route::post('/articoli/{articolo}/widgets', [WidgetController::class, 'store'])->name('widgets.store');
    Route::put('/widgets/{widget}', [WidgetController::class, 'update'])->name('widgets.update');
    Route::delete('/widgets/{widget}', [WidgetController::class, 'destroy'])->name('widgets.destroy');
    Route::post('/widgets/reorder', [WidgetController::class, 'reorder'])->name('widgets.reorder');

    // Shop B2B (E-commerce)
    Route::prefix('shop')->name('shop.')->group(function () {
        Route::resource('categorie', \App\Http\Controllers\ShopCategoryController::class)->parameters(['categorie' => 'categoria']);
        Route::resource('collezioni', \App\Http\Controllers\ShopCollectionController::class)->parameters(['collezioni' => 'collezione']);
        Route::resource('marche', \App\Http\Controllers\ShopBrandController::class)->parameters(['marche' => 'marca']);
        Route::resource('prodotti', \App\Http\Controllers\ShopProductController::class)->parameters(['prodotti' => 'prodotto']);
        Route::resource('clienti', \App\Http\Controllers\CustomerController::class)->parameters(['clienti' => 'cliente']);
        Route::resource('ordini', \App\Http\Controllers\ShopOrderController::class)->parameters(['ordini' => 'ordine']);
        
        // Configurazione Avanzata Shop
        Route::get('configurazione', [\App\Http\Controllers\ShopConfigurationController::class, 'edit'])->name('configuration');
        Route::post('configurazione', [\App\Http\Controllers\ShopConfigurationController::class, 'update'])->name('configuration.update');
        
        // Gestione Spedizioni
        Route::get('shipping-costs', [\App\Http\Controllers\Admin\ShopShippingCostController::class, 'index'])->name('shipping_costs.index');
        Route::post('shipping-costs', [\App\Http\Controllers\Admin\ShopShippingCostController::class, 'store'])->name('shipping_costs.store');
        Route::patch('shipping-costs/threshold', [\App\Http\Controllers\Admin\ShopShippingCostController::class, 'updateThreshold'])->name('shipping_costs.threshold');
        Route::delete('shipping-costs/{shippingCost}', [\App\Http\Controllers\Admin\ShopShippingCostController::class, 'destroy'])->name('shipping_costs.destroy');

        Route::resource('tags', \App\Http\Controllers\TagController::class);
    });

    // Booking System
    Route::prefix('booking')->name('booking.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdminBookingController::class, 'index'])->name('bookings.index');
        Route::get('/archive', [\App\Http\Controllers\Admin\AdminBookingController::class, 'archive'])->name('bookings.archive');
        Route::get('/calendar', [\App\Http\Controllers\Admin\AdminBookingController::class, 'calendar'])->name('calendar');
        Route::get('/block', [\App\Http\Controllers\Admin\AdminBookingController::class, 'block'])->name('bookings.block');
        Route::post('/block', [\App\Http\Controllers\Admin\AdminBookingController::class, 'storeBlock'])->name('bookings.store_block');
        Route::resource('structures', \App\Http\Controllers\Admin\BookingStructureController::class);
        Route::resource('customers', \App\Http\Controllers\Admin\BookingCustomerController::class); // Added this line

        // Booking Services (Flat Structure)
        Route::get('services', [\App\Http\Controllers\Admin\BookingServiceController::class, 'index'])->name('services.index');
        Route::post('services', [\App\Http\Controllers\Admin\BookingServiceController::class, 'store'])->name('services.store');
        Route::put('services/{service}', [\App\Http\Controllers\Admin\BookingServiceController::class, 'update'])->name('services.update');
        Route::delete('services/{service}', [\App\Http\Controllers\Admin\BookingServiceController::class, 'destroy'])->name('services.destroy');
        
        // Booking Extras
        Route::resource('extras', \App\Http\Controllers\Admin\BookingExtraController::class)->except(['create', 'edit', 'show']);
        Route::post('/{booking}/mark-as-paid', [\App\Http\Controllers\Admin\AdminBookingController::class, 'markAsPaid'])->name('bookings.mark_as_paid');
        Route::post('/{booking}/cancel', [\App\Http\Controllers\Admin\AdminBookingController::class, 'cancel'])->name('bookings.cancel');
        Route::get('/api/occupied-dates/{id}', [\App\Http\Controllers\Admin\AdminBookingController::class, 'getOccupiedDates'])->name('bookings.occupied_dates');
        Route::get('/{booking}', [\App\Http\Controllers\Admin\AdminBookingController::class, 'show'])->name('bookings.show');
    });

    // Customer Management
    Route::get('customers/export', [\App\Http\Controllers\Admin\CustomerController::class, 'exportCsv'])->name('customers.export');
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\CustomerController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\CustomerController::class, 'store'])->name('store');
        Route::get('/{customer}', [\App\Http\Controllers\Admin\CustomerController::class, 'show'])->name('show');
        Route::get('/{customer}/edit', [\App\Http\Controllers\Admin\CustomerController::class, 'edit'])->name('edit');
        Route::put('/{customer}', [\App\Http\Controllers\Admin\CustomerController::class, 'update'])->name('update');
        Route::delete('/{customer}', [\App\Http\Controllers\Admin\CustomerController::class, 'destroy'])->name('destroy');
        Route::post('/{customer}/tags/add', [\App\Http\Controllers\Admin\CustomerController::class, 'addTag'])->name('add-tag');
        Route::delete('/{customer}/tags/remove', [\App\Http\Controllers\Admin\CustomerController::class, 'removeTag'])->name('remove-tag');
        Route::post('/{customer}/toggle-feature', [\App\Http\Controllers\Admin\CustomerController::class, 'toggleFeature'])->name('toggle-feature');
    });

    // Admin User Management (Super Admin only recommended but accessible to all admins for now as per request)
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);

    // AI Agent Management (Vapi.ai)
    Route::get('vapi', [\App\Http\Controllers\Admin\VapiController::class, 'index'])->name('vapi.index');
    Route::patch('vapi', [\App\Http\Controllers\Admin\VapiController::class, 'update'])->name('vapi.update');
    Route::post('vapi/files', [\App\Http\Controllers\Admin\VapiController::class, 'uploadFile'])->name('vapi.files.upload');
    Route::delete('vapi/files/{id}', [\App\Http\Controllers\Admin\VapiController::class, 'deleteFile'])->name('vapi.files.destroy');
    Route::resource('departments', \App\Http\Controllers\Admin\DepartmentController::class);
    
    // SMS Vapi Management
    Route::get('vapi/sms', [\App\Http\Controllers\Admin\VapiSmsController::class, 'index'])->name('vapi.sms.index');
    Route::delete('vapi/sms/{sms}', [\App\Http\Controllers\Admin\VapiSmsController::class, 'destroy'])->name('vapi.sms.destroy');
    
    // Appointment Management (AI Agenda)
    Route::get('appointments', [\App\Http\Controllers\Admin\AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('appointments/events', [\App\Http\Controllers\Admin\AppointmentController::class, 'events'])->name('appointments.events');
    Route::get('appointments/{appointment}', [\App\Http\Controllers\Admin\AppointmentController::class, 'show'])->name('appointments.show');
    Route::delete('appointments/{appointment}', [\App\Http\Controllers\Admin\AppointmentController::class, 'destroy'])->name('appointments.destroy');
    Route::patch('appointments/{appointment}/cancel', [\App\Http\Controllers\Admin\AppointmentController::class, 'cancel'])->name('appointments.cancel');
    
    // AI Tickets Management
    Route::get('vapi/tickets', [\App\Http\Controllers\Admin\AiTicketController::class, 'index'])->name('vapi.tickets.index');
    Route::get('vapi/tickets/{ticket}', [\App\Http\Controllers\Admin\AiTicketController::class, 'show'])->name('vapi.tickets.show');
    Route::post('vapi/tickets/sync-all', [\App\Http\Controllers\Admin\AiTicketController::class, 'bulkSync'])->name('vapi.tickets.bulk-sync');
    Route::post('vapi/tickets/{ticket}/sync', [\App\Http\Controllers\Admin\AiTicketController::class, 'syncCall'])->name('vapi.tickets.sync');
    Route::patch('vapi/tickets/{ticket}/close', [\App\Http\Controllers\Admin\AiTicketController::class, 'close'])->name('vapi.tickets.close');
    Route::patch('vapi/tickets/{ticket}/comments', [\App\Http\Controllers\Admin\AiTicketController::class, 'updateComments'])->name('vapi.tickets.update-comments');
    Route::delete('vapi/tickets/{ticket}', [\App\Http\Controllers\Admin\AiTicketController::class, 'destroy'])->name('vapi.tickets.destroy');

    // Modulo B2B Agenti
    Route::prefix('b2b')->name('b2b.')->group(function () {
        Route::resource('brands', \App\Http\Controllers\Admin\B2b\B2bBrandController::class);
        Route::resource('payment-conditions', \App\Http\Controllers\Admin\B2b\B2bPaymentConditionController::class);
        Route::post('customers/import', [\App\Http\Controllers\Admin\B2b\B2bCustomerController::class, 'import'])->name('customers.import');
        Route::post('customers/sync-ftps', [\App\Http\Controllers\Admin\B2b\B2bCustomerController::class, 'syncFtps'])->name('customers.sync_ftps');
        Route::resource('customers', \App\Http\Controllers\Admin\B2b\B2bCustomerController::class);
        Route::resource('agents', \App\Http\Controllers\Admin\B2b\AgentController::class);
        Route::post('products/import', [\App\Http\Controllers\Admin\B2b\B2bProductController::class, 'import'])->name('products.import');
        Route::post('products/sync-giacenze', [\App\Http\Controllers\Admin\B2b\B2bProductController::class, 'syncGiacenze'])->name('products.sync_giacenze');
        Route::resource('products', \App\Http\Controllers\Admin\B2b\B2bProductController::class);
        Route::get('orders/{order}/pdf', [\App\Http\Controllers\Admin\B2b\B2bOrderController::class, 'pdf'])->name('orders.pdf');
        Route::resource('orders', \App\Http\Controllers\Admin\B2b\B2bOrderController::class);
        Route::post('orders/{order}/send-copy', [\App\Http\Controllers\Admin\B2b\B2bOrderController::class, 'sendOrderCopy'])->name('orders.send_copy');
        Route::get('dashboard', [\App\Http\Controllers\Admin\B2b\B2bDashboardController::class, 'index'])->name('dashboard');
    });
});

// Portale Agente B2B
Route::middleware(['auth', 'agent'])->prefix('agenti')->name('agent.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Agent\AgentPortalController::class, 'dashboard'])->name('dashboard');
    Route::post('/seleziona-cliente', [\App\Http\Controllers\Agent\AgentPortalController::class, 'selectCustomer'])->name('select_customer');
    Route::get('/catalogo', [\App\Http\Controllers\Agent\AgentPortalController::class, 'catalog'])->name('catalog');
    Route::get('/prodotto/{product}', [\App\Http\Controllers\Agent\AgentPortalController::class, 'product'])->name('product');
    Route::get('/prodotto-variant/{product}', [\App\Http\Controllers\Agent\AgentPortalController::class, 'productVariant'])->name('product.variant');
    Route::get('/carrello', [\App\Http\Controllers\Agent\AgentPortalController::class, 'cart'])->name('cart');
    Route::post('/carrello/add', [\App\Http\Controllers\Agent\AgentPortalController::class, 'addToCart'])->name('cart.add');
    Route::post('/carrello/update', [\App\Http\Controllers\Agent\AgentPortalController::class, 'updateCart'])->name('cart.update');
    Route::delete('/carrello/remove/{index}', [\App\Http\Controllers\Agent\AgentPortalController::class, 'removeFromCart'])->name('cart.remove');
    Route::get('/checkout', [\App\Http\Controllers\Agent\AgentPortalController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [\App\Http\Controllers\Agent\AgentPortalController::class, 'processCheckout'])->name('process_checkout');
    Route::get('/ordini', [\App\Http\Controllers\Agent\AgentPortalController::class, 'orders'])->name('orders');
    Route::get('/ordini/{order}', [\App\Http\Controllers\Agent\AgentPortalController::class, 'orderDetail'])->name('order_detail');
    Route::get('/ordini/{order}/pdf', [\App\Http\Controllers\Agent\AgentPortalController::class, 'orderPdf'])->name('orders.pdf');
    Route::put('/ordini/{order}/items', [\App\Http\Controllers\Agent\AgentPortalController::class, 'updateOrderItems'])->name('orders.update_items');
    Route::post('/ordini/{order}/accept', [\App\Http\Controllers\Agent\AgentPortalController::class, 'acceptOrderModifications'])->name('orders.accept');
    Route::post('/ordini/{order}/reject', [\App\Http\Controllers\Agent\AgentPortalController::class, 'rejectOrderModifications'])->name('orders.reject');
    Route::post('/ordini/{order}/cancel', [\App\Http\Controllers\Agent\AgentPortalController::class, 'cancelOrder'])->name('orders.cancel');
    Route::post('/ordini/{order}/confirm', [\App\Http\Controllers\Agent\AgentPortalController::class, 'confirmOrder'])->name('orders.confirm');
    Route::get('/profilo', [\App\Http\Controllers\Agent\AgentPortalController::class, 'profile'])->name('profile');
    Route::get('/ping-sync', [\App\Http\Controllers\Agent\AgentPortalController::class, 'pingSync'])->name('ping_sync');
    
    // Gestione Listini Prezzi Agente
    Route::post('/price-lists/assign-customer', [\App\Http\Controllers\Agent\AgentPriceListController::class, 'assignCustomer'])->name('price-lists.assign_customer');
    Route::resource('price-lists', \App\Http\Controllers\Agent\AgentPriceListController::class);
});

require __DIR__.'/auth.php';

// Form Contatti Pubblico
Route::post('/contatti/invia', [PublicController::class, 'submitContactForm'])->name('public.contact.submit');
Route::get('/contatti/conversazione/{token}', [PublicController::class, 'viewContactThread'])->name('public.contact.thread');
Route::post('/contatti/conversazione/{token}/rispondi', [PublicController::class, 'replyContactThread'])->name('public.contact.thread.reply');

Route::post('/transfer/invia', [PublicController::class, 'submitTransferForm'])->name('public.transfer.submit');
Route::get('/transfer/conversazione/{token}', [PublicController::class, 'viewTransferThread'])->name('public.transfer.thread');
Route::post('/transfer/conversazione/{token}/rispondi', [PublicController::class, 'replyTransferThread'])->name('public.transfer.thread.reply');

Route::post('/car-rental/invia', [PublicController::class, 'submitCarRentalForm'])->name('public.car_rental.submit');
Route::get('/car-rental/conversazione/{token}', [PublicController::class, 'viewCarRentalThread'])->name('public.car_rental.thread');
Route::post('/car-rental/conversazione/{token}/rispondi', [PublicController::class, 'replyCarRentalThread'])->name('public.car_rental.thread.reply');
// Spoki Webhook
Route::post('/webhook/spoki', [\App\Http\Controllers\SpokiWebhookController::class, 'handle']);
Route::get('/webhook/spoki', function () {
    return response()->json(['status' => 'active', 'message' => 'Spoki Webhook is ready for POST requests.']);
});

// Shop Pubblico
Route::get('/shop', [\App\Http\Controllers\PublicShopController::class, 'index'])->name('public.shop.index');
Route::get('/shop/cart', [\App\Http\Controllers\PublicShopCartController::class, 'index'])->name('public.shop.cart.index');
Route::post('/shop/cart/add', [\App\Http\Controllers\PublicShopCartController::class, 'add'])->name('public.shop.cart.add');
Route::delete('/shop/cart/remove/{variant_id}', [\App\Http\Controllers\PublicShopCartController::class, 'remove'])->name('public.shop.cart.remove');

Route::get('/shop/checkout', [\App\Http\Controllers\PublicShopCartController::class, 'checkout'])->name('public.shop.cart.checkout');
Route::post('/shop/checkout/process', [\App\Http\Controllers\PublicShopCartController::class, 'processCheckout'])->name('public.shop.cart.process');
Route::get('/shop/checkout/success', [\App\Http\Controllers\PublicShopCartController::class, 'success'])->name('public.shop.cart.success');
Route::get('/shop/checkout/stripe/success', [\App\Http\Controllers\PublicShopCartController::class, 'stripeSuccess'])->name('public.shop.cart.stripe.success');
Route::get('/shop/checkout/stripe/cancel', [\App\Http\Controllers\PublicShopCartController::class, 'stripeCancel'])->name('public.shop.cart.stripe.cancel');
Route::get('/shop/checkout/paypal/success', [\App\Http\Controllers\PublicShopCartController::class, 'paypalSuccess'])->name('public.shop.cart.paypal.success');
Route::get('/shop/checkout/paypal/cancel', [\App\Http\Controllers\PublicShopCartController::class, 'paypalCancel'])->name('public.shop.cart.paypal.cancel');

Route::get('/shop/collezione/{slug}', [\App\Http\Controllers\PublicShopController::class, 'collezione'])->name('public.shop.collezione');
Route::get('/shop/categoria/{slug}', [\App\Http\Controllers\PublicShopController::class, 'categoria'])->name('public.shop.categoria');
Route::get('/shop/{collezione_slug}/{prodotto_slug}', [\App\Http\Controllers\PublicShopController::class, 'prodotto'])->name('public.shop.prodotto');

// Booking Pubblico
Route::get('/booking', [\App\Http\Controllers\PublicBookingController::class, 'index'])->name('public.booking.index');
Route::get('/booking/checkout', [\App\Http\Controllers\PublicBookingController::class, 'checkout'])->name('public.booking.checkout');
    Route::get('/booking/login', [\App\Http\Controllers\PublicBookingController::class, 'showLogin'])->name('public.booking.login.view');
    Route::post('/booking/login', [\App\Http\Controllers\PublicBookingController::class, 'loginCheckout'])->name('public.booking.login');
    Route::get('/booking/forgot-password', [\App\Http\Controllers\PublicBookingController::class, 'showForgotPassword'])->name('public.booking.forgot_password');
    Route::post('/booking/forgot-password', [\App\Http\Controllers\PublicBookingController::class, 'sendResetPasswordEmail'])->name('public.booking.forgot_password.submit');
    Route::post('/booking/process-checkout', [\App\Http\Controllers\PublicBookingController::class, 'processCheckout'])->name('public.booking.process_checkout');
    Route::get('/booking/success/{id}', [\App\Http\Controllers\PublicBookingController::class, 'success'])->name('public.booking.success');

    // Booking Customer Dashboard
    Route::middleware('auth:booking_customer')->prefix('booking/dashboard')->name('public.booking.dashboard.')->group(function() {
        Route::get('/', [\App\Http\Controllers\BookingCustomerDashboardController::class, 'index'])->name('index');
        Route::get('/profile', [\App\Http\Controllers\BookingCustomerDashboardController::class, 'profile'])->name('profile');
        Route::post('/profile', [\App\Http\Controllers\BookingCustomerDashboardController::class, 'updateProfile'])->name('profile.update');
        Route::post('/{booking}/cancel-request', [\App\Http\Controllers\BookingCustomerDashboardController::class, 'requestCancellation'])->name('cancel_request');
        Route::post('/logout', [\App\Http\Controllers\BookingCustomerDashboardController::class, 'logout'])->name('logout');
    });
Route::get('/booking/stripe/success', [\App\Http\Controllers\PublicBookingController::class, 'stripeSuccess'])->name('public.booking.stripe.success');
Route::get('/booking/paypal/success', [\App\Http\Controllers\PublicBookingController::class, 'paypalSuccess'])->name('public.booking.paypal.success');
Route::get('/booking/search', [\App\Http\Controllers\PublicBookingController::class, 'search'])->name('public.booking.search');
Route::get('/booking/{id}-{slug?}', [\App\Http\Controllers\PublicBookingController::class, 'show'])->name('public.booking.show')->where('id', '[0-9]+');
Route::post('/booking/check-availability', [\App\Http\Controllers\PublicBookingController::class, 'checkAvailability'])->name('public.booking.check');
Route::post('/booking/reserve', [\App\Http\Controllers\PublicBookingController::class, 'reserve'])->name('public.booking.reserve');

// Catch-all Routes (Devono stare in fondo per non sovrascrivere /login, /amministrazione, ecc.)
Route::get('/{slug}', [PublicController::class, 'sezione'])
    ->name('public.sezione');

Route::get('/{sezione_slug}/{articolo_slug}', [PublicController::class, 'articolo'])
    ->name('public.articolo');

