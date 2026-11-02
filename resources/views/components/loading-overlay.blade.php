@php
    $isEn = ($isEn ?? false) || (app()->getLocale() === 'en');
@endphp

<!-- BICAP Global Loading Overlay Component -->
<div id="bicap-loading-overlay" class="bicap-loading-overlay" aria-hidden="true" role="status" aria-live="polite">
    <div class="bicap-loading-card">
        <!-- Top animated glowing gradient bar -->
        <div class="bicap-loading-accent-bar"></div>

        <!-- Central animated spinner / icon -->
        <div class="bicap-loading-spinner-wrap">
            <div class="bicap-loading-ring"></div>
            <div class="bicap-loading-icon-box">
                <svg id="bicap-loading-icon-svg" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
            </div>
        </div>

        <!-- Dynamic Title -->
        <h3 id="bicap-loading-title" class="bicap-loading-title">
            {{ $isEn ? 'Processing Order...' : 'Invio dell\'ordine in corso...' }}
        </h3>

        <!-- Dynamic Descriptive Message -->
        <p id="bicap-loading-message" class="bicap-loading-message">
            {{ $isEn 
                ? 'We are recording your order and syncing data with the system. This may take a few moments: please wait without refreshing or clicking back.' 
                : 'Stiamo registrando l\'ordine e inviando i dati al gestionale aziendale. L\'operazione potrebbe richiedere alcuni secondi: si prega di attendere senza ricaricare la pagina né premere indietro.' }}
        </p>

        <!-- Indeterminate progress bar -->
        <div class="bicap-loading-progress-track">
            <div class="bicap-loading-progress-fill"></div>
        </div>

        <!-- Security footer indicator -->
        <div class="bicap-loading-safety-hint">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
            <span>{{ $isEn ? 'SECURE TRANSACTION • BICAP SYSTEMS' : 'TRANSAZIONE SICURA • SISTEMI BICAP' }}</span>
        </div>
    </div>
</div>

<style>
    /* Scope del Loading Overlay */
    .bicap-loading-overlay {
        position: fixed !important;
        inset: 0 !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        z-index: 2147483647 !important; /* Top-most layer */
        background: rgba(10, 10, 10, 0.72) !important;
        backdrop-filter: blur(7px) !important;
        -webkit-backdrop-filter: blur(7px) !important;
        display: none !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 20px !important;
        box-sizing: border-box !important;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
        opacity: 0;
        transition: opacity 0.25s ease !important;
    }

    .bicap-loading-overlay.bicap-active {
        display: flex !important;
        opacity: 1 !important;
    }

    .bicap-loading-card {
        background: #ffffff !important;
        border-radius: 26px !important;
        padding: 38px 30px 28px !important;
        max-width: 450px !important;
        width: 100% !important;
        text-align: center !important;
        box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.15) !important;
        position: relative !important;
        overflow: hidden !important;
        animation: bicapCardPop 0.28s cubic-bezier(0.16, 1, 0.3, 1) !important;
        color: #1e293b !important;
    }

    @keyframes bicapCardPop {
        from {
            opacity: 0;
            transform: scale(0.92) translateY(12px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .bicap-loading-accent-bar {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        height: 5px !important;
        background: linear-gradient(90deg, #fedb00, #e35205, #fedb00) !important;
        background-size: 200% 100% !important;
        animation: bicapBarFlow 2s infinite linear !important;
    }

    @keyframes bicapBarFlow {
        0% { background-position: 100% 0; }
        100% { background-position: -100% 0; }
    }

    .bicap-loading-spinner-wrap {
        width: 68px !important;
        height: 68px !important;
        margin: 0 auto 20px auto !important;
        position: relative !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .bicap-loading-ring {
        position: absolute !important;
        inset: 0 !important;
        border: 3.5px solid rgba(227, 82, 5, 0.15) !important;
        border-top-color: #e35205 !important;
        border-right-color: #fedb00 !important;
        border-radius: 50% !important;
        animation: bicapSpinRing 0.9s cubic-bezier(0.55, 0.15, 0.45, 0.85) infinite !important;
    }

    @keyframes bicapSpinRing {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .bicap-loading-icon-box {
        width: 44px !important;
        height: 44px !important;
        background: #0f172a !important;
        color: #fedb00 !important;
        border-radius: 50% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25) !important;
        animation: bicapIconPulse 1.8s infinite ease-in-out !important;
    }

    @keyframes bicapIconPulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.08); }
    }

    .bicap-loading-title {
        font-size: 1.25rem !important;
        font-weight: 800 !important;
        color: #0f172a !important;
        margin: 0 0 10px 0 !important;
        letter-spacing: -0.3px !important;
        line-height: 1.3 !important;
        text-transform: uppercase !important;
    }

    .bicap-loading-message {
        font-size: 0.88rem !important;
        color: #475569 !important;
        line-height: 1.55 !important;
        margin: 0 0 24px 0 !important;
        font-weight: 500 !important;
    }

    .bicap-loading-progress-track {
        height: 6px !important;
        width: 100% !important;
        background: #f1f5f9 !important;
        border-radius: 6px !important;
        overflow: hidden !important;
        position: relative !important;
    }

    .bicap-loading-progress-fill {
        width: 45% !important;
        height: 100% !important;
        background: linear-gradient(90deg, #fedb00, #e35205) !important;
        border-radius: 6px !important;
        position: absolute !important;
        left: -45% !important;
        animation: bicapProgressSlide 1.5s infinite ease-in-out !important;
    }

    @keyframes bicapProgressSlide {
        0% { left: -45%; }
        50% { left: 40%; }
        100% { left: 105%; }
    }

    .bicap-loading-safety-hint {
        font-size: 0.72rem !important;
        color: #94a3b8 !important;
        margin-top: 16px !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.6px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px !important;
    }

    /* Style for buttons in loading state */
    button.bicap-submitting-btn,
    .btn.bicap-submitting-btn {
        pointer-events: none !important;
        opacity: 0.82 !important;
        cursor: wait !important;
        position: relative !important;
    }

    .bicap-btn-spinner {
        display: inline-block !important;
        width: 1.1em !important;
        height: 1.1em !important;
        border: 2.2px solid currentColor !important;
        border-top-color: transparent !important;
        border-radius: 50% !important;
        animation: bicapSpinRing 0.7s linear infinite !important;
        vertical-align: -0.15em !important;
        margin-right: 8px !important;
        box-sizing: border-box !important;
    }
</style>

<script>
(function() {
    const isEn = {{ $isEn ? 'true' : 'false' }};

    const TEXTS = {
        order: {
            title: isEn ? 'Submitting Order...' : 'Invio dell\'ordine in corso...',
            message: isEn 
                ? 'We are recording your order and syncing data with the system. This may take a few moments: please wait without refreshing or clicking back.'
                : 'Stiamo registrando l\'ordine e inviando i dati al gestionale aziendale. L\'operazione potrebbe richiedere alcuni secondi: si prega di attendere senza ricaricare la pagina né premere indietro.',
            btnText: isEn ? 'Submitting Order...' : 'Invio ordine in corso...',
            icon: '<circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>'
        },
        cart: {
            title: isEn ? 'Adding to Cart...' : 'Aggiunta al carrello in corso...',
            message: isEn 
                ? 'Checking inventory availability and updating your cart. Please wait a moment...'
                : 'Stiamo verificando le disponibilità e aggiornando il tuo carrello. Un attimo di pazienza...',
            btnText: isEn ? 'Adding...' : 'Aggiunta in corso...',
            icon: '<circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>'
        },
        confirmOrder: {
            title: isEn ? 'Confirming Order...' : 'Conferma ordine in corso...',
            message: isEn 
                ? 'Verifying stock and transmitting order confirmation to head office. Please wait...'
                : 'Stiamo verificando le giacenze e inviando la conferma dell\'ordine alla sede aziendale. Attendere prego...',
            btnText: isEn ? 'Confirming...' : 'Conferma in corso...',
            icon: '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline>'
        },
        generic: {
            title: isEn ? 'Processing...' : 'Elaborazione in corso...',
            message: isEn 
                ? 'Your request is being processed. Please wait without closing or refreshing the page.'
                : 'L\'operazione è in fase di elaborazione. Attendere qualche istante senza chiudere o ricaricare la pagina.',
            btnText: isEn ? 'Processing...' : 'Attendere prego...',
            icon: '<path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path>'
        }
    };

    let safetyTimer = null;
    let activeSubmittingForms = new Set();
    let originalButtonsState = new Map();

    function getOverlay() {
        return document.getElementById('bicap-loading-overlay');
    }

    function showOverlay(type, customTitle, customMessage) {
        const overlay = getOverlay();
        if (!overlay) return;

        const config = TEXTS[type] || TEXTS.generic;
        const titleEl = document.getElementById('bicap-loading-title');
        const messageEl = document.getElementById('bicap-loading-message');
        const iconSvg = document.getElementById('bicap-loading-icon-svg');

        if (titleEl) titleEl.textContent = customTitle || config.title;
        if (messageEl) messageEl.textContent = customMessage || config.message;
        if (iconSvg && config.icon) iconSvg.innerHTML = config.icon;

        overlay.classList.add('bicap-active');
        overlay.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        // Timer di sicurezza (dopo 45 secondi, se ancora non ha completato, sblocca per non intrappolare l'utente)
        clearTimeout(safetyTimer);
        safetyTimer = setTimeout(function() {
            hideOverlay();
        }, 45000);
    }

    function hideOverlay() {
        const overlay = getOverlay();
        if (!overlay) return;

        overlay.classList.remove('bicap-active');
        overlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        clearTimeout(safetyTimer);

        // Ripristina eventuali form e bottoni
        activeSubmittingForms.forEach(function(form) {
            form.removeAttribute('data-bicap-submitting');
        });
        activeSubmittingForms.clear();

        originalButtonsState.forEach(function(orig, btn) {
            if (btn && btn.isConnected) {
                btn.classList.remove('bicap-submitting-btn');
                btn.innerHTML = orig.html;
                btn.disabled = orig.disabled || false;
                btn.style.minWidth = orig.minWidth || '';
                btn.style.minHeight = orig.minHeight || '';
                btn.style.pointerEvents = '';
                btn.style.opacity = '';
                btn.style.cursor = '';
            }
        });
        originalButtonsState.clear();
    }

    // Esponi API globale window.BicapLoading
    window.BicapLoading = {
        show: showOverlay,
        hide: hideOverlay
    };

    // Quando la pagina viene ripristinata dalla cache del browser (Back/Forward cache)
    window.addEventListener('pageshow', function(event) {
        hideOverlay();
    });

    // Risoluzione tipo di form in base ad action e attributi
    function detectFormType(form) {
        const customType = form.getAttribute('data-loading-type');
        if (customType) return customType;

        const action = (form.getAttribute('action') || '').toLowerCase();
        const id = (form.getAttribute('id') || '').toLowerCase();

        if (action.includes('checkout') || id.includes('checkout') || action.includes('process-checkout')) {
            return 'order';
        }
        if (action.includes('cart/add') || action.includes('agent/cart/add')) {
            return 'cart';
        }
        if (action.includes('orders') && action.includes('confirm')) {
            return 'confirmOrder';
        }
        if (action.includes('cart/update') || action.includes('cart/remove')) {
            return 'generic';
        }

        return null; // Non intercettare form normali (es. ricerche o filtri veloci)
    }

    function handleFormSubmit(e) {
        const form = e.target;
        if (!form || form.nodeName !== 'FORM') return;

        const type = detectFormType(form);
        if (!type) return;

        // Se la validazione HTML5 non passa (es. campi required vuoti), lascia che il browser segnali l'errore senza mostrare l'overlay
        if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
            return;
        }

        // Prevenzione di doppio invio / click multipli
        if (form.getAttribute('data-bicap-submitting') === 'true') {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }

        form.setAttribute('data-bicap-submitting', 'true');
        activeSubmittingForms.add(form);

        // Trova il pulsante di submit
        let submitBtn = form.querySelector('button[type="submit"]:not([disabled]), input[type="submit"]:not([disabled])');
        if (!submitBtn && form.id) {
            submitBtn = document.querySelector('button[form="' + form.id + '"]:not([disabled])');
        }

        const config = TEXTS[type] || TEXTS.generic;

        if (submitBtn) {
            // Salva stato originale
            if (!originalButtonsState.has(submitBtn)) {
                originalButtonsState.set(submitBtn, {
                    html: submitBtn.innerHTML,
                    disabled: submitBtn.disabled,
                    minWidth: submitBtn.style.minWidth,
                    minHeight: submitBtn.style.minHeight
                });
            }

            // Preserva dimensioni esatte per evitare scatti visivi
            try {
                const rect = submitBtn.getBoundingClientRect();
                if (rect.width > 0) submitBtn.style.minWidth = rect.width + 'px';
                if (rect.height > 0) submitBtn.style.minHeight = rect.height + 'px';
            } catch(e) {}

            // Aggiungi classe e spinner al bottone
            submitBtn.classList.add('bicap-submitting-btn');
            submitBtn.style.pointerEvents = 'none';
            submitBtn.style.cursor = 'wait';
            
            // Inserisci spinner interno mantenendo l'altezza
            submitBtn.innerHTML = '<span class="bicap-btn-spinner" aria-hidden="true"></span> <span>' + (config.btnText || 'Caricamento...') + '</span>';

            // Disabilita dopo un breve ritardo per consentire l'avvio della richiesta POST nativa da parte del browser
            setTimeout(function() {
                submitBtn.disabled = true;
            }, 30);
        }

        // Mostra l'overlay a tutto schermo con avviso di caricamento
        showOverlay(type);
    }

    // Registra listener globale su submit
    document.addEventListener('submit', handleFormSubmit, true);

})();
</script>
