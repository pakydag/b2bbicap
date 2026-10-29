@php
    $isEn = app()->getLocale() === 'en';
@endphp
<!-- ================= 1. NEWSLETTER SECTION (1-to-1 bicap.shop) ================= -->
<section class="newsletter-centro" style="position: relative; background-image: url('{{ asset('storage/images/newsletter-bg.jpg') }}'); background-size: cover; background-position: center; width: 100%;">
    <!-- Dark overlay for readability -->
    <div style="background-color: rgba(0, 0, 0, 0.65); padding: 65px 0 55px 0; width: 100%;">
        <div style="max-width: 1250px; margin: 0 auto; padding: 0 24px; display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 40px;">
            
            <!-- Left Text Column -->
            <div style="flex: 1 1 480px; min-width: 280px; color: #ffffff;">
                <h4 style="font-family: 'Bebas Neue', sans-serif !important; font-size: 32px !important; font-weight: 700 !important; letter-spacing: 1.5px !important; color: #ffffff !important; margin: 0 0 16px 0 !important; line-height: 1.1 !important; text-transform: uppercase !important;">
                    NEWSLETTER
                </h4>
                <p style="font-family: 'Montserrat', sans-serif !important; font-size: 14px !important; line-height: 1.6 !important; color: #ffffff !important; margin: 0 0 14px 0 !important;">
                    {{ $isEn ? 'Stay updated on the latest products and get a 5% discount on your next purchase!' : 'Aggiornati sugli ultimi prodotti ed ottieni subito uno sconto del 5% da usare per il tuo prossimo acquisto!' }}
                </p>
                <p style="font-family: 'Montserrat', sans-serif !important; font-size: 13px !important; line-height: 1.5 !important; color: #cccccc !important; margin: 0 !important;">
                    {{ $isEn ? 'Discount valid on a minimum order of €100.' : 'Sconto utilizzabile su una spesa minima del valore di 100€.' }}
                </p>
            </div>

            <!-- Right Form Column -->
            <div style="flex: 1 1 380px; max-width: 440px; width: 100%;" x-data="newsletterForm()">
                <!-- Inline Feedback Alert (Box ad altissimo contrasto) -->
                <div x-show="message" 
                     x-transition 
                     :style="success ? 'background-color: #ffffff; color: #111111; border-left: 5px solid #fedb00;' : 'background-color: #ffffff; color: #b91c1c; border-left: 5px solid #dc2626;'" 
                     class="p-4 mb-4 rounded-none shadow-lg text-xs leading-relaxed" 
                     style="display: none;">
                    <div class="flex items-start gap-2.5">
                        <span x-show="success && couponCode" class="text-base shrink-0">🎟️</span>
                        <span x-show="success && !couponCode" class="text-base shrink-0">✅</span>
                        <span x-show="!success" class="text-base shrink-0">⚠️</span>
                        <div class="flex-1">
                            <p class="font-bold text-sm" x-text="message" :style="success ? 'margin: 0; color: #111111 !important;' : 'margin: 0; color: #b91c1c !important;'"></p>
                            <template x-if="couponCode">
                                <div class="mt-2 pt-2 border-t border-gray-200">
                                    <span class="text-[11px] uppercase tracking-wider text-gray-500 font-semibold">{{ $isEn ? 'Your Welcome Code:' : 'Il tuo Codice Benvenuto:' }}</span>
                                    <div class="mt-0.5 inline-block font-mono font-bold text-sm bg-yellow-100 text-black px-2.5 py-1 rounded border border-yellow-300">
                                        <span x-text="couponCode"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="submitNewsletter" action="{{ route('public.newsletter.subscribe') }}" method="POST" style="width: 100%; display: flex; flex-direction: column;">
                    @csrf
                    <!-- Email input -->
                    <input type="email" 
                           name="email"
                           x-model="email"
                           required 
                           placeholder="Email" 
                           style="width: 100%; height: 44px; background-color: #363636; border: 1px solid #5e5e5e; color: #ffffff; padding: 0 16px; font-family: 'Montserrat', sans-serif; font-size: 13px; outline: none; margin: 0 0 10px 0; box-sizing: border-box;">
                    
                    <!-- Submit button (Full width bright yellow) -->
                    <button type="submit" 
                            :disabled="loading"
                            style="width: 100%; height: 44px; background-color: #fedb00 !important; color: #000000 !important; font-family: 'Bebas Neue', sans-serif !important; font-size: 22px !important; font-weight: 700 !important; letter-spacing: 1px !important; text-transform: uppercase !important; border: 1px solid #fedb00 !important; cursor: pointer !important; display: flex; align-items: center; justify-content: center; margin: 0 0 10px 0; transition: opacity 0.2s;"
                            onmouseover="this.style.opacity='0.9'" 
                            onmouseout="this.style.opacity='1'">
                        <span x-show="!loading && nl_op === 'IN'">{{ $isEn ? 'SUBSCRIBE' : 'ISCRIVITI' }}</span>
                        <span x-show="!loading && nl_op === 'OUT'" style="display: none;">{{ $isEn ? 'UNSUBSCRIBE' : 'CANCELLATI' }}</span>
                        <span x-show="loading" style="display: none;">{{ $isEn ? 'SENDING...' : 'INVIO IN CORSO...' }}</span>
                    </button>

                    <!-- Informativa Newsletter link -->
                    <div style="margin-bottom: 8px;">
                        <a href="https://www.bicap.it/bicap-Informativa-Newsletter" target="_blank" rel="noopener noreferrer" style="color: #fedb00 !important; font-family: 'Montserrat', sans-serif !important; font-size: 12px !important; font-weight: 600 !important; text-decoration: underline !important;">
                            <u>{{ $isEn ? 'Newsletter Information Notice' : 'Informativa Newsletter' }}</u>
                        </a>
                    </div>

                    <!-- Radios Opzione Iscrizione / Cancellazione (Ben visibile con testo bianco in grassetto) -->
                    <div style="background-color: rgba(20, 20, 20, 0.85); border: 1px solid #4a4a4a; padding: 8px 14px; border-radius: 4px; display: flex; align-items: center; gap: 24px; margin-top: 4px;">
                        <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; margin: 0;">
                            <input type="radio" name="nl_op" value="IN" x-model="nl_op" style="accent-color: #fedb00; width: 18px; height: 18px; cursor: pointer;">
                            <span style="color: #ffffff !important; font-family: 'Montserrat', sans-serif !important; font-size: 13px !important; font-weight: 700 !important; letter-spacing: 0.5px !important; text-transform: uppercase !important;">
                                {{ $isEn ? 'Subscribe' : 'Iscriviti' }}
                            </span>
                        </label>
                        <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; margin: 0;">
                            <input type="radio" name="nl_op" value="OUT" x-model="nl_op" style="accent-color: #fedb00; width: 18px; height: 18px; cursor: pointer;">
                            <span style="color: #ffffff !important; font-family: 'Montserrat', sans-serif !important; font-size: 13px !important; font-weight: 700 !important; letter-spacing: 0.5px !important; text-transform: uppercase !important;">
                                {{ $isEn ? 'Unsubscribe' : 'Cancellati' }}
                            </span>
                        </label>
                    </div>
                </form>
            </div>

        </div>
    </div>
</section>

<!-- ================= 2. MAIN 4-COLUMN FOOTER ================= -->
<footer class="bicap-footer-section" style="background-color: #151515 !important; border-top: 1px solid #000000 !important; padding: 60px 0 40px 0 !important; color: #ffffff !important; font-family: 'Montserrat', sans-serif !important; width: 100%;">
    <div style="max-width: 1350px; margin: 0 auto; padding: 0 24px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 40px; padding-bottom: 50px;">
            
            <!-- Colonna 1: MENÙ -->
            <div>
                <h4 style="color: #ffffff !important; font-family: 'Montserrat', sans-serif !important; font-weight: 700 !important; font-size: 14px !important; letter-spacing: 1px !important; text-transform: uppercase !important; margin: 0 0 24px 0 !important;">
                    {{ $isEn ? 'MENU' : 'MENÙ' }}
                </h4>
                <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px;">
                    <li><a href="{{ url('/mission-vision') }}">MISSION &amp; VISION</a></li>
                    <li><a href="{{ url('/condizioni-di-vendita') }}">{{ $isEn ? 'SUPPORT' : 'SUPPORTO' }}</a></li>
                    <li><a href="{{ route('public.home') }}">SHOP</a></li>
                    <li><a href="{{ route('public.account.dashboard') }}" style="color: #ffffff !important; font-weight: 700;">{{ $isEn ? 'PERSONAL AREA' : 'AREA PERSONALE' }}</a></li>
                    <li><a href="{{ route('login') }}" style="color: #fedb00 !important; font-weight: 700;">PORTALE B2B</a></li>
                </ul>
            </div>

            <!-- Colonna 2: AREA LEGALE -->
            <div>
                <h4 style="color: #ffffff !important; font-family: 'Montserrat', sans-serif !important; font-weight: 700 !important; font-size: 14px !important; letter-spacing: 1px !important; text-transform: uppercase !important; margin: 0 0 24px 0 !important;">
                    {{ $isEn ? 'LEGAL AREA' : 'AREA LEGALE' }}
                </h4>
                <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px;">
                    <li><a href="https://www.bicap.it/bicap-Privacy-Policy" target="_blank" rel="noopener noreferrer">PRIVACY POLICY</a></li>
                    <li><a href="https://www.bicap.it/bicap-Informativa-Form-Contatti" target="_blank" rel="noopener noreferrer">{{ $isEn ? 'CONTACT FORM NOTICE' : 'INFORMATIVA FORM CONTATTI' }}</a></li>
                    <li><a href="https://www.bicap.it/bicap-Informativa-Newsletter" target="_blank" rel="noopener noreferrer">{{ $isEn ? 'NEWSLETTER NOTICE' : 'INFORMATIVA NEWSLETTER' }}</a></li>
                    <li><a href="https://www.bicap.it/bicap-Informativa-Cookie" target="_blank" rel="noopener noreferrer">{{ $isEn ? 'COOKIE NOTICE' : 'INFORMATIVA COOKIE' }}</a></li>
                    <li><a href="https://www.bicap.it/bicap-Informativa-Form-Registrazione" target="_blank" rel="noopener noreferrer">{{ $isEn ? 'REGISTRATION FORM NOTICE' : 'INFORMATIVA FORM REGISTRAZIONE' }}</a></li>
                </ul>
            </div>

            <!-- Colonna 3: SUPPORTO -->
            <div>
                <h4 style="color: #ffffff !important; font-family: 'Montserrat', sans-serif !important; font-weight: 700 !important; font-size: 14px !important; letter-spacing: 1px !important; text-transform: uppercase !important; margin: 0 0 24px 0 !important;">
                    {{ $isEn ? 'SUPPORT' : 'SUPPORTO' }}
                </h4>
                <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px;">
                    <li><a href="{{ url('/condizioni-di-vendita') }}">{{ $isEn ? 'TERMS OF SALE' : 'CONDIZIONI DI VENDITA' }}</a></li>
                    <li><a href="{{ url('/resi-e-rimborsi') }}">{{ $isEn ? 'RETURNS & REFUNDS' : 'RESI E RIMBORSI' }}</a></li>
                    <li><a href="{{ url('/spedizioni') }}">{{ $isEn ? 'SHIPPING' : 'SPEDIZIONI' }}</a></li>
                    <li><a href="{{ url('/guida-taglie') }}">{{ $isEn ? 'SIZE GUIDE' : 'GUIDA ALLE TAGLIE' }}</a></li>
                    <li><a href="{{ url('/faq') }}">FAQ</a></li>
                </ul>
            </div>

            <!-- Colonna 4: CALZATURIFICIO 5BI S.R.L. -->
            <div>
                <h4 style="color: #ffffff !important; font-family: 'Montserrat', sans-serif !important; font-weight: 700 !important; font-size: 14px !important; letter-spacing: 1px !important; text-transform: uppercase !important; margin: 0 0 24px 0 !important;">
                    CALZATURIFICIO 5BI S.R.L.
                </h4>
                <div style="display: flex; flex-direction: column; gap: 10px; font-size: 12px; color: #ffffff; line-height: 1.6;">
                    <!-- Indirizzo -->
                    <div style="display: flex; align-items: flex-start; gap: 8px;">
                        <span class="bicap-footer-icon-yellow" style="color: #fedb00; margin-top: 3px;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span>Via Trani, 94-100 - 76121 Barletta (BT)</span>
                    </div>

                    <!-- Telefono -->
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span class="bicap-footer-icon-yellow" style="color: #fedb00;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                            </svg>
                        </span>
                        <a href="tel:+390883332137" style="color: #ffffff; font-size: 12px; text-transform: none;">+39 0883 332137</a>
                    </div>

                    <!-- Email -->
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span class="bicap-footer-icon-yellow" style="color: #fedb00;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                            </svg>
                        </span>
                        <a href="mailto:info@bicap.it" style="color: #ffffff; font-size: 12px; text-transform: none;">info@bicap.it</a>
                    </div>

                    <!-- Partita IVA -->
                    <div style="color: #888888; font-size: 12px; padding-top: 2px;">
                        Partita IVA 04056210729
                    </div>

                    <!-- Link con icone gialle -->
                    <div style="display: flex; flex-direction: column; gap: 8px; padding-top: 8px;">
                        <a href="https://bicap.it/documents/" target="_blank" rel="noopener noreferrer" style="color: #fedb00 !important; font-size: 12px; display: inline-flex; align-items: center; gap: 6px; font-weight: 600;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                            <span>Scarica dichiarazioni di conformità UE</span>
                        </a>
                        <a href="https://www.bicap.shop/bicap-Disposizioni-ambientali" target="_blank" rel="noopener noreferrer" style="color: #fedb00 !important; font-size: 12px; display: inline-flex; align-items: center; gap: 6px; font-weight: 600;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 2a2 2 0 00-2 2v11a3 3 0 106 0V4a2 2 0 00-2-2H4zm1 14a1 1 0 100-2 1 1 0 000 2zm5-1.757l4.9-4.9a2 2 0 000-2.828L13.485 5.1a2 2 0 00-2.828 0L10 5.757v8.486zM16 18H9.071l6-6H16a2 2 0 012 2v2a2 2 0 01-2 2z" clip-rule="evenodd" />
                            </svg>
                            <span>Disposizioni ambientali</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ================= 3. BOTTOM BAR (Copyright, Payments, Socials) ================= -->
    <div style="border-top: 1px solid #222222; padding-top: 25px; margin-top: 10px;">
        <div style="max-width: 1350px; margin: 0 auto; padding: 0 24px; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 20px;">
            
            <!-- Copyright -->
            <div style="color: #777777; font-size: 11px; font-family: 'Montserrat', sans-serif;">
                Calzaturificio 5BI s.r.l. - Tutti i diritti riservati 2022 &copy; Cedam srl
            </div>

            <!-- Payment Badges (Clean monochrome like bicap.shop) -->
            <div style="display: flex; align-items: center; gap: 20px; color: #999999; font-size: 13px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase;">
                <span>VISA</span>
                <span style="font-size: 11px;">AMERICAN EXPRESS</span>
                <span>MasterCard</span>
                <span>PayPal</span>
            </div>

            <!-- Social Links -->
            <div style="display: flex; align-items: center; gap: 16px;">
                <a href="https://www.linkedin.com/company/bicap-calzaturificio-5bi" target="_blank" rel="noopener noreferrer" style="color: #ffffff;" title="LinkedIn" onmouseover="this.style.color='#fedb00'" onmouseout="this.style.color='#ffffff'">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                </a>
                <a href="https://www.facebook.com/bicap.it" target="_blank" rel="noopener noreferrer" style="color: #ffffff;" title="Facebook" onmouseover="this.style.color='#fedb00'" onmouseout="this.style.color='#ffffff'">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
                </a>
                <a href="https://www.instagram.com/bicap_official/" target="_blank" rel="noopener noreferrer" style="color: #ffffff;" title="Instagram" onmouseover="this.style.color='#fedb00'" onmouseout="this.style.color='#ffffff'">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                </a>
                <a href="https://www.youtube.com/@bicap_official" target="_blank" rel="noopener noreferrer" style="color: #ffffff;" title="YouTube" onmouseover="this.style.color='#fedb00'" onmouseout="this.style.color='#ffffff'">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                </a>
            </div>

        </div>
    </div>
</footer>

<!-- ================= 4. FLOATING WIDGETS (WhatsApp & Scroll to top) ================= -->
<!-- WhatsApp Floating Button -->
<a href="https://wa.me/393518581006" 
   target="_blank" 
   rel="noopener noreferrer" 
   class="bicap-whatsapp-btn" 
   title="Contattaci su WhatsApp">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
    </svg>
</a>

<!-- Scroll To Top Button -->
<button onclick="window.scrollTo({top: 0, behavior: 'smooth'});" 
        class="bicap-scroll-top-btn" 
        title="Torna su">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 font-bold" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
    </svg>
</button>

<script>
    function newsletterForm() {
        return {
            email: '',
            nl_op: 'IN',
            loading: false,
            message: '',
            couponCode: '',
            success: true,

            async submitNewsletter() {
                if (!this.email) return;
                this.loading = true;
                this.message = '';
                this.couponCode = '';

                try {
                    const response = await fetch('{{ route('public.newsletter.subscribe') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            email: this.email,
                            nl_op: this.nl_op
                        })
                    });

                    const data = await response.json();
                    this.loading = false;

                    if (response.ok && data.success) {
                        this.success = true;
                        this.message = data.message;
                        this.couponCode = data.coupon || '';
                        if (this.nl_op === 'IN') {
                            this.email = '';
                        }
                    } else {
                        this.success = false;
                        this.message = data.message || '{{ $isEn ? "An error occurred, please try again." : "Si è verificato un errore, riprova più tardi." }}';
                        this.couponCode = '';
                    }
                } catch (err) {
                    this.loading = false;
                    this.success = false;
                    this.message = '{{ $isEn ? "Connection error. Please try again later." : "Errore di connessione. Riprova più tardi." }}';
                    this.couponCode = '';
                }
            }
        };
    }
</script>
